<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=attandance_system', 'root', '');
    echo "=== Staff Attendance Management System DB Status ===\n";
    echo 'Total Users: '.$pdo->query('SELECT COUNT(*) FROM `user`')->fetchColumn()."\n";
    echo 'Total Attendance Records: '.$pdo->query('SELECT COUNT(*) FROM `clock_in_out`')->fetchColumn()."\n";
} catch (Exception $e) {
    echo 'DB Error: '.$e->getMessage()."\n";
}
