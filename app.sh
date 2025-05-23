#!/bin/bash

git pull 

echo "You want to run migrations (by default no)"
read migration

migration=${migration:-no}

if [ "$migration" == "yes" ]; then

php bin/console make:migration 

php bin/console d:m:m

fi

echo "You want to load some permissions (by default no)"
read permissions

permissions=${permissions:-no}

if [ "$permissions" == "yes" ]; then

php bin/console load:permissions --admin --update

fi

echo "You want to run asset:install (by default no)"
read asset

asset=${asset:-no}

if [ "$asset" == "yes" ]; then

php bin/console asset:install

fi

php bin/console cache:clear --env=prod