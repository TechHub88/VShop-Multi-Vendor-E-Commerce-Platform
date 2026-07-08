<?php
$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($logPath)) {
    echo "Log file does not exist at: " . htmlspecialchars($logPath);
    exit;
}
$logs = file_get_contents($logPath);
echo '<pre>' . htmlspecialchars(substr($logs, -100000)) . '</pre>';
