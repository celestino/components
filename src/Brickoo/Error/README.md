##Error Handler
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
    $ErrorHandler->handleError(E_ERROR , 'User not found.', 'user.php', 123);
    $ErrorHandler->unregister();

    

The `ErrorHandler` provides also the posibility to **convert errors into exceptions** for further processing. 
The error level is equal to the PHP configuration levels. The log event will still be triggered.

    $ErrorHandler->setErrorLevel(E_ALL ^ E_NOTICE);


This is an example of the `ExceptionHandler` usage.

    use Brickoo\Error;

    $ExceptionHandler = new Error\ExceptionHandler();
    $ExceptionHandler->register();
    $ExceptionHandler->handleException(new \Exception('User not found.'));
    $ExceptionHandler->unregister();

By default the `ExceptionHandler` triggers the log event but does not display the exception information. 
To display the exception information, the `displayExceptions` property can be set.

    $ExceptionHandler->displayExceptions = true;

Setting the `displayExceptions` property does not affect the log event execution.


###Notes
The default setting of the `ExceptionHandler` is to not display exceptions.
This should be changed while in development mode to avoid unexpected behaviours.