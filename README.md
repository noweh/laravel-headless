# FUTURE PLAYER - Backstage

### Requirements

Here are the requirements for the project:

- [PHP 7.3](http://www.php.net)
- [MySql 5.7](https://www.mysql.com)
- [Composer](https://getcomposer.org) (installed as an executable)

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
- To reset database:
```
cd website
sh scripts/reset_database.sh
```
- To generate fake data:
```
cd website
sh scripts/generate_fake_data.sh
```
- To update existing database:
```
cd website
php artisan migrate
```
- To launch all API Commands with configured schedule, add in crontab these:
```
* * * * * cd /path-to-your-project && php artisan schedule:run
```

## Included Schedule Commands



## Fake data

- You can log to the interface with fake data as follows:<br />
    Login: Fake user email<br />
    Pwd: local-part/account name of the email address (before the @)

    Example:<br />
    Login: test-6@mazarinedigital.com<br />
    Pwd: test-6

## Swagger

- You can access to the Swagger interface from url : {APP_URL}/api. {APP_URL} is setted in your .env file
- To regenerate the documentation, you can run the following command:
```
cd website
php artisan l5-swagger:generate
```
- Alternatively, you can set L5_SWAGGER_GENERATE_ALWAYS to true in your .env file so that your documentation will automatically be generated.

You can **retrieve an User token** from the route /users/auth/login in Authentication Tag on Swagger interface.<br />
Copy this token and past it in "Authorize" button (see above right of the Swagger interface) to activate the JWT mode