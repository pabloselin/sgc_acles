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

function sgcinsc_displaydiacursos($dia) {
	if($dia) {
		foreach($dia as $curdia) {
			sgcinsc_acleitem($curdia->ID);
		}
	} else {
			echo '<em>Sin cursos para este tramo horario</em>';
		}
}

function sgcinsc_displaycursos() {
	$curso = $_POST['nivel'];	
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
	echo '<table class="aclecursostabla" valign="top" cellpadding="0" cellspacing="0">';
	//dias de la semana
	echo '<thead><tr><th>Horario</th><th class="curso">Lunes</th><th class="curso">Martes</th><th class="curso">Miércoles</th><th class="curso">Jueves</th><th class="curso">Viernes</th></tr></thead>';
	//Del primer horario		
	echo '<tr>';
	echo '<td class="horario">15:20 a 16:50</td>';
	echo '<td class="curso">';
		sgcinsc_displaydiacursos($lunes1);
	echo '</td>';
	//martes
	echo '<td class="curso">';
		sgcinsc_displaydiacursos($martes1);
	echo '</td>';
	//miercoles
	echo '<td class="curso">';
		sgcinsc_displaydiacursos($miercoles1);
	echo '</td>';
	//jueves
	echo '<td class="curso">';
		sgcinsc_displaydiacursos($jueves1);
	echo '</td>';
	//viernes
	echo '<td class="curso">';
		sgcinsc_displaydiacursos($viernes1);
	echo '</td>';
	echo '</tr>';
	//Del segundo horario		
	echo '<tr>';
	echo '<td class="horario">17:00 a 18:30</td>';
	echo '<td class="curso">';
	sgcinsc_displaydiacursos($lunes2);
	echo '</td>';
	//martes
	echo '<td class="curso">';
	sgcinsc_displaydiacursos($martes2);
	echo '</td>';
	//miercoles
	echo '<td class="curso">';
	sgcinsc_displaydiacursos($miercoles2);
	echo '</td>';
	//jueves
	echo '<td class="curso">';
	sgcinsc_displaydiacursos($jueves2);
	echo '</td>';
	//viernes
	echo '<td class="curso">';
	sgcinsc_displaydiacursos($viernes2);
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	exit();
}

add_action('wp_ajax_sgcinsc_displaycursos', 'sgcinsc_displaycursos');
add_action('wp_ajax_nopriv_sgcinsc_displaycursos', 'sgcinsc_displaycursos');


function sgcinsc_acleitem($acleid) {
	
	$cupos = sgcinsc_cupos($acleid);
	$prof = get_post_meta($acleid, 'sgcinsc_profacle', true);

	echo '<div class="control-group acleitemcurso aclecupos-' . $cupos . '" id="curso-' . $acleid . '">';
		echo '<label class="control-label" for="aclecurso-'.$acleid.'"><span class="aclename">'.get_the_title($acleid) . '</span>';	
		// if($prof):			
		// 	echo '<span class="prof">Profesor(a):<br/>' . $prof . '</span>';
		// endif;
		if($cupos < 0):					
		echo '<span class="aclecupos">Cerrado</span>';
		endif;
		echo '</label>';
		//echo '<a class="masinfocurso" href="'.get_permalink($acleitem->ID).'"><i class="icon-link"></i> Ver más </a>';
				if($cupos > 0):
					echo '<div class="controls"><input class="input-xlarge aclecheckbox" id="aclecurso-'.$acleid.'" name="aclecurso[]" type="checkbox" value="'.$acleid.'"></input></div>';					
				else:
					echo '<strong class="full">Curso completo</strong>';
				endif;		
		echo '</div>';
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
	else:
		return 'Sin información de cupos';	
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

function sgcinsc_resetcupos() {

}

