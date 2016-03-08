<?php

add_action( 'admin_menu', 'sgcinsc_add_admin_menu' );

function sgcinsc_add_admin_menu(  ) { 

	add_theme_page( 
		'Configuración A.C.L.E.', 
		'Configuración A.C.L.E.', 
		'manage_options', 
		'acle-configuration-page', 
		'sgcinsc_aclesconfigpage' 
		);

}

add_action('admin_init', 'sgcinsc_initialize_aclesconfig');

function sgcinsc_initialize_aclesconfig() {

	if(false == get_option('acle-configuration-options')) {
		add_option('acle-configuration-options');
	}
	
	add_settings_section(
		'sgcinsc_aclesconfig',
		'Configuración Inscripción A.C.L.E.',
		'sgcinsc_callback_aclesconfig',
		'acle-configuration'
	 );	

	add_settings_field(
		'sgcinsc_acleopen',
		'¿Inscripciones Abiertas?',
		'sgcinsc_openinsc_callback',
		'acle-configuration-page',
		'sgcinsc_aclesconfig',
		array(
			'Marque esta casilla para activar el sistema de inscripciones al público.'
			)
		);

	register_setting(
		'acle-configuration-options',
		'acle-configuration-options'
		);

}

function sgcinsc_openinsc_callback($args) {

	$options = get_option('acle-configuration-options');

	$html = '<input type="checkbox" name="acle-configuration-options[sgcinsc_acleopen]" id="acle-configuration-options[sgcinsc_acleopen]" value="1" ' .  checked(1, $options['sgcinsc_acleopen'], false) .'/>';

	$html .= '<label for="sgcinsc_acleopen">' . $args[0] . '</label>';

	echo $html;
}

function sgcinsc_callback_aclesconfig() {
	echo '<p>Abrir inscripciones A.C.L.E.</p>';
}

function sgcinsc_aclesconfigpage() {
	
	?>

	<div class="wrap">
		<div id="icon-themes" class="icon32"></div>
		<h2>Configuración A.C.L.E.</h2>

		<?php settings_errors();?>
		
		<form method="post" action="options.php">
			
			<?php
				
				settings_fields('acle-configuration-options');
				do_settings_sections( 'acle-configuration-options' );
				submit_button();

				?>

		</form>
	</div>

	<?php
}