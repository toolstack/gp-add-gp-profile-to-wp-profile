<?php
/*
Plugin Name: GP Add GP Profile to WP Profile
Plugin URI: https://glotpress.blog/
Description: A plugin for GlotPress as a WordPress Plugin that adds the GlotPress user profile settings to the WordPress profile page.
Version: 0.6
Author: Greg Ross
Author URI: https://toolstack.com/
Text Domain: gp-add-gp-profile-to-wp-profile
Tags: glotpress, glotpress plugin, translate
License: GPLv2 or later
*/

class GP_Add_GP_Profile_to_WP_Profile {
	public $id = 'additional-links';

	public function __construct() {
		// Handle the WordPress user profile items
		add_action( 'show_user_profile', array( $this, 'gp_wp_profile' ), 10, 1 );
		add_action( 'edit_user_profile', array( $this, 'gp_wp_profile' ), 10, 1 );
		add_action( 'personal_options_update', array( $this, 'gp_wp_profile_update' ), 10, 1 );
		add_action( 'edit_user_profile_update', array( $this, 'gp_wp_profile_update' ), 10, 1 );

	}

	public function gp_wp_profile( $user ) {
		// If the user can edit their profile, then show the edit screen otherwise don't display anything.
		if ( ! current_user_can( 'edit_user', $user->ID ) ) { 
			return;
		}		
	?>
		<h3 id="gp-profile"><?php _e( 'GlotPress Profile', 'gp-add-gp-profile-to-wp-profile' ); ?></h3>
	<?php		
		$template = 'settings-edit';
		$locations = array( GP_TMPL_PATH );
		$locations = apply_filters( 'gp_tmpl_load_locations', $locations, $template, array(), null );

		foreach( $locations as $location ) {
			$file = $location . $template . '.php';
			if ( is_readable( $file ) ) {
				include( $file );
			}
		}	
	}

	public function load_text_domain() {
		load_plugin_textdomain( gp-add-gp-profile-to-wp-profile, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	public function gp_wp_profile_update( $user_id ) {
		// If the user cannot edit their profile, then don't save the settings
		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		
		$gp_route_settings = new GP_Route_Settings;
		
		$gp_route_settings->settings_post( $user_id );
		
		return true;
	}

}

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_add_gp_profile_to_wp_profile_init' );

// This function creates the plugin.
function gp_add_gp_profile_to_wp_profile_init() {
	GLOBAL $gp_add_gp_profile_to_wp_profile;
	
	if( version_compare( GP_VERSION, '2.0', '>=' ) ) {
		$gp_add_gp_profile_to_wp_profile = new GP_Add_GP_Profile_to_WP_Profile;
	}
}
