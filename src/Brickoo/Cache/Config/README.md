##Memcache Config
The memcache configuration is a object to configure a `Memcache` object with pre defined configurations. An use case could be a configuration which is loaded from a resource.


###Example
    use Brickoo\Cache\Config;

    $MemcacheConfig = new Config\Memcache();
    $MemcacheConfig->addServer('127.0.0.1')
                   ->addServer('192.168.0.1')
                   ->addServer('192.168.0.2', 11212);

    $Memcache = $MemcacheConfig->configure(new \Memcache());

Also, you can pass the configuration to the constructor as an array containing associated arrays with the keys `host` and `port`.

    use Brickoo\Cache\Config;

    $MemcacheConfig = new Config\Memcache(array(
        array('host' => '127.0.0.1'),
        array('host' => '192.168.0.1'),
        array('host' => '192.168.0.2', 'port' => 11212)
    ));

    $Memcache = $MemcacheConfig->configure(new \Memcache());


###See also
- [Cache Manager](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Cache)
- [Provider](https://github.com/brickoo/brickoo/tree/master/src/Brickoo/Cache/Provider)
