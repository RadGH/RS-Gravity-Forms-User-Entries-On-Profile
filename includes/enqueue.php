<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function gfue_enqueue_scripts() {
	global $pagenow;
	if ( !isset($pagenow) ) return;
	if ( $pagenow !== 'user-edit.php' && $pagenow !== 'profile.php'  ) return;
	
	wp_enqueue_style( 'rs-gfue', RS_GFUE_URL . '/assets/gfue.css', array(), RS_GFUE_VERSION );
}
add_action( 'admin_enqueue_scripts', 'gfue_enqueue_scripts' );