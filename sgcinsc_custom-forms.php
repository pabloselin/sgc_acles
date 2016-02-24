<?php 

//Hacerlo via shortcode así tenemos mayor control sobre la URL y es más independiente del tema

function sgcinsc_shortcode($atts) {	
		ob_start();
		include( plugin_dir_path(__FILE__) . '/views/steps.php');	
		return ob_get_clean();	
}

add_shortcode( 'sgcinsc_form', 'sgcinsc_shortcode' );

function sgcinsc_viewaclesshortcode($atts) {
	$a = shortcode_atts( array('cupos' => false) , $atts );	
	$cupos = $a['cupos'];
	return sgcinsc_todoacles($cupos);
}

add_shortcode('sgcinsc_acles', 'sgcinsc_viewaclesshortcode');

function sgcinsc_checkrep($rut, $columna) {
	$stage = SGCINSC_STAGE;
	if($stage > 1):
		return sgcinsc_checksecondreprut($rut, $columna);
	else:
		return sgcinsc_checkreprut($rut, $columna);
	endif;
}

function sgcinsc_checkreprut($rut, $columna) {
	global $wpdb, $table_name;
	$consulta = $wpdb->get_var(
			"SELECT id FROM $table_name
			WHERE $columna = $rut"			
		);
	if($consulta > 0):
		return false;
	else:
		return true;
	endif;
}

function sgcinsc_checksecondreprut($rut, $columna) {
	//chequea que el rut no se haya usado para una tercera inscripción
	global $wpdb, $table_name, $table2_name;
	$puede = true;
	$consulta = $wpdb->get_results("SELECT * FROM $table_name WHERE $columna = $rut");
	//chequea los múltiples insc donde hay un sgcinsc second
	foreach($consulta as $consult) {
		//Ya hay un rut inscrito
		if($consult->rut_alumno == $rut) {
			$secondsult = $wpdb->get_var("SELECT etapa_insc FROM $table2_name WHERE id_inscripcion = $consult->id");
			if($secondsult == SGCINSC_STAGE) {
				//Ya hay una segunda inscripción
				$puede = false;
			}
		}
	}

	return $puede;
}

function sgcinsc_verifydata($data) {
	global $wpdb, $post;
	// 1.Verificar que el nonce funcione
	

	if(!wp_verify_nonce( $_POST['sgcinsc_nonce'], 'submit_stepone' )) {
		echo 'El nonce es inválido';
		die();
	} else {
		//verificar que no estoy recargando url

		//Escapar todos los datos
		$data = esc_sql( $data );
		
		//Arreglar las variables
		$rut_apoderado = sgcinsc_processrut($data['rut_apoderado']);
		$rut_alumno = sgcinsc_processrut($data['rut_alumno']);				

		 // 1. Verificar que los ruts no estén repetidos dependiendo de la etapa puede haber 1 o 2 etapas de inscripción
		
		if(sgcinsc_checkrep($rut_alumno, 'rut_alumno')):
			//Verificar que los cursos no se hayan llenado mientras se producía la postulación.
			$preserialize = sgcinsc_serializeacles($data);
			//Verificar que haya llenado el mínimo de cursos requeridos		
			foreach($preserialize[0] as $precupo) {						
					$cupos = sgcinsc_cupos($precupo);					
					if( $cupos <= 0):
						$cuposurl = esc_url_raw( add_query_arg('excode', 3, get_permalink()) );
						wp_redirect($cuposurl, 303);
						die();
					endif;
			}


			//Verificar que no hayan cosas raras

			// 3.Envía correo de confirmación
			$email_apoderado = $data['email_apoderado'];

			//el ingreso devuelve un ID de regalo
			$ID_inscripcion = sgcinsc_storeformdata($data);		
			$acles = sgcinsc_serializeacles($data);		
			
			//añado un validador de nonce par ala URL

			$nonce = wp_create_nonce( 'checkinsc' );
			
			$successarr = array(
							'excode' => 1,
							'idinsc' => $ID_inscripcion,
							'checkinsc' => $nonce
							);
			$finalurl = add_query_arg($successarr, get_permalink());
						

			wp_redirect($finalurl, 303);

		else:
			$errorurl = esc_url_raw( add_query_arg('excode', 2, get_permalink()) );
			wp_redirect($errorurl, 303);
		endif;
		
	}	
}

