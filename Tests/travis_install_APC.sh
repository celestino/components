#!/bin/bash
wget http://pecl.php.net/get/APC
tar -xzf APC-*
sh -c "cd APC-* && phpize && ./configure --enable-apc && make && sudo make install"
echo "extension=apc.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`