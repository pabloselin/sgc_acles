<?php
/**
 * Funciones relacionadas con el envío de mensajes de mail y certificados
 */


function sgcinsc_confirmail($id) {

	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];

	$inscripcion = sgcinsc_getinsc($id);
	$nombre_apoderado = $inscripcion[0]->nombre_apoderado;
	$nombre_alumno = $inscripcion[0]->nombre_alumno;
	$cursoalumno = sgcinsc_nicecurso($inscripcion[0]->curso_alumno) . ' ' .  $inscripcion[0]->letracurso_alumno;
	$acles = unserialize($inscripcion[0]->acles_inscritos);
	$email_apoderado = $inscripcion[0]->email_apoderado;


	$message .= '<table cellpadding="20" cellspacing="0" width="600" style="background-color:#D3E3EB;margin:24px;border:1px solid #1470A2;font-family:sans-serif;"><tr><td>';
	$message .= '<p style="text-align:center"><img style="margin:0 auto;" src="http://www.saintgasparcollege.cl/wp-content/themes/sangaspar/i/logosgc2013.png"><h2 style="text-align:center;color:#1470A2">Saint Gaspar College</h2><h3 style="text-align:center;font-size:24px;color:#2C86C7;">';
	$message .= 'Inscripción en A.C.L.E. ';
	
	if($stage > 1):
		$message .= ' adicional ';
	endif;

	$message .=  date('Y') .'</h3></p>';
	$message .= '<p>Estimado(a) <strong>' . $nombre_apoderado . ':</strong></p>';

	if($stage > 1):
		$message .= '<p>Este correo es una confirmación del proceso de inscripción de A.C.L.E. adicionales para el alumno(a) <strong>' . $nombre_alumno . ' del curso ' .  $cursoalumno . '</strong> </p>';
	else:
		$message .= '<p>Este correo es una confirmación del proceso de inscripción de A.C.L.E. para el alumno(a) <strong>' . $nombre_alumno . ' del curso ' .  $cursoalumno . '</strong> </p>';
	endif;


	$message .= '<p>Su número identificador de inscripción es el <strong>'. $id . '</strong></p>';
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
	
	if($stage > 1):

		$message .= '<tr><td><p><strong>' . SGCINSC_ACLERESP . '</strong></p></td></tr>';

	endif;
	
	$message .= '</table>';
	$message .= '<p>En caso que deba modificar su inscripción, podrá hacerlo en el  <a href="' . sgcinsc_url($id) . '">siguiente link</a> pero solo podrá reasignar con cursos que tengan cupos en ese momento.</p>';
	$message .= '<p>Para consultas escriba a ' . SGCINSC_MAILINSC .'</p>';
	$message .= '<p>Muchas gracias por su inscripción!</p>' ;
	$message .= '</td></tr></table>';
	
	if($stage > 1):
		$subject = "Inscripción de ACLE adicional en Saint Gaspar College";
	else:
		$subject = "Inscripción de ACLE en Saint Gaspar College";
	endif;

	$headers = array(
		'From: Saint Gaspar College <info@saintgasparcollege.cl>',
		'Reply-To: Ayuda Acle Saint Gaspar College <' .  SGCINSC_MAILINSC . '>',
		'MIME-Version: 1.0',
		'Content-Type: text/html; charset=UTF-8'
		);

	$debug = SGCINSC_DEBUG;
	
	//Email al alumno
	if(!$debug) {
		if(get_bloginfo('url') == 'http://saintgasparcollege.cl'):
			if (wp_mail( SGCINSC_MAILINSC, $subject, $message, $headers )):
			 	echo '.';
			 endif;

			//Email al apoderado
			if (wp_mail( $email_apoderado, $subject, $message, $headers )):
				echo '.';
			endif;
		else:
			if (wp_mail( SGCINSC_MAILDEBUG, $subject, $message, $headers )):
			 	echo '.';
			 endif;
		endif;
	}
}

