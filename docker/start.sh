#!/usr/bin/env bash

echo "SERVICE_STATE : $SERVICE_STATE\n"

if [ "$SERVICE_STATE" != "product" ]
then
    sed -i -e 's/sendfile on/sendfile off/g' /etc/nginx/nginx.conf
    echo "\ndisplay_errors = On\ndisplay_startup_errors = On\nerror_reporting = E_ERROR | E_WARNING | E_PARSE" > /etc/php/7.4/fpm/conf.d/display_error.ini
fi

cd /usr/share/nginx/html/www && composer.phar update

service memcached start

service memcached status

/etc/init.d/php7.4-fpm start
nginx
