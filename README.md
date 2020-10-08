# PHP Auto Refresh
For those of us who don't like having to refresh the browser tab every time we make a change in our php source code, this adds a live editing feature to your projects, that you can enable or disable directly from the browser, without having to add any code to your files

Many IDEs are capable of doing this, but only for front-end files. 

## Installing
Download this repository and install it in the web root of your local web server

## Usage
Enter in your php.ini file and search for the directive auto_prepend_file and asign to this directive the absolute path to the file php-auto-refresh/prepend.php. For example, on a xampp installation on a windows machine, this could look like this:

auto_prepend_file= C:\xampp\htdocs\php-auto-refresh\prepend.php

If you're running php as an apache module, you will need to restart apache for changes to be applied.

And that's it.


To enable/disable it for a specific file, open that file on your browser and check/uncheck the checkbox that will appear when you hover your mouse over the top right corner of the viewport.

If you are experiencing any problems on your projects after having enabled php auto refresh, just comment out the auto_prepend_file directive in php.ini file and everything should return to normal (don't forget to restart apache). Please let me know about any issues.


## Configuration

It doesn't require any configurations to work properly, but keep reading in case you want to customize what subdirectories and files are watched.

By default, it will watch the directory where is located the file we want to auto refresh, and it will watch all of the files in that directory, but you can change this behaviour if you need, by using the settings.json file.

 Open the file php-auto-refresh/settings.json in your editor.
 Inside this file you will encounter an "items" element with an array of objects inside of it. One object like this one will be created for each file for which you have enabled php auto refresh functionality from the browser:
  
        {
          "items": [
            {
              "file": "path/to/file",
              "dir": [],
              "exc": {
                "extensions": [],
                "filenames": [],
                "dirnames": []
              }
            }
          ]
        }
      
 -file: the absolute path to the file you want to auto refresh when any change is saved in any of the files of the directory/directories specified in "dir". This key is automatically created for you when you enable php auto refresh from the browser for that particular file. The rest of the keys will be empty unless you modify them to customize what is being watched.
  
  -dir: an array of absolute paths to the directories that will be continuosly and recursively traversed, waiting for changes to occur in any of the files inside of them. You can specify only one directory or more. If left empty, the directory in which the file specified in "file" is located will be used by default.
  
  -exc: this is an object that contains everything in "dir" that we want to ignore.
  
  For example, if we are working on a wordpress theme and a wordpress plugin for the same project, and we want the browser to auto refresh our index.php every time we modify and save a file in the theme or plugin directory, but we want to ignore the file readme.md, and all css and javascript files, and we also want to ignore completely the fonts' directory, our settings.json would look something like this:
  
    {
      "items": [
        {
          "file": "C:/xampp/htdocs/wordpress/index.php",
          "dir": [
            "C:/xampp/htdocs/wordpress/wp-content/themes/my-theme",
            "C:/xampp/htdocs/wordpress/wp-content/plugins/my-plugin"
            ],
          "exc": {
            "extensions": [
              "css",
              "js"
            ],
            "filenames": [
              "readme.md"
            ],
            "dirnames": [
              "fonts"
            ]
          }
        }
      ]
    }
    
    
    
Of course, you can omit this step if you're not interested in ignoring certain files, or watching specifics subdirectories instead of the default.
  

## License
[MIT](https://choosealicense.com/licenses/mit/)
