<?php

$backup = require base_path('vendor/spatie/laravel-backup/config/backup.php');
$sender = env('MAIL_FROM_ADDRESS');

// Archives without configured mail can still boot and create local backups.
if (!is_string($sender) || !filter_var($sender, FILTER_VALIDATE_EMAIL)) {
    $backup['notifications']['notifications'] = [];
    $backup['notifications']['mail']['from']['address'] = 'archive@example.test';
}

return $backup;
