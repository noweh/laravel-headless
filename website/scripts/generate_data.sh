#!/usr/bin/env bash

php -d memory_limit=-1 artisan migrate:fresh --force
php -d memory_limit=-1 artisan db:seed --force
sh scripts/refresh_cache.sh