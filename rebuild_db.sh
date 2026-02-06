#!/bin/bash

echo "Rebuilding database ..."
./bin/console doctrine:schema:drop -n -q --force --full-database
rm migrations/*.php 2>/dev/null
./bin/console make:migration
./bin/console doctrine:migrations:migrate -n -q
./bin/console doctrine:fixtures:load -n -q