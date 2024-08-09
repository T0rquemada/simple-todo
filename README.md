# ToDo

## Prerequisites
- You have installed and running [MySQL](https://www.mysql.com/)
- You have installed [Composer](https://getcomposer.org/)
- You have installed [npm](https://www.npmjs.com/)

## To start

### Install dependencies
#### Automatically
Run ```chmod +x ./install.sh && ./install.sh```
#### Manually
##### for server (from root of project)
1. ```cd server```
2. ```composer install```
##### for client (from root of project)
1. ```cd client```
2. ```npm install```

### Prepare DB
1. Enter your MySQL info in .env
2. Run in terminal: ```mysql -u root -p```
3. Enter password from your MySQL
4. Create database ```CREATE DATABASE todo;```  (Instead 'todo' you can enter any title, which you want)
5. Switch to it ```USE todo;```
6. Then: ```SOURCE database/tables.sql;``` (This will create tables)

### Generate JWT
1. Run in terminal ```openssl rand -base64 64```
2. Put result in .env (JWT=)

## Run
You need two terminal tabs, to lauch web page, first for client, second for php server
### Client
1. ```cd client```
2. ```npm run dev```
### Server
1. ```cd client```
2. ```php -S localhost:5174 -t .```

## Used packages
- For server
    1. [firebase/php-jwt](https://github.com/firebase/php-jwt)
    2. [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv)