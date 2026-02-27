<?php

class DefaultController extends Controller
{
    public $layout = '/layouts/main';

    public function actionIndex()
    {
        $code = Yii::app()->request->getParam('code');
        $attendee = null;
        $isCheckedIn = false;

        if (!empty($code)) {
            $connection = Yii::app()->db;

            // Query attendees table first (if it exists and has the data)
            try {
                $sql = "SELECT * FROM attendees WHERE qr_code = :code LIMIT 1";
                $cmd = $connection->createCommand($sql);
                $cmd->bindValue(':code', $code, PDO::PARAM_STR);
                $attendee = $cmd->queryRow();

                if ($attendee) {
                    $gender = isset($attendee['gender']) ? $attendee['gender'] : null;
                    $namePrefix = ($gender === '1') ? 'Mr. ' : (($gender === '0') ? 'Ms. ' : '');
                    $attendee['full_name'] = $namePrefix . $attendee['name'];
                }
            } catch (Exception $e) {
                // Ignore if table attendees doesn't exist
                $attendee = null;
            }

            // Fallback to participants table 
            if (!$attendee) {
                try {
                    // participants has full_name, department
                    $sql = "SELECT id, full_name, NULL as photo_url, '' as position, department as company FROM participants WHERE code = :code LIMIT 1";
                    $cmd = $connection->createCommand($sql);
                    $cmd->bindValue(':code', $code, PDO::PARAM_STR);
                    $attendee = $cmd->queryRow();
                } catch (Exception $e) {
                    $attendee = null;
                }
            }

            if ($attendee) {
                try {
                    $sqlCheckin = "SELECT COUNT(id) FROM checkins WHERE attendee_id = :id";
                    $cmdCheckin = $connection->createCommand($sqlCheckin);
                    $cmdCheckin->bindValue(':id', $attendee['id'], PDO::PARAM_INT);
                    $isCheckedIn = $cmdCheckin->queryScalar() > 0;
                } catch (Exception $e) {
                    $isCheckedIn = false; // checkins table might not exist
                }
            }
        }

        $this->render('index', array(
            'attendee' => $attendee,
            'isCheckedIn' => $isCheckedIn
        ));
    }

    public function actionAgenda()
    {
        $days = [];
        $eventsGroupedByDay = [];

        try {
            // Fetch configuration days
            $days = Yii::app()->db->createCommand("SELECT * FROM agenda_days ORDER BY sort_order ASC, id ASC")->queryAll();

            // Fetch events and group them by day
            $allEvents = Yii::app()->db->createCommand("SELECT * FROM agenda_events ORDER BY sort_order ASC, id ASC")->queryAll();
            foreach ($allEvents as $event) {
                if (!isset($eventsGroupedByDay[$event['day_id']])) {
                    $eventsGroupedByDay[$event['day_id']] = [];
                }
                $eventsGroupedByDay[$event['day_id']][] = $event;
            }
        } catch (Exception $e) {
            // In case tables do not exist yet
            error_log("Agenda error: " . $e->getMessage());
        }

        $this->render('agenda', array(
            'days' => $days,
            'eventsGroupedByDay' => $eventsGroupedByDay,
        ));
    }
}
