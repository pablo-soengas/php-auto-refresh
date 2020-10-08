<?php

function set_globals()
{
// Retrieves the path to the main requested php file
    if (isset($_GET['currentFile']))
        $GLOBALS['file'] = $_GET['currentFile'];

// will contain the contents of the file where the php files that must be refresh upon any changes are listed
    $GLOBALS['settings'] = false;

// Retrieves path to php-auto-refresh directory
    $GLOBALS['refresh_dir'] = dirname(__FILE__);
// stores the time when settings.json was last modified
    clearstatcache();
    $GLOBALS['settings_last_mod'] = filemtime($GLOBALS['refresh_dir'] . '/settings.json');
}

/**
 * @param $file
 * @return array|bool
 */
function &get_file_settings($file)
{
    global $settings;
    get_settings();

    /**
     * modify settings.json file so that the foreach loop below can be replace by something simpler like "if($settings['items'][$file])"
     */
// Iterates through the array to check if $file is in settings.json, and in such case it sets to true its "enabled" key
    foreach ($settings['items'] as &$item) {
        if ($item['file'] == $file) {
            return $item;
        }
    }
    return false;
}

function has_settings_changed()
{
    global $settings_last_mod, $refresh_dir;
    clearstatcache();
    if ($settings_last_mod === filemtime($refresh_dir . '/settings.json')) {
        return false;
    }
    return true;
}

/**
 * @return void
 */
function get_settings()
{
    global $settings, $refresh_dir, $settings_last_mod;
    // if file settings.json was already retrieved and it hasn't been modified since then, we don't need to request the file again
    if ($settings !== false && !has_settings_changed()) {
        return;
    }
    /**
     * @todo If many files are enabled to be auto refreshed, then the settings.json file will grow too much and this function will become slower,so instead of having the configuration of all files in settings.json, it would be better to have one json file per file.
     */
    // Retrieves the contents of the file where the php files that must be refresh upon any changes are listed
    $settings = file_get_contents($refresh_dir . '/settings.json');
// Parses it into an associative array
    $settings = json_decode($settings, true);
}

/**
 * Updates content of settings.json file
 *
 * @return void
 */
function update_settings()
{
    global $settings, $refresh_dir;
    // convert associative array into json, without escaping forward slashed, and pretty formats it
    $settings = json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    //update settings.json file
    file_put_contents($refresh_dir . '/settings.json', $settings);
}


function watch_dir_files($dir)
{
    global $array_archivos, $exc_ext, $exc_files, $exc_dirs;
    $dir_files = scandir($dir);

    // prevent processing empty directories
    if (count($dir_files) < 3) {
        return;
    }

    // remove references to current and parent directories
    unset($dir_files[array_search('.', $dir_files, true)]);
    unset($dir_files[array_search('..', $dir_files, true)]);


    foreach ($dir_files as $file_name) {

        if (is_dir($dir . '/' . $file_name)) {
            /**
             * @todo I'm not sure here if $exc_dirs should contain full or relative paths, or just the name of the directory (the later case could be a problem if inside the directory being watched there two or more subdirectories with the same name, and we only want to exclude one of those)
             */
            if ($exc_dirs) {
                if (in_array($file_name, $exc_dirs))
                    continue;
            }
            watch_dir_files($dir . '/' . $file_name);
        } else {
            // we filter by extension of the file
            if ($exc_ext) {
                $ext = strtolower(pathinfo($file_name)['extension']);
                if (in_array($ext, $exc_ext))
                    continue;
            }
            if ($exc_files) {
                if (in_array($file_name, $exc_files))
                    continue;
            }
            if (isset($array_archivos[$dir . '/' . $file_name])) {
                clearstatcache();
                if ($array_archivos[$dir . '/' . $file_name] !== filemtime($dir . '/' . $file_name)) {
                    /*  while ($array_archivos[$dir . '/' . $file_name] !== filemtime($dir . '/' . $file_name)) {

                          $array_archivos[$dir . '/' . $file_name] = filemtime($dir . '/' . $file_name);

                  //      sleep( 1 );
                          clearstatcache();
                      }*/
                    exit();
                }

            } else {
                $array_archivos[$dir . '/' . $file_name] = filemtime($dir . '/' . $file_name);
            }
        }

    }

}


function start_php_ar()
{
    global $file, $refresh_dir, $settings;
    if ($file_to_watch =& get_file_settings($file)) {
        if ($file_to_watch['enabled'] !== true) {
            $file_to_watch['enabled'] = true;
            //update settings.json file
            update_settings();
        }
    } else {
// If the current file is not present in settings.json, we need to add it
        //create the new item for the current file
        $file_to_watch = array(
            'file' => $file,
            'enabled' => true,
            'dir' => '',
            'exc' => array('extensions' => array(), 'filenames' => array(), 'dirnames' => array())
        );
        //append the new item
        $settings['items'][] = $file_to_watch;

        //update settings.json file
        update_settings();

        //assign the item to the variable we will be working with
    }


    $folders_to_watch = array();
    $folders_to_watch = $file_to_watch['dir'] ? $file_to_watch['dir'] : array(dirname($file_to_watch['file']));

//    $folders_to_watch = $file_to_watch['dir'] ? $file_to_watch['dir'] : dirname($file_to_watch['file']);

// this will contain an array with keys as file paths and values as last modified time of the respective file
    $array_archivos = array();
// array with file extensions to ignore and the  convert file extensions to lowercase to allow the user to enter the extensions in settings.json without worrying about case

    $exc_ext = (isset($file_to_watch['exc']['extensions']) && is_array($file_to_watch['exc']['extensions']) && !empty($file_to_watch['exc']['extensions'])) ? $file_to_watch['exc']['extensions'] : false;
//$exc_ext = array('html');
    if ($exc_ext)
        $exc_ext = array_map('strtolower', $exc_ext);

// files to ignore
    $exc_files = (isset($file_to_watch['exc']['filenames']) && is_array($file_to_watch['exc']['filenames']) && !empty($file_to_watch['exc']['filenames'])) ? $file_to_watch['exc']['filenames'] : false;

// directories to ignore
    $exc_dirs = (isset($file_to_watch['exc']['dirnames']) && is_array($file_to_watch['exc']['dirnames']) && !empty($file_to_watch['exc']['dirnames'])) ? $file_to_watch['exc']['dirnames'] : false;

//store the time when settings.json was last modified
    clearstatcache();
    $settings_last_mod = filemtime($refresh_dir . '/settings.json');


    // starts watching recursively files and folders inside $folders_to_watch until the stop_php_ar() function is called and processed
    while (!(has_settings_changed() && get_file_settings($file)['enabled'] == false)) {
        //we use array_map to apply the watch_dir_files function to each folder containin the array $folders_to_watch
        array_map('watch_dir_files', $folders_to_watch);

        sleep(1);

        clearstatcache();
    }
}


function stop_php_ar()
{
    global $file;
    $file_to_watch =& get_file_settings($file);
    $file_to_watch['enabled'] = false;

    update_settings();
}
