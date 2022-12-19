# blog-symfony

## About Project

Blog web page in Symfony framework.

## Instalation

Open project and run command:
- **composer install**

## Database

Change login to database in file .env:

DATABASE_URL="mysql://db_user:db_password@127.0.0.1:port/db_name?serverVersion=server_version

(Optional) Create database - if there is no database with name 'db_name'
- **php bin/console doctrine:database:create**

(Optional) Load data

- **php bin/console doctrine:fixtures:load**

## Run

Open project and run command:
- **symfony server:start**

Open in web browser: 127.0.0.1:port/blog

Now you can create new blog posts and read them.

## License

The project Cities Search is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
