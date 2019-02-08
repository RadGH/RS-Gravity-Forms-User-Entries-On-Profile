<?php
/*
Plugin Name: RS Gravity Forms - User Entries On Profile
Description: Displays form entries submitted by a user on their profile. Accessible only to administrators and anyone with the "gravityforms_view_entries" role.
Version:     1.0.3
Author:      Radley Sustaire
Author URI:  http://radleysustaire.com/
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'RS_GFUE_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'RS_GFUE_PATH', dirname( __FILE__ ) );
define( 'RS_GFUE_VERSION', '1.0.3' );

add_action( 'plugins_loaded', 'gfue_init_plugin' );

// Initialize plugin: Load plugin files
function gfue_init_plugin() {
	if ( !class_exists( 'GFForms' ) ) {
		add_action( 'admin_notices', 'gfue_warn_no_gforms' );
		return;
	}
	
	include_once( RS_GFUE_PATH . '/includes/enqueue.php' );
	include_once( RS_GFUE_PATH . '/includes/user-profile.php' );
}

// Require Gravity Forms
function gfue_warn_no_gforms() {
	?>
	<div class="error">
		<p><strong>RS Gravity Forms - User Entries On Profile:</strong> This plugin requires Gravity Forms in order to operate. Please install and activate Gravity Forms, or disable this plugin.</p>
	</div>
	<?php
}