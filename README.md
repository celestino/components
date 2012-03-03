
##BrickOO
BrickOO is a PHP framework which focus on module based applications and has been developed in strict mode and with focus on coming PHP requirements.


###Requirements
PHP 5.3.x / 5.4 or any later version required.

Maybe some of the framework libraries require PHP libraries not installed on your machine
or PHP configuration need to be done to enable the full functionality.

Using BrickOO for web applications, rewrite rules are required.

See [Apache mod_rewrite](http://httpd.apache.org/docs/2.2/mod/mod_rewrite.html),
[Nginx NginxHttpRewriteModule](http://wiki.nginx.org/NginxHttpRewriteModule),
[Lighttpd mod_rewrite](http://redmine.lighttpd.net/projects/lighttpd/wiki/Docs:ModRewrite)


###Installation
Just copy the Brickoo folder to a location you have access to.

For an example of how to create and configure an entry point (aka index.php), take a look at the example included
in this archive or available on github [index.example.php](https://github.com/celestino/BrickOO/blob/master/index.example.php)

###Testing
BrickOO is tested with PHPUnit 3.6.10. PHPUnit installation information can be found [here](http://www.phpunit.de/manual/3.6/en/installation.html) 

`phpunit -c /path/to/Brickoo/Tests/phpunit.xml /path/to/Brickoo/Tests`

###License
The files in this archive are released under the new BSD license.
You can find a copy of this license in the LICENSE file included.


Have fun !