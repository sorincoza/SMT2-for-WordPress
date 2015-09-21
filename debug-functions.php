<?php


// just a log function
if ( !function_exists('__log') ){
    function __log( $data, $append = true ){
        $file = __DIR__ . '/logs.txt';
        if ( $append ){
            $data .= PHP_EOL . PHP_EOL . '===========================' . PHP_EOL . PHP_EOL . file_get_contents($file);
        }

        file_put_contents($file, $data);
    }
}



function get_smt2wp_diagnose_info(){

    $wp_base_dir = get_wp_base_dir();

    $folders = array(
        'server root' => $_SERVER['DOCUMENT_ROOT'],
        'wp root' => $wp_base_dir ,
        'wp content' => $wp_base_dir . '/wp-content',
        'wp uploads' => $wp_base_dir . '/wp-content/uploads',
        'smt root' => $wp_base_dir . '/wp-content/plugins/' . basename( dirname( __FILE__ ) ) ,
    );

    $folders_check_result = array();

    foreach ($folders as $key => $folder) {
        $folder = smt2wp_sanitize_dir_path( $folder );
        // var_dump($folder);

        $folders_check_result[$key]['path'] = $folder;
        $folders_check_result[$key]['permissions'] = base_convert(fileperms($folder),10,8);
        // $folders_check_result[$key]['owner'] = posix_getpwuid(fileowner($folder));
        $folders_check_result[$key]['owner_id'] = fileowner($folder);
        // $folders_check_result[$key]['group'] = posix_getgrgid(filegroup($folder));
        $folders_check_result[$key]['group_id'] = filegroup($folder);
        // $folders_check_result[$key]['other_stats'] = stat($folder);
        $folders_check_result[$key]['is_writable'] = is_writable($folder);



        $test_folder = smt2wp_sanitize_dir_path( $folder . '/test-348214' );
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


    $separator = '<<+>>';

    $res = $separator . json_encode($folders_check_result);
    $res .= $separator . json_encode($all_ini_settings);
    // var_dump($all_ini_settings);
    return $res;

}


