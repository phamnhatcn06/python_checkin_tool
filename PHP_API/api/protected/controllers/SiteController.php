<?php

class SiteController extends Controller
{
    public $defaultAction = 'index';

    public function actionIndex()
    {
        $this->redirect(['site']);
    }

    public function actionError()
    {
        $error = Yii::app()->errorHandler->error;
        if ($error) {
            echo CHtml::encode($error['message']);
        }
    }

    // SiteController.php
    public function actionShow()
    {
        $currentPrizeId = Yii::app()->db->createCommand("
        SELECT value FROM settings WHERE name='current_prize_id'
    ")->queryScalar();
        $winnerList = Yii::app()->db->createCommand("
        SELECT 
            p.code,
            p.full_name,
            p.department,
            p.id,
            t.prize_id
        FROM winners t
        LEFT JOIN participants p ON t.participant_id = p.id
        WHERE t.prize_id = :id
          AND t.confirm = 1
        ORDER BY t.id ASC
    ")->queryAll(true, [
                    ':id' => $currentPrizeId
                ]);


        $currentPrize = Yii::app()->db->createCommand("SELECT * FROM prizes WHERE id=:id")
            ->queryRow(true, [':id' => $currentPrizeId]);

        $this->render('show', ['winnerList' => $winnerList, 'prize' => $currentPrize]);
    }
    public function actionRemote()
    {
        $this->layout = false; // No layout for simple remote
        $this->render('remote');
    }

    public function actionSetupAdmin()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
          `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
          `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
          PRIMARY KEY (`id`),
          UNIQUE KEY `username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        INSERT INTO `users` (`username`, `password`, `role`) VALUES
        ('admin', '$2y$10\$aGZtBBLd/i04l9m.BHDSB.wFODS8nJ2Q.hIsmPeJqCiqWE.mmFpN2', 'admin')
        ON DUPLICATE KEY UPDATE `username`=`username`;
        ";

        try {
            Yii::app()->db->createCommand($sql)->execute();
            echo "Đã tạo bảng users và tài khoản admin/admin123 thành công!";
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }

    public function actionAddGender()
    {
        try {
            Yii::app()->db->createCommand('ALTER TABLE attendees ADD COLUMN gender VARCHAR(10) NULL')->execute();
            echo "Successfully added gender column to attendees table.";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "Gender column already exists.";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    }
}
