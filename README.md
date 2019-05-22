# API LARAVEL

### Requirements

Here are the requirements for the project:

- [PHP 7.1](http://www.php.net)
- [MySql 5.6](https://www.mysql.com)
- [Composer](https://getcomposer.org) (installed as a executable)

## Procedures

#### Install the project:

- Create one vhost with documentRoot positionned on "website/public"
- Duplicate **website/.env.example** to **website/.env** 
- Modify **website/.env** with expected data (mainly DB parameters)
- Run the script:

```
sh scripts/install.sh
```

If for some reason your project stop working do these:

```
cd website
composer install
php artisan migrate
```

## Commands

- To refresh cache, do these:
```
cd website
sh scripts/refresh_cache.sh
```
- To generate fake data:
```
cd website
sh scripts/reset_database.sh
```
