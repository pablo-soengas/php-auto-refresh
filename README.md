# PHP Auto Refresh
For those of us who don't like having to refresh the browser tab every time we make a change in our php source code, this adds a live editing feature to your projects.

Many IDEs are capable of doing this, but only for front-end files. 

# Installing
Download this repository and install it in the web root of your local web server

# Configuring
1. Enter in your php.ini file and search for the directive auto_prepend_file and asign to this directive the absolute path to the file php-auto-refresh/shutdown_function. For example, on a xampp installation on a windows machine, this could look like this:

  auto_prepend_file= C:\xampp\htdocs\php-auto-refresh\shutdown_function.php

  (If you're running php as an apache module, you will need to restart apache for changes to be applied)
  
2. Open the file php-auto-refresh/settings.json. Here you will be adding the files you want to auto refresh, as well as the directory to watch for a certain file, file extensions and subdirectories to ignore, etc.
  Inside this file you will encounter an "items" element with an object inside:
  
        {
          "items": [
            {
              "file": "",
              "dir": "",
              "exc": {
                "extensions": [],
                "filenames": [],
                "dirnames": []
              }
            }
          ]
        }
      
 -file: the absolute path to the file you want to auto refresh when any change is saved in any of the files of the directory specified in "dir".
  
  -dir: the absolute path to the directory that will be continuosly and recursively traversed, waiting for changes to occur in any of the files inside of it. If not specified, the directory in which the file specified in "file" is located will be used by default.
  
  -exc: this is an object wich contains everything in "dir" that we want to ignore.
  
  For example, if we want are working on a wordpress theme project, and we want to auto refresh our index.php everytime we modify and save a file in the theme directory, but we want to ignore the file readme.md, and all css and javascript files, and we also want to ignore completely the fonts directory, our settings.json would look something like this:
  
    {
      "items": [
        {
          "file": "C:/xampp/htdocs/wordpress/index.php",
          "dir": "C:/xampp/htdocs/wordpress/wp-content/themes/my-theme",
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
    
    
    
  You will have to add a new object like the above to the items array, for every file you want to auto reload. The only field required is "file", you can omit the rest if you're not interested in ignoring certain files, or watching a specific subdirectory instead of the default (i.e. the directory in wich the file to auto refresh is located)
  
