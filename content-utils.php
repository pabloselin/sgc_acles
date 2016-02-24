<?php
//Llamadas personalizadas a contenido de la base de datos

function sgcinsc_aclesporcurso($curso) {
	//cada curso es una letra de 1 a 10 (1° Básico a II Medio)
	$args = array(
		'post_type' => 'sginsc_acles2014',
		'numberposts' => -1,
		'meta_query' => array(
			array(
				'key' => 'sgcinsc_cursos',
				'value' => $curso
				)
			)
		);
	$cursos = get_posts($args);	
	return $cursos;	
}

function sgcinsc_displaydiacursos($dia, $ndia) {
	if($dia) {
			$output = '<div class="curso">';
			$output .= '<div class="mdia">' . $ndia . '</div>';
		foreach($dia as $curdia) {
			$output .= sgcinsc_acleitem($curdia->ID);
		}
			$output .= '</div>';
	} else {
			$output = '<div class="curso"><em class="sincursos">Sin cursos para este tramo horario</em></div>';
		}
	return $output;
}

function sgcinsc_publicacle($id) {
	$acleunit = '<div class="acleunit" data-area="{idarea}" data-curso="{idcurso}" data-horario="{idhorario}">
					<h2>{title}</h2>
					<p class="info">
						<span class="horario">{horario}</span>
						<span class="area">{area}</span>
						<span class="curso"{curso}></span>
					</p>
				</div>';
}

function sgcinsc_aclesftable($id) {
	$output = '<div class="aclecursostabla">';
	$output .= '<div class="acleheading"><div class="fcol">Horario</div><div class="curso">Lunes</div><div class="curso">Martes</div><div class="curso">Miércoles</div><div class="curso">Jueves</div><div class="curso">Viernes</div></div>';
	$output .= '</div>';
}

function sgcinsc_todoacles($cupos = true) {
	
	$output = '';

	$args = array(
		'fields' => 'id=>name',
		'hide_empty' => 0
		);


 	$optionarea = get_terms( 'sgcinsc_area', $args );
 	$optioncurso = array(
 					1 => '1º Básico',
 					2 => '2º Básico',
 					3 => '3º Básico',
 					4 => '4º Básico',
 					5 => '5º Básico',
 					6 => '6º Básico',
 					7 => '7º Básico',
 					8 => '8º Básico',
 					9 => 'Iº Medio',
 					10 => 'IIº Medio'
 		);
 	$optionhorario = array(
 						1 => '15:20 a 16:50',
 						2 => '17:00 a 18:30'
 						);
 	$dias = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');
 	$horarios = array('horario1', 'horario2');
 	$opfieldsarea = '<option value="0" default>Escoge un área</option>';
 	$opfieldscurso = '<option value="0" default>Escoge un curso</option>';
 	$opfieldshorario = '<option value="0" default>Escoge un horario</option>';

 	foreach($optionarea as $key=>$optarea) {
 		$opfieldsarea .= '<option value="'.$key.'">'.$optarea.'</option>';
 	}
 	foreach($optioncurso as $key=>$optcurso) {
 		$opfieldscurso .= '<option value="'.$key.'">'.$optcurso.'</option>';
 	}
 	foreach($optionhorario as $key=>$opthorario) {
 		$opfieldshorario .= '<option value="horario'.$key.'">'.$opthorario.'</option>';
 	}

	$filtercurso = '<div class="selecthold"><h4>Filtrar por curso</h4><select name="filtercurso" data-filter="curso">'.$opfieldscurso.'</select></div>';
	$filterarea = '<div class="selecthold"><h4>Filtrar por área</h4><select name="aclesareas" data-filter="area">'.$opfieldsarea.'</select></div>';
	$filterhorario = '<div class="selecthold"><h4>Filtar por horario</h4><select name="acleshorario" data-filter="horario">'.$opfieldshorario.'</select></div>';


	$acles = sgcinsc_orderedacles($cupos);
	

	$filtermessage = 'En esta página están todos los A.C.L.E. Puedes utilizar las cajas de más abajo para filtrar los A.C.L.E. por <strong>curso, área u horario.</strong>';

	$output .= '<div class="filteracles container"><div class="row"><div class="span12"><h2>Oferta A.C.L.E. 2015</h2><p>'.$filtermessage.'</p>'.$filtercurso. $filterarea . $filterhorario . ' <button class="btn btn-primary clear">Quitar filtros</button></div></div></div>';
	$output .= '<div class="publicacles container"><div class="row"><div class="container">
		<div class="alert alertacle
		">VIENDO A.C.L.E.s PARA: <span class="tipcurso">todos los cursos</span>, <span class="tiparea">todas las áreas</span>, <span class="tiphorario">todos los horarios</span> </div>
	</div>';

		foreach($dias as $key=>$dia) {
			$output .= '<div class="dia span2">';
			$output .= '<h1>'.sgcinsc_nicedia($dia).'</h1>';
			foreach($horarios as $horario) 
			{	
				$output .= '<div class="horario"><h2>'.sgcinsc_renderhorario($horario).'</h2>';
				$output .= $acles[$dia][$horario];
				$output .= '</div>';
			}
			$output .= '</div>';
		}
	$output .= '</div></div>';
	return $output;
}

