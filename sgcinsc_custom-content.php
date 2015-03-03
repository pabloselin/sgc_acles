<?php

//Registrar custom posts
function sgcinsc_cpt() {
	register_post_type( 'sginsc_psu2014',
		array(
			'labels' => array(
				'name'               => 'Preuniversitario SGC 2014',
			    'singular_name'      => 'Preuniversitario SGC 2014',
			    'add_new'            => 'Añadir nuevo',
			    'add_new_item'       => 'Añadir nuevo Preuniversitario SGC 2014',
			    'edit_item'          => 'Editar Preuniversitario SGC 2014',
			    'new_item'           => 'Nuevo Preuniversitario SGC 2014',
			    'all_items'          => 'Todos los Preuniversitario SGC 2014',
			    'view_item'          => 'Ver Preuniversitario SGC 2014',
			    'search_items'       => 'Buscar Preuniversitario SGC 2014',
			    'not_found'          => 'No Preuniversitario SGC 2014 found',
			    'not_found_in_trash' => 'No Preuniversitario SGC 2014 found in Trash',
			    'parent_item_colon'  => '',
			    'menu_name'          => 'Preuniversitario PSU 2014'			
			),
			'public' => true,
			'supports' => array('title', 'editor', 'excerpt', 'custom-fields'),
			'has_archive' => true,
			'hierarchical' => true,
			'menu_position' => 5,						
			'rewrite' => array(
				'slug' => 'inscripciones/psu2014',
				'with_front' => false
				)
		)		
	);

	register_post_type( 'sginsc_acles2014',
		array(
			'labels' => array(
				  	'name'               => 'A.C.L.E.S.',
				    'singular_name'      => 'A.C.L.E.',
				    'add_new'            => 'Añadir nueva',
				    'add_new_item'       => 'Añadir nueva A.C.L.E.',
				    'edit_item'          => 'Editar A.C.L.E.',
				    'new_item'           => 'Nueva A.C.L.E.',
				    'all_items'          => 'Todas las A.C.L.E.S',
				    'view_item'          => 'Ver A.C.L.E.',
				    'search_items'       => 'Buscar A.C.L.E.S',
				    'not_found'          => 'No se encontraron A.C.L.E.S',
				    'not_found_in_trash' => 'No se encontraron A.C.L.E. en la papelera',
				    'parent_item_colon'  => '',
				    'menu_name'          => 'A.C.L.E.S'			
			),
			'public' => true,
			'supports' => array('title', 'editor', 'excerpt', 'custom-fields'),
			'has_archive' => true,
			'hierarchical' => true,
			'menu_position' => 5,						
			'rewrite' => array(
				'slug' => 'inscripciones/acles',
				'with_front' => false
				)
		)		
	);	
}

add_action('init', 'sgcinsc_cpt');

