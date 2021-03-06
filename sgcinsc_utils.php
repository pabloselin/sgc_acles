<?php
/**
 * Utilidades para trabajar con el contenido de A.C.L.E.s
 */

function sgcinsc_displaycursos() {
	/**
	 * Muestra los cursos y los cursos preinscritos en el formulario
	 */
	$curso = $_POST['nivel'];
	$rutalumno = sgcinsc_processrut($_POST['rutalumno']);
	$modcond = $_POST['mod'];
	$id = $_POST['idinsc'];

	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];
	$pdfacles = $options['sgcinsc_results_url'];

	$bloquehorario_1 = '15:20 a 16:50';
	$bloquehorario_2 = '17:00 a 18:30';

	$inscripcion = sgcinsc_aclesporalumno($rutalumno);

	$html = '';

	$html .= '<div class="alert alert-info">';

	if($inscripcion && $stage > 1 && $modcond != 1):
		$nombrealumno = sgcinsc_nombrealumno($rutalumno);

		$html .= '<p><strong>' . SGCINSC_RECORDOTORIO . '</strong></p>';
		$html .= '<p>' . SGCINSC_RECPRIMERAETAPA . ' <strong>' . $nombrealumno . '</strong></p>';

		foreach($inscripcion as $acle) {
			$html .= '<p class="oldacle" data-id="'.$acle.'"><strong>'.get_the_title($acle).'</strong> <br> '. 
			sgcinsc_nicehorario(get_post_meta($acle, 'sgcinsc_horaacle', true)). ' ' . sgcinsc_nicedia(get_post_meta($acle, 'sgcinsc_diaacle', true)) . '</p>';
			
		}

	endif;

	if($modcond == 1):
		
		$inscripcion = sgcinsc_getinsc($id);
		$cursos_preinscritos = unserialize($inscripcion[0]->acles_inscritos);

		$nombrealumno = sgcinsc_nombrealumnoporid($id);

		$html .= '<p>ID Inscripción:' . $id .'</p>';
		if($stage > 1):
			$html .= '<p><strong>RECORDATORIO:</strong> ' . SGCINSC_MODWARNSTAGE . ' </p>';
		else:
			$html .= '<p><strong>RECORDATORIO:</strong> ' . SGCINSC_MODWARN . ' </p>';
		endif;

		foreach($cursos_preinscritos as $acle) {
			$html .= '<p class="oldacle" data-id="'.$acle.'"><strong>'.get_the_title($acle).'</strong> <br> '. 
			sgcinsc_nicehorario(get_post_meta($acle, 'sgcinsc_horaacle', true)). ' ' . sgcinsc_nicedia(get_post_meta($acle, 'sgcinsc_diaacle', true)) . '</p>';
			
		}

	endif;

	if($modcond != 1):

		if($inscripcion):

			$html .= '<p><strong>' . SGCINSC_APODERADOREC . '</strong></p>';

		endif;

		if($inscripcion && $stage > 1):	

			$html .= '<p><strong>' . SGCINSC_OFICIALREC . '</strong></p>
			<p><a class="btn btn-danger" href="' . $pdfacles .'" target="_blank"><i class="icon icon-file-text"></i> ' . SGCINSC_TXTPDFACLES . '</a></p>';
			$html .= '<p><strong>' . SGCINSC_WARNSTAGE .'</strong></p>';
			
		endif;

	endif;

	$html .= '</div>';

	$acleitems = sgcinsc_aclesporcurso($curso);	


	foreach($acleitems as $key=>$acleitem):
		//Distribuir cursos en columnas según día y hora	
		
		$dia = get_post_meta($acleitem->ID, 'sgcinsc_diaacle', true );
		$horario = get_post_meta($acleitem->ID, 'sgcinsc_horaacle', true);

		//Guardar en variables
		//Dia / Horario		
		switch($dia):
			case('lunes'):
				if($horario == 'horario1'):
					$lunes1[$key] = $acleitem;
				else:
					$lunes2[$key] = $acleitem;
				endif;
			break;
			case('martes'):
				if($horario == 'horario1'):
					$martes1[$key] = $acleitem;
				else:
					$martes2[$key] = $acleitem;
				endif;
			break;
			case('miercoles'):
				if($horario == 'horario1'):
					$miercoles1[$key] = $acleitem;
				else:
					$miercoles2[$key] = $acleitem;
				endif;
			break;
			case('jueves'):
				if($horario == 'horario1'):
					$jueves1[$key] = $acleitem;
				else:
					$jueves2[$key] = $acleitem;
				endif;
			break;
			case('viernes'):
				if($horario == 'horario1'):
					$viernes1[$key] = $acleitem;
				else:
					$viernes2[$key] = $acleitem;
				endif;
			break;
		endswitch;		
	endforeach;

	//Genero la tabla de horarios
	$html .= '<div class="aclecursostabla">';
	//dias de la semana
	$html .= '<div class="acleheading">
			
			<div class="curso">Lunes</div>
			<div class="curso">Martes</div>
			<div class="curso">Miércoles</div>
			<div class="curso">Jueves</div>
			<div class="curso">Viernes</div>

			</div>';
	//Del primer horario		
	$html .= '<div class="bloque1">';
	
	
		$html .= '<div class="dia">';
		//lunes
			if(count($lunes1) > 0 || count($lunes2) > 0) {
				$html .= '<div class="mdia">Lunes</div>';	
			}
			$html .= sgcinsc_displaydiacursos($lunes1, 'Lunes', $bloquehorario_1, $inscripcion, $modcond, $id);
			$html .= sgcinsc_displaydiacursos($lunes2, 'Lunes', $bloquehorario_2, $inscripcion, $modcond, $id);

		$html .= '</div>';	

		$html .= '<div class="dia">';
	
	//martes
		if(count($martes1) > 0 || count($martes2) > 0) {
				$html .= '<div class="mdia">Martes</div>';	
			}
		$html .= sgcinsc_displaydiacursos($martes1, 'Martes', $bloquehorario_1, $inscripcion, $modcond, $id);
		$html .= sgcinsc_displaydiacursos($martes2, 'Martes', $bloquehorario_2, $inscripcion, $modcond, $id);

		$html .= '</div>';

		$html .= '<div class="dia">';
	//miercoles
		if(count($miercoles1) > 0 || count($miercoles2) > 0) {
				$html .= '<div class="mdia">Miércoles</div>';	
			}
		$html .= sgcinsc_displaydiacursos($miercoles1, 'Miércoles', $bloquehorario_1, $inscripcion, $modcond, $id);
		$html .= sgcinsc_displaydiacursos($miercoles2, 'Miércoles', $bloquehorario_2, $inscripcion, $modcond, $id);

		$html .= '</div>';

		$html .= '<div class="dia">';
	//jueves
		if(count($jueves1) > 0 || count($jueves2) > 0) {
				$html .= '<div class="mdia">Jueves</div>';	
			}
		$html .= sgcinsc_displaydiacursos($jueves1, 'Jueves', $bloquehorario_1, $inscripcion, $modcond, $id);
		$html .= sgcinsc_displaydiacursos($jueves2, 'Jueves', $bloquehorario_2, $inscripcion, $modcond, $id);

		$html .= '</div>';

		$html .= '<div class="dia">';
	//viernes
		if(count($viernes1) > 0 || count($viernes2) > 0) {
				$html .= '<div class="mdia">Viernes</div>';	
			}
		$html .= sgcinsc_displaydiacursos($viernes1, 'Viernes', $bloquehorario_1, $inscripcion, $modcond, $id);
		$html .= sgcinsc_displaydiacursos($viernes2, 'Viernes', $bloquehorario_2, $inscripcion, $modcond, $id);

		$html .= '</div>';

	$html .= '</div><!--bloque 1-->';
		
	$html .= '</div>';

	echo $html;
	exit();
}

