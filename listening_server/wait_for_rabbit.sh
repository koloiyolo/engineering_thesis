#!/bin/bash
# Wait for RabbitMQ to be available

until nc -z -v -w30 rabbit 5672
do
  echo "Waiting for RabbitMQ to start..."
  sleep 1
done

echo "RabbitMQ is up, starting the service"
exec "$@"
