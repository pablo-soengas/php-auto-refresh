
<!DOCTYPE html>
<html>
<head>
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript"  src="refresh_on_save.js" data-folderToMonitor="C:\Program Files (x86)\EasyPHP-Devserver-17\eds-www\refrescarAlGuardar"></script>
<!--<script type="text/javascript" src="script.js"></script>-->
<title>HTML, CSS and JavaScript demo</title>
</head>
<body>


 

<!-- End your code here -->
</body>
</html>

 
<?php
 
 

include "functions.php";

register_shutdown_function("shutdownHandler");

function shutdownHandler(){

    echo "holaa";
}




?>



