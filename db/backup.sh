#!/bin/bash

CONTAINER_NAME="online-shop-admin-db-1"
DB_USER="php_docker"
DB_PASSWORD="password"
DB_NAME="php_docker"

docker exec $CONTAINER_NAME mysqldump --no-tablespaces --routines -u $DB_USER -p$DB_PASSWORD $DB_NAME > db/data/backup.sql