add_action('wp_ajax_sgcinsc_displaycursos', 'sgcinsc_displaycursos');
add_action('wp_ajax_nopriv_sgcinsc_displaycursos', 'sgcinsc_displaycursos');


function sgcinsc_displaydiacursos($dia, $ndia, $bloquehorario, $inscripcion, $modcond, $id) {
	if($dia) {
			$output = '<div class="curso">';
			$output .= '<h4>' . $bloquehorario .'</h4>';
		foreach($dia as $curdia) {
			$output .= sgcinsc_acleitem($curdia->ID, $inscripcion, $modcond, $id);
		}
			$output .= '</div>';
	} else {
			$output = '<div class="curso"><em class="sincursos">Sin cursos para este tramo horario</em></div>';
		}
	return $output;
}

function sgcinsc_acleitem($acleid, $inscripcion, $modcond, $id) {
	
	$cupos = sgcinsc_cupos($acleid);
	$prof = get_post_meta($acleid, 'sgcinsc_profacle', true);

	//Necesito diferenciar entre cursos seleccionados en primera etapa y en segunda etapa, los de segunda etapa se pueden modificar, los de primera no.

	//La variable inscripción es distinta
	if($modcond == 1):
		$oldinsc = sgcinsc_aclesporalumno($inscripcion[0]->rut_alumno);
		$oldrut = $inscripcion[0]->rut_alumno;
		$inscripcion = unserialize($inscripcion[0]->acles_inscritos);
		if(is_array($oldinsc)):
			$checkold = in_array($acleid, $oldinsc);
		else:
			$checkold = false;
		endif;
		//tengo que chequear que el ACLE se haya inscrito en la etapa 1 o 2
		
		//si está el RUT más de una vez la persona está en una modificación de segunda etapa.

		$checkrep = sgcinsc_inscripcionesporalumno($oldrut);

	endif;

	$output = '<div class="control-group acleitemcurso aclecupos-' . $cupos . '" id="curso-' . $acleid . '" data-id="' . $acleid . '">';
		$output .= '<label class="control-label" for="aclecurso-'.$acleid.'"><span class="aclename">'.get_the_title($acleid) . '</span>';	
		$output .= '</label>';
		
	if($modcond == 1){
		if($checkold && $checkrep > 1) {
			$output .= '<span class="yainsc in">insc. 1º etapa</span>';
		} else {
			if($cupos > 0):
				$output .= '<div class="controls"><input class="input-xlarge aclecheckbox" id="aclecurso-'.$acleid.'" name="aclecurso[]" type="checkbox" value="'.$acleid.'"></input></div>';					
			else:
				if($cupos <= 0 && !in_array($acleid, $inscripcion)):
					$output .= '<span class="full">SIN CUPOS</span>';
				endif;
			endif;	
		} 
		
	} else {
		
		if(is_array($inscripcion)):
			$previnsc = in_array($acleid, $inscripcion);
		else:
			$previnsc = false;
		endif;

		if($cupos > 0 && $previnsc != true):
			$output .= '<div class="controls"><input class="input-xlarge aclecheckbox" id="aclecurso-'.$acleid.'" name="aclecurso[]" type="checkbox" value="'.$acleid.'"></input></div>';					
		else:
			if($cupos <= 0 && $previnsc != true):
				$output .= '<span class="full">SIN CUPOS</span>';
			endif;
		endif;	

	}
					
		$output .= '</div>';
		return $output;
}

