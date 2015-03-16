<?php 
function sgcinsgc_forms_sections($template)
{	
	/*
	The answer to this is quite involved and getting it right takes a little care, but here are the essential points of one approach:

		1. The action must be set to load the current page and you can do this by changing the action attribute in the <form> tag to: action=""
		
		2. In the template file for this page, detect when the page loads after a form submission. You can do this by checking the state of the $_POST variable.
		
		3. If there are post variables, process the form submission in the template file. You need to look at what happens in "send_form.php" to figure out what to do. Take care that you don't introduce security issues (e.g. be sure to use the NONCE).
		
		4. Redirect back to the same page to get rid of the post variables. If you don't do this people will get those "Do you want to resubmit the form" warning messages. See the PHP header() function for doing the redirect and note that this must happen before any output is sent to the page.

		5. Use server sessions to display a "Form submission successful". Set a session variable to your message before the redirect, then detect it when the page loads, display it, and remove it so the message doesn't display the next time the page loads.

	*/

	//Tomar variables
		if($_GET['sgcinsc_cert'] == 1 && $_GET['sgcinsc_nonce'] && $_GET['idinsc']):
			$newtemplate = plugin_dir_path(__FILE__) . '/views/certificado.php';		
			return $newtemplate;
		else:
			return $template;
		endif;	
}

//add_action('template_include', 'sgcinsgc_forms_sections', 1);

//Hacerlo via shortcode así tenemos mayor control sobre la URL y es más independiente del tema

function sgcinsc_shortcode($atts) {	
	ob_start();
		include( plugin_dir_path(__FILE__) . '/views/steps.php');	
		return ob_get_clean();	
}

add_shortcode( 'sgcinsc_form', 'sgcinsc_shortcode' );

function sgcinsc_viewaclesshortcode($atts) {
	return sgcinsc_todoacles();
}

add_shortcode('sgcinsc_acles', 'sgcinsc_viewaclesshortcode');

function sgcinsc_getsessiondata() {

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
			$secondsult = $wpdb->get_var("SELECT second_insc FROM $table2_name WHERE id_inscripcion = $consult->id");
			if($secondsult == 1) {
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

		// 1. Verificar que los ruts no estén repetidos
		if(sgcinsc_checksecondreprut($rut_alumno, 'rut_alumno')):
			//Verificar que los cursos no se hayan llenado mientras se producía la postulación.
			$preserialize = sgcinsc_serializeacles($data);
			//Verificar que haya llenado el mínimo de cursos requeridos		
			foreach($preserialize[0] as $precupo) {						
					$cupos = sgcinsc_cupos($precupo);					
					if( $cupos <= 0):
						$cuposurl = add_query_arg('excode', 3, get_permalink());
						wp_redirect($cuposurl, 303);
						die();
					endif;
			}


			//Verificar que no hayan cosas raras

			// 3.Envía correo de confirmación
			$email_alumno = $data['email_alumno'];
			$email_apoderado = $data['email_apoderado'];

			//el ingreso devuelve un ID de regalo
			$ID_inscripcion = sgcinsc_storeformdata($data);		
			$acles = sgcinsc_serializeacles($data);		
			$successarr = array(
							'excode' => 1,
							'idinsc' => $ID_inscripcion
							);
			$successurl = add_query_arg($successarr, get_permalink());
			wp_redirect($successurl, 303);

		else:
			$errorurl = add_query_arg('excode', 2, get_permalink());
			wp_redirect($errorurl, 303);
		endif;
		
	}	
}

function sgcinsc_confirmail($email_alumno, $email_apoderado, $nombre_alumno, $nombre_apoderado, $acles, $ID_inscripcion, $cursoalumno) {	
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
						'acles_inscritos' => $aclestring
						)
					);	
	$lastid = $wpdb->insert_id;	

	//Inserta cupos
	foreach($acles[0] as $acle){
		$wpdb->insert($table2_name,
			array(				
				'id_curso' => $acle,
				'id_inscripcion' => $lastid,
				'second_insc' => $prev				
				)
			);
	}
	return $lastid;
}