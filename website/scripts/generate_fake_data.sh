#!/usr/bin/env bash

php -d memory_limit=-1 artisan migrate:fresh
php -d memory_limit=-1 artisan db:seed
sh scripts/refresh_cache.sh