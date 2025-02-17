# books-symfony

## Description
Website that contains books with detailed informations and authors of them.

## On the website you can
- browse books and authors with details
- search books
- create account and sign in to it
- manage your account - verify email, change name, change password, change photo, change email and delete account
- if you have Moderator or Admin role, you can create, edit and delete books and authors
- if you have Admin role, you can also change role of other users

After running project, it has one account that has Admin role.


## Screenshots
![alt text](https://github.com/programejt/books-symfony/blob/main/screenshots/screenshot-1.png)
![alt text](https://github.com/programejt/books-symfony/blob/main/screenshots/screenshot-2.png)
![alt text](https://github.com/programejt/books-symfony/blob/main/screenshots/screenshot-3.png)
![alt text](https://github.com/programejt/books-symfony/blob/main/screenshots/screenshot-4.png)
![alt text](https://github.com/programejt/books-symfony/blob/main/screenshots/screenshot-5.png)
![alt text](https://github.com/programejt/books-symfony/blob/main/screenshots/screenshot-6.png)
![alt text](https://github.com/programejt/books-symfony/blob/main/screenshots/screenshot-7.png)
![alt text](https://github.com/programejt/books-symfony/blob/main/screenshots/screenshot-8.png)


## How to run project

1. Clone this repo to your computer (`git clone https://github.com/programejt/books-symfony.git`)
2. Go to directory of cloned repo
3. Setup your database environment (the best for this project will be `postgres`)
4. Create copy of `.env` file (in project's root directory) and name it `.env.local`, then configure your database connection in it:
  - pattern: `DATABASE_URL="<database engine>://<user>:<password>@<host>:<port>/<database name>?serverVersion=16&charset=utf8"`
  - example: `DATABASE_URL="postgresql://app_books:qwerty123@127.0.0.1:5432/books?serverVersion=16&charset=utf8"`
5. Run in terminal `composer install`
6. Create database for your application with one of following steps:
  - if database's user that your application is connecting to has permission to create databases, run in terminal `php bin/console doctrine:database:create`
  - create manually in your database environment with name (`<database name>`) you configured in previous step
7. Run in terminal `php bin/console doctrine:migrations:migrate`
8. Run in terminal `php bin/console doctrine:fixtures:load`
9. Run in terminal `php bin/console asset-map:compile`
10. Run in terminal `symfony server:start` (you need to have installed symfony cli)


## PHPUnit tests
1. Create new file (in project's root directory) and name it `.env.test.local`, then configure your database connection in it:
  - pattern: `DATABASE_URL="<database engine>://<user>:<password>@<host>:<port>/<database name>?serverVersion=16&charset=utf8"`
  - example: `DATABASE_URL="postgresql://app_books:qwerty123@127.0.0.1:5432/books?serverVersion=16&charset=utf8"`
2. Create database for your tests with one of following steps:
  - if database's user that your application is connecting to has permission to create databases, run in terminal `php bin/console  --env=test doctrine:database:create`
  - create manually in your database environment
3. Run in terminal `php bin/console --env=test doctrine:migrations:migrate`
4. To run PHPUnit tests, run in terminal `php bin/phpunit`
