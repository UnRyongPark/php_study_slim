FROM ubuntu:18.04
MAINTAINER Brian Park <brian.park@wrong.tips>

ARG SERVICE_STATE=dev
ENV SERVICE_STATE ${SERVICE_STATE}

RUN sed -i 's%archive.ubuntu.com%kr.archive.ubuntu.com%' /etc/apt/sources.list;

ENV TZ=Asia/Seoul
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update -y

RUN apt-get install -y software-properties-common language-pack-en-base
RUN LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php
RUN LC_ALL=en_US.UTF-8 add-apt-repository ppa:nginx/stable
RUN apt-get update -y --fix-missing

RUN apt-get install -y curl wget pkg-config zip unzip
RUN apt-get install -y nginx php7.4-dev php7.4-fpm php7.4-cli php7.4-mysql memcached php-xdebug php7.4-bcmath dos2unix git php-mbstring php7.4-zip

RUN pecl install mongodb;

RUN php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php \
	&& php composer-setup.php --install-dir=/usr/local/bin \
	&& php -r "unlink('composer-setup.php');";

RUN echo "\ndaemon off;" >> /etc/nginx/nginx.conf
RUN sed -i 's%# multi_accept on;%multi_accept on;%' /etc/nginx/nginx.conf
RUN sed -i 's%worker_connections 768;%worker_connections 4096;%' /etc/nginx/nginx.conf
RUN sed -i 's%keepalive_timeout 65;%keepalive_timeout 3600;%' /etc/nginx/nginx.conf

#php-fpm
RUN echo "\nenv[SERVICE_STATE] = $SERVICE_STATE" >> /etc/php/7.4/fpm/pool.d/www.conf
RUN echo "\nlisten = 127.0.0.1:9000" >> /etc/php/7.4/fpm/pool.d/www.conf
RUN echo "\nslowlog = /var/log/php-slow.log" >> /etc/php/7.4/fpm/pool.d/www.conf
RUN echo "\nrequest_slowlog_timeout = 1s" >> /etc/php/7.4/fpm/pool.d/www.conf
RUN sed -i 's%pm.max_children = 5%pm.max_children = 50%' /etc/php/7.4/fpm/pool.d/www.conf
RUN sed -i 's%pm.start_servers = 2%pm.start_servers = 25%' /etc/php/7.4/fpm/pool.d/www.conf
RUN sed -i 's%pm.min_spare_servers = 1%pm.min_spare_servers = 25%' /etc/php/7.4/fpm/pool.d/www.conf
RUN sed -i 's%pm.max_spare_servers = 3%pm.max_spare_servers = 25%' /etc/php/7.4/fpm/pool.d/www.conf

RUN echo "\ndisplay_errors = On\ndisplay_startup_errors = On\nerror_reporting = E_ERROR | E_WARNING | E_PARSE" >> /etc/php/7.4/fpm/php.ini
RUN echo "\ncgi.fix_pathinfo = 0" >> /etc/php/7.4/fpm/php.ini
RUN echo "\nmax_input_vars = 1000000" >> /etc/php/7.4/fpm/php.ini
RUN echo "\nopcache.enable = 0" >> /etc/php/7.4/fpm/php.ini
RUN echo "\nextension=mongodb.so" >> /etc/php/7.4/fpm/php.ini
RUN echo "\ndate.timezone = Asia/Seoul" >> /etc/php/7.4/fpm/conf.d/timezone.ini
RUN sed -i 's%default_socket_timeout = 60%default_socket_timeout = 120%' /etc/php/7.4/fpm/php.ini
RUN sed -i 's%max_input_time = -1%max_input_time = -1%' /etc/php/7.4/fpm/php.ini

RUN echo "\ndate.timezone = Asia/Seoul" >> /etc/php/7.4/cli/conf.d/timezone.ini

ADD conf/nginx.conf /etc/nginx/sites-available/default

ADD start.sh /root/start.sh
RUN dos2unix /root/start.sh /root/start.sh;

WORKDIR /root

EXPOSE 80
EXPOSE 443
#for xdebug, 9000은 fpm에서 사용중
EXPOSE 9001

VOLUME ["/usr/share/nginx/html/www"]

CMD ["sh", "/root/start.sh"]