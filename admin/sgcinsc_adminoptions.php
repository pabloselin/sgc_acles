<?php

/**
 * Opciones del Admin
 */

add_action( 'admin_menu', 'sgcinsc_putoptionspage' );

function sgcinsc_putoptionspage() {
	add_options_page( __( 'Opciones A.C.L.E.', 'sgc' ), __( 'Optiones A.C.L.E.', 'sgc' ), 'manage_options', 'sgc_aclesoptions', 'sgcinsc_doadminoptions' );
}

function sgcinsc_doadminoptions() {
	/**
	 * Opciones para el admin
	 */

	if (!current_user_can('manage_options'))  {
		wp_die( __('No tienes permisos suficientes para ver esta página.') );
	}
	global $wpdb;
	?>
	<div class="wrap">
		<h2>Opciones A.C.L.E.s</h2>
	</div>

	<?php


	// Inscripciones abiertas / cerradas

	// Emails de contacto

	// Etapa de inscripción

	// Fecha de Cierre etapa

	// Página de inscripción


	//Submit
	
}

function sgcinsc_settings_init() {
	
	register_setting( 'sgcinsc_options', 'sgcinsc_settings' );


}