function sgcinsc_getacles() {
	$acles = $_POST['acles'];	
	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];

	echo '<h3>ACLE';
	if($stage > 1):
		echo ' adicionales ';
	endif;
	echo ' seleccionado(s)</h3>';
	foreach($acles as $acle):		
		$aclepost = get_post($acle);
		$prof = get_post_meta($aclepost->ID, 'sgcinsc_profacle', true);
		echo '<p class="acleinfocurso">' . $aclepost->post_title . '<br/>';
		// if($prof):
		// 	echo ' Profesor(a): ' . get_post_meta($acle, 'sgcinsc_profacle', true) .'<br/>';
		// endif;
		echo 'Horario: ' . get_post_meta($acle, 'sgcinsc_diaacle', true) . ' - ' . sgcinsc_renderhorario(get_post_meta($acle, 'sgcinsc_horaacle', true)) . '</p>';	
	endforeach;
	exit();
}

add_action('wp_ajax_sgcinsc_getacles', 'sgcinsc_getacles');
add_action('wp_ajax_nopriv_sgcinsc_getacles', 'sgcinsc_getacles');


function sgcinsc_aclesporalumno($rutalumno) {
	global $wpdb, $table_name;

	$clean_rutalumno = mysql_escape_string($rutalumno);
	//me devuelve solo la primera vez que se usó ese RUT...
	$consulta = $wpdb->get_var("SELECT acles_inscritos FROM $table_name WHERE rut_alumno = $clean_rutalumno");
	$consulta = unserialize($consulta);
	return $consulta;
}

