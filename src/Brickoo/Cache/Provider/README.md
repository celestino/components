##Cache Provider
The cache provider implement the logic of the cache operations.
Currently four providers are included `Apc`, `File`, `Memcache`  and `Null`. The `Null` provider is just a implementation to do nothing like `dev/null`. The providers implement the `Brickoo\Cache\Provider\Interfaces\Provider` interface which can be used to build an own implementation.


###Example
For the example we use the most complex provider initialization.

    use Brickoo\Cache,
        Brickoo\Cache\Provider,
        Brickoo\Cache\Config;

    $MemcacheConfig = new Config\Memcache();
    $MemcacheConfig->addServer('127.0.0.1');

    $MemcacheProvider = new Provider\Memcache(
        $MemcacheConfig->configure(new \Memcache())
    );

    $CacheManager = new Cache\Manager($MemcacheProvider);

Or the shorter initialization using less stack space.

    use Brickoo\Cache,
        Brickoo\Cache\Provider,
        Brickoo\Cache\Config;

    $Config = new Config\Memcache(array(array('host' => '127.0.0.1')));

    $CacheManager = new Cache\Manager(new Provider\Memcache(
        $Config->configure(new \Memcache())
    ));


###See also
- [Cache Manager](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Cache)
- [Memcache Configuration](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Cache/Config)