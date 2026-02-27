<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$cacheDir = dirname(dirname(__FILE__)) . '/assets/avatars';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=checkin;charset=utf8mb4', 'root', '123456a@');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage() . "\n");
}

$stmt = $db->query("SELECT id, name, photo_url FROM attendees WHERE photo_url IS NOT NULL AND photo_url != ''");
$attendees = $stmt->fetchAll();

echo "Found " . count($attendees) . " attendees with photos. Starting preload...\n";

$success = 0;
$skipped = 0;
$errors = 0;

foreach ($attendees as $a) {
    $url = $a['photo_url'];
    $name = $a['name'];

    $urlHash = md5($url);
    $cachedPath = $cacheDir . '/' . $urlHash . '.jpg';

    if (file_exists($cachedPath)) {
        echo "Skipped (already cached): {$name}\n";
        $skipped++;
        continue;
    }

    echo "Downloading for: {$name} ... ";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (!curl_errno($ch) && $httpCode == 200) {
        file_put_contents($cachedPath, $data);
        echo "OK\n";
        $success++;
    } else {
        echo "Failed (HTTP $httpCode, cURL " . curl_error($ch) . ")\n";
        $errors++;
    }
    curl_close($ch);
}

echo "\nPreload Complete! Downloaded: $success, Skipped: $skipped, Errors: $errors\n";
