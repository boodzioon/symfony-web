#!/bin/bash

if [ -n "$1" ]
then
./bin/phpunit $1
else
./bin/phpunit
fi
