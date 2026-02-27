<?php
class ApiController extends CController
{
    // Make sure we respond with JSON
    protected function beforeAction($action)
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        return parent::beforeAction($action);
    }

    private function renderJson($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo CJSON::encode($data);
        Yii::app()->end();
    }

    public function actionGetAttendee()
    {
        $qrCode = Yii::app()->request->getParam('qr_code');

        if (empty($qrCode)) {
            $this->renderJson(['success' => false, 'message' => 'Missing qr_code parameter'], 400);
        }

        $connection = Yii::app()->db;
        $sql = "SELECT a.*, (SELECT COUNT(id) FROM checkins c WHERE c.attendee_id = a.id) as checkin_count FROM attendees a WHERE a.qr_code = :qrCode LIMIT 1";
        $command = $connection->createCommand($sql);
        $command->bindValue(':qrCode', $qrCode, PDO::PARAM_STR);
        $attendee = $command->queryRow();

        if ($attendee) {
            $gender = isset($attendee['gender']) ? $attendee['gender'] : null;
            $namePrefix = ($gender == '1') ? 'Mr. ' : (($gender == '0') ? 'Ms. ' : '');
            $fullName = $namePrefix . $attendee['name'];

            $this->renderJson([
                'success' => true,
                'data' => [
                    'name' => $fullName,
                    'position' => $attendee['position'],
                    'company' => $attendee['company'],
                    'photo_url' => $attendee['photo_url'],
                    'checked_in' => $attendee['checkin_count'] > 0
                ]
            ]);
        } else {
            $this->renderJson(['success' => false, 'message' => 'Attendee not found'], 404);
        }
    }

    public function actionCheckIn()
    {
        // Accept both GET and POST for ease of testing, but POST is better.
        $qrCode = Yii::app()->request->getParam('qr_code');

        if (empty($qrCode)) {
            $this->renderJson(['success' => false, 'message' => 'Missing qr_code parameter'], 400);
        }

        $connection = Yii::app()->db;

        // Find existing
        $sql = "SELECT id FROM attendees WHERE qr_code = :qrCode LIMIT 1";
        $command = $connection->createCommand($sql);
        $command->bindValue(':qrCode', $qrCode, PDO::PARAM_STR);
        $attendee = $command->queryRow();

        if (!$attendee) {
            $this->renderJson(['success' => false, 'message' => 'Attendee not found'], 404);
        }

        // Insert into checkins table
        $insertSql = "INSERT INTO checkins (attendee_id, qr_code, check_in_time) VALUES (:id, :qrCode, NOW())";
        $insertCommand = $connection->createCommand($insertSql);
        $insertCommand->bindValue(':id', $attendee['id'], PDO::PARAM_INT);
        $insertCommand->bindValue(':qrCode', $qrCode, PDO::PARAM_STR);
        $inserted = $insertCommand->execute();

        if ($inserted) {
            $this->renderJson(['success' => true, 'message' => 'Check-in successful']);
        } else {
            $this->renderJson(['success' => false, 'message' => 'Database update failed'], 500);
        }
    }

    public function actionAddAgendaTables()
    {
        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();
        try {
            $sqlDays = "
            CREATE TABLE IF NOT EXISTS `agenda_days` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `day_label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                `date_label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                `sort_order` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $connection->createCommand($sqlDays)->execute();

            $sqlEvents = "
            CREATE TABLE IF NOT EXISTS `agenda_events` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `day_id` int(11) NOT NULL,
                `time_label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `description` text COLLATE utf8mb4_unicode_ci,
                `event_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
                `sort_order` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `day_id_idx` (`day_id`),
                CONSTRAINT `fk_agenda_event_day` FOREIGN KEY (`day_id`) REFERENCES `agenda_days` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $connection->createCommand($sqlEvents)->execute();
            $transaction->commit();

            $this->renderJson([
                'success' => true,
                'message' => 'Agenda tables created successfully'
            ]);
        } catch (Exception $e) {
            $transaction->rollback();
            $this->renderJson([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
