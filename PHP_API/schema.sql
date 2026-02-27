CREATE TABLE IF NOT EXISTS `attendees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qr_code` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `photo_url` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qr_code_UNIQUE` (`qr_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `checkins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendee_id` int(11) NOT NULL,
  `qr_code` varchar(100) NOT NULL,
  `check_in_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_checkin_attendee` (`attendee_id`),
  CONSTRAINT `fk_checkin_attendee` FOREIGN KEY (`attendee_id`) REFERENCES `attendees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some mock data for testing
INSERT IGNORE INTO `attendees` (`qr_code`, `name`, `position`, `company`, `photo_url`) VALUES
('USER01', 'Nguyễn Văn A', 'CEO', 'Tech Corp', 'https://via.placeholder.com/400x600?text=Nguyen+Van+A'),
('USER02', 'Trần Thị B', 'Director', 'Design Studio', 'https://via.placeholder.com/400x600?text=Tran+Thi+B'),
('USER03', 'Lê Văn C', 'Developer', 'Code Factory', 'https://via.placeholder.com/400x600?text=Le+Van+C');
