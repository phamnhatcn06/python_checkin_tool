<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=checkin;charset=utf8mb4', 'root', '123456a@');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Auto-create gender column if it doesn't exist
    try {
        $db->exec('ALTER TABLE attendees ADD COLUMN gender VARCHAR(10) NULL');
        echo "Added gender column.<br>\n";
    } catch (PDOException $e) {
        // Ignore duplicate column errors
    }

} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage() . "<br>\n");
}

$hotelCodes = [
    1002,
    1004,
    1008,
    1011,
    1012,
    1014,
    1016,
    2002,
    2003,
    2004,
    2006,
    2007,
    2008,
    2009,
    2010,
    2011,
    2012,
    2013,
    2014,
    2015,
    2016,
    2018,
    2020,
    2022,
    3001,
    3002,
    3003,
    3004,
    3006,
    3008,
    3016,
    4002,
    4004,
    4006,
    4007,
    4008,
    4010,
    4012,
    4016,
    5002,
    5004,
    5006,
    5008,
    5010,
    5012,
    6002,
    6006,
    6008,
    6010,
    6012,
    6013,
    6014,
    6016,
    6018,
    6020,
    7002,
    7003,
    7004,
    7006,
    7008,
    7010,
    8002,
    8902,
    9012,
    9024,
    9979,
    9983,
    9999
];

$totalSuccess = 0;
$totalUpdate = 0;
$totalError = 0;

// Set max execution time if looping takes too long
set_time_limit(0);

foreach ($hotelCodes as $code) {
    $apiUrl = "https://portal.muongthanh.vn/EventRegistration/Api/RegistrationList?eventId=13&hotelCode={$code}";

    echo "Fetching data from: " . $apiUrl . "<br>\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bypass SSL verification locally
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $jsonStr = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "cURL Error for code {$code}: " . curl_error($ch) . "<br>\n<hr>";
        curl_close($ch);
        continue;
    }
    curl_close($ch);

    if (empty($jsonStr)) {
        echo "Warning: No data received from API for code {$code}.<br>\n<hr>";
        continue;
    }

    $attendees = json_decode($jsonStr, true);
    if (!is_array($attendees)) {
        echo "Warning: Invalid JSON format for code {$code}.<br>\n<hr>";
        continue;
    }

    echo "Found " . count($attendees) . " attendees for code {$code}. Syncing...<br>\n";

    foreach ($attendees as $a) {
        try {
            $id = $a['Id'];
            $name = $a['FullName'];
            // Use ID as QR code
            $qrCode = (string) $id;
            $position = isset($a['PositionName']) ? $a['PositionName'] : '';
            $company = isset($a['HotelName']) ? $a['HotelName'] : '';
            $photoUrl = isset($a['ImageUrl']) ? urldecode($a['ImageUrl']) : '';
            $gender = isset($a['Gender']) ? $a['Gender'] : null;

            // Check if exists by qr_code
            $sqlCheck = "SELECT id FROM attendees WHERE qr_code = :qrCode";
            $cmdCheck = $db->prepare($sqlCheck);
            $cmdCheck->bindValue(':qrCode', $qrCode);
            $cmdCheck->execute();
            $existing = $cmdCheck->fetch();

            if ($existing) {
                // Update
                $sqlUp = "UPDATE attendees 
                          SET name = :name, position = :position, company = :company, photo_url = :photo_url, gender = :gender 
                          WHERE id = :id";
                $cmdUp = $db->prepare($sqlUp);
                $cmdUp->bindValue(':name', $name);
                $cmdUp->bindValue(':position', $position);
                $cmdUp->bindValue(':company', $company);
                $cmdUp->bindValue(':photo_url', $photoUrl);
                $cmdUp->bindValue(':gender', $gender);
                $cmdUp->bindValue(':id', $existing['id']);
                $cmdUp->execute();
                $totalUpdate++;
            } else {
                // Insert
                $sqlIn = "INSERT INTO attendees (name, qr_code, position, company, photo_url, gender) 
                          VALUES (:name, :qrCode, :position, :company, :photo_url, :gender)";
                $cmdIn = $db->prepare($sqlIn);
                $cmdIn->bindValue(':name', $name);
                $cmdIn->bindValue(':qrCode', $qrCode);
                $cmdIn->bindValue(':position', $position);
                $cmdIn->bindValue(':company', $company);
                $cmdIn->bindValue(':photo_url', $photoUrl);
                $cmdIn->bindValue(':gender', $gender);
                $cmdIn->execute();
                $totalSuccess++;
            }
        } catch (Exception $e) {
            $nameToPrint = isset($name) ? $name : 'Unknown';
            echo "Error saving attendee {$nameToPrint} (Code: {$code}): " . $e->getMessage() . "<br>\n";
            $totalError++;
        }
    }

    echo "Done with code {$code}.<br>\n<hr>";

    // Auto flush so the browser doesn't wait till the end to show output
    if (ob_get_level() > 0)
        ob_flush();
    flush();
}

echo "<br>\n<b>--- All Sync Completed! ---</b><br>\n";
echo "Total Inserted: $totalSuccess<br>\n";
echo "Total Updated:  $totalUpdate<br>\n";
echo "Total Errors:   $totalError<br>\n";
