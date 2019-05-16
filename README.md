# ONP ACADEMY - Backstage

### Requirements

Here are the requirements for the project:

- [PHP 7.1](http://www.php.net)
- [MySql 5.6](https://www.mysql.com)
- [Composer](https://getcomposer.org) (installed as a executable)

## Procedures

####Install the project:

- Create one vhost with documentRoot positionned on "website/public"
- Duplicate **website/.env.example** to **website/.env** 
- Modify **website/.env** with good datas (mainly DB parameters)
- In website, run the script :

```
sh scripts/reset_database.sh
```

If for some reason your project stop working do these:

```
composer install
php artisan migrate
```