#!/bin/bash

DB_HOST="db"
DB_PORT="3306"
DB_USER="root"
DB_PASSWORD="password"

MAX_ATTEMPTS=30
SLEEP_INTERVAL=5

echo "Waiting for the database to initialize..."

for ((i=0; i<MAX_ATTEMPTS; i++)); do
    if mysqladmin -h$DB_HOST -P$DB_PORT -u$DB_USER -p$DB_PASSWORD ping &> /dev/null; then
        echo "Database is ready!"
        break
    else
        echo "Attempt $((i+1)): Database is not yet ready. Retrying in $SLEEP_INTERVAL seconds..."
        sleep $SLEEP_INTERVAL
    fi

    if [ $i -eq $((MAX_ATTEMPTS-1)) ]; then
        echo "Max attempts reached. Database initialization failed."
        exit 1
    fi
done

