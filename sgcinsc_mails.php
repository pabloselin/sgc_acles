<?php
/**
 * Funciones relacionadas con el envío de mensajes de mail y certificados
 */


function sgcinsc_confirmail($email_apoderado, $nombre_alumno, $nombre_apoderado, $acles, $ID_inscripcion, $cursoalumno) {	
	$message .= '<table cellpadding="20" cellspacing="0" width="600" style="background-color:#D3E3EB;margin:24px;border:1px solid #1470A2;"><tr><td>';
	$message .= '<p style="text-align:center"><img style="margin:0 auto;" src="http://www.saintgasparcollege.cl/wp-content/themes/sangaspar/i/logosgc2013.png"><h2 style="text-align:center;color:#1470A2">Saint Gaspar College</h2><h3 style="text-align:center;font-size:24px;color:#2C86C7;">Inscripción en A.C.L.E. ' . date('Y') .'</h3></p>';
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
	$message .= '<tr><td><p><strong>' . SGCINSC_ACLERESP . '</strong></p></td></tr>';
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