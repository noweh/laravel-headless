#!/usr/bin/env bash

php -d memory_limit=-1 artisan cache:clear
php -d memory_limit=-1 artisan config:clear
php -d memory_limit=-1 artisan config:cache
php -d memory_limit=-1 artisan route:clear
php -d memory_limit=-1 artisan route:cache

APP_URL=$(grep APP_URL .env | cut -d '=' -f2)
APP_ENV=$(grep APP_ENV .env | cut -d '=' -f2)

case $APP_URL in
"https://mazarine-player-backstage-pp.mzrn.net")
  php /data/mazarine-player-backstage-pp.mzrn.net/cachetool.phar opcache:reset --fcgi=127.0.0.1:9073
  printf "\033[1;32mOPCACHE cleaned.\033[0m\n"
  ;;
*)
  if [ $APP_ENV != "local" ]
  then
    printf "\033[1;31m/!\ Remember to clean OPCACHE for the changes to take effect.\033[0m\n"
  fi
  ;;
esac