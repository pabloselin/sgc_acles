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

function sgcinsc_verifydata($data) {
	global $wpdb, $post;
	// 1.Verificar que el nonce funcione
	if(!wp_verify_nonce( $_POST['sgcinsc_nonce'], 'submit_stepone' )) {
		echo 'El nonce es inválido';
		die();
	} else {
		//Escapar todos los datos
		$data = esc_sql( $data );
		
		//Arreglar las variables
		$rut_apoderado = sgcinsc_processrut($data['rut_apoderado']);
		$rut_alumno = sgcinsc_processrut($data['rut_alumno']);				

		// 1. Verificar que los ruts no estén repetidos
		if(sgcinsc_checkreprut($rut_alumno, 'rut_alumno')):
			//Verificar que los cursos no se hayan llenado mientras se producía la postulación.
			$preserialize = sgcinsc_serializeacles($data);
			//Verificar que haya llenado el mínimo de cursos requeridos		
			foreach($preserialize[0] as $precupo) {						
					$cupos = sgcinsc_cupos($precupo);					
					if( $cupos <= 0):
						echo '<div class="alert alert-error">';
						echo '<h1>Error en la inscripción</h1>';
						echo '<p>Uno de sus cursos seleccionados ya no tiene vacantes, probablemente alguien completó el proceso de postulación antes de que usted lo enviara.</p>';
						echo '<p>Si cree que se trata de un error, por favor comuníquese con inscripcionacle@gmail.com</p>';
						echo '<p><a class="btn btn-success" href="' . get_permalink($post->ID) . '">Intentar inscripción nuevamente</a></p>';
						echo '</div>';
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

			echo '<div class="alert alert-success">';			
			echo '<h1>¡Hemos recibido su inscripción!</h2>';
			echo '<p>En unos minutos recibirá un aviso informativo en el email del apoderado(a) <strong>&lt;' . $email_apoderado . '&gt;</strong>';
			if($email_alumno):
				echo ' y el email del alumno(a) <strong>&lt;' . $email_alumno . '&gt;</strong>';
			endif;
			echo '<p> El número identificador de su inscripción es el <strong>'. $ID_inscripcion . '</strong></p>';
			echo '<p> Si no lo recibiera, revise su casilla spam, de todos modos la inscripción ya ha sido registrada y se encuentra en nuestra base de datos.</p>';			
			echo '<p><a id="certinsc" data-idinsc="'.$ID_inscripcion.'" class="btn btn-info btn-large" href="#">Ver certificado de inscripción (en una ventana emergente)</a>';
			echo '<p> Dudas y consultas sobre acles y su proceso de inscripción: <a href="mailto:inscripcionacle@gmail.com">inscripcionacle@gmail.com</a> </p>';
			echo '<p> ¡Gracias!</p>';
			echo '<div class="hidden" id="certificado">';
			echo '<style>@media print { body { text-align:center; } button { display:none !important;} }</style>';			
			echo '<table cellpadding="20" cellspacing="0" width="500" style="font-family:sans-serif;text-align:center;background-color:#D3E3EB;margin:24px;border:1px solid #1470A2;">
    		<tr><td>
    			<p style="text-align:center"><img style="margin:0 auto;" src="http://www.saintgasparcollege.cl/wp-content/themes/sangaspar/i/logosgc2013.png"><h2 style="text-align:center;color:#1470A2;font-weight:normal;">Saint Gaspar College</h2><h3 style="text-align:center;">Inscripción en A.C.L.E. 2015</h3></p>
		        <h1>Comprobante de Inscripción</h1>
		        <p>El apoderado(a) <strong>'.$data['nombre_apoderado'].'</strong> inscribió los siguientes A.C.L.E. para el alumno(a) <strong>'.$data['nombre_alumno'].' del curso ' . sgcinsc_nicecurso($data['curso_alumno']). ' ' . $data['letracurso_alumno'] . '</strong></p>';
		        echo '</td>';
				echo '</tr>';

		        foreach($acles[0] as $acle):
					$aclepost = get_post($acle);
					echo '<tr style="border-bottom:1px solid #456A7D;"><td style="border-bottom:1px solid #456A7D;">';
					echo '<strong>' . $aclepost->post_title . '</strong>';
					$prof = get_post_meta($aclepost->ID, 'sgcinsc_profacle', true);
					// if($prof):
					// 	echo '<p>Profesor: ' . get_post_meta($aclepost->ID, 'sgcinsc_profacle', true) . '</p>';
					// endif;
					echo '<p>Horario: '. sgcinsc_nicedia(get_post_meta($aclepost->ID, 'sgcinsc_diaacle', true)) . ' ' . sgcinsc_renderhorario(get_post_meta($aclepost->ID, 'sgcinsc_horaacle', true)) . '</p>';
					echo '</td></tr>';	
				endforeach;				             

			echo '<tr><td>';
			echo '<p>Su número de inscripción es el '. $ID_inscripcion . '</p>';
			echo '</td></tr>';
			echo '</table>';
			echo '<p style="text-align:center;"><button style="font-size:16px;" target="_blank" class="btn btn-info" href="#" onclick="window.print();" id="printcert"><i class="icon icon-printer"></i> Imprimir comprobante</button></p>';
			echo '</div>';			
			
			$curso = sgcinsc_nicecurso($data['curso_alumno']) . ' ' . $data['letracurso_alumno'];		
			sgcinsc_confirmail($email_alumno, $email_apoderado, $data['nombre_alumno'], $data['nombre_apoderado'], $acles[0], $ID_inscripcion, $curso);		

			echo '</div>';

		else:

			echo '<div class="alert alert-error">';
			echo '<h1>Error en la inscripción</h1>';
			echo '<p>Ya existe una inscripción asociada al RUT del alumno.</p>';
			echo '<p>Si cree que se trata de un error, por favor comuníquese con inscripcionacle@gmail.com</p>';
			echo '<p><a class="btn btn-success" href="' . get_permalink($post->ID) . '">Intentar inscripción nuevamente</a></p>';
			echo '</div>';
		endif;
		
	}	
}

function sgcinsc_confirmail($email_alumno, $email_apoderado, $nombre_alumno, $nombre_apoderado, $acles, $ID_inscripcion, $cursoalumno) {	
	$message .= '<table cellpadding="20" cellspacing="0" width="600" style="background-color:#D3E3EB;margin:24px;border:1px solid #1470A2;"><tr><td>';
	$message .= '<p style="text-align:center"><img style="margin:0 auto;" src="http://www.saintgasparcollege.cl/wp-content/themes/sangaspar/i/logosgc2013.png"><h2 style="text-align:center;color:#1470A2">Saint Gaspar College</h2><h3 style="text-align:center;font-size:24px;color:#2C86C7;">Inscripción en A.C.L.E. 2015</h3></p>';
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
				'id_inscripcion' => $lastid				
				)
			);
	}
	return $lastid;
}