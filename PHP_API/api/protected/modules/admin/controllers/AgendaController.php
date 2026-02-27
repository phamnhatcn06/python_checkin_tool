<?php
class AgendaController extends Controller
{
    public $layout = '/layouts/main';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('index', 'initDb', 'createDay', 'updateDay', 'deleteDay', 'viewDay', 'createEvent', 'updateEvent', 'deleteEvent'),
                'users' => array('@'), // authenticated users
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    // Helper method to setup the database if not exists
    public function actionInitDb()
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

            Yii::app()->user->setFlash('success', "Database tables created successfully.");
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::app()->user->setFlash('error', "Database Error: " . $e->getMessage());
        }
        $this->redirect(array('index'));
    }

    public function actionIndex()
    {
        $sql = "SELECT * FROM agenda_days ORDER BY sort_order ASC, id ASC";
        try {
            $days = Yii::app()->db->createCommand($sql)->queryAll();
        } catch (Exception $e) {
            // Table might not exist yet
            $days = array();
            Yii::app()->user->setFlash('warning', "Vui lòng bấm 'Khởi tạo Database' trước khi dùng.");
        }

        $this->render('index', array(
            'days' => $days,
        ));
    }

    // --- DAY MANAGEMENT --- //

    public function actionCreateDay()
    {
        $model = array('day_label' => '', 'date_label' => '', 'sort_order' => 0);

        if (isset($_POST['AgendaDay'])) {
            $model = $_POST['AgendaDay'];
            $sql = "INSERT INTO agenda_days (day_label, date_label, sort_order) VALUES (:day_label, :date_label, :sort_order)";
            try {
                $cmd = Yii::app()->db->createCommand($sql);
                $cmd->bindValue(':day_label', $model['day_label'], PDO::PARAM_STR);
                $cmd->bindValue(':date_label', $model['date_label'], PDO::PARAM_STR);
                $cmd->bindValue(':sort_order', $model['sort_order'], PDO::PARAM_INT);
                $cmd->execute();

                Yii::app()->user->setFlash('success', 'Thêm Ngày thành công!');
                $this->redirect(array('index'));
            } catch (Exception $e) {
                Yii::app()->user->setFlash('error', 'Lỗi: ' . $e->getMessage());
            }
        }

        $this->render('form_day', array(
            'model' => $model,
            'isNewRecord' => true,
        ));
    }

    public function actionUpdateDay($id)
    {
        $id = (int) $id;
        $model = Yii::app()->db->createCommand("SELECT * FROM agenda_days WHERE id = :id")->bindValue(':id', $id)->queryRow();
        if (!$model)
            throw new CHttpException(404, 'Không tìm thấy dữ liệu.');

        if (isset($_POST['AgendaDay'])) {
            $post = $_POST['AgendaDay'];
            $sql = "UPDATE agenda_days SET day_label = :day_label, date_label = :date_label, sort_order = :sort_order WHERE id = :id";
            try {
                $cmd = Yii::app()->db->createCommand($sql);
                $cmd->bindValue(':day_label', $post['day_label'], PDO::PARAM_STR);
                $cmd->bindValue(':date_label', $post['date_label'], PDO::PARAM_STR);
                $cmd->bindValue(':sort_order', $post['sort_order'], PDO::PARAM_INT);
                $cmd->bindValue(':id', $id, PDO::PARAM_INT);
                $cmd->execute();

                Yii::app()->user->setFlash('success', 'Cập nhật Ngày thành công!');
                $this->redirect(array('index'));
            } catch (Exception $e) {
                Yii::app()->user->setFlash('error', 'Lỗi: ' . $e->getMessage());
            }
        }

        $this->render('form_day', array(
            'model' => $model,
            'isNewRecord' => false,
        ));
    }

    public function actionDeleteDay($id)
    {
        $id = (int) $id;
        try {
            Yii::app()->db->createCommand("DELETE FROM agenda_days WHERE id = :id")->bindValue(':id', $id)->execute();
            Yii::app()->user->setFlash('success', 'Xoá Ngày thành công (Các sự kiện bên trong cũng bị xoá).');
        } catch (Exception $e) {
            Yii::app()->user->setFlash('error', 'Lỗi xoá: ' . $e->getMessage());
        }
        $this->redirect(array('index'));
    }

    // --- EVENT MANAGEMENT --- //

    public function actionViewDay($id)
    {
        $id = (int) $id;
        $day = Yii::app()->db->createCommand("SELECT * FROM agenda_days WHERE id = :id")->bindValue(':id', $id)->queryRow();
        if (!$day)
            throw new CHttpException(404, 'Không tìm thấy Ngày.');

        $events = Yii::app()->db->createCommand("SELECT * FROM agenda_events WHERE day_id = :day_id ORDER BY sort_order ASC, id ASC")->bindValue(':day_id', $id)->queryAll();

        $this->render('view_day', array(
            'day' => $day,
            'events' => $events,
        ));
    }

    public function actionCreateEvent($day_id)
    {
        $day_id = (int) $day_id;
        $day = Yii::app()->db->createCommand("SELECT * FROM agenda_days WHERE id = :id")->bindValue(':id', $day_id)->queryRow();
        if (!$day)
            throw new CHttpException(404, 'Ngày không hợp lệ.');

        $model = array('day_id' => $day_id, 'time_label' => '', 'title' => '', 'location' => '', 'description' => '', 'event_type' => 'normal', 'sort_order' => 0);

        if (isset($_POST['AgendaEvent'])) {
            $post = $_POST['AgendaEvent'];
            $sql = "INSERT INTO agenda_events (day_id, time_label, title, location, description, event_type, sort_order) 
                    VALUES (:day_id, :time_label, :title, :location, :description, :event_type, :sort_order)";
            try {
                $cmd = Yii::app()->db->createCommand($sql);
                $cmd->bindValue(':day_id', $day_id, PDO::PARAM_INT);
                $cmd->bindValue(':time_label', $post['time_label'], PDO::PARAM_STR);
                $cmd->bindValue(':title', $post['title'], PDO::PARAM_STR);
                $cmd->bindValue(':location', $post['location'], PDO::PARAM_STR);
                $cmd->bindValue(':description', $post['description'], PDO::PARAM_STR);
                $cmd->bindValue(':event_type', $post['event_type'], PDO::PARAM_STR);
                $cmd->bindValue(':sort_order', $post['sort_order'], PDO::PARAM_INT);
                $cmd->execute();

                Yii::app()->user->setFlash('success', 'Thêm Sự kiện thành công!');
                $this->redirect(array('viewDay', 'id' => $day_id));
            } catch (Exception $e) {
                Yii::app()->user->setFlash('error', 'Lỗi: ' . $e->getMessage());
            }
        }

        $this->render('form_event', array(
            'model' => $model,
            'day' => $day,
            'isNewRecord' => true,
        ));
    }

    public function actionUpdateEvent($id)
    {
        $id = (int) $id;
        $model = Yii::app()->db->createCommand("SELECT * FROM agenda_events WHERE id = :id")->bindValue(':id', $id)->queryRow();
        if (!$model)
            throw new CHttpException(404, 'Không tìm thấy sự kiện.');

        $day = Yii::app()->db->createCommand("SELECT * FROM agenda_days WHERE id = :id")->bindValue(':id', $model['day_id'])->queryRow();

        if (isset($_POST['AgendaEvent'])) {
            $post = $_POST['AgendaEvent'];
            $sql = "UPDATE agenda_events SET time_label = :time_label, title = :title, location = :location, description = :description, event_type = :event_type, sort_order = :sort_order WHERE id = :id";
            try {
                $cmd = Yii::app()->db->createCommand($sql);
                $cmd->bindValue(':time_label', $post['time_label'], PDO::PARAM_STR);
                $cmd->bindValue(':title', $post['title'], PDO::PARAM_STR);
                $cmd->bindValue(':location', $post['location'], PDO::PARAM_STR);
                $cmd->bindValue(':description', $post['description'], PDO::PARAM_STR);
                $cmd->bindValue(':event_type', $post['event_type'], PDO::PARAM_STR);
                $cmd->bindValue(':sort_order', $post['sort_order'], PDO::PARAM_INT);
                $cmd->bindValue(':id', $id, PDO::PARAM_INT);
                $cmd->execute();

                Yii::app()->user->setFlash('success', 'Cập nhật Sự kiện thành công!');
                $this->redirect(array('viewDay', 'id' => $model['day_id']));
            } catch (Exception $e) {
                Yii::app()->user->setFlash('error', 'Lỗi: ' . $e->getMessage());
            }
        }

        $this->render('form_event', array(
            'model' => $model,
            'day' => $day,
            'isNewRecord' => false,
        ));
    }

    public function actionDeleteEvent($id)
    {
        $id = (int) $id;
        $model = Yii::app()->db->createCommand("SELECT day_id FROM agenda_events WHERE id = :id")->bindValue(':id', $id)->queryRow();

        try {
            Yii::app()->db->createCommand("DELETE FROM agenda_events WHERE id = :id")->bindValue(':id', $id)->execute();
            Yii::app()->user->setFlash('success', 'Xoá Sự kiện thành công.');
        } catch (Exception $e) {
            Yii::app()->user->setFlash('error', 'Lỗi xoá: ' . $e->getMessage());
        }

        if ($model) {
            $this->redirect(array('viewDay', 'id' => $model['day_id']));
        } else {
            $this->redirect(array('index'));
        }
    }
}
