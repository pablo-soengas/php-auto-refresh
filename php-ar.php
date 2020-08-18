<?php
include 'functions.php';
// We send this header because otherwise corps will not allow us to call this script from a domain other than the domain of this script, and in a local development environment this could easily happen if we have virtual hosts that work with different servernames
header('Access-Control-Allow-Origin: *');
// Prevent caching of this script
header('Cache-Control: no-cache');
// This is to prevent a max execution time exceeded error, since this script will be executing untill one change is detected and this could happen after 30 seconds which is the default value for max_execution_time
set_time_limit(0);

set_globals();

$_GET['action']();








