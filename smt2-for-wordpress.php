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




// include options page class and instantiate
if ( is_admin() ){
	include 'options-page-lib/class.php';
	new WordPress_Plugin_Template_Settings( __FILE__ );
}


// add and instantiate the GitHub updater
add_action( 'init', 'github_plugin_updater_test_init' );
function github_plugin_updater_test_init() {

	include_once 'updater.php';

	define( 'WP_GITHUB_FORCE_UPDATE', true );
	define( 'GITHUB_USERNAME', 'sorincoza' );
	define( 'GITHUB_APP_NAME', 'SMT2-for-WordPress');
	define( 'GITHUB_TOKEN', 'c83cd96e9f9f5c1934507e9349e9b63a258f48ad' );

	if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin

		// get proper directory name:
		$pieces = explode( '/', SMT2WP_PLUGIN_DIR );
		$p_len = count($pieces);
		$proper_folder_name = ( !empty($pieces[ $p_len - 1 ]) )   ?   $pieces[ $p_len - 1 ]   :   $pieces[ $p_len - 2 ] ;


		// configuration:
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => $proper_folder_name,
			'api_url' => 'https://api.github.com/repos/'. GITHUB_USERNAME .'/' . GITHUB_APP_NAME,
			'raw_url' => 'https://raw.github.com/' . GITHUB_USERNAME .'/' . GITHUB_APP_NAME . '/master',
			'github_url' => 'https://github.com/' . GITHUB_USERNAME .'/' . GITHUB_APP_NAME,
			'zip_url' => 'https://github.com/' . GITHUB_USERNAME .'/' . GITHUB_APP_NAME . '/zipball/master',
			'sslverify' => true,
			'requires' => '4.0',
			'tested' => '4.3',
			'readme' => 'README.md',
			'access_token' => GITHUB_TOKEN,
		);

		var_dump($pieces, $config);
		echo '<style>#adminmenuback{display:none}</style>';

		new WP_GitHub_Updater( $config );

	}

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