//Devuelve una versión ordenada por día y horario de los posts de ACLES
function sgcinsc_orderedacles($cupos) {
	$dias = array('lunes', 'martes', 'miercoles', 'jueves', 'viernes');
	$horarios = array('horario1', 'horario2');
	foreach($horarios as $horario) {
		foreach($dias as $dia) {
			$args = array(
				'post_type' => 'sginsc_acles2014',
				'numberposts' => -1,
				);
			$args['meta_query'] = array(
				array(
					'key' => 'sgcinsc_horaacle',
					'value' => $horario
					),
				array(
					'key' => 'sgcinsc_diaacle',
					'value' => $dia
					)
				);
			$aclesposts = get_posts($args);
			foreach($aclesposts as $aclespost) {
				$cursos = get_post_meta($aclespost->ID, 'sgcinsc_cursos', false);
				$nicecursos = array();
				$open = 0;
				// if(sgcinsc_cupos($aclespost->ID) <= 0 && $cupos == false) {
				// 	$open = 1;
				// }
				foreach($cursos as $curso) {
					$nicecursos[] = sgcinsc_nicecurso($curso);
				}
				$areas = get_the_terms( $aclespost->ID, 'sgcinsc_area' );
				$niceareas = array();
				if($areas):
					foreach($areas as $area) {
						$niceareas[] = $area->term_id;
					}
				endif;
				$niceareas = implode(' ', $niceareas);

				$acles[$dia][$horario] .= '<div class="acleitem open-'.$open.'" data-dia="'.$dia.'" data-horario="'.$horario.'" data-id="id-'.$aclespost->ID.'" data-curso="'.implode(' ', $cursos).'" data-area="'.$niceareas.'" data-open="'.$open.'"><h4>'.$aclespost->post_title.'</h4>';
				if($open == 1) {
					$acles[$dia][$horario] .= '<span class="nocupos">(SIN CUPOS)</span>';
				}
				$acles[$dia][$horario] .= ' <dl><dd>'.implode(', ', $nicecursos).'</dd></dl></div>';
			}
		}
	}
	return $acles;
}

