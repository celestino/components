##Event Handling
This component provides functionality for event handling. 
The component includes the `Event\Manager`which does manage any *Listener* registration and notifications, 
also a default `Event` class which can be used to trigger an event.


###Example
This is an example of the `Event\Manager` usage. 
The `Event\Manager` uses the `ListenerQueue` which is used to keep an listener queue with priority levels.
Each registered *Listener* is registered under an unique identifier to provide the posibility to be removed.

    use Brickoo\Event;

    $EventManager = new Event\Manager();
    $listenerUID = $EventManager->attachListener(
        'my.event.id', function($Event){echo($Event->getName();}
    );

    $EventManager->notify(new Event\Event('my.event.id));
    $EventManager->detachListener($listenerUID);


The `Event\Manager` also provides a static method `Instance()` to retrieve a singleton from any place. 
This should not be used, but sometimes needed to trigger an `Event` without having the `Event\Manager` as dependency.

    Event\Manager::Instance()->notify(new Event\Event('my.event.id));


If conditions have to match *before* the *Listener* is called, the must have parameters and a callback can be specified. 
Here explained shortly the arguments of a more complex listener registration.

    Event\Manager::Instance()->attachListener(
        // the event unique identifier
        'my.event.id', 
        // the Listener callback, any PHP callable
        function($Event){echo($Event->getName();},
        // optional: the Listener priority, any integer (low)0<----->100(high)
        100,
        // optional, condition: the Event instance has to have this parameters set
        array('id', 'name'),
        // optional, condition: the callback hast to return boolean true, any PHP callable
        function($Event){return ($Event->Sender() instanceof \My\Caller);} 
    );

    // FAIL: the Event does not have the required Sender dependency
    Event\Manager::Instance()->notify(new Event\Event('my.event.id'));

    // FAIL: the Event does not have the required parameters
    Event\Manager::Instance()->notify(new Event\Event('my.event.id', new \My\Caller());

    // FAIL: The Event does not have ALL the required parameters
    Event\Manager::Instance()->notify(new Event\Event(
        'my.event.id', new \My\Caller(), array('id' => 'someID')
    );

    // SUCCESS: all conditions match
    Event\Manager::Instance()->notify(new Event\Event(
        'my.event.id', new \My\Caller(), array('id' => 'someID', 'name' => 'BrickOO')
    );


###Notes
The `Event\Manager` *knows* three kind of notifications. 
The `notify()` notification is used to call all *Listeners* of an event, a response is not expected.
The `notifyOnce()` notification, only the *Listener* with the highest priority should be called, a response is not expected.
The `ask()` notification is used if a response is expected. 
The *Listeners* are called from high to low priority, the first which does not return `null` stops the process and his response is returned. 
This can be used for example by caching and filter notifications.