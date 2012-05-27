##Error Handling
This component includes the `ErrorHandler` and `ExceptionsHandler` which are used to handle any kind of errors. 
By default the `ErrorHandler` and `ExceptionHandler` do only trigger the event `Brickoo\Log\Event::LOG` to notify 
that an error or exception occured which could/should be logged. 


###Example
This is an example of the `ErrorHandler` usage. 
The registration is done to PHP for the levels `E_ALL | E_STRICT`, 
if you do unregister the previoues error handler will be restored.

    use Brickoo\Error;

    $ErrorHandler = new Error\ErrorHandler();
    $ErrorHandler->register();
    trigger_error("User not found.", E_USER_ERROR);
    $ErrorHandler->unregister();

    

The `ErrorHandler` provides also the posibility to **convert errors into exceptions** for further processing. 
The error levels are equal to the PHP [error levels](http://www.php.net/manual/en/errorfunc.constants.php). The log event will still be triggered.

    $ErrorHandler->setErrorLevel(E_ALL ^ E_NOTICE);


This is an example of the `ExceptionHandler` usage.

    use Brickoo\Error;

    $ExceptionHandler = new Error\ExceptionHandler();
    $ExceptionHandler->register();
    throw new \Exception('User not found.');
    $ExceptionHandler->unregister();

By default the `ExceptionHandler` triggers the log event but does not display the exception information. 
To display the exception information, the `displayExceptions` property can be set.

    $ExceptionHandler->displayExceptions = true;

Setting the `displayExceptions` property does not affect the log event execution.


###Notes
The default behaviour of the `ExceptionHandler` is to not display exceptions.
This should be changed while in development mode to avoid unexpected behaviours.