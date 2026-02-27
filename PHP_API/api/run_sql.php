<?php
try {
  $pdo = new PDO('mysql:host=127.0.0.1;dbname=checkin', 'root', '123456a@');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->query("DESCRIBE attendees");
  $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . "\n";
  }
} catch (PDOException $e) {
  echo "Connection/SQL failed: " . $e->getMessage();
}
