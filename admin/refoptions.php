<?php
add_action( 'admin_menu', 'sgcinsc__add_admin_menu' );
add_action( 'admin_init', 'sgcinsc__settings_init' );


function sgcinsc__add_admin_menu(  ) { 

	add_options_page( 'Configuración A.C.L.E.', 'Configuración A.C.L.E.', 'manage_options', 'opciones_acle', 'sgcinsc__options_page' );

}


function sgcinsc__settings_init(  ) { 

	register_setting( 'sgcinsc_opt', 'sgcinsc__settings' );

	add_settings_section(
		'sgcinsc__sgcinsc_opt_section', 
		__( 'Sistema de inscripciones', 'sgc_' ), 
		'sgcinsc__settings_section_callback', 
		'sgcinsc_opt'
	);

	add_settings_field( 
		'sgcinsc__checkbox_field_0', 
		__( 'Inscripciones abiertas', 'sgc_' ), 
		'sgcinsc__checkbox_field_0_render', 
		'sgcinsc_opt', 
		'sgcinsc__sgcinsc_opt_section' 
	);


}


function sgcinsc__checkbox_field_0_render(  ) { 

	$options = get_option( 'sgcinsc__settings' );
	?>
	<input type='checkbox' name='sgcinsc__settings[sgcinsc__checkbox_field_0]' <?php checked( $options['sgcinsc__checkbox_field_0'], 1 ); ?> value='1'>
	<?php

}


function sgcinsc__settings_section_callback(  ) { 

	echo __( 'Configuración de las distintas opciones para la inscripción de A.C.L.E.', 'sgc_' );

}


function sgcinsc__options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>Configuración sistema inscripción A.C.L.E.</h2>
		
		<?php
		settings_fields( 'sgcinsc_opt' );
		do_settings_sections( 'sgcinsc_opt' );
		submit_button();
		?>
		
	</form>
	<?php

}

?>