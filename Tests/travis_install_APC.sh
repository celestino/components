#!/bin/bash

wget http://pecl.php.net/get/APC-3.1.10.tgz
tar -xzf APC-3.1.10.tgz
sh -c "cd APC-3.1.10 && phpize && ./configure && sudo make install"
echo "extension=apc.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`