#!/bin/bash

echo "Starting Standalone WebSocket server..."
cd vshop-websocket
npm start &
WEBSOCKET_PID=$!

echo "Starting Laravel server..."
cd ../vshop-laravel
php artisan serve &
LARAVEL_PID=$!

trap "kill $WEBSOCKET_PID $LARAVEL_PID; exit" INT TERM EXIT

wait
