<?php
$mysqli = new mysqli("127.0.0.1", "root", "123456a@", "checkin");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

if ($result = $mysqli->query("SHOW TABLES")) {
    while ($row = $result->fetch_row()) {
        $table = $row[0];
        echo "Table: $table\n";
        $columns = $mysqli->query("DESCRIBE `$table`");
        while ($col = $columns->fetch_assoc()) {
            echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    }
    $result->free();
}

$mysqli->close();
