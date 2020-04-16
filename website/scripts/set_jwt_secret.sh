#!/usr/bin/env bash

php -d memory_limit=-1 artisan jwt:secret
