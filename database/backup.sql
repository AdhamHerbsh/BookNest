-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for booknest
CREATE DATABASE IF NOT EXISTS `booknest` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `booknest`;

-- Dumping structure for table booknest.books
CREATE TABLE IF NOT EXISTS `books` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TITLE` varchar(400) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DESCRIPTION` varchar(4000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `AUTHOR` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `LANGUAGE` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `AGE_GROUP` enum('4-6','7-9','10-12') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `COVER` varchar(400) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `FILE_PATH` varchar(400) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `IS_ACTIVE` enum('Y','N') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.books: ~3 rows (approximately)
INSERT INTO `books` (`ID`, `TITLE`, `DESCRIPTION`, `AUTHOR`, `LANGUAGE`, `AGE_GROUP`, `COVER`, `FILE_PATH`, `IS_ACTIVE`, `CREATED_DATE`) VALUES
	(1, 'Book One', 'This for test', 'Adham Altonsy', 'English', '4-6', 'assets/books/images/cover_692f65d7601e0.jpg', 'assets/books/book_692f65d760988.pdf', 'Y', '2025-12-03 00:19:03');
INSERT INTO `books` (`ID`, `TITLE`, `DESCRIPTION`, `AUTHOR`, `LANGUAGE`, `AGE_GROUP`, `COVER`, `FILE_PATH`, `IS_ACTIVE`, `CREATED_DATE`) VALUES
	(2, 'Book Two', 'This for test', 'Adham Altonsy', 'English', '4-6', 'assets/books/images/cover_692f743dcbcc4.jpg', 'assets/books/files/book_692f743dcc13d.pdf', 'Y', '2025-12-03 01:20:29');
INSERT INTO `books` (`ID`, `TITLE`, `DESCRIPTION`, `AUTHOR`, `LANGUAGE`, `AGE_GROUP`, `COVER`, `FILE_PATH`, `IS_ACTIVE`, `CREATED_DATE`) VALUES
	(7, 'Book Three 3', 'This for test This for testThis for testThis for test', 'Hana 2', 'Spanish', '10-12', 'assets/books/images/cover_693016d5d1312.jpg', 'assets/books/files/book_693016d5d16d5.pdf', 'N', '2025-12-03 12:54:13');

-- Dumping structure for table booknest.children
CREATE TABLE IF NOT EXISTS `children` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CODE` varchar(200) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `NAME` varchar(400) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `DOB` date NOT NULL,
  `AGE` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `AVATER` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CREADTED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  `USER_ID` int DEFAULT NULL,
  `ROLE_ID` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_children_users` (`USER_ID`),
  KEY `FK_children_roles` (`ROLE_ID`),
  CONSTRAINT `FK_children_roles` FOREIGN KEY (`ROLE_ID`) REFERENCES `roles` (`ID`),
  CONSTRAINT `FK_children_users` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.children: ~0 rows (approximately)

-- Dumping structure for table booknest.favorites
CREATE TABLE IF NOT EXISTS `favorites` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CREATED_DATE` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `USER_ID` int NOT NULL,
  `BOOK_ID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_favorites_users` (`USER_ID`),
  KEY `FK_favorites_books` (`BOOK_ID`),
  CONSTRAINT `FK_favorites_books` FOREIGN KEY (`BOOK_ID`) REFERENCES `books` (`ID`),
  CONSTRAINT `FK_favorites_users` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.favorites: ~0 rows (approximately)

-- Dumping structure for table booknest.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TITLE` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `MESSAGE` varchar(500) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `TYPE` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `IS_READ` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `CREATED_DATE` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `USER_ID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_notifications_users` (`USER_ID`),
  CONSTRAINT `FK_notifications_users` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.notifications: ~0 rows (approximately)

-- Dumping structure for table booknest.options
CREATE TABLE IF NOT EXISTS `options` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `OPTION` varchar(400) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `IS_CORRECT` enum('Y','N') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `QUESTION_ID` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_options_questions` (`QUESTION_ID`),
  CONSTRAINT `FK_options_questions` FOREIGN KEY (`QUESTION_ID`) REFERENCES `questions` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.options: ~0 rows (approximately)

-- Dumping structure for table booknest.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `QUESTION` text COLLATE utf8mb4_general_ci NOT NULL,
  `TYPE` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'e.g., multiple_choice, true_false',
  `CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  `QUIZ_ID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `QUIZ_ID` (`QUIZ_ID`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`QUIZ_ID`) REFERENCES `quizzes` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.questions: ~0 rows (approximately)

-- Dumping structure for table booknest.quizzes
CREATE TABLE IF NOT EXISTS `quizzes` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TITLE` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `DESCRIPTION` text COLLATE utf8mb4_general_ci,
  `CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  `USER_ID` int DEFAULT NULL,
  `BOOK_ID` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_quizzes_books` (`BOOK_ID`),
  KEY `FK_quizzes_users` (`USER_ID`),
  CONSTRAINT `FK_quizzes_books` FOREIGN KEY (`BOOK_ID`) REFERENCES `books` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `FK_quizzes_users` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Collect all quizzes';

-- Dumping data for table booknest.quizzes: ~0 rows (approximately)

-- Dumping structure for table booknest.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NAME` enum('ADMIN','EDU','PARENT','CHILD') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Define access for each user';

-- Dumping data for table booknest.roles: ~4 rows (approximately)
INSERT INTO `roles` (`ID`, `NAME`) VALUES
	(1, 'ADMIN');
INSERT INTO `roles` (`ID`, `NAME`) VALUES
	(2, 'EDU');
INSERT INTO `roles` (`ID`, `NAME`) VALUES
	(3, 'PARENT');
INSERT INTO `roles` (`ID`, `NAME`) VALUES
	(4, 'CHILD');

-- Dumping structure for table booknest.scores
CREATE TABLE IF NOT EXISTS `scores` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `SCORE` decimal(5,2) NOT NULL,
  `COMPLETED_AT` datetime NOT NULL,
  `QUIZ_ID` int NOT NULL,
  `CHILD_ID` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `QUIZ_ID` (`QUIZ_ID`),
  KEY `CHILD_ID` (`CHILD_ID`),
  CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`QUIZ_ID`) REFERENCES `quizzes` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`CHILD_ID`) REFERENCES `children` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `scores_chk_1` CHECK ((`SCORE` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='To know the score of  each child';

-- Dumping data for table booknest.scores: ~0 rows (approximately)

-- Dumping structure for table booknest.users
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `FIRST_NAME` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `LAST_NAME` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `USERNAME` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `PASSWORD` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PASSKEY` int DEFAULT NULL,
  `PHONE` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `IS_SUBSCRIBED` enum('Y','N') COLLATE utf8mb4_general_ci DEFAULT 'N',
  `CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  `ROLE_ID` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_users_roles` (`ROLE_ID`),
  CONSTRAINT `FK_users_roles` FOREIGN KEY (`ROLE_ID`) REFERENCES `roles` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='These users for booknest website';

-- Dumping data for table booknest.users: ~5 rows (approximately)
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(1, 'Adham', 'Altonsy', 'admin@gmail.com', '$2y$10$9U7SjlvjVqOIwE/13LA5P.5d3TyXYpedTX7RWSuRECsGm.mII8yai', NULL, '0123456789', 'N', '2025-11-22 23:27:54', 1);
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(2, 'Edu', 'Institue', 'edu@gmail.com', '$2y$10$9U7SjlvjVqOIwE/13LA5P.5d3TyXYpedTX7RWSuRECsGm.mII8yai', NULL, '123546880', 'N', '2025-12-01 11:19:07', 2);
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(3, 'Adham', 'Altonsy', 'at@gmail.com', '$2y$10$9U7SjlvjVqOIwE/13LA5P.5d3TyXYpedTX7RWSuRECsGm.mII8yai', NULL, '1278769920', 'Y', '2025-11-30 03:49:17', 3);
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(16, 'OO', 'PP', 'op@gmail.com', '$2y$10$URAQvhhoD6k3p0Mm42QLgOPBPtXqiLnzTetIGtVZ5YpLlfRyPscZi', NULL, '1234567890', 'Y', '2025-12-02 16:09:26', 3);
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(17, 'Karem', 'Mohamed', 'km@gmail.com', '$2y$10$0EqXdY5ZvGv08SwjPxFgA.DL9hKEKy6ghhXW6jYoFt/O4Wt.giDCC', NULL, '8237639298', 'Y', '2025-12-02 19:48:51', 3);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
