<?php
/**
 * Plugin Name: ACLES - Saint Gaspar College
 * Plugin URI: http://www.saintgasparcollege.cl
 * Description: Sistema de inscripción de actividades SGC.
 * Version: 0.8
 * Author: A Pie
 * Author URI: http://apie.cl
 * License: GPL2
 */

/*
TODO

1. Validar campos del formulario en PHP
2. Cargar ACLES según disponibilidad y curso
3. Crear vistas de ACLES
5. Enviar correos de confirmación
6. Procesar formulario vía AJAX.

*/

define( 'SGCINSC_CSVPATH', WP_CONTENT_DIR . '/sgccsv/');
define( 'SGCINSC_CSVURL', WP_CONTENT_URL . '/sgccsv/');

//Modo debug para no enviar chorradas
define ('SGCINSC_DEBUG', true);

if(!is_dir(SGCINSC_CSVPATH)){
	mkdir(WP_CONTENT_DIR . '/sgccsv', 0755);
}

//Clases requeridas

// 1.Meta Box

// 2.Bootstrap

//Synbolic links for wp dev
//include( plugin_dir_path( __FILE__ ) . 'lib/class-symbolic-press.php');
//new Symbolic_Press(__FILE__);

//Páginas de administración
include( plugin_dir_path( __FILE__ ) . 'admin/sgcinsc_adminpage.php');

//Contenidos especiales
include( plugin_dir_path( __FILE__ ) . 'sgcinsc_custom-content.php');

//Formularios
include( plugin_dir_path( __FILE__ ) . 'sgcinsc_custom-forms.php');

include( plugin_dir_path( __FILE__ ) . 'content-utils.php');




/* Inicialización: Crear tablas de base de datos */

global $wpdb;

//Nombre de tabla y versión
$table_name = $wpdb->prefix . 'sgcinsc';
$table2_name = $wpdb->prefix . 'sgccupos';
$sgcinsc_dbversion = 1.0;

//Cursos obligatorios por nivel
$obcursos = array(
	1 => 1,
	2 => 1,
	3 => 2,
	4 => 2,
	5 => 2,
	6 => 2,
	7 => 1,
	8 => 1,
	9 => 1,
	10 => 1
	);

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
			insc_year int(4) NOT NULL,
			UNIQUE KEY id (id))";
	
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	//Necesito una tabla para manejar decentemente los cupos de las inscripciones
	$sql2 = "CREATE TABLE $table2_name (id mediumint(9) NOT NULL AUTO_INCREMENT,
			id_curso int(9) NOT NULL,
			id_inscripcion int(9) NOT NULL,
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

//Meter scripts de JS
function sgcinsc_scripts() {
	if(!is_admin()):			
		wp_deregister_script('jquery' );
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
		wp_register_script( 'jquery_validation', plugins_url('js/jquery.validate.min.js', __FILE__ ), array('jquery_rut', 'jquery'));
		wp_register_script( 'jquery_rut', plugins_url('js/jquery.Rut.min.js', __FILE__), array('jquery'));
		wp_register_script( 'jquery_cookie', plugins_url('js/jquery.cookie.js', __FILE__), array('jquery'));
		wp_register_script( 'jquery_steps', plugins_url('js/jquery.steps.min.js', __FILE__), array('jquery_cookie', 'jquery'));
		wp_register_script( 'sgc_acles-ajax', plugins_url('js/sgc_acles-ajax.js', __FILE__ ), array('jquery_rut', 'jquery_validation', 'jquery_steps', 'jquery'));

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery_validation' );
		wp_enqueue_script( 'sgc_acles-ajax' );	
		wp_enqueue_script( 'jquery_rut');
		wp_enqueue_script( 'jquery_cookie');
		wp_enqueue_script( 'jquery_steps');

		wp_localize_script('sgc_acles-ajax', 'sgcajax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
			) );
	endif;
}

add_action('wp_enqueue_scripts', 'sgcinsc_scripts');

function sgcinsc_styles() {
	if(!is_admin()):
		wp_register_style( 'sgcformscss', plugins_url('css/sgcinsc_form.css', __FILE__ ));
		wp_enqueue_style( 'sgcformscss' );
	endif;	
}

add_action('wp_enqueue_scripts', 'sgcinsc_styles');

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



