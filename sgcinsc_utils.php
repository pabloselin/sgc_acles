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

	$bloquehorario_1 = '15:20 a 16:50';
	$bloquehorario_2 = '17:00 a 18:30';

	$inscripcion = sgcinsc_getinsc($id);
	$cursos_preinscritos = unserialize($inscripcion[0]->acles_inscritos);
	
	if($cursos_preinscritos && SGCINSC_STAGE > 1):
		$nombrealumno = sgcinsc_nombrealumno($rutalumno);
		echo '<div class="preinsc well">';
		echo '<p><strong>RECORDATORIO:</strong> ' . SGCINSC_STAGEMODWARN .'<strong>'.$nombrealumno.'</strong>:<br/<br/></p>';

		foreach($cursos_preinscritos as $acle) {
			echo '<p class="oldacle" data-id="'.$acle.'"><strong>'.get_the_title($acle).'</strong> <br> '. 
			sgcinsc_nicehorario(get_post_meta($acle, 'sgcinsc_horaacle', true)). ' ' . sgcinsc_nicedia(get_post_meta($acle, 'sgcinsc_diaacle', true)) . '</p>';
			
		}
		
		echo '</div>';

		echo '<div class="alert alert-info">';
		echo '<p><strong>' . SGCINSC_INFOACLES .'</strong></p>
		<p><a class="btn btn-danger" href="' . SGCINSC_PDFACLES .'" target="_blank"><i class="icon icon-file-text"></i> ' . SGCINSC_TXTPDFACLES . '</a></p>';
		echo '<p>' . SGCINSC_WARNSTAGE .'</p>';
		echo '</div>';
	endif;

	if($modcond == 1):
		$nombrealumno = sgcinsc_nombrealumnoporid($id);

		echo '<div class="preinsc mod well">';
		echo '<p>ID Inscripción:' . $id .'</p>';
		echo '<p><strong>RECORDATORIO:</strong> ' . SGCINSC_MODWARN . ' </p>';

		foreach($cursos_preinscritos as $acle) {
			echo '<p class="oldacle" data-id="'.$acle.'"><strong>'.get_the_title($acle).'</strong> <br> '. 
			sgcinsc_nicehorario(get_post_meta($acle, 'sgcinsc_horaacle', true)). ' ' . sgcinsc_nicedia(get_post_meta($acle, 'sgcinsc_diaacle', true)) . '</p>';
			
		}

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
			
			<div class="curso">Lunes</div>
			<div class="curso">Martes</div>
			<div class="curso">Miércoles</div>
			<div class="curso">Jueves</div>
			<div class="curso">Viernes</div>

			</div>';
	//Del primer horario		
	echo '<div class="bloque1">';
	
	
		echo '<div class="dia">';
		//lunes
			if(count($lunes1) > 0 || count($lunes2) > 0) {
				echo '<div class="mdia">Lunes</div>';	
			}
			echo sgcinsc_displaydiacursos($lunes1, 'Lunes', $bloquehorario_1);
			echo sgcinsc_displaydiacursos($lunes2, 'Lunes', $bloquehorario_2);

		echo '</div>';	

		echo '<div class="dia">';
	
	//martes
		if(count($martes1) > 0 || count($martes2) > 0) {
				echo '<div class="mdia">Martes</div>';	
			}
		echo sgcinsc_displaydiacursos($martes1, 'Martes', $bloquehorario_1);
		echo sgcinsc_displaydiacursos($martes2, 'Martes', $bloquehorario_2);

		echo '</div>';

		echo '<div class="dia">';
	//miercoles
		if(count($miercoles1) > 0 || count($miercoles2) > 0) {
				echo '<div class="mdia">Miércoles</div>';	
			}
		echo sgcinsc_displaydiacursos($miercoles1, 'Miércoles', $bloquehorario_1);
		echo sgcinsc_displaydiacursos($miercoles2, 'Miércoles', $bloquehorario_2);

		echo '</div>';

		echo '<div class="dia">';
	//jueves
		if(count($jueves1) > 0 || count($jueves2) > 0) {
				echo '<div class="mdia">Jueves</div>';	
			}
		echo sgcinsc_displaydiacursos($jueves1, 'Jueves', $bloquehorario_1);
		echo sgcinsc_displaydiacursos($jueves2, 'Jueves', $bloquehorario_2);

		echo '</div>';

		echo '<div class="dia">';
	//viernes
		if(count($viernes1) > 0 || count($viernes2) > 0) {
				echo '<div class="mdia">Viernes</div>';	
			}
		echo sgcinsc_displaydiacursos($viernes1, 'Viernes', $bloquehorario_1);
		echo sgcinsc_displaydiacursos($viernes2, 'Viernes', $bloquehorario_2);

		echo '</div>';

	echo '</div><!--bloque 1-->';
	
		
	//martes
		
	//miercoles
		
	//jueves
		
	//viernes
		
	echo '</div>';
	exit();
}

