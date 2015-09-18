<?php

//-------------------------------------------------------------------------------




// smt2 dir relative to the wp installation folder
$smt2_dir = '/wp-content/plugins/' . basename( __DIR__ );




// we don't want all those deprecated notices
ini_set('display_errors', false);



// --------------------------------------------------------------------------------







function sanitize_dir_path( $path ){
    return str_replace('//', '/', $path);
}

function forwardslashit( $path ){
    return str_replace( '\\', '/', $path );
}

function get_server_root(){
    return forwardslashit( $_SERVER['DOCUMENT_ROOT'] );
}

function get_this_file_dir(){
    return forwardslashit( __DIR__ );
}

function substract_strings( $big_string, $small_string ){
    return str_ireplace( $small_string, '', $big_string );
}







/**
 * This is the directory where you put the smt2 CMS.
 * You can use relative as well as full URLs like /smt2/ or http://myserver.name/smt2/
 */

// prepare ABS_PATH
$abs_path = substract_strings( get_this_file_dir(), get_server_root() ) ;
$abs_path = '/' . $abs_path . '/';
$abs_path = sanitize_dir_path( $abs_path );


define ('ABS_PATH', $abs_path); // always put an ending slash (/)









/**
* Load th WP config file and extract our needed constants from there.
*/

define( 'WP_BASE_LOCAL_DIR', substract_strings( get_this_file_dir(), $smt2_dir ) );

$wp_config_file = sanitize_dir_path( WP_BASE_LOCAL_DIR . '/wp-config.php' );





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







// ----------------------------------------------------- MySQL database info ---

/** 
 * Your MySQL database name. 
 * If you did not create one, smt2 will do it for you.
 * If you cannot create *new* databases, write the name of your current database
 * and smt2 will store their tables there.
 */
// define ('DB_NAME',     "smt");
/** 
 * Your MySQL username.
 * This user must have grants to SELECT, INSERT, UPDATE, and DELETE tables.
 */
// define ('DB_USER',     "root");
/** 
 * Your MySQL password. 
 */
// define ('DB_PASSWORD', "admin");
/** 
 * Your MySQL server. 
 * If port number ### were needed, use 'servername:###'. 
 */
// define ('DB_HOST',     "localhost");



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
define ('BASE_PATH', dirname(__FILE__));
define ('INC_PATH', BASE_PATH.'/admin/');
require_once INC_PATH.'sys/functions.php';

?>
