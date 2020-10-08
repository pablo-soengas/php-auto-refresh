/**
 *
 * @todo add keystroke support to enable/disable
 * @todo implement checkbox hide permanently, show always, show on mouse over
 */

/**
 * this code deals with adding the button to enable/disable php-ar functionality
 */
window.onload = function() {
//obtains full path to the current file being executed and if the file is already enabled for auto refresh in settings.json, from data-currentFile and data-enabled (respectively) custom attributes of the script tag that called this javascript
    var script = document.querySelector('script[data-currentFile]');
    if (script) {
        var currentFile = script.getAttribute('data-currentFile');
        var refreshUrl = script.getAttribute('data-refreshUrl');
        if (script.getAttribute('data-enabled') !== null) {
            var enabled = true;
        }
    }
    /*
    * builds the checkbox input to enable/disable the auto refresh feature
    */
//main container. this container exists so that the checkbox appears when we position the cursor near the top rigth corner of the screen. It sticks out a little in relation to the checkbox so it's not necessary to position the cursor exactly at the top right corner of the screen in order to make the checkbox appear
    let parMain = document.createElement("div");
    parMain.id = "par-main";

//container of toggle
    let parContainer = document.createElement("div");
    parContainer.id = "toggle-par-container";


//input checkbox and label
    let checkbox = document.createElement("input");
    let label = document.createElement("label");
    checkbox.type = "checkbox";
    checkbox.id = "toggle-par-checkbox";

//If the current file being executed is already enabled for auto refresh in settings.json, the checkbox must appear already checked from the beginning, and we must call par_init function to start monitoring files
    if ((typeof enabled !== "undefined") && enabled) {
        checkbox.checked = true;
        par_init();
    }

    label.htmlFor = "toggle-par-checkbox";
    label.appendChild(document.createTextNode("php-ar"));

    parContainer.appendChild(checkbox);
    parContainer.appendChild(label);

    parMain.appendChild(parContainer);

//styles for the subcontainer
    parContainer.style = "font-weight:700;font-family:Sans-serif;border-radius:0 0 0px 5px;color:#fff; background-color:#7478AE;height:0px;width:100px; overflow:hidden";

//styles for the main container, the z-index attribute is set to a very high value so that no other element can overlap it
    parMain.style = "height:6px;position:fixed; top:0px;right:0px;z-index:10000000000000;";

    /**
     * @todo keep commenting from here
     */

// add handlers to show/hide checkbox
    parMain.onmouseover = function () {
        parContainer.style.height = '25px';
        parContainer.style.borderLeft = '1px solid black';
        parContainer.style.borderBottom = '1px solid black';
    };
    parMain.onmouseout = function () {
        parContainer.style.height = '0px';
        parContainer.style.border = 'none';
    };

// add handlers to checkbox to toggle php-auto-refresh functionality
    checkbox.onchange = toggle;

    function toggle() {
        if (this.checked) {
            par_init();
        } else {
            par_stop();
        }
    }

//append to the current document the main container of the checkbox
    document.getElementsByTagName('body')[0].appendChild(parMain);

//Calls the script php-ar.php, passing it the following parameters: the current file, the action to be perform (start or stop monitoring), and the current time (to avoid caching)
    function ajaxReq(action) {
        let xhr = new XMLHttpRequest();

        let url = refreshUrl + "/php-ar.php?currentFile=" + currentFile + '&action=' + action + '&d=' + (new Date()).getTime()

        /**
         * @todo remove for production
         */
        //this is for debugging purposes only
        url += '&XDEBUG_SESSION_START=PHPSTORM';

        xhr.open('GET', url);
        xhr.send();
        return xhr;
    }

//Calls script php-ar.php passing it the following parameters: the current file, the current date (to avoid caching)
    function par_init() {
        let xhr = ajaxReq('start_php_ar');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                if (checkbox.checked)
                    location.reload(true);
            }
        }
    }

//Calls php script stop_monitor passing it the following parameters: the current file, the current date (to avoid caching)
    function par_stop() {
        ajaxReq('stop_php_ar');
    }
}
