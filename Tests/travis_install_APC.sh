#!/bin/bash
echo "Downloading APC-3.1.10 ..."
wget http://pecl.php.net/get/APC-3.1.10.tgz
tar -xzf APC-3.1.10.tgz
sh -c "cd APC-3.1.10 && phpize && ./configure --enable-apc && make && sudo make install"
echo "extension=apc.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`
echo "Done!" 