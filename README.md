## Presentation
This app allows to upload/download files and register/retrieve notes.
This app is writen by using PHP and the framework Slim.
The directory api contains backend code to deal with HTTP POST and GET request.
The directory template contains html page so we can test sending HTTP request to backend.

## Run in Docker containers
dev environment: Ubuntu 16.04.4 LTS, Docker 19.03.4

Execute the script which contains the 3 steps:
 - build docker image
 - run the app and mysql in docker containers
 - initialize the mysql database

You can choose run by docker compose:
```
./setup-compose.sh
```
Or you can choose run by docker swarm:
```
./setup-swarm.sh
```

## Run in Linux computer
dev environment: Ubuntu 16.04.4 LTS, PHP 7.0, Mysql 5.7

You need a Mysql running on your computer. You need first create a database myNotes:
```
mysql -u root -p
create database myNotes
```

This app uses these 4 variables to connect to Mysql, you can set them in .env file:
```
MYSQL_HOST=localhost
MYSQL_DATABASE=myNotes
MYSQL_USER=root
MYSQL_PASSWORD=0000
```

Initialize the database: create tables user and note, insert data into table user:
```
php -f ./dev/seed.php
```

To run this app, you may need install the php-mysql:
```
sudo apt-get install php7.0-mysql
```

Run this app with built-in web server in the port 8081:
```
php -S 0.0.0.0:8081
```

## Test
visit http://localhost:8081, you can login with username hxy and password 0000
