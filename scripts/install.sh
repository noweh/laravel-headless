#!/usr/bin/env bash

cd website
sh scripts/refresh_cache.sh
rm -Rf vendor
composer install
sh scripts/set_jwt_secret.sh
sh scripts/generate_admin_user.sh
