<?php
// we want to see all errors:
ini_set('display_errors', false);



require '../config.php';

// We'll be outputting a txt
header('Content-type: application/txt');

// Set file name
header('Content-Disposition: attachment; filename="debug-info.txt"');

// Write the contents to output stream
file_put_contents( "php://output", get_smt2wp_diagnose_info() );
