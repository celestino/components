##Cache Manager
The cache manager provides functionality for caching operations.
Using the included or own defined providers the behaviour and storage engines can be changed.
To define your own provider you just need to implement the `Brickoo\Cache\Provider\Interfaces\Provider` interface.


###Example
This example shows how to build and configure a cache manager with the `Brickoo\Cache\Provider\File` provider.

    use Brickoo\Cache,
        Brickoo\Cache\Provider;

    $CacheManager = new Cache\Manager(new Provider\File('/tmp/cache'));

    $CacheManager->set('myIdentifier', 'some content to store', 60);
    $cachedContent = $CacheManager->get('myIdentifier');
    $CacheManager->delete('myIdentifier');

The cache manager has also a special method `getByCallback` to do a callback *before* the provider is called with `get()`. An use case could be if you like to retrieve the data through an event.

    $CacheManager->getByCallback(
        'myIdentifier',
        function($eventID, $CallingObject) {
            return \Brickoo\Event\EventManager::Instance()->ask(
                new \Brickoo\Event\Event($eventID, $CallingObject)
            );
        },
        array('get.my.identifier', $this),
        60
    );


###Notes
The cache manager has a built in local cache management. After an identifier is successful retrieved with `get()`, the content is stored locally and refreshed if `set()` is used. This allows you to retrieve the same identifier many times
without having to call the provider everytime, which does prevent overhead.
However, this makes the heap to grow depending on the data really fast, it can be turned off by a single line:

    $CacheManager->disableLocalCache();


###See also
- [Provider](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Cache/Provider)
- [Memcache Configuration](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Cache/Config)