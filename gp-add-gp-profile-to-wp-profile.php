<?php
/*
Plugin Name: GP Add GP Profile to WP Profile
Plugin URI: http://glotpress.org/
Description: A plugin for GlotPress as a WordPress Plugin that adds the GlotPress user profile settings to the WordPress profile page.
Version: 0.5
Author: GregRoss
Author URI: http://toolstack.com
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
		<h3 id="gp-profile"><?php _e( 'GlotPress Profile', 'glotpress' ); ?></h3>
	<?php		
		include( GP_PATH . './gp-templates/settings-edit.php' );
	}

	public function gp_wp_profile_update( $user_id ) {
		// If the user cannot edit their profile, then don't save the settings
		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
		
		$gp_route_profile = new GP_Route_Profile;
		
		$gp_route_profile->profile_post( $user_id );
		
		return true;
	}

}

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_add_gp_profile_to_wp_profile_init' );

// This function creates the plugin.
function gp_add_gp_profile_to_wp_profile_init() {
	GLOBAL $gp_add_gp_profile_to_wp_profile;
	
	if( version_compare( GP_VERSION, '1.1', '>=' ) ) {
		$gp_add_gp_profile_to_wp_profile = new GP_Add_GP_Profile_to_WP_Profile;
	}
}
