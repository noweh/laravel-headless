#!/usr/bin/env bash

php  -d memory_limit=-1 artisan db:seed --class=AdminUserTableSeeder