function sgcinsc_inscripcionesporalumno($rutalumno) {
	global $wpdb, $table_name;

	$clean_rutalumno = mysql_escape_string($rutalumno);
	//me devuelve el número de veces que se encuentra el rut de un alumno
	$inscripciones = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE rut_alumno = $clean_rutalumno");
	return $inscripciones;
}

function sgcinsc_aclesporapoderado($rutapoderado) {
	global $wpdb, $table_name;
	$consulta = $wpdb->get_var("SELECT acles_inscritos FROM $table_name WHERE rut_apoderado = $rutapoderado");
	$consulta = unserialize($consulta);
	return $consulta;
}

function sgcinsc_infoporrut_apoderado($rut) {
  	global $wpdb, $table_name;
  	$datos = $wpdb->get_results("SELECT * FROM $table_name WHERE rut_apoderado = $rut");
  	
  	return $datos;
}

function sgcinsc_renderhorario($horario) {
	if($horario == 'horario1') {
		$nicehorario = '15:20 a 16:50';		
	} else {
		$nicehorario = '17:00 a 18:30';
	}

	return $nicehorario;
}

//Mostrar loop de acles

//Calcular cupos
function sgcinsc_cupos($curso) {
	//Contar cuantos inscritos hay en un curso
	global $wpdb, $table_name, $table2_name;
	$inscritos = $wpdb->get_var("SELECT COUNT(*) FROM $table2_name WHERE id_curso = $curso");
	$cupos = get_post_meta($curso, 'sgcinsc_cuposacle', true);
	if($cupos):		
		$cupos_restantes = $cupos - $inscritos;
		return $cupos_restantes;		
	elseif($cupos == 0):
		return 0;	
	else: 
		return 'x';
	endif;
}

//Adaptar seguro
function sgcinsc_niceseguro($seguro) {
	switch($seguro){
		case('alemana'):
			$niceseguro = 'Clínica Alemana';
		break;
		case('indisa'):
			$niceseguro = 'Clínica Indisa';
		break;
		case('santamaria'):
			$niceseguro = 'Clínica Santa María';
		break;
		case('uc');
			$niceseguro = 'Clínica Universidad Católica';
		break;
		case('davila'):
			$niceseguro = 'Clínica Dávila';
		break;
		default:
			$niceseguro = $seguro;
		break;
	}
	return $niceseguro;
}

function sgcinsc_nicehorario($horario) {
	switch($horario) {
		case('horario1'):
			$nicehorario = '15:20 a 16:50';
		break;
		case('horario2'):
			$nicehorario = '17:00 a 18:30';
		break;
	}
	return $nicehorario;
}

//Adaptar curso
function sgcinsc_nicecurso($curso) {
	switch($curso){
		case('1'):
			$nicecurso = '1° Básico';
		break;
		case('2'):
			$nicecurso = '2° Básico';
		break;
		case('3'):
			$nicecurso = '3° Básico';
		break;
		case('4'):
			$nicecurso = '4° Básico';
		break;
		case('5'):
			$nicecurso = '5° Básico';
		break;
		case('6'):
			$nicecurso = '6° Básico';
		break;
		case('7'):
			$nicecurso = '7° Básico';
		break;
		case('8'):
			$nicecurso = '8° Básico';
		break;
		case('9'):
			$nicecurso = 'I° Medio';
		break;
		case('10'):
			$nicecurso = 'II° Medio';
		break;		
	}
	return $nicecurso;
}

function sgcinsc_nicedia($dia) {
	switch($dia) {
		case('lunes'):
			$nicedia = 'Lunes';
		break;
		case('martes'):
			$nicedia = 'Martes';
		break;
		case('miercoles'):
			$nicedia = 'Miércoles';
		break;
		case('jueves'):
			$nicedia = 'Jueves';
		break;
		case('viernes'):
			$nicedia = 'Viernes';
		break;
	}
	return $nicedia;
}