add_action('wp_ajax_sgcinsc_displaycursos', 'sgcinsc_displaycursos');
add_action('wp_ajax_nopriv_sgcinsc_displaycursos', 'sgcinsc_displaycursos');


function sgcinsc_displaydiacursos($dia, $ndia, $bloquehorario) {
	if($dia) {
			$output = '<div class="curso">';
			$output .= '<h4>' . $bloquehorario .'</h4>';
		foreach($dia as $curdia) {
			$output .= sgcinsc_acleitem($curdia->ID);
		}
			$output .= '</div>';
	} else {
			$output = '<div class="curso"><em class="sincursos">Sin cursos para este tramo horario</em></div>';
		}
	return $output;
}

function sgcinsc_acleitem($acleid) {
	
	$cupos = sgcinsc_cupos($acleid);
	$prof = get_post_meta($acleid, 'sgcinsc_profacle', true);

	$output = '<div class="control-group acleitemcurso aclecupos-' . $cupos . '" id="curso-' . $acleid . '" data-id="' . $acleid . '">';
		$output .= '<label class="control-label" for="aclecurso-'.$acleid.'"><span class="aclename">'.get_the_title($acleid) . '</span>';	
		
		if($cupos < 0):					
		$output .= '<span class="aclecupos">Cerrado</span>';
		endif;
		$output .= '</label>';
		
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
	echo '<h3>Curso(s) seleccionado(s)</h3>';
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

function sgcinsc_nombrealumnoporid($id) {
	//devuelve el nombre del alumno por el rut
	global $wpdb, $table_name;
	$nombre = $wpdb->get_var("SELECT nombre_alumno FROM $table_name WHERE id = $id");
	return $nombre;
}

function sgcinsc_getinsc($idinsc) {
	global $wpdb, $table_name, $table2_name;
	$iteminsc = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $idinsc");
	return $iteminsc;
}

function sgcinsc_inscbotonshortcode($atts) {
	/**
	 * Muestra el botón de ACLE linkeando a la inscripción
	 */

	global $openinsc;

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

	if($openinsc == 1) {
		$output .= '<a id="inscboton" style="margin:0 auto;" class="btn btn-warning btn-large" title="'.$text.'" href="'.$link.'">'.$text.'</a>';	
	} else {
		$output .= '<a id="inscboton" style="margin:0 auto;" class="btn btn-primary btn-large disabled" title="'.$text.'" href="#">'.$text.'</a>';
	}
	
	$output .= '</p>';
	$output .= '</div>';
	return $output;
}

add_shortcode('sgcinsc_aclesboton', 'sgcinsc_inscbotonshortcode');

function sgcinsc_insctemplate($idinsc) {
	global $inscid;

	$data = sgcinsc_getinsc($idinsc);
	$args = array(
		'ih' => $_GET['ih'],
		'id' => $idinsc,
		'mod' => 1
		);
	$modlink = add_query_arg( $args, get_permalink($inscid) );
	//var_dump($modlink);	


		$output .= '<h1>Información de la inscripción</h1>';

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
			<li>' . SGCINSC_WARNLIST_1 . '</li>
			<li>' . SGCINSC_WARNLIST_2 . '</li>
			<li>' . SGCINSC_WARNLIST_3 . '</li>
		</ul></div>';
	$output .= '<a href="' . $modlink .'" class="btn btn-success populateacles">' .  SGCINSC_MODLINKTXT . '</a>';

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
	global $wpdb, $table_name, $inscid;
	$inschash = $wpdb->get_var("SELECT hash_inscripcion FROM $table_name WHERE id = $idinsc");
	$acleurl = get_permalink($inscid);
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
	$checkhash = $wpdb->get_var("SELECT hash_inscripcion FROM $table_name WHERE id = $id");
	if($checkhash == $hash) {
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
	$stage = SGCINSC_STAGE;
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
