<?php

/*
* Needs to be at the same level with config.php, 
* because of the use of __FILE__
*/


// first, let's get the debug functions
include_once 'debug-functions.php';





function get_smt2wp_base_path(){
	return dirname(__FILE__);
}

function get_smt2wp_base_path_slash(){
	return smt2wp_forwardslashit( get_smt2wp_base_path() );
}

function get_smt2wp_cache_path(){
	return get_wp_base_dir() . '/wp-content/smt2wp-cache/' ;  // needs slash "/"  at the end
}

function get_smt2wp_path_relative_to_wp(){
	return '/wp-content/plugins/' . basename( get_smt2wp_base_path() );
}

function get_wp_base_dir(){
	return smt2wp_substract_strings( get_smt2wp_base_path_slash(), get_smt2wp_path_relative_to_wp() );
}







// helpers:

function smt2wp_sanitize_dir_path( $path ){
    return str_replace('//', '/', $path);
}

function smt2wp_forwardslashit( $path ){
    return str_replace( '\\', '/', $path );
}

function get_smt2wp_server_root(){
    return smt2wp_forwardslashit( $_SERVER['DOCUMENT_ROOT'] );
}

function smt2wp_substract_strings( $big_string, $small_string ){
    return str_ireplace( $small_string, '', $big_string );
}