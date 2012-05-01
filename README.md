
##BrickOO [![Build Status](https://secure.travis-ci.org/celestino/BrickOO.png?branch=master)](http://travis-ci.org/celestino/BrickOO)
BrickOO is a PHP framework which focus on module based applications
 and has been developed in strict mode with focus on coming PHP requirements.


###Requirements
PHP 5.3.2 or any later version required.

Maybe some of the framework libraries require PHP libraries not installed on your machine
 or PHP configuration need to be done to provide full functionality.

Using BrickOO for web applications, rewrite rules are required.

See [Apache mod_rewrite](http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html),
[Nginx NginxHttpRewriteModule](http://wiki.nginx.org/NginxHttpRewriteModule),
[Lighttpd mod_rewrite](http://redmine.lighttpd.net/projects/lighttpd/wiki/Docs:ModRewrite)


###Installation
Just copy the Brickoo folder to a location you have read access to.

An example of how to create an entry point (aka index.php), can be found in the
 [index.example.php](https://github.com/celestino/BrickOO/blob/master/index.example.php) 
 file included.

###Testing
BrickOO is tested with PHPUnit 3.6.10. PHPUnit installation information can be found [here](http://www.phpunit.de/manual/3.6/en/installation.html) 

`phpunit -c /path/to/Brickoo/Tests/phpunit.xml`

`phpunit -c /path/to/Brickoo/Tests/phpunit.xml --coverage-html  /path/to/coverage/directory`

###License
The files in this archive are released under the new BSD license.
You can find a copy of this license in the LICENSE file included.


Have fun !