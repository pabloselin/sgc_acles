<?php
/**
 * Plugin Name: ACLES
 * Plugin URI: http://apie.cl
 * Description: Sistema de inscripción de actividades A.C.L.E. para colegios
 * Version: 0.99
 * Author: A Pie
 * Author URI: http://apie.cl
 * License: MIT
 */

/*
TODO 2017

+ Implementar flujo en GULP con cambio de nombre de archivo
+ Implementar distintas etapas de inscripción por curso
- Revisar que escogerá el apoderado (curso 2016 o 2017)
- Revisar BUGs
	+ No llega correo al apoderado
	+ Error en los límites de cursos
	+ A veces no se muestra la casilla para marcar un curso
	+ No se muestra correctamente la info de los cursos mínimos y máximos para inscribirse
	- Al modificar una inscripción, el sistema permite añadir otro curso adicional a la inscripción
		- Modificar el validador de cursos mínimos y máximos para que sean transversales independiente de la etapa del formulario o inscripción

to-do

1. Definir etapa de inscripción y hacer que se inscriban o no.
2. Admin options
3. Mensajes de resultados para cada inscripción
4. Desplazar mensajes a una sola sección 

*/

define( 'SGCINSC_CSVPATH', WP_CONTENT_DIR . '/acles/');
define( 'SGCINSC_CSVURL', WP_CONTENT_URL . '/acles/');

//Variables a configurar en página aparte
define( 'SGCINSC_MAILINSC', 'ayudaacle@gmail.com');
define( 'SGCINSC_NOTIFYMAIL', 'inscripcionesaclessgc@gmail.com');
define( 'SGCINSC_MAILDEBUG', 'jorge@apie.cl, pablo@apie.cl, web@saintgasparcollege.cl');

if(!is_dir(SGCINSC_CSVPATH)){
	mkdir(WP_CONTENT_DIR . '/acles', 0755);
}

//Ver si están las inscripciones abiertas
$options = get_option('sgcinsc_config_options');

$openinsc = $options['sgcinsc_open_insc'];

//El ID de la página que regula las inscripciones

$inscidpage = $options['sgcinsc_pagina_insc'];

//La etapa de inscripción

$inscstage = $options['sgcinsc_etapa_insc'];

//$modexptime = '12:57';
$modexptime = $options['sgcinsc_exptime'];

//Clases requeridas

// 1.Meta Box

// 2.Bootstrap

//Páginas de administración
include( plugin_dir_path( __FILE__ ) . 'admin/sgcinsc_adminpage.php');

include( plugin_dir_path( __FILE__ ) . 'admin/sgcinsc_adminoptions.php');

include( plugin_dir_path( __FILE__ ) . 'scripts.php');

//Contenidos especiales
include( plugin_dir_path( __FILE__ ) . 'sgcinsc_custom-content.php');

//Formularios
include( plugin_dir_path( __FILE__ ) . 'sgcinsc_custom-forms.php');

include( plugin_dir_path( __FILE__ ) . 'sgcinsc_utils.php');

//Mails
include( plugin_dir_path( __FILE__ ) . 'sgcinsc_mails.php');

//Funciones para visualización pública (No formulario)
include( plugin_dir_path( __FILE__ ) . 'sgcinsc_public.php');

//Mensajes
include( plugin_dir_path( __FILE__ ) . 'sgcinsc_messages.php');

/* Inicialización: Crear tablas de base de datos */

global $wpdb;

//Nombre de tabla y versión
$table_name = $wpdb->prefix . 'inscacles';
$table2_name = $wpdb->prefix . 'cuposacles';
$sgcinsc_dbversion = 1.65;

//año actual
$year = date('Y');

function sgcinsc_createinsctables() {		
	global $sgcinsc_dbversion, $table_name, $table2_name;
	$installed_version = get_option('sgcinsc_dbversion');		
	/*
	Información que no se puede repetir: RUT ALUMNO, RUT APODERADO
	- Identificador general: ALUMNO
	- Identificador asociado: APODERADO
	- Tabla Apoderado
	- Tabla Alumno
	*/	

	if($installed_version != $sgcinsc_dbversion) {
	$sql = "CREATE TABLE $table_name (id mediumint(9) NOT NULL AUTO_INCREMENT,
			rut_apoderado int(8) NOT NULL,rut_alumno int(8) NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			nombre_alumno text NOT NULL,
			nombre_apoderado text NOT NULL,
			curso_alumno int(2) NOT NULL,
			letracurso_alumno text NOT NULL,
			celu_apoderado text NOT NULL,
			redfija_apoderado text NOT NULL,
			email_apoderado text NOT NULL,
			seguro_escolar text NOT NULL,
			acles_inscritos text NOT NULL,
			hash_inscripcion text NOT NULL,
			mod_data text NOT NULL,
			UNIQUE KEY id (id))";
	
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	//Necesito una tabla para manejar decentemente los cupos de las inscripciones
	$sql2 = "CREATE TABLE $table2_name (id mediumint(9) NOT NULL AUTO_INCREMENT,
			id_curso int(9) NOT NULL,
			id_inscripcion int(9) NOT NULL,
			etapa_insc tinyint(1) NOT NULL,
			UNIQUE KEY id (id));
			";

			dbDelta( $sql );
			dbDelta( $sql2 );		
			update_option( 'sgcinsc_dbversion', $sgcinsc_dbversion );
	}

	//Urls personalizadas

	//add_rewrite_tag('%sgcinsc_step%','([^&]+)');
	//add_rewrite_rule('^inscripciones/acles-2014/paso/([^/]*)/?','index.php?sgcinsc_step=$matches[1]','top');	

	//flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'sgcinsc_createinsctables' );

function sgcinsc_checkupdate() {
	global $sgcinsc_dbversion;
	if( get_site_option( 'sgcinsc_dbversion') != $sgcinsc_dbversion ) {
		sgcinsc_createinsctables();
	}
}

add_action( 'plugins_loaded', 'sgcinsc_checkupdate');

/*Registrar campos personalizados privados (para consulta de cupos)
 	1. Asignación total de cupos
 	2. Inscripciones efectivas
 	3. Restar asignación total con inscripciones para listar cupos disponibles
 	4. Asignar lista de espera en caso de inscripción sobre el cupo. 	
*/

/* Crear una tabla que muestre los horarios */


add_filter( 'template_include', 'sgcinsc_templates' );

function sgcinsc_templates( $template ) {
    $post_types = array( 'sginsc_psu2014', 'sginsc_acles2014' );

    if ( is_post_type_archive( $post_types ) && ! file_exists( get_stylesheet_directory() . '/archive-sginsc.php' ) )
        $template = plugin_dir_path( __FILE__ ) . 'views/archive-sginsc.php';
    if ( is_singular( $post_types ) && ! file_exists( get_stylesheet_directory() . '/single-sginsc.php' ) )
        $template = plugin_dir_path( __FILE__ ) . 'views/single-sginsc.php';

    return $template;
}





