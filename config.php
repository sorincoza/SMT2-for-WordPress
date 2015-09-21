<?php

//-------------------------------------------------------------------------------



// we don't want all those deprecated notices
ini_set('display_errors', false);


// include the shared functions
include_once 'shared-functions.php';



// --------------------------------------------------------------------------------







/**
 * This is the directory where you put the smt2 CMS.
 * You can use relative as well as full URLs like /smt2/ or http://myserver.name/smt2/
 */

// prepare ABS_PATH
$abs_path = smt2wp_substract_strings( get_smt2wp_base_path_slash(), get_smt2wp_server_root() ) ;
$abs_path = '/' . $abs_path . '/';
$abs_path = smt2wp_sanitize_dir_path( $abs_path );


define ('ABS_PATH', $abs_path); // always put an ending slash (/)









/**
* Load th WP config file and extract our needed constants from there.
*/


$wp_config_file = smt2wp_sanitize_dir_path( get_wp_base_dir() . '/wp-config.php' );





// read the wp_config and extract and eval() the DB constants:
$php_code = '';

$handle = fopen($wp_config_file, "r");
if ($handle) {
    while (($line = fgets( $handle )) !== false) {
        
        if (  // conditions for getting the line:
        	strpos( trim($line), 'define' ) === 0
        	&&
        	( strpos( $line, 'DB_' ) !== false )
        
        ) {  // THEN:
        	$php_code .= $line;
        }

    }
    if (!feof($handle)) {
        die( "Error: unexpected fgets() fail\n" );
    }
    fclose($handle);
}


eval( $php_code );  // now we have the constants for the database






/** 
 * Prefix for creating smt2 tables.
 * That's really useful if you have only one database.
 */
define ('TBL_PREFIX',  "smt2_");

// ----------------------------------------------------------------- Add-ons ---

/** 
 * Your Google maps key for client localization. This one is for localhost.
 * If you put smt2 on your own production server, you should register (for free)
 * at http://code.google.com/apis/maps/signup.html
 */
define ('GG_KEY', "ABQIAAAAElGM1_G8Y0SLRJtsUmEeART2yXp_ZAY8_ufC3CFXhHIE1NvwkxTjJAIz5IfhLGJPdYN9-8jws6kgmQ");

// ------------------------------------------ (smt) functions - do not edit! ---
define ('BASE_PATH', get_smt2wp_base_path() );
define ('INC_PATH', BASE_PATH.'/admin/');
require_once INC_PATH.'sys/functions.php';

?>
