<?php 
//Meter scripts de JS
function sgcinsc_scripts() {
	$minmaxacles = sgcinsc_minmaxacles();
	$options = get_option('sgcinsc_config_options');
	$openinsc = $options['sgcinsc_open_insc'];
	$stage = $options['sgcinsc_etapa_insc'];

	if(!is_admin()):			
		wp_deregister_script('jquery' );
		wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js');
		
		// wp_register_script( 'jquery_validation', plugins_url('js/jquery.validate.min.js', __FILE__ ), array('jquery_rut', 'jquery'));
		// wp_register_script( 'jquery_rut', plugins_url('js/jquery.Rut.min.js', __FILE__), array('jquery'));
		// wp_register_script( 'jquery_cookie', plugins_url('js/jquery.cookie.js', __FILE__), array('jquery'));
		// wp_register_script( 'jquery_steps', plugins_url('js/jquery.steps.min.js', __FILE__), array('jquery_cookie', 'jquery'));

		wp_register_script( 'acles-ajax', plugins_url('assets/js/main-acles-build-bca3337ea1.js', __FILE__ ), array('jquery'));
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'acles-ajax');

		wp_localize_script('acles-ajax', 'sgcajax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'minmaxacles'=> $minmaxacles,
			'stage' => $stage
			) );

	endif;
}

add_action('wp_enqueue_scripts', 'sgcinsc_scripts');

function sgcinsc_styles() {
	if(!is_admin()):
		wp_register_style( 'acles-css', plugins_url('assets/css/acles-form-aff4eb2e21.css', __FILE__ ));
		wp_enqueue_style( 'acles-css' );
	endif;	
}

add_action('wp_enqueue_scripts', 'sgcinsc_styles');