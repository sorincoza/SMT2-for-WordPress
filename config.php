<?php

//-------------------------------------------------------------------------------




// smt2 dir relative to the wp installation folder
$smt2_dir = '/wp-content/plugins/' . basename( dirname( __FILE__ ) );




// we don't want all those deprecated notices
// ini_set('display_errors', false);



// --------------------------------------------------------------------------------




// just a log function
function __log( $data, $append = true ){
    $file = __DIR__ . '/logs.txt';
    if ( $append ){
        $data .= PHP_EOL . PHP_EOL . '===========================' . PHP_EOL . PHP_EOL . file_get_contents($file);
    }

    file_put_contents($file, $data);
}






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
    return forwardslashit( dirname( __FILE__ ) );
}

function substract_strings( $big_string, $small_string ){
    return str_ireplace( $small_string, '', $big_string );
}



function diagnose_folders(){

    $folders = array(
        'doc root' => $_SERVER['DOCUMENT_ROOT'],
        'wp root' => WP_BASE_LOCAL_DIR ,
        'wp content' => WP_BASE_LOCAL_DIR . '/wp-content',
        'wp uploads' => WP_BASE_LOCAL_DIR . '/wp-content/uploads',
        'smt root (dirname( __FILE__ ))' => WP_BASE_LOCAL_DIR . '/wp-content/plugins/' . basename( dirname( __FILE__ ) ) ,
        'smt root (__DIR__)' =>WP_BASE_LOCAL_DIR . '/wp-content/plugins/' . basename( __DIR__ ) ,

        'smt cache' => WP_BASE_LOCAL_DIR . '/wp-content/plugins/' . basename( dirname( __FILE__ ) ) . '/cache',

    );

    $folders_check_result = array();

    foreach ($folders as $key => $folder) {
        $folder = sanitize_dir_path( $folder );
        // var_dump($folder);

        $folders_check_result[$key]['path'] = $folder;
        $folders_check_result[$key]['permissions'] = base_convert(fileperms($folder),10,8);
        // $folders_check_result[$key]['owner'] = posix_getpwuid(fileowner($folder));
        $folders_check_result[$key]['owner_id'] = fileowner($folder);
        // $folders_check_result[$key]['group'] = posix_getgrgid(filegroup($folder));
        $folders_check_result[$key]['group_id'] = filegroup($folder);
        // $folders_check_result[$key]['other_stats'] = stat($folder);
        $folders_check_result[$key]['is_writable'] = is_writable($folder);



        $test_folder = sanitize_dir_path( $folder . '/test-348214' );
        $test_file = $test_folder . '/test-file-6542.txt'; 

        $mkdir = mkdir($test_folder);
        $put_contents = file_put_contents($test_file , 'this is just a test. you can delete this file');

        $delete_file = unlink( $test_file );
        $rm_dir = rmdir($test_folder);

        $folders_check_result[$key]['can_make_dir'] = $mkdir;
        $folders_check_result[$key]['can_write_file'] = ( $put_contents === false  ?  false  :  true );
        $folders_check_result[$key]['can_delete_file'] = $delete_file;
        $folders_check_result[$key]['can_rm_dir'] = $rm_dir;

    }

    $all_ini_settings = ini_get_all();


    echo '<br><br>' . json_encode($folders_check_result);
    echo '<br><br><br><br>' . json_encode($all_ini_settings);
    // var_dump($all_ini_settings);
    exit;

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




diagnose_folders();




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
