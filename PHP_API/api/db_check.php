<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=checkin', 'root', '123456a@');
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables:\n";
    print_r($tables);
    foreach ($tables as $table) {
        echo "\nTable: $table\n";
        $stmt = $pdo->query("DESCRIBE `$table`");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
