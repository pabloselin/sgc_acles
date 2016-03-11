<?php

/**
  * Public facing functions
  */ 

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
	/**
	 * Muestra la tabla de todos los acles disponibles con sus filtros para vista pública antes de empezar la inscripción o como consulta a través de un shortcode
	 */
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

	$output .= '<div class="filteracles container"><div class="row"><div class="span12"><h2>Oferta A.C.L.E. ' . date('Y') . '</h2><p>'.$filtermessage.'</p>'.$filtercurso. $filterarea . $filterhorario . ' <button class="btn btn-primary clear">Limpiar filtros</button></div></div></div>';
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
				 if(sgcinsc_cupos($aclespost->ID) <= 0 && $cupos == false) {
				 	$open = 1;
				 }
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
				$acles[$dia][$horario] .= ' <dl><dd>'.implode(', ', $nicecursos).'</dd></dl>';
				
				if(is_user_logged_in()) {

					$totcupos = get_post_meta($aclespost->ID, 'sgcinsc_cuposacle', true);
					$acles[$dia][$horario] .= '<p class="admincupos">cupos: ' . sgcinsc_cupos($aclespost->ID) .' de ' . $totcupos . '</p>';
				}
					
				

				$acles[$dia][$horario] .= '</div>';
			}
		}
	}
	return $acles;
}

function sgcinsc_certificado($id, $mod) {
	/**
	 * genera un certificado imprimible de la inscripción
	 */
	$options = get_option('sgcinsc_config_options');
	$openinsc = $options['sgcinsc_open_insc'];

	//El ID de la página que regula las inscripciones

	$inscidpage = $options['sgcinsc_pagina_insc'];

	//La etapa de inscripción

	$inscstage = $options['sgcinsc_etapa_insc'];

	//El objeto inscripción

	$rawinsc = sgcinsc_getinsc($id);
	$insc = $rawinsc[0];
	$rawacles = $insc->acles_inscritos;
	$acles = unserialize($rawacles);

	if($mod == true):
		$cert_1 = 'Modificación';
	else:
		$cert_1 = 'Inscripción';
	endif;

	$html = '<div class="hidden" id="certificado">';
	$html .= '<style>@media print { body { text-align:center; } button { display:none !important;} }</style>';

	$html .= '<table cellpadding="20" cellspacing="0" width="500" style="font-family:sans-serif;text-align:center;background-color:#D3E3EB;margin:24px;border:1px solid #1470A2;"><tr><td>';
			    			
	$html .= '<p style="text-align:center"><img style="margin:0 auto;" src="http://www.saintgasparcollege.cl/wp-content/themes/sangaspar/i/logosgc2013.png"><h2 style="text-align:center;color:#1470A2;font-weight:normal;">Saint Gaspar College</h2>';

	$html .= '<h3 style="text-align:center;">' . $cert_1 . ' A.C.L.E. ' . date('Y') . '</h3></p>';
					        
	$html .= '<h1>Comprobante de ' . $cert_1 .'</h1>';

	$html .= '<p>El apoderado(a) <strong>'.$insc->nombre_apoderado.'</strong> inscribió los siguientes A.C.L.E. para el alumno(a) <strong>'.$insc->nombre_alumno.' del curso ' . sgcinsc_nicecurso($insc->curso_alumno). ' ' . $insc->letracurso_alumno . '</strong></p>';

	$html .= '</td>';
	$html .= '</tr>';

		foreach($acles as $acle):
				$aclepost = get_post($acle);
				$html .= '<tr style="border-bottom:1px solid #456A7D;"><td style="border-bottom:1px solid #456A7D;">';
				$html .= '<strong>' . $aclepost->post_title . '</strong>';
				$html .= '<p>Horario: '. sgcinsc_nicedia(get_post_meta($aclepost->ID, 'sgcinsc_diaacle', true)) . ' ' . sgcinsc_renderhorario(get_post_meta($aclepost->ID, 'sgcinsc_horaacle', true)) . '</p>';
				$html .= '</td></tr>';	
		endforeach;				             

	$html .= '<tr><td>';
	$html .= '<p>Su número de inscripción es el '. $id . '</p>';
	$html .= '</td></tr>';
						
	if($inscstage > 1):
		$html .= '<tr><td><p><strong>' . SGCINSC_ACLERESP .'</strong></p></td></tr>';
	endif;

	$html .= '</table>';

	$html .= '<p style="text-align:center;"><button style="font-size:16px;" target="_blank" class="btn btn-info" href="#" onclick="window.print();" id="printcert"><i class="icon icon-printer"></i> Imprimir comprobante</button></p>';
	$html .= '</div>';	

	return $html;

}