function sgcinsc_displaycursos() {
	/**
	 * Muestra los cursos y los cursos preinscritos
	 */
	$curso = $_POST['nivel'];
	$rutalumno = sgcinsc_processrut($_POST['rutalumno']);
	$modcond = $_POST['mod'];
	var_dump($modcond);

	$cursos_preinscritos = sgcinsc_aclesporalumno($rutalumno);
	
	if($cursos_preinscritos && SGCINSC_STAGE > 1):
		$nombrealumno = sgcinsc_nombrealumno($rutalumno);
		echo '<div class="preinsc well">';
		echo '<p><strong>RECORDATORIO:</strong> Usted en la Primera etapa de inscripción ha inscrito los siguientes cursos para el alumno/a <strong>'.$nombrealumno.'</strong>:<br/<br/></p>';

		foreach($cursos_preinscritos as $acle) {
			echo '<p class="oldacle" data-id="'.$acle.'"><strong>'.get_the_title($acle).'</strong> <br> '. 
			sgcinsc_nicehorario(get_post_meta($acle, 'sgcinsc_horaacle', true)). ' ' . sgcinsc_nicedia(get_post_meta($acle, 'sgcinsc_diaacle', true)) . '</p>';
			
		}
		
		echo '</div>';

		echo '<div class="alert alert-info">';
		echo '<p><strong>IMPORTANTE: Los datos oficiales de inscripción ACLE de la primera etapa se encuentran disponible para descargar en la página principal de ACLE 2015.</strong></p>
		<p><a class="btn btn-danger" href="http://www.saintgasparcollege.cl/wp-content/uploads/2013/10/LISTA-ACLE-2015.pdf" target="_blank"><i class="icon icon-file-text"></i> Resultados inscripción ACLE 2015 Primera Etapa (pdf)</a></p>';
		echo '<p>No se pueden seleccionar cursos en el mismo horario que los cursos inscritos en la primera etapa.</p>';
		echo '</div>';
	endif;

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
	echo '<div class="aclecursostabla">';
	//dias de la semana
	echo '<div class="acleheading">
			
			<div class="fcol">Horario</div><div class="curso">Lunes</div><div class="curso">Martes</div><div class="curso">Miércoles</div><div class="curso">Jueves</div><div class="curso">Viernes</div>
			</div>';
	//Del primer horario		
	echo '<div class="bloque1">';
	echo '<div class="horario fcol">15:20 a 16:50</div>';
		echo sgcinsc_displaydiacursos($lunes1, 'Lunes');
	//martes
		echo sgcinsc_displaydiacursos($martes1, 'Martes');
	//miercoles
		echo sgcinsc_displaydiacursos($miercoles1, 'Miércoles');
	//jueves
		echo sgcinsc_displaydiacursos($jueves1, 'Jueves');
	//viernes
		echo sgcinsc_displaydiacursos($viernes1, 'Viernes');
	echo '</div>';
	//Del segundo horario		
	echo '<div class="bloque2">';
	echo '<div class="horario fcol">17:00 a 18:30</div>';
		echo sgcinsc_displaydiacursos($lunes2, 'Lunes');
	//martes
		echo sgcinsc_displaydiacursos($martes2, 'Martes');
	//miercoles
		echo sgcinsc_displaydiacursos($miercoles2, 'Miércoles');
	//jueves
		echo sgcinsc_displaydiacursos($jueves2, 'Jueves');
	//viernes
		echo sgcinsc_displaydiacursos($viernes2, 'Viernes');
	echo '</div>';
	echo '</div>';
	exit();
}

add_action('wp_ajax_sgcinsc_displaycursos', 'sgcinsc_displaycursos');
add_action('wp_ajax_nopriv_sgcinsc_displaycursos', 'sgcinsc_displaycursos');


function sgcinsc_acleitem($acleid) {
	
	$cupos = sgcinsc_cupos($acleid);
	$prof = get_post_meta($acleid, 'sgcinsc_profacle', true);

	$output = '<div class="control-group acleitemcurso aclecupos-' . $cupos . '" id="curso-' . $acleid . '" data-id="' . $acleid . '">';
		$output .= '<label class="control-label" for="aclecurso-'.$acleid.'"><span class="aclename">'.get_the_title($acleid) . '</span>';	
		// if($prof):			
		// 	echo '<span class="prof">Profesor(a):<br/>' . $prof . '</span>';
		// endif;
		if($cupos < 0):					
		$output .= '<span class="aclecupos">Cerrado</span>';
		endif;
		$output .= '</label>';
		//echo '<a class="masinfocurso" href="'.get_permalink($acleitem->ID).'"><i class="icon-link"></i> Ver más </a>';
				if($cupos > 0):
					$output .= '<div class="controls"><input class="input-xlarge aclecheckbox" id="aclecurso-'.$acleid.'" name="aclecurso[]" type="checkbox" value="'.$acleid.'"></input></div>';					
				else:
					$output .= '<strong class="full">(Curso completo)</strong>';
				endif;		
		$output .= '</div>';
		return $output;
}

