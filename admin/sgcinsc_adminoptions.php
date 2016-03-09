<?php
 
function sandbox_example_theme_menu() {
 
    add_theme_page(
        'Configuración A.C.L.E.',            // The title to be displayed in the browser window for this page.
        'Configuración A.C.L.E.',            // The text to be displayed for this menu item
        'administrator',            // Which type of users can see this menu item
        'sgcinsc_configacle',    // The unique ID - that is, the slug - for this menu item
        'sgcinsc_configpage'     // The name of the function to call when rendering this menu's page
    );
 
} // end sandbox_example_theme_menu
add_action( 'admin_menu', 'sandbox_example_theme_menu' );
 
/**
 * Renders a simple page to display for the theme menu defined above.
 */
function sgcinsc_configpage() {
?>
    <!-- Create a header in the default WordPress 'wrap' container -->
    <div class="wrap">
     
        <div id="icon-themes" class="icon32"></div>
        <h2>Activar inscripción A.C.L.E.</h2>
        <?php settings_errors(); ?>
         
        <form method="post" action="options.php">
            <?php settings_fields( 'sgcinsc_config_options' ); ?>
            <?php do_settings_sections( 'sgcinsc_config_options' ); ?>         
            <?php submit_button(); ?>
        </form>
         
    </div><!-- /.wrap -->
<?php
} // end sandbox_theme_display
 
function sandbox_initialize_theme_options() {
 
    // If the theme options don't exist, create them.
    if( false == get_option( 'sgcinsc_config_options' ) ) {  
        add_option( 'sgcinsc_config_options' );
    } // end if
 
    // First, we register a section. This is necessary since all future options must belong to a 
    add_settings_section(
        'general_settings_section',         // ID used to identify this section and with which to register options
        'Activar inscripciones',                  // Title to be displayed on the administration page
        'sandbox_general_options_callback', // Callback used to render the description of the section
        'sgcinsc_config_options'     // Page on which to add this section of options
    );
     
    // Next, we'll introduce the fields for toggling the visibility of content elements.
    add_settings_field( 
        'sgcinsc_open_insc',                      // ID used to identify the field throughout the theme
        'Inscripciones abiertas?',                           // The label to the left of the option interface element
        'sgcinsc_open_insc_callback',   // The name of the function responsible for rendering the option interface
        'sgcinsc_config_options',    // The page on which this option will be displayed
        'general_settings_section',         // The name of the section to which this field belongs
        array(                              // The array of arguments to pass to the callback. In this case, just a description.
            'Activa esta casilla para abrir las inscripciones.'
        )
    );

     // Next, we'll introduce the fields for toggling the visibility of content elements.
    add_settings_field( 
        'sgcinsc_pagina_insc',                      // ID used to identify the field throughout the theme
        'Página con formulario de inscripción',                           // The label to the left of the option interface element
        'sgcinsc_selectpage_callback',   // The name of the function responsible for rendering the option interface
        'sgcinsc_config_options',    // The page on which this option will be displayed
        'general_settings_section',         // The name of the section to which this field belongs
        array(                              // The array of arguments to pass to the callback. In this case, just a description.
            'Escoge en qué página se mostrará el formulario.'
        )
    );
     
    // Finally, we register the fields with WordPress
    register_setting(
        'sgcinsc_config_options',
        'sgcinsc_config_options'
    );
     
} // end sandbox_initialize_theme_options
add_action('admin_init', 'sandbox_initialize_theme_options');
 
function sandbox_general_options_callback() {
    echo '<p>Configuración de A.C.L.E.</p>';
} // end sandbox_general_options_callback
 
function sgcinsc_open_insc_callback($args) {
     
    // First, we read the options collection
    $options = get_option('sgcinsc_config_options');
     
    // Next, we update the name attribute to access this element's ID in the context of the display options array
    // We also access the sgcinsc_open_insc element of the options collection in the call to the checked() helper function
    $html = '<input type="checkbox" id="sgcinsc_open_insc" name="sgcinsc_config_options[sgcinsc_open_insc]" value="1" ' . checked(1, $options['sgcinsc_open_insc'], false) . '/>'; 
     
    // Here, we'll take the first argument of the array and add it to a label next to the checkbox
    $html .= '<label for="sgcinsc_open_insc"> '  . $args[0] . '</label>'; 
     
    echo $html;
     
} // end sgcinsc_open_insc_callback
 
function sgcinsc_selectpage_callback($args) {
    $options = get_option('sgcinsc_config_options');

    $args = array(
        'post_type' => 'page',
        'numberposts' => -1
        );

    $pages = get_posts( $args );

    $html = '<select name="sgcinsc_config_options[sgcinsc_pagina_insc]">';
    
    foreach($pages as $page) {
        
        $selected = $options['sgcinsc_pagina_insc'];

        $html .= '<option value="0">Escoge una página</option>';

        if($selected == $page->ID) {

            $html .= '<option value="' . $page->ID .'" selected>' . $page->post_title  
        . '</option>';    

        } else {

            $html .= '<option value="' . $page->ID .'">' . $page->post_title  
        . '</option>';    
        }

        
    }
    
    $html .= '</select>';

    echo $html;

} //end sgcinsc_selectpage_callback


?>