function sgcinsc_nicerut($nodvrut) {
	return $nodvrut . '-' . dv($nodvrut);
}

function sgcinsc_nombrealumno($rut) {
	//devuelve el nombre del alumno por el rut
	global $wpdb, $table_name;

	$clean_rut = mysql_escape_string($rut);

	$nombre = $wpdb->get_var("SELECT nombre_alumno FROM $table_name WHERE rut_alumno = $clean_rut");
	return $nombre;
}

function sgcinsc_nombrealumnoporid($id) {
	//devuelve el nombre del alumno por el rut
	global $wpdb, $table_name;

	$clean_id = mysql_escape_string($id);

	$nombre = $wpdb->get_var("SELECT nombre_alumno FROM $table_name WHERE id = $clean_id");
	return $nombre;
}

function sgcinsc_getinsc($idinsc) {
	global $wpdb, $table_name, $table2_name;

	$clean_idinsc = mysql_escape_string($idinsc);

	$iteminsc = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $clean_idinsc");
	return $iteminsc;
}

function sgcinsc_getinscdate($idinsc) {
	global $wpdb, $table_name, $table2_name;

	$insc = sgcinsc_getinsc($idinsc);
	$time = $insc[0]->time;
	return $time;

}

function sgcinsc_modrange($dateinsc) {
	global $modexptime;

	$time = new DateTime($dateinsc);
	$curtime = new DateTime(current_time('mysql'));
	$exptime = new DateTime($modexptime);
	$testime = $time->format('H:i');
	if($time->format('Y-m-d') == $curtime->format('Y-m-d') && $curtime->format('H:i') < $exptime->format('H:i')) {
		//mismo día
		//límite de hora
		return true;

	} else {
		return false;
	}
	
}

function sgcinsc_getinscstage($idinsc) {
	/**
	 * Devuelve el número de etapa en que se realizó la inscripción
	 */
	global $wpdb, $table_name, $table2_name;

	$clean_id = mysql_escape_string($idinsc);

	$stage = $wpdb->get_var("SELECT etapa_insc FROM $table2_name WHERE id_inscripcion = $clean_id");

	return $stage;
}

function sgcinsc_inscbotonshortcode($atts) {
	/**
	 * Muestra el botón de ACLE linkeando a la inscripción
	 */

	global $openinsc;
	$options = get_option('sgcinsc_config_options');
	$infoaclespage = $options['sgcinsc_pagina_info'];
	//El ID de la página que regula las inscripciones
	$inscidpage = $options['sgcinsc_pagina_insc'];

	$texto_boton_on = get_post_meta($infoaclespage, 'sgc_txtbotonacle_on', true);
	$texto_boton_off = get_post_meta($infoaclespage, 'sgc_txtbotonacle_off', true);
	
	$link = get_permalink($inscidpage);
	$output = '<div class="btncontainer">';
	$output .= '<p style="text-align:center;">';

	if($openinsc == 1) {
		$output .= '<a id="inscboton" style="margin:0 auto;" class="btn btn-success btn-large" title="'.$texto_boton_on.'" href="'.$link.'">'.$texto_boton_on.'</a>';	
	} else {
		$output .= '<a id="inscboton" style="margin:0 auto;" class="btn btn-primary btn-large disabled" title="'.$texto_boton_off.'" href="#">'.$texto_boton_off.'</a>';
	}
	
	$output .= '</p>';
	$output .= '</div>';
	return $output;
}

add_shortcode('sgcinsc_aclesboton', 'sgcinsc_inscbotonshortcode');