function sgcinsc_getacles() {
	$acles = $_POST['acles'];	
	echo '<h3>Curso(s) seleccionado(s) en SEGUNDA ETAPA DE INSCRIPCIÓN</h3>';
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
	$consulta = $wpdb->get_var("SELECT acles_inscritos FROM $table_name WHERE rut_alumno = $rutalumno");
	$consulta = unserialize($consulta);
	return $consulta;
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
	$nombre = $wpdb->get_var("SELECT nombre_alumno FROM $table_name WHERE rut_alumno = $rut");
	return $nombre;
}

function sgcinsc_resetcupos() {

}

function sgcinsc_getinsc($idinsc) {
	global $wpdb, $table_name, $table2_name;
	$iteminsc = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $idinsc");
	return $iteminsc;
}

function sgcinsc_inscbotonshortcode($atts) {
	$a = shortcode_atts(array(
		'texto' => 'Texto Botón',
		'id' => 0,
		'off' => false
		), $atts );
	$text = $atts['texto'];
	$link = get_permalink($atts['id']);
	$off = $atts['off'];
	$output = '<div class="btncontainer">';
	$output .= '<p style="text-align:center;">';
	$endtext = 'Inscripciones Cerradas';
	if($off == 'true' || strtotime(SGCINSC_ENDINSC) <= strtotime(date('m-d-Y'))) {
		$output .= '<a id="inscboton" style="margin:0 auto;" class="btn btn-primary btn-large disabled" title="'.$endtext.'" href="#">'.$endtext.'</a>';
	} else {
		$output .= '<a id="inscboton" style="margin:0 auto;" class="btn btn-warning btn-large" title="'.$text.'" href="'.$link.'">'.$text.'</a>';	
	}
	
	$output .= '</p>';
	$output .= '</div>';
	return $output;
}

add_shortcode('sgcinsc_aclesboton', 'sgcinsc_inscbotonshortcode');

function sgcinsc_insctemplate($idinsc) {
	$data = sgcinsc_getinsc($idinsc);
	$args = array(
		'ih' => $_GET['ih'],
		'id' => $idinsc,
		'mod' => 1
		);
	$modlink = add_query_arg( $args, get_permalink(SGCINSC_INSCID) );
		

		$output .= '<p></p><div class="alert alert-info">Información de la inscripción</div>';

		//Plantilla
		$output .= '<div class="datos-alumno well">';
		$output .= '<h3>Datos del Alumno/a</h3>';
		$output .= '<ul>';
		$output .= '<li><strong>Nombre:</strong> ' . $data[0]->nombre_alumno .'</li>';
		$output .= '<li><strong>RUT:</strong> ' . sgcinsc_nicerut($data[0]->rut_alumno) . '</li>';
		$output .= '<li><strong>Curso:</strong> ' . sgcinsc_nicecurso($data[0]->curso_alumno) .' </li>';
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
		$output .= '<h3>A.C.L.E.s inscritos</h3>';
		foreach($acles as $acle) {
			$output .= '<p class="oldacle" data-id="'.$acle.'"><strong>'.get_the_title($acle).'</strong> <br> '. 
			sgcinsc_nicehorario(get_post_meta($acle, 'sgcinsc_horaacle', true)). ' ' . sgcinsc_nicedia(get_post_meta($acle, 'sgcinsc_diaacle', true)) . '</p>';
		}
		$output .= '</div>';


	//2.5. Poblar variables por curso (minacles)
	$output .= '<div class="alert"><h3>Importante:</h3> 
		<ul>
			<li>En caso de modificar los ACLES inscritos se invalidarán los ACLES inscritos previamente y sólo podrá inscribir cursos que cuenten con cupos disponibles hasta ahora.</li>
			<li>En caso de que haya ingresado mal el curso, también se invalidarán los ACLES inscritos previamente y se podrán inscribir nuevamente los cursos disponibles para el curso del alumno.</li>
			<li>En caso de modificar únicamente información de contacto o información personal del alumno, se respetará la inscripción previa de los ACLES.</li>
		</ul></div>';
	$output .= '<a href="' . $modlink .'" class="btn btn-success populateacles">Modificar inscripción</a>';

	return $output;
}
