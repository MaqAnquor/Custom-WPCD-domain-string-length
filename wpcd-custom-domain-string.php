<?php
/*
Plugin Name: Custom Subdomain String
Description: A plugin to customize the subdomain string generation for WPCD.
Version: 1.5
Author: Abhijeet Verma
*/

if ( class_exists( 'WPCD_Init' ) ) {

    // Add a submenu page under WPCloudDeploy Settings
    function my_custom_subdomain_settings_subpage() {
        add_submenu_page(
            'edit.php?post_type=wpcd_app_server',    // Parent slug (WPCloudDeploy settings page)
            'Custom Subdomain Settings',             // Page title
            'Custom Subdomain String',               // Menu title
            'manage_options',                        // Capability
            'custom-domain-string-length',           // Menu slug
            'my_custom_subdomain_settings_page'      // Function to render the settings page
        );
    }
    add_action( 'admin_menu', 'my_custom_subdomain_settings_subpage' );

    // Render the settings page
    function my_custom_subdomain_settings_page() {
        ?>
        <div class="wrap">
            <h1>Custom Subdomain String Length</h1>
            <form method="post" action="options.php">
                <?php
                // Output security fields for the registered settings
                settings_fields( 'custom_domain_settings' );

                // Output setting sections and their fields
                do_settings_sections( 'custom-domain-string-length' );

                // Submit button to save changes
                submit_button( 'Save Settings' );
                ?>
            </form>
        </div>
        <?php
    }

    // Register settings and add fields to the subpage
    function my_custom_subdomain_setting_field() {
        // Add the section to the custom settings page
        add_settings_section(
            'custom_domain_string_length_section',  // Section ID
            'Custom Subdomain String Length',       // Section title
            'my_custom_subdomain_section_callback', // Section description (optional)
            'custom-domain-string-length'           // Page where the section will be displayed (slug of the subpage)
        );

        // Add the field for subdomain string length
        add_settings_field(
            'my_subdomain_length',                  // Field ID
            'Subdomain String Length',              // Field title
            'my_subdomain_length_callback',         // Callback to render the input field
            'custom-domain-string-length',          // Page where the field will be displayed (slug of the subpage)
            'custom_domain_string_length_section'   // Section ID where the field will be placed
        );

        // Register the setting so it can be saved in the database
        register_setting( 'custom_domain_settings', 'my_subdomain_length', [
            'type' => 'integer',
            'description' => 'Length of the subdomain string',
            'default' => 25,
            'sanitize_callback' => 'absint',
        ]);
    }
    add_action( 'admin_init', 'my_custom_subdomain_setting_field' );

    // Callback to render the section description (optional)
    function my_custom_subdomain_section_callback() {
        echo '<p>Enter a custom length for the subdomain if your domain length is above 25 characters.</p>';
    }

    // Callback to render the input field for subdomain string length
    function my_subdomain_length_callback() {
        $subdomain_length = get_option( 'my_subdomain_length', 25 );
        echo '<input type="number" id="my_subdomain_length" name="my_subdomain_length" value="' . esc_attr( $subdomain_length ) . '" />';
    }

    // Custom function to generate a subdomain string using the saved length
    function my_custom_subdomain_string( $length ) {
        $custom_length = get_option( 'my_subdomain_length', 25 );
        return wpcd_random_str( $custom_length, '0123456789abcdefghijklmnopqrstuvwxyz' );
    }
    add_filter( 'wpcd_wpapp_subdomain_string', 'my_custom_subdomain_string', 12 );

} else {
    // Show an admin notice if WPCloudDeploy is not active
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>WPCloudDeploy is required for the Custom Subdomain String plugin to work.</p></div>';
    });
}