function sgcinsc_insctemplate($idinsc) {
	global $inscidpage;
	global $modexptime;

	$options = get_option('sgcinsc_config_options');
	$openinsc = $options['sgcinsc_open_insc'];
	$stage = $options['sgcinsc_etapa_insc'];

	$data = sgcinsc_getinsc($idinsc);
	$args = array(
		'ih' => mysql_escape_string($_GET['ih']),
		'id' => $idinsc,
		'mod' => 1
		);
	$modlink = add_query_arg( $args, get_permalink($inscidpage) );
	//var_dump($modlink);	


		$output .= '<h1>Resumen inscripción ID# ' . $idinsc . '</h1>';

		//Plantilla
		$output .= '<div class="datos-alumno well">';
		$output .= '<h3>Datos del Alumno/a</h3>';
		$output .= '<ul>';
		$output .= '<li><strong>Nombre:</strong> ' . $data[0]->nombre_alumno .'</li>';
		$output .= '<li><strong>RUT:</strong> ' . sgcinsc_nicerut($data[0]->rut_alumno) . '</li>';
		$output .= '<li><strong>Curso:</strong> ' . sgcinsc_nicecurso($data[0]->curso_alumno) .' </li>';
		$output .= '<li><strong>Seguro Médico:</strong> ' . sgcinsc_niceseguro($data[0]->seguro_escolar) .' </li>';
		$output .= '</ul>';
		$output .= '</div>';

		$output .= '<div class="datos-apoderado well">';
		$output .= '<h3>Datos del Apoderado/a</h3>';
		$output .= '<ul>';
		$output .= '<li><strong>Nombre:</strong> ' . $data[0]->nombre_apoderado . '</li>';
		$output .= '<li><strong>RUT:</strong> ' . sgcinsc_nicerut($data[0]->rut_apoderado) . '</li>';
		$output .= '<li><strong>Email:</strong> ' . $data[0]->email_apoderado . '</li>';
		$output .= '<li><strong>Teléfono Fijo:</strong>' . $data[0]->redfija_apoderado . '</li>';
		$output .= '<li><strong>Celular: </strong>' . $data[0]->celu_apoderado . '</li>';
		$output .= '</div>';

		$acles = unserialize($data[0]->acles_inscritos);

		$output .= '<div class="datos-acle well">';
		$output .= '<h3>A.C.L.E.s inscritos (' . sgcinsc_getinscstage($idinsc) . 'ª etapa)</h3>';
		foreach($acles as $acle) {
			$output .= '<p class="oldacle" data-id="'.$acle.'"><strong>'.get_the_title($acle).'</strong> <br> '. 
			sgcinsc_nicehorario(get_post_meta($acle, 'sgcinsc_horaacle', true)). ' ' . sgcinsc_nicedia(get_post_meta($acle, 'sgcinsc_diaacle', true)) . '</p>';
		}
		$output .= '</div>';


	//2.5. Poblar variables por curso (minacles)
	$output .= '<div class="alert"><h3>Importante:</h3> 
		<ul>
			<li>' . SGCINSC_WARNLIST_1 . '</li>
			<li>' . SGCINSC_WARNLIST_2 . '</li>
			<li>' . SGCINSC_WARNLIST_3 . '</li>
		</ul></div>';

	//Condicionar link a que estén abiertas las inscripciones y que corresponda a la etapa del id
	if(sgcinsc_getinscstage($idinsc) == $stage && $openinsc == 1 || is_user_logged_in() ):

		$allowed_date = sgcinsc_modrange(sgcinsc_getinscdate($idinsc));

		$output .= '<p>La modificación de esta inscripción estará abierta hasta <strong>hoy (' . current_time('d-m-Y'). ') a las ' . $modexptime . ' horas.</strong></p>';

		if(is_user_logged_in()):

			$output .= 	'<p>Como administrador es posible modificar la inscripción fuera de plazo</p>';

		endif;

		if($allowed_date == true || is_user_logged_in() ) {
			
			$output .= '<a href="' . $modlink .'" class="btn btn-large btn-success populateacles">' .  SGCINSC_MODLINKTXT . '</a>';

		} else {

			$output .= '<button class="btn btn-large btn-disabled populateacles">' .  SGCINSC_MODLINKTXT_EXPIRED . '</a>';

		}
		

	else:

		$output .= '<a href="#" class="btn btn-large btn-disabled populateacles disabled">' .  SGCINSC_NOTMODLINKTXT . '</a>';

	endif;

	return $output;
}

