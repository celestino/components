<?php

    // require bootstrap for Brickoo Framework
    require_once (substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), 'Testing')) . 'bootstrap.php');

    date_default_timezone_set('UTC');

    $ErrorHandler = new Brickoo\Library\Error\ErrorHandler();
    $ErrorHandler->setErrorLevel(E_ALL);
    $ErrorHandler->register();

    $ExceptionHandler = new Brickoo\Library\Error\ExceptionHandler();
    $ExceptionHandler->displayExceptions = true;
    $ExceptionHandler->register();

    $FileObject =  new Brickoo\Library\System\FileObject();
    $FileObject->setLocation(__FILE__)->setMode('r');
    $content = '';
    while(! $FileObject->feof())
    {
        $content .= $FileObject->read(500);
    }
    $FileObject->close();

    $Logger = new Brickoo\Library\Log\Logger();
    $Logger->injectLogHandler(new Brickoo\Library\Log\Handler\FileHandler());
    $Logger->getLogHandler()
           ->setDirectory(realpath(dirname(__FILE__)))
           ->setFilePrefix('test_');

    // $Logger->log('huiiiiiiii', LOG_DEBUG);


?>