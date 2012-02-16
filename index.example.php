<?php

    /**
     * This is a sample of how to set up the entry point for web based applications.
     * Copy this file to your server root directory and replace the example paths.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    use Brickoo\Library\Core\Autoloader,
        Brickoo\Library\Core\Registry,
        Brickoo\Library\Http\Application,
        Brickoo\Library\Http\Request,
        Brickoo\Library\Error\ErrorHandler,
        Brickoo\Library\Error\ExceptionHandler;

    /**
    * Define the BrickOO Framework root directory which needs just read access.
    * This has to be the realpath to the root directory containing the Library.
    */
    if (! defined ('BRICKOO_DIR')) {
        define ('BRICKOO_DIR', '/path/to/Brickoo/directory');
    }

    /**
     * Require the Core\Autoloader.
     */
    require_once (BRICKOO_DIR . '/Library/Core/Autoloader.php');

    /**
     * Create an instance of the Autoloader class.
     * Register the brickoo path as new namespace to the autoloader.
     * Registers some example company namespaces which do contain vendors/own modules.
     * This example namespaces registrations are used for the examples below.
     * Register the autoloader instance as callback to php.
     */
    $Autoloader = new Autoloader();
    $Autoloader->registerNamespace('Brickoo', BRICKOO_DIR)
               ->registerNamespace('MyCompany', '/path/to/modules/MyCompany')
               ->registerNamespace('ExampleCompany', '/path/to/modules/vendor/ExampleCompany')
               ->registerNamespace('OtherCompany', '/path/to/modules/vendor/OtherCompany')
               ->register();

    /**
     * Create an instance of the Application class.
     * The Application class is used as a global registry.
     * You can register any configuration or global objects.
     * Registers the Registry used as global container.
     * Registers the Request which should be processed, this can be a Http\Request instance
     * or any other object implementing the Core\Interfaces\RequestInterface.
     * Values will be copy by value and made readonly while objects are not cloned.
     * Registers the Autoloader to the Registry which will be used to load the modules.
     * Set the cache directory to use (needs read and write access).
     * If you do not set the cache directory the routes will not be cached to increase performance,
     * this can otherwise be useful during developemnt.
     * Set the logs directory to use (needs read and write access).
     * Register vendors or own modules available, this makes it possible to offer all or just
     * as stable marked vendors/own modules.
     * The modules registrations are also used to look for routes, infos and any other information
     * which should be located in the root path of each module.
     */

    $Application = new Application(new Registry(), new Request());
    $Application->registerAutoloader($Autoloader)
                ->registerCacheDirectory(__DIR__ .'/cache')
                ->registerLogDirectory(__DIR__ . '/logs')
                ->registerModules(array(
                    'CMS'      => '/path/to/modules/MyCompany/ContentManagementSystem',
                    'Forum'    => '/path/to/modules/vendor/ExampleCompany/Forum',
                    'Blog'     => '/path/to/modules/vendor/OtherCompany/Blog'
                ));

    /**
     * [OPTIONAL]
     * Create a CacheManager instance for cache purposes.
     * The CacheManager needs a Provider to handle cache operations, for simplicity
     * we do use the FileProvider to cache the content.
     * Registers the same CacheManager as reponse and as data cache.
     * Recommended to enable output caching and/or other heavy performace cost data.
     */
    $CacheProvider = new \Brickoo\Library\Cache\Provider\FileProvider();
    $CacheProvider->setDirectory($Application->cacheDirectory);
    $CacheManager = new \Brickoo\Library\Cache\CacheManager($CacheProvider);
    $Application->registerCacheManager($CacheManager)
                ->registerResponseCacheManager($CacheManager);
    /**
     * [OPTIONAL]
     * Create a Logger instance and add it to the Registry.
     * Create a LogHandler which uses the cache directory as target directory.
     * Recommended to enable vendors modules to have a common log possibility.
     */
    $LogHandler = new \Brickoo\Library\Log\Handler\FileHandler(new \Brickoo\Library\System\FileObject());
    $LogHandler->setDirectory($Application->logDirectory);
    $Logger = new Brickoo\Library\Log\Logger($LogHandler);
    $Application->registerLogger($Logger);

    /**
     * [OPTIONAL]
     * Create an ErrorHandler and register to handle errors.
     * Sets the error level E_ALL to convert this errors to exceptions.
     * Uses the Logger instance previous created to log errors to the log directory.
     * Recommended in production mode.
     */
    $ErrorHandler = new ErrorHandler();
    $ErrorHandler->Logger($Logger)->setErrorLevel(E_ALL)->register();
    $Application->registerErrorHandler($ErrorHandler);

    /**
     * [OPTIONAL]
     * Create an ExceptionHandler and register to catch exceptions throwed.
     * Uses the Logger instance previous created to log exceptions to the log directory.
     * Recommended in production mode.
     */
    $ExceptionHandler = new ExceptionHandler();
    $ExceptionHandler->Logger($Logger)->register()->displayExceptions = true;
    $Application->registerExceptionHandler($ExceptionHandler);

    /**
     * The next step is to run the application which does execute any cache operations or
     * calls to the request controller from the available routes.
     */
    $Application->run();

    /**
     * The last step is to send the cached or fresh generated response headers and content.
     * For debugging reasons the send method is not called automaticly, this offers you during
     * development to take a look at the response output which would be sent.
     * <code>
     *     echo '<pre>' . htmlentities($Application->Response->toString()) . '</pre>';
     * </code>
     */
    $Application->send();