function sgcinsc_confirmail($email_apoderado, $nombre_alumno, $nombre_apoderado, $acles, $ID_inscripcion, $cursoalumno) {	
	$message .= '<table cellpadding="20" cellspacing="0" width="600" style="background-color:#D3E3EB;margin:24px;border:1px solid #1470A2;"><tr><td>';
	$message .= '<p style="text-align:center"><img style="margin:0 auto;" src="http://www.saintgasparcollege.cl/wp-content/themes/sangaspar/i/logosgc2013.png"><h2 style="text-align:center;color:#1470A2">Saint Gaspar College</h2><h3 style="text-align:center;font-size:24px;color:#2C86C7;">Inscripción en A.C.L.E. 2015 (segunda etapa)</h3></p>';
	$message .= '<p>Estimado(a) <strong>' . $nombre_apoderado . ':</strong></p>';
	$message .= '<p>Este correo es una confirmación del proceso de inscripción de A.C.L.E. para el alumno(a) <strong>' . $nombre_alumno . ' del curso ' .  $cursoalumno . '</strong> </p>';
	$message .= '<p>Su número identificador de inscripción es el <strong>'. $ID_inscripcion . '</strong></p>';
	$message .= '<p>Usted inscribió los siguientes cursos:</p>';	
	$message .= '<table cellpadding="5" cellspacing="0" style="background-color:#AFD4E4;margin:0 auto;" width="70%">';
	foreach($acles as $acle):
		$aclepost = get_post($acle);
		$message .= '<tr style="border-bottom:1px solid #456A7D;"><td style="border-bottom:1px solid #456A7D;">';
		$message .= '<strong>' . $aclepost->post_title . '</strong>';
		$prof = get_post_meta($aclepost->ID, 'sgcinsc_profacle', true);
		// if($prof):
		// 	$message .= '<p>Profesor: ' . get_post_meta($aclepost->ID, 'sgcinsc_profacle', true) . '</p>';
		// endif;
		$message .= '<p>Horario: ' . sgcinsc_nicedia(get_post_meta($aclepost->ID, 'sgcinsc_diaacle', true)) . ' ' . sgcinsc_renderhorario(get_post_meta($aclepost->ID, 'sgcinsc_horaacle', true)) . '</p>';
		$message .= '</td></tr>';	
	endforeach;
	$message .= '<tr><td><p><strong>Una vez inscrita la/s ACLE adicional/es, el/la alumno/a tiene el deber de asistir y  responder a las exigencias planteadas en la/s ACLE, según señala el Reglamento de Actividades Curriculares de Libre Elección. En el caso de no asistir, el/la alumno/a obtendrá la nota mínima (2.0) por asistencia, la que será registrada en el sector de aprendizaje afín a la ACLE elegida.</strong></p></td></tr>';
	$message .= '</table>';
	$message .= '<p>En caso que deba modificar su inscripción, podrá hacerlo en el  <a href="' . sgcinsc_url($ID_inscripcion) . '">siguiente link</a> pero solo podrá reasignar con cursos que tengan cupos en ese momento.</p>';
	$message .= '<p>Para consultas escriba a inscripcionacle@gmail.com</p>';
	$message .= '<p>Muchas gracias por su inscripción!</p>' ;
	$message .= '</td></tr></table>';
	$subject = 'Inscripción de A.C.L.E. en Saint Gaspar College';
	$headers = "From: Saint Gaspar College <info@saintgasparcollege.cl>" . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

	$debug = SGCINSC_DEBUG;
	//Email al alumno
	if(!$debug) {
		if (wp_mail( SGCINSC_MAILINSC, $subject, $message, $headers )):
		 	echo '.';
		 endif;

		//Email al apoderado
		if (wp_mail( $email_apoderado, $subject, $message, $headers )):
			echo '.';
		endif;
	}	
}

