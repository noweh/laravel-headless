# Laravel Headless

## What about?

This allows a fast and simple implementation of a REST API based on the [Laravel Framework](https://packagist.org/packages/laravel/laravel), [Repository Pattern](https://designpatternsphp.readthedocs.io/en/latest/More/Repository/README.html), [Eloquent Resources](https://laravel.com/docs/8.x/eloquent-resources), [Translatability](https://packagist.org/packages/astrotomic/laravel-translatable), and [Swagger](https://swagger.io/) for the documentation.

The objective is to have the least code to write for adding new kind of content: the most important process is carried out in the Abstract files.

In a development environment, the display of the Debugbar ([barryvdh/laravel-debugbar](https://packagist.org/packages/barryvdh/laravel-debugbar)) has been modified in a Middleware to integrate with the json return.

## Cache

Several levels of caches are used to optimize the display: a first one with the bundle [genealabs/laravel-model-caching](https://packagist.org/packages/genealabs/laravel-model-caching) for Objects, a second one in the process of the Resources display, and a third one with a configurable Cache-Control in Header.

By default, GET routes are behind cache. To remove that, you have to add the following GET parameter:
`removeCache=true`

## Requirements

Here are the requirements for the project:

- [PHP 7.3](http://www.php.net)
- [MySql 5.7](https://www.mysql.com)
- [Composer 2](https://getcomposer.org) (installed as an executable)

## Procedures

#### To install the project:

- Create one vhost with documentRoot positionned on "website/public"
- Duplicate **website/.env.example** to **website/.env** 
- Modify **website/.env** with expected data (mainly DB parameters)
- Run the script:

```
sh scripts/install.sh
```

If for some reasons your project stop working, do the following:

```
cd website
composer install
php artisan migrate
```

## Commands

- To refresh cache, do the following:
```
cd website
sh scripts/refresh_cache.sh
```
- To reset database:
```
cd website
sh scripts/reset_database.sh
```
- To reset database with default user:
```
cd website
sh scripts/generate_data.sh
```
- To update existing database:
```
cd website
php artisan migrate
```
- To launch all API Commands with configured schedule, add in crontab the following:
```
* * * * * cd /path-to-your-project && php artisan schedule:run
```

## Swagger

- You can access to the Swagger interface from url : {APP_URL}/api. {APP_URL} is setted in your .env file
- To regenerate the documentation, you can run the following command:
```
cd website
php artisan l5-swagger:generate
```
- Alternatively, you can set `L5_SWAGGER_GENERATE_ALWAYS` to `true` in your .env file. It will allow your documentation to be automatically generated.

You can **retrieve an User Token** from the route /users/auth/login in Authentication Tag on Swagger interface.<br />
Copy this token and past it in "Authorize" button (see above right of the Swagger interface) to activate the JWT mode