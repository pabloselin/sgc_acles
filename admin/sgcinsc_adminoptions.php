<?php

/**
 * Opciones del Admin
 */

add_action( 'admin_menu', 'sgcinsc_putoptionspage' );

function sgcinsc_putoptionspage() {
	add_options_page( __( 'Opciones A.C.L.E.', 'sgc' ), __( 'Optiones A.C.L.E.', 'sgc' ), 'manage_options', 'sgc_aclesadmin', 'sgcinsc_doadminoptions' );
}

function sgcinsc_doadminoptions() {
	/**
	 * Opciones para el admin
	 */

	// Inscripciones abiertas / cerradas

	// Emails de contacto

	// Etapa de inscripción

	// Fecha de Cierre etapa

	//Submit
	
}

function sgcinsc_settings() {

}