function sgcinsc_processrut($rut) {
	//Saco los puntos
	$rut = str_replace('.', '', $rut);
	//Elimino el guión
	$rut = str_replace('-', '', $rut);
	//Saco el último dígito
	$rut = substr($rut, 0, -1);

	return $rut;
}

function sgcinsc_serializeacles($data) {
	$acles = array();
	foreach($data as $key=>$dat) {		
		if(in_string('aclecurso', $key)):
			$acles[] = $dat;
		endif;
	}
	return $acles;
}

function sgcinsc_url($idinsc) {
	/**
	 * Devuelve la URL de la inscripción basado en ID
	 */
	global $wpdb, $table_name;
	$inschash = $wpdb->get_var("SELECT hash_inscripcion FROM $table_name WHERE id = $idinsc");
	$acleurl = get_permalink(35861);
	$args = array(
		'ih' => $inschash,
		'id' => $idinsc
		);
	$inscurl = add_query_arg($args, $acleurl);
	return $inscurl;
}

function sgcinsc_storeformdata($data) {
	global $wpdb, $table_name, $table2_name;

	//Preparar info de cursos
	// $acles = array();
	// foreach($data as $key=>$dat) {		
	// 	if(in_string('aclecurso-', $key)):
	// 		$acles[] = $dat;
	// 	endif;
	// }

	$acles = sgcinsc_serializeacles($data);
	$aclestring = serialize($acles[0]);
	$rut_apoderado = sgcinsc_processrut($data['rut_apoderado']);
	$rut_alumno = sgcinsc_processrut($data['rut_alumno']);

	//chequea que no haya otra inscripción en el mismo momento para el mismo curso
	

	//chequea si hubo o no antes inscripción
	$prev = 0;

	$consprev = $wpdb->get_var("SELECT id FROM $table_name WHERE rut_alumno = $rut_alumno");
	if($consprev > 0) {
		$prev = 1;
	}

	//Genera el hash para la inscripción
	$hash = wp_hash( $rut_apoderado );
	//Inserta inscripción y detalles
	$insertinsc = $wpdb->insert($table_name, 
					array(
						'time' => current_time('mysql'),
						'rut_alumno' => $rut_alumno,
						'rut_apoderado' => $rut_apoderado,
						'nombre_alumno' => $data['nombre_alumno'],
						'nombre_apoderado' => $data['nombre_apoderado'],
						'curso_alumno' => $data['curso_alumno'],
						'letracurso_alumno' => $data['letracurso'],
						'email_apoderado' => $data['email_apoderado'],
						'redfija_apoderado' => $data['fono_apoderado'],
						'celu_apoderado' => $data['celu_apoderado'],
						'seguro_escolar' => $data['seguro_alumno'],
						'acles_inscritos' => $aclestring,
						'hash_inscripcion' => $hash
						)
					);	
	$lastid = $wpdb->insert_id;	

	//Inserta cupos son todas inscripciones de segunda etapa
	foreach($acles[0] as $acle){
		$wpdb->insert($table2_name,
			array(				
				'id_curso' => $acle,
				'id_inscripcion' => $lastid,
				'etapa_insc' => SGCINSC_STAGE				
				)
			);
	}
	return $lastid;
}

function sgcinsc_validatehash($id, $hash) {
	/**
	 * Valida el hash en relación con el ID
	 */
	global $wpdb, $table_name;
	$checkhash = $wpdb->get_var("SELECT hash_inscripcion FROM $table_name WHERE id = $id");
	if($checkhash == $hash) {
		//Chequeo correcto
		return true;
	} else {
		//Chequeo incorrecto
		return false;
	}
}

function sgcinsc_changeinsc($id, $hash) {
	//comprobar el hash con el ID
	if(sgcinsc_validatehash($id, $hash)) {
		return '<div id="sgcinsc_reinscripcion">' . sgcinsc_insctemplate($id) . '</div>';
	} else {
		return 'Hash inválido';
	}
}