function sgcinsc_modifymail($id, $mod) {
	/**
	 * Email que se manda al modificar la inscripción, dependiendo del tipo de modificación que se define en el parámetro * $mod.
	 */

	$options = get_option('sgcinsc_config_options');
	$inscripcion = sgcinsc_getinsc($id);
	$stage = $options['sgcinsc_etapa_insc'];

	$nombre_apoderado = $inscripcion[0]->nombre_apoderado;
	$nombre_alumno = $inscripcion[0]->nombre_alumno;
	$cursoalumno = sgcinsc_nicecurso($inscripcion[0]->curso_alumno) . ' ' .  $inscripcion[0]->letracurso_alumno;
	$acles = unserialize($inscripcion[0]->acles_inscritos);
	$email_apoderado = $inscripcion[0]->email_apoderado;


	$message .= '<table cellpadding="20" cellspacing="0" width="600" style="background-color:#D3E3EB;margin:24px;border:1px solid #1470A2;font-family:sans-serif;"><tr><td>';
	$message .= '<p style="text-align:center"><img style="margin:0 auto;" src="http://www.saintgasparcollege.cl/wp-content/themes/sangaspar/i/logosgc2013.png"><h2 style="text-align:center;color:#1470A2">Saint Gaspar College</h2><h3 style="text-align:center;font-size:24px;color:#2C86C7;">Modificación inscripción ACLE ';
	
	if($stage > 1):
		$message .= ' adicional ';
	endif;

	$message .= date('Y') .'</h3></p>';
	$message .= '<p>Estimado(a) <strong>' . $nombre_apoderado . ':</strong></p>';
	$message .= '<p>Este correo es una confirmación de su modificación de inscripción de ACLE para el alumno(a) <strong>' . $nombre_alumno . ' del curso ' .  $cursoalumno . '</strong> </p>';
	$message .= '<p>Su número identificador de inscripción es el <strong>'. $id . '</strong></p>';

	//Poner que datos se modificaron aquí

	$message .= '<p>Usted tiene inscritos los siguientes A.C.L.E.:</p>';	
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
	
	if($stage > 1):

		$message .= '<tr><td><p><strong>' . SGCINSC_ACLERESP . '</strong></p></td></tr>';

	endif;
	
	$message .= '</table>';
	$message .= '<p>En caso que deba modificar nuevamente su inscripción, podrá hacerlo en el  <a href="' . sgcinsc_url($id) . '">siguiente link</a> pero solo podrá reasignar con cursos que tengan cupos en ese momento.</p>';
	$message .= '<p>Para consultas escriba a ' . SGCINSC_MAILINSC .'</p>';
	$message .= '<p>Muchas gracias!</p>' ;
	$message .= '</td></tr></table>';
	
	if($stage > 1):
		$subject = "Modificación inscripción de ACLE adicional en Saint Gaspar College";
	else:
		$subject = "Modificación inscripción de ACLE en Saint Gaspar College";
	endif;
	

	$headers = array(
		'From: Saint Gaspar College <info@saintgasparcollege.cl>',
		'Reply-To: Ayuda Acle Saint Gaspar College <' .  SGCINSC_MAILINSC . '>',
		'MIME-Version: 1.0',
		'Content-Type: text/html; charset=UTF-8'
		);

	$debug = SGCINSC_DEBUG;
	//Email al alumno
	if(!$debug) {
		if(get_bloginfo('url') == 'http://saintgasparcollege.cl'):
			if (wp_mail( SGCINSC_MAILINSC, $subject, $message, $headers )):
			 	echo '.';
			 endif;

			//Email al apoderado
			if (wp_mail( $email_apoderado, $subject, $message, $headers )):
				echo '.';
			endif;
		else:
			if (wp_mail( SGCINSC_MAILDEBUG, $subject, $message, $headers )):
			 	echo '.';
			 endif;
		endif;
	}	
}