function sgcinsc_identical_values( $arrayA , $arrayB ) { 

    sort( $arrayA ); 
    sort( $arrayB ); 

    return $arrayA == $arrayB; 
} 


//Verificación de datos

//RUT:

function dv($r){
     $s=1;
     for($m=0;$r!=0;$r/=10)
         $s=($s+$r%10*(9-$m++%6))%11;
     return chr($s?$s+47:75);
  }  

function in_string($needle, $haystack, $insensitive = false) { 
    if ($insensitive) { 
        return false !== stristr($haystack, $needle); 
    } else { 
        return false !== strpos($haystack, $needle); 
    } 
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

	$options = get_option('sgcinsc_config_options');

	//El ID de la página que regula las inscripciones

	$inscidpage = $options['sgcinsc_pagina_insc'];

	$clean_id = mysql_escape_string($idinsc);

	$inschash = $wpdb->get_var("SELECT hash_inscripcion FROM $table_name WHERE id = $clean_id");
	$acleurl = get_permalink($inscidpage);
	$args = array(
		'ih' => $inschash,
		'id' => $idinsc
		);
	$inscurl = add_query_arg($args, $acleurl);
	return $inscurl;
}

function sgcinsc_validatehash($id, $hash) {
	/**
	 * Valida el hash en relación con el ID
	 */
	global $wpdb, $table_name;

	//limpio el hash por si aca
	$clean_hash = mysql_escape_string($hash);
	$clean_id = mysql_escape_string($id);

	$checkhash = $wpdb->get_var("SELECT hash_inscripcion FROM $table_name WHERE id = $clean_id");
	if($checkhash == $clean_hash) {
		//Chequeo correcto
		return true;
	} else {
		//Chequeo incorrecto
		return false;
	}
}

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
	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];

	if($stage > 1):
		return sgcinsc_checksecondreprut($rut, $columna);
	else:
		return sgcinsc_checkreprut($rut, $columna);
	endif;
}

function sgcinsc_checkreprut($rut, $columna) {
	/**
	 * Busca si hay otro rut, devuelve FALSE en caso que haya otro RUT
	 */
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

	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];

	$puede = true;
	$consulta = $wpdb->get_results("SELECT * FROM $table_name WHERE $columna = $rut");

	
	//chequea los múltiples insc donde hay un sgcinsc second
	foreach($consulta as $consult) {
		//Ya hay un rut inscrito
		if($consult->rut_alumno == $rut) {
			$secondsult = $wpdb->get_var("SELECT etapa_insc FROM $table2_name WHERE id_inscripcion = $consult->id");
			if($secondsult == $stage) {
				//Ya hay una segunda inscripción
				$puede = false;
			}
		}
	}

	return $puede;
}

function sgcinsc_processrut($rut) {
	/**
	 * Limpia el rut para guardarlo en la base de datos y usarlo en comparaciones
	 */
	//Saco los puntos
	$rut = str_replace('.', '', $rut);
	//Elimino el guión
	$rut = str_replace('-', '', $rut);
	//Saco el último dígito
	$rut = substr($rut, 0, -1);

	return $rut;
}

function sgcinsc_compareinsc($oldinsc, $newinsc) {
	/**
	 * Compara inscripciones y devuelve las diferencias en un array
	 */


}

function sgcinsc_minmaxacles( ) {
	$options = get_option('sgcinsc_config_options');
	$stage = $options['sgcinsc_etapa_insc'];

	if($stage == 1) {
		$minmaxacles = array(
			1 => array(1,1),
			2 => array(1,1),
			3 => array(2,2),
			4 => array(2,2),
			5 => array(2,2),
			6 => array(2,2),
			7 => array(1,1),
			8 => array(1,1),
			9 => array(1,1),
			10 => array(1,1)
		);

	} else {

		$minmaxacles = array(
			1 => array(1,3),
			2 => array(1,3),
			3 => array(1,3),
			4 => array(1,3),
			5 => array(1,3),
			6 => array(1,3),
			7 => array(1,3),
			8 => array(1,3),
			9 => array(1,3),
			10 => array(1,3)
		);

	}

	return $minmaxacles;
}

function sgcinsc_getmodurl($idinsc) {

}