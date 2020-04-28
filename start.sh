#!/bin/sh

docker kill study_wrong_tips wrongtips_mysql
docker rm study_wrong_tips wrongtips_mysql

#database
docker run --name wrongtips_mysql -p 3306:3306 -v $(pwd)/database:/var/lib/mysql -e MYSQL_ROOT_PASSWORD=wrongtips -d mysql:latest

#webserver
docker build --build-arg SERVICE_STATE=dev -t study_wrong_tips ./docker
ip addr
docker run --rm --name study_wrong_tips -p 8080:80 -v $(pwd)/www:/usr/share/nginx/html/www --link wrongtips_mysql:wrongtips_mysql study_wrong_tips