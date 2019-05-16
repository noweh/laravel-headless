#!/usr/bin/env bash

cd website
rm -Rf vendor
composer install
sh scripts/reset_database.sh
sh scripts/refresh_cache.sh