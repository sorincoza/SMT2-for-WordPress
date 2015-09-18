<?php
/**
* Plugin Name: SMT2 for WordPress
* Description: An adaptation of SMT2 for WordPress.
* Version: 1.0.1
* Author: Sorin Coza
* Author URI: http://sorincoza.com
*
* Original code: https://code.google.com/p/smt2/
* GitHub Plugin URI: https://github.com/sorincoza/SMT2-for-WordPress
*/



// before anything else, we need some constants:
define( 'SMT2WP_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
define( 'SMT2WP_PLUGIN_BASENAME', plugin_basename(__FILE__) );



// include options page class and instantiate
if ( is_admin() ){
	include 'options-page-lib/class.php';
	new WordPress_Plugin_Template_Settings( __FILE__ );
}




// then bussiness as usual
add_action( 'wp_enqueue_scripts', 'smt2wp_scripts', 1000 );
add_action( 'admin_bar_menu', 'smt2wp_admin_bar_link', 999 );






// the functions:

function smt2wp_scripts(){

	wp_enqueue_script( 'smt2-main-script', SMT2WP_PLUGIN_DIR . 'core/js/smt2e.min.js', array(), '', true );
	
	// pass the init options to script
	wp_localize_script( 
		'smt2-main-script',
		'smt2_init_options',
        smt2wp_get_init_options()
    );

}


function smt2wp_admin_bar_link( $wp_admin_bar ) {
	$args = array(
		'id'    => 'smt2_link_to_admin',
		'title' => 'SMT2 Admin',
		'href'  => SMT2WP_PLUGIN_DIR . 'admin',
	);
	$wp_admin_bar->add_node( $args );	

	$args = array(
		'id'    => 'smt2_link_to_admin__child',
		'title' => 'SMT2 Dashboard',
		'href'  => SMT2WP_PLUGIN_DIR . 'admin',
		'parent'=> 'smt2_link_to_admin'
	);
	$wp_admin_bar->add_node( $args );

	$args = array(
		'id'    => 'smt2_link_to_settings',
		'title' => 'SMT2 Settings',
		'href'  => 'options-general.php?page=smt2wp_plugin_settings',
		'parent'=> 'smt2_link_to_admin'
	);
	$wp_admin_bar->add_node( $args );
}


function smt2wp_get_init_options(){
	$res = array();
	$prefix = 'smt2wp_';

	$keys = array(
		'post_interval' => 30,
		'fps' => 24,
		'rec_time' => 3600,

		'disabled' => 0,
		'cont_recording' => true,
		'warn_text' => '',
		'cookie_days' => 365
	);

	foreach ( $keys as $key => $default) {
		// convert to camelCase	:
		$parts = explode( '_', $key );
		for ( $i=count($parts); $i-->1; ){   $parts[$i] = ucfirst( $parts[$i] );   }
		$key_cc = implode( '', $parts );
		

		// get option:
		$res[ $key_cc ] = get_option( $prefix . $key, $default );

		// sanitize values:
		if ( $key !== 'disabled'  &&  $key !== 'cont_recording'  &&  $key !== 'warn_text' ){
			if ( empty( $res[ $key_cc ] ) ){
				$res[ $key_cc ] = $default;
			}
		}

	}


	// finally, don't let the tracking path to chance
	$res[ 'trackingServer' ] = SMT2WP_PLUGIN_DIR;

	return $res;

}

