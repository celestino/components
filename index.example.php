<?php

    /**
     * This is an example of how to set up the base entry point for web based applications.
     * Copy this file to your server root directory and replace the example paths.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    use Brickoo\Core\Autoloader,
        Brickoo\Core\Application,
        Brickoo\Http\Application as MainApplication;

    /**
     * Require the Core\Autoloader.
     */
    require_once ('/path/to/directory/containing_brickoo/Brickoo/Core/Autoloader.php');

    /**
     * Create an instance of the Autoloader class.
     * Register the brickoo path as new namespace to the autoloader.
     * Registers some example company namespaces which do contain vendors modules.
     * The (vendor or own) modules can be located all in one directory or different ones, its up to you.
     * This example namespaces registrations are used in the example modules below.
     * Register the autoloader instance as callback to php.
     */
    $Autoloader = new Autoloader();
    $Autoloader->registerNamespace('Brickoo', '/path/to/directory/containing_brickoo')
               ->registerNamespace('YourCompany/Forum', '/path/to/directory/containing_your_module/')
               ->registerNamespace('OtherCompany/Blog', '/path/to/directory/containg_vendor_modules')
               ->register();

    /**
     * Create an instance of the Application class.
     * The Application class holds anRegistry instance so you can register any global configuration needed.
     * Values will be copy by value and made readonly while objects are not cloned.
     * Registers the Autoloader to the Registry to offer modules the ability to register further registrations.
     * Set the public directory path where all direct accessed file (css, js, images) are located (subdirectories).
     * Register vendors and own modules available, this makes it possible to select which modules
     * should be explicit available and can be accessed through the router.
     * The modules registrations are also used to look for routes, infos and any other information
     * which should be located in the root path of each module.
     *
     * The last step is to run the application which does call all events to setup and run your application.
     * We do use the example http application, but you can build of course your own application
     * containing the logic you are conform with.
     * @see \Brickoo\Http\Application
     */
    $Application = new Application();
    $Application->registerAutoloader($Autoloader)
                ->registerPublicDirectory('/public')
                ->registerModules(array(
                    'YourCompany/Forum'    => '/path/to/modules/YourCompany/Forum',
                    'OtherCompany/Blog'    => '/path/to/modules/vendor/OtherCompany/Blog'
                ))
                ->run(new MainApplication());