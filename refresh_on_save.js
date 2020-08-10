
jQuery(document).ready(function () {

    var me = document.querySelector('script[data-currentFile]');
    if (me)
        var currentFile = me.getAttribute('data-currentFile');




    jQuery.ajax({
        url: "http://localhost/php-auto-refresh/folder_monitor.php",
        success: function () {
//                        console.log(result);
            location.reload(true);


        },
        data: {
            currentFile: currentFile
        }
    });




});
