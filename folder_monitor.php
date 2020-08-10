<?php
//este encabezado lo enviamos porque sino corps no nos permitirá llamar a este script desde un dominio diferente al dominio de este script, y en un entorno de desarrollo local esto tranquilamente podria ocuerrir si tenemos virtual hosts que trabajan con  distintos servername
header('Access-Control-Allow-Origin: *');
set_time_limit( 0 );

if(isset($_GET['currentFile']))
	$file_to_watch = $_GET['currentFile'];

$refresh_dir = dirname(__FILE__);
$settings = file_get_contents($refresh_dir . '/settings.json');
$settings = json_decode($settings, true);
foreach($settings['items'] as $item){
	if($item['file'] == $file_to_watch){
		$file_to_watch = $item;
		break;
	}
}

$folder_to_watch = $file_to_watch['dir'] ?  $file_to_watch['dir'] : dirname($file_to_watch['file']);

// this will contain an array with keys as file paths and values as last modified time of the respective file
$array_archivos = array();
// array with file extensions to ignore and the  convert file extensions to lowercase to allow the user to enter the extensions in settings.json without worrying about case

$exc_ext = (isset($file_to_watch['exc']['extensions']) && is_array($file_to_watch['exc']['extensions']) && !empty($file_to_watch['exc']['extensions'])) ? $file_to_watch['exc']['extensions'] : false;
//$exc_ext = array('html');
if($exc_ext)
	$exc_ext = array_map( 'strtolower', $exc_ext );

// files to ignore
$exc_files = (isset($file_to_watch['exc']['filenames']) && is_array($file_to_watch['exc']['filenames']) && !empty($file_to_watch['exc']['filenames'])) ? $file_to_watch['exc']['filenames'] : false;

// directories to ignore
$exc_dirs = (isset($file_to_watch['exc']['dirnames']) && is_array($file_to_watch['exc']['dirnames']) && !empty($file_to_watch['exc']['dirnames'])) ? $file_to_watch['exc']['dirnames'] : false;



while ( true ) {

	sleep( 1 );

	listFolderFiles( $folder_to_watch );

	$pasada_1er_iteracion = true;

}


function listFolderFiles( $dir ) {
	global $array_archivos, $pasada_1er_iteracion, $exc_ext, $exc_files, $exc_dirs;
	$ffs = scandir( $dir );

	// prevent processing empty directories
	if ( count( $ffs ) < 3 ) {
		return;
	}

	// remove references to current and parent directories
	unset( $ffs[ array_search( '.', $ffs, true ) ] );
	unset( $ffs[ array_search( '..', $ffs, true ) ] );



	foreach ( $ffs as $ff ) {

		if ( is_dir( $dir . '/' . $ff ) ) {
			/**
			 * @todo I'm not sure here if $exc_dirs should contain full or relative paths, or just the name of the directory (the later case could be a problem if inside the directory being watched there two or more subdirectories with the same name, and we only want to exclude one of those)
			 */
			if ($exc_dirs){
			if (in_array($ff, $exc_dirs))
				continue;
			}
			listFolderFiles( $dir . '/' . $ff );
		} else {
			// we filter by extension of the file
			if ($exc_ext) {
				$ext = strtolower(pathinfo($ff)['extension']);
				if (in_array($ext, $exc_ext))
					continue;
			}
			if ($exc_files ) {
				if (in_array($ff, $exc_files))
					continue;
			}
			if ( @$pasada_1er_iteracion ) {
				clearstatcache();
				if ( $array_archivos[ $dir . '/' . $ff ] !== filemtime( $dir . '/' . $ff ) ) {
					while ( $array_archivos[ $dir . '/' . $ff ] !== filemtime( $dir . '/' . $ff ) ) {

						$array_archivos[ $dir . '/' . $ff ] = filemtime( $dir . '/' . $ff );

//						sleep( 1 );
						clearstatcache();
					}
					exit( );
				}

			} else {
				$array_archivos[ $dir . '/' . $ff ] = filemtime( $dir . '/' . $ff );
			}
		}

	}


}


?>




