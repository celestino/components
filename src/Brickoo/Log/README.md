##Logging
This component provides functionality to log to specific location. 
The component includes the `Logger` which does handle the logging through a `Handler` dependency. 
Currently, the log component provides the `Filesystem` and `SyslogNG` handler for storing logs to the local filesystem 
and through the network to a common syslog server.
To define your own handler you just need to implement the `Brickoo\Log\Handler\Interfaces\Handler` interface.



###Example
In this example we are using the common `Filesystem` handler to log to the local filesystem.
We do use the `/tmp` directory as our logging directory. Also the `Filesystem` handler has a dependency to an object implementing the `Brickoo\System\Interfaces\FileObject`.


    use Brickoo\Log,
        Brickoo\System;

    $Logger = new Log\Logger(new Log\Handler\Filesystem(new System\FileObject(), '/tmp'));
    $Logger->log('New message to log.', Log\Logger::SEVERITY_INFO);


The log component also provides a `Listener` to register caching listeners for logging with a specific `Logger` instance.
The `Log\Events` provides an event `Brickoo\Log\Events::LOG` to trigger logging listeners. 
Logging through the `Event\Manager` is a better solution as using a singleton, 
since depending on the workspace or development stage you would like to log errors to a specific location.


    use Brickoo\Event;

    $EventManager = Event\Manager::Instance();
    $EventManager->attachAggregatedListeners(new Log\Listener($Logger));

    $EventManager->notifyOnce(new Event\Event(
        Log\Events::LOG, null, array('messages' => 'myIdentifier as', 'severity' => Log\Logger::SEVERITY_INFO)
    ));


###Notes
The `Logger` uses the `Logger::SEVERITY_INFO` as default severity. 
Using the logger to log error messages, the severity can be changed to `Logger::SEVERITY_ERROR` as default, 
this way  you do not to have to pass it everytime.


###See also
- [Log Handler](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Log/Handler)