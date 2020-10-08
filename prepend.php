<?php

/**
 *This script will be auto prepended to the php file that the user has requested, and is responsible for registering a function that will be called when the execution of the requested php file finishes. This function, in turn, will inject a javascript to the html outputted in order to enable or disable the auto refresh capability
 */

/**
 * @todo Create php file with all settings (files to refresh, directories to monitor for each file, whether to show checkbox permanently or on mouse over, or hide it, etc)
 */

// Prevent php auto refresh from executing if php is running from cli
if (php_sapi_name() == "cli")
    return;

// check if it's an ajax request, and return if true, because we don't need the auto refresh functionality for scripts other than the main requested php file
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    return;

//Registers a function to be executed after the execution of the main script has finished
register_shutdown_function(
/**
 * Function to be executed after $current_file execution finishes
 *
 * It will output a script tag at the end of the result of the execution of $current_file, in order to call the javascript file php_ar.js.     *
 * We pass a callback instead of a function name, in order to avoid polluting the global namespace
 *
 *
 * @param string $current_file the file that the current request is for
 * @param bool $enabled whether an entry for $current_file was found in settings.json and also the key named "enabled" in that entry has a value of "true", which means that $current_file is enabled for auto refresh in settings.json.     *
 * @param $refresh_url
 * @return void
 */
    function () {

        // this helper function retrieves the response headers calling the php function headers_list() and turns the indexed array returned by that function into an associative array so that is simpler to handle
            function response_headers()
            {
                $arh = array();
                $headers = headers_list();
                foreach ($headers as $header) {
                    // First we make the header lowercase to avoid comparisons returning false due to case-sensitiveness
                    $header = strtolower($header);
                    $header = explode(":", $header);
                    $arh[array_shift($header)] = trim(implode(":", $header));
                }
                return $arh;
            }

        // if the php script requested doensn't generate an html file (for example, it generated a json file) , php auto refresh won't work, so it wouldn't make any sense to inject scripts tags on it. Fist we call the function defined above to obtain an associative array with the response headers. Then we need to check if 'Content-type' header is set and if it doesn't contain 'text/html' as its value. If true, we just return.
        $response_headers = response_headers();
        if (isset($response_headers['content-type']) && strpos($response_headers['content-type'], 'text/html') === FALSE)
            return;



        //Obtains the full path to the directory where php auto refresh files were installed
        $refresh_dir = dirname(ini_get('auto_prepend_file'));
        // Obtains the name of the directory where php auto refresh files were installed
        $refresh_dirname = pathinfo($refresh_dir)['basename'];
        /**
         * @todo create function to check if $refresh_url is correct. If not, maybe because the user changed its hosts file, try o access using ip 127.0.0.1 or ::1
         */
        // the url to access the refresher files.
        $refresh_url = 'http://localhost/' . $refresh_dirname;
        //obtains the contents of the json file with the settings
        $settings = file_get_contents($refresh_dir . '/settings.json');
        //Obtains the current main file that is being request and must be refreshed in the browser upon changes
        $current_file = $_SERVER['SCRIPT_FILENAME'];
        //Obtains the directory where the main file that is being request and must be refreshed in the browser upon changes is located . This directory will be monitored by default unless other specified in settings.json
        $current_dir = pathinfo($_SERVER['SCRIPT_FILENAME'])['dirname'];
        //is $current_file in settings.json? (i.e., we want to auto refresh this file)
        $file_in = false;
        //parse the json file with the settings into an associative array
        $settings = json_decode($settings, true);
        //iterates through the settings array to search for $current_file
        foreach ($settings['items'] as $item) {
            if ($item['file'] == $current_file) {
                //$current_file is in settings.json
                $file_in = true;
                break;
            }
        }
        //if $current_file was found in settings.json, check if it's enabled
        if ($file_in && $item['enabled']) {
            $enabled = true;
        } else {
            $enabled = false;
        }

        $script_tag = '<script type="text/javascript" src="' . $refresh_url . '/php_ar.js" data-currentFile="' . $current_file . '" ';
        //if the file is already enabled for auto refresh in settings.json, add the data-enabled custom attribute to the script tag, so that the javascript called by this script tag knows that
        if (isset($enabled) && $enabled)
            $script_tag .= ' data-enabled ';
        $script_tag .= 'data-refreshUrl="' . $refresh_url . '" ';
        $script_tag .= '></script>';
        echo $script_tag;
    });


