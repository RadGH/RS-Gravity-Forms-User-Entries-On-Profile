<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function gfue_enqueue_scripts() {
	global $pagenow;
	if ( !isset($pagenow) ) return;
	if ( $pagenow !== 'user-edit.php' && $pagenow !== 'profile.php'  ) return;
	
	wp_enqueue_style( 'aa-gfue', AA_GFUE_URL . '/assets/gfue.css', array(), AA_GFUE_VERSION );
	
	wp_enqueue_script( 'tablesort', AA_GFUE_URL . '/assets/tablesort-5.1.0-all.min.js', array(), '5.1.0.all' );
	wp_enqueue_script( 'dtl-eval-admin', AA_GFUE_URL . '/assets/gfue.js', array( 'jquery', 'tablesort' ), AA_GFUE_VERSION );
}
add_action( 'admin_enqueue_scripts', 'gfue_enqueue_scripts' );