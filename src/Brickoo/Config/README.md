##Configuration
The configuration is a object to load and store configuration data from and/or to a resource.
Using the `Brickoo\Config\Interfaces\Provider` interface an own implementation with a different behaviour, 
like loading from a database, could be done.


###Example
This is an example using the `Standard` provider.
For this example we use a file called *config.php* located in the */tmp* directory.

*config.php*

    <?php
    return array(
        'name' => 'BrickOO',
        'language' => array('en' => 'en')
    );

*usage.php*

    use Brickoo\Config,
        Brickoo\Config\Provider;

    $Configuration = new Config\Configuration(
        new Provider\Standard('/tmp/config.php')
    );

    $Configuration->load();
    $name = $Configuration->get('name');
    $Configuration->set('name', 'name changed')
                  ->set('version', '3.x');
    $Configuration->save();

A special ability of the `Configuration ` is to convert first level keys/sections to PHP constants.
The first level key (array) / section (ini) will be used as prefix following by a underscore and the associated keys.
This mapping can only done for the first level of the configuration containing *scalar* values.

    $Configuration->convertToConstants('language');
    $constantValue = LANGUAGE_EN;


###Notes
Loading the configuration will remove any previous assigned entries. 
It is important to keep im mind to **load the configuration first** and make any modifications afterwards. 
Saving the configuration will overwrite the current file.


###See also
- [Configuration Provider](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Config/Provider)