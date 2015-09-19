<?php
require '../config.php';

// We'll be outputting a txt
header('Content-type: application/txt');

// Set file name
header('Content-Disposition: attachment; filename="debug-info.txt"');

// Write the contents to output stream
file_put_contents( "php://output", get_diagnose_info() );