// Register Custom Taxonomy
function sgcinsc_taxarea() {

	$labels = array(
		'name'                       => _x( 'Áreas', 'Taxonomy General Name', 'sgcinsc' ),
		'singular_name'              => _x( 'Área', 'Taxonomy Singular Name', 'sgcinsc' ),
		'menu_name'                  => __( 'Áreas de A.C.L.E.', 'sgcinsc' ),
		'all_items'                  => __( 'Todas las Áreas', 'sgcinsc' ),
		'parent_item'                => __( 'Área superior', 'sgcinsc' ),
		'parent_item_colon'          => __( 'Área superior:', 'sgcinsc' ),
		'new_item_name'              => __( 'Nueva Área de A.C.L.E.', 'sgcinsc' ),
		'add_new_item'               => __( 'Añadir nueva Área de A.C.L.E.', 'sgcinsc' ),
		'edit_item'                  => __( 'Editar Área de A.C.L.E.', 'sgcinsc' ),
		'update_item'                => __( 'Actualizar Área de A.C.L.E.', 'sgcinsc' ),
		'separate_items_with_commas' => __( 'Separar áreas con comas', 'sgcinsc' ),
		'search_items'               => __( 'Buscar áreas', 'sgcinsc' ),
		'add_or_remove_items'        => __( 'Añadir área', 'sgcinsc' ),
		'choose_from_most_used'      => __( 'Escoger entre las más usadas', 'sgcinsc' ),
		'not_found'                  => __( 'No encontrada', 'sgcinsc' ),
	);
	$rewrite = array(
		'slug'                       => 'acles-areas',
		'with_front'                 => true,
		'hierarchical'               => false,
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'rewrite'                    => $rewrite,
	);
	register_taxonomy( 'sgcinsc_area', array( 'sginsc_acles2014' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'sgcinsc_taxarea', 0 );

//Registrar meta-boxes

function sgcinsc_metaboxes($sgcinsc_boxes) {
	$prefix = 'sgcinsc_';



//Campos para PSU
$sgcinsc_boxes[] = array(
	'id' => $prefix . 'psu2014',
	'title' => 'Formulario PSU 2014',
	'pages' => array('sginsc_psu2014'),
	'context' => 'normal',
	'priority' => 'high',
	'autosave' => true,
	'fields' => array(		
		array(
			'name' => 'URL del formulario de Google DOCS',
			'id' => $prefix . 'urlpsu',
			'type' => 'text',
			'desc' => 'URL para insertar formulario'
			)
		)
	);


/*Registrar sistemas de asociación (taxonomías o campos)
 	1. ¿para que niveles son estos cursos?
 	2. ¿que horarios?
 	3. ¿qué días?
 	4. ¿qué profesores?
 */

//Campos para ACLES
$sgcinsc_boxes[] = array(
	'id' => $prefix . 'acles2014',
	'title' => 'Información de la A.C.L.E.',
	'pages' => array('sginsc_acles2014'),
	'context' => 'normal',
	'priority' => 'high',
	'autosave' => true,
	'fields' => array(		
		array(
			'name' => 'Día',
			'id' => $prefix . 'diaacle',
			'type' => 'select',			
			'desc' => 'Día que se da el curso',
			'options' => array(
				'lunes' => 'Lunes',
				'martes' => 'Martes',
				'miercoles' => 'Miércoles',
				'jueves' => 'Jueves',
				'viernes' => 'Viernes'				
				)
			),		
		array(
			'name' => 'Horas',
			'id' => $prefix . 'horaacle',
			'type' => 'select',
			'options' => array(
				'horario1' => '15:20 a 16:50',
				'horario2' => '17:00 a 18:30',
				),
			'desc' => 'Horas a las que se da el curso'
			),
		array(
			'name' => 'Profesor(a)',
			'id' => $prefix . 'profacle',
			'type' => 'text',
			'desc' => 'Profesor(a) responsable'
			),
		array(
			'name' => 'Cupos',
			'id' => $prefix . 'cuposacle',
			'type' => 'number',
			'desc' => 'Cupos disponibles para el curso',
			'suffix' => ' alumnos(as)',
			'js_options' => array(
				'min' => 1,
				'max' => 40,
				'step' => 1
				)
			),		
		array(
			'name' => 'Cursos en los que está disponible este A.C.L.E.',
			'id' => $prefix . 'cursos',
			'type' => 'checkbox_list',
			'desc' => 'Cursos para los que está disponible este A.C.L.E.',			
			'options' => array(
				'1' => '1° Básico',
				'2' => '2° Básico',
				'3' => '3° Básico',
				'4' => '4° Básico',
				'5' => '5° Básico',
				'6' => '6° Básico',
				'7' => '7° Básico',
				'8' => '8° Básico',
				'9' => 'I° Medio',
				'10' => 'II° Medio',
				)
			)
		)
	);

	return $sgcinsc_boxes;
}


	

//Registrar cajitas
// function sgcinsc_register_meta_boxes()
// {
// 	global $sgcinsc_boxes;	

// 	// Make sure there's no errors when the plugin is deactivated or during upgrade
// 	if ( ! class_exists( 'RW_Meta_Box' ) )
// 		return;	

// 	foreach ( $sgcinsc_boxes as $meta_box )
// 	{
// 		new RW_Meta_Box( $meta_box );
// 	}
// }

// add_action( 'admin_init', 'sgcinsc_register_meta_boxes' );
add_filter('rwmb_meta_boxes', 'sgcinsc_metaboxes');

//Columnas especiales
// Add to admin_init function

add_filter('manage_sginsc_acles2014_posts_columns', 'acles2014_columnshead');
add_action('manage_sginsc_acles2014_posts_custom_column', 'acles2014_columnscontent', 10, 2);


function acles2014_columnshead($defaults) {	
    $defaults['cupos'] = __('Cupos totales');
    $defaults['cursos'] = __('Cursos');
    $defaults['dia'] = __('Día'); 
    $defaults['horario'] = __('Horario');     
    return $defaults;
}

function acles2014_columnscontent($column_name, $post_ID) {
	if($column_name == 'cupos') {
		$cupos = get_post_meta($post_ID, 'sgcinsc_cuposacle', true);
		if($cupos) {
			echo $cupos;
		} else {
			echo 'Sin información de Cupos';
		}
	}
	if($column_name == 'profesor') {
		$prof = get_post_meta($post_ID, 'sgcinsc_profacle', true);
		if($prof) {
			echo $prof;
		} else {
			echo 'Sin profesor responsable';
		}
	}
	if($column_name == 'dia') {
		$dia = get_post_meta($post_ID, 'sgcinsc_diaacle', true);
		if($dia) {
			switch($dia):
				case('lunes'):
					echo 'Lunes';
				break;
				case('martes'):
					echo 'Martes';
				break;
				case('miercoles'):
					echo 'Miércoles';
				break;
				case('jueves');
					echo 'Jueves';
				break;
				case('viernes'):
					echo 'Viernes';
				break;
			endswitch;
		} else {
			echo 'Sin día definido';
		}
	}
	if($column_name == 'horario') {		
		$hora = get_post_meta($post_ID, 'sgcinsc_horaacle', true);
		if($hora) {
			if($hora == 'horario1'):
				echo '15:20 a 16:50';
			elseif($hora == 'horario2'):
				echo '17:00 a 18:30';
			endif;
		} else {
			echo 'Sin horario definido';
		}
	}
	if($column_name == 'cursos') {
		$cursos = get_post_meta($post_ID, 'sgcinsc_cursos', false);
		$nicecursos = array();
		if($cursos) {
			foreach($cursos as $curso) {
				$nicecursos[] = sgcinsc_nicecurso($curso);
			}
			echo implode(', ', $nicecursos);
		} else {
			echo 'Sin cursos asignados';
		}

	}
}