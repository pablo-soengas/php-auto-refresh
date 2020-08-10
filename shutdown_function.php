<?php

function main () {
    //Obtains the directory where php-auto-refresh folder was installed
    $refresh_dir = dirname(ini_get('auto_prepend_file'));
    //obtains the file with settings
    $settings = file_get_contents($refresh_dir . '/settings.json');
    //Obtains the current main file that is being request and must be refreshed in the browser upon any changes
    $current_file = $_SERVER['SCRIPT_FILENAME'];
    //Obtains the directory which is located in the main file that is being request and must be refreshed in the browser upon any changes. This directory will be monitored by default unless other specified in settings.json
    $current_dir = pathinfo($_SERVER['SCRIPT_FILENAME'])['dirname'];
    //is $current_file in settings.json, e.j., we want to auto refresh this file?
    $file_in = false;
    $settings = json_decode($settings, true);
    foreach($settings['items'] as $item){
        if($item['file'] == $current_file){
            $file_in = true;
            break;
        }
    }
    if(!$file_in)
        return;




    function shutdownHandler($current_file) {
        $script_tag = '<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script><script type="text/javascript"  
src="http://localhost/php-auto-refresh/refresh_on_save.js" data-currentFile="' . $current_file . '"></script>';
        echo $script_tag;
    }
    register_shutdown_function( "shutdownHandler", $current_file );
}

main();
