<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=checkin;charset=utf8mb4', 'root', '123456a@');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('ALTER TABLE attendees ADD COLUMN gender VARCHAR(10) NULL');
    echo "Successfully added gender column to attendees table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Gender column already exists.\n";
    } else {
        die("Database Error: " . $e->getMessage() . "\n");
    }
}
