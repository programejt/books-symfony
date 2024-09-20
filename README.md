# books-symfony

How to run project:

1. Create postgres database named `books`
2. Configure postgres database connection (
  example:
  - user = app_book
  - password = qwerty123
)
3. git-clone this repo to your computer
4. Go to directory of cloned repo
5. Run in terminal `composer install`
6. Run in terminal `php bin/console doctrine:migration:execute --up "DoctrineMigrations\Version20240920202752"`
7. Run in terminal `php bin/console doctrine:fixtures:load`
8. Run in terminal `php bin/console asset-map:compile`
9. Run in terminal `symfony server:start` (you need to have installed symfony cli)