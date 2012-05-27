##Configuration Provider
The configuration provider implements methods to handle loading and save operations.
Currently two configuration providers are included `Standard` (since `Array` is a reserved word) and `Ini`.
Unsing the `Standard` provider, the file must return an `array`.


###Example
This is an example using the `Ini` provider. For this example we use a file called *config.ini* located in the */tmp* directory.

*config.ini*

    name = BrickOO
    [language]
    en = EN

*usage.php*

    use Brickoo\Config\Provider;

    $IniProvider = new Provider\Ini('/tmp/config.ini');

    $configuration = $IniProvider->load();
    $configuration['version'] = '3.x';
    $IniProvider->save($configuration);

Each provider implements the `toString(array $configuration)` method to convert an `array` configuration to a `string` representation which can be loaded from a file by the provider.

    $myConfig = array(
        'default' => array(
            'name' => 'Brickoo Framework',
            'version' => 3.1
            'stage' => 'dev'
            'enabled' => true
        ),
        'language' => array(
            'en' => 'EN',
            'de' => 'DE',
            array('countries' => array('USA', 'Germany'))
        )
    );
    $iniContent = $Configuration->toString($myConfig);

This configuration will return this `string`:
    
    [default]
    name = "Brickoo Framework"
    version = "3.1"
    stage = dev
    enabled = 1
    [language]
    en = EN
    de = DE
    countries[] = USA
    countries[] = Germany


###Notes
Using the `Ini` provider, `boolean` values (false, true) will be converted to `integer` (0, 1).


###See also
- [Configuration](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Config)