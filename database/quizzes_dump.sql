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

-- Dumping structure for table booknest.options
CREATE TABLE IF NOT EXISTS `options` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `OPTION` varchar(400) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `IS_CORRECT` enum('Y','N') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `QUESTION_ID` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `FK_options_questions` (`QUESTION_ID`),
  CONSTRAINT `FK_options_questions` FOREIGN KEY (`QUESTION_ID`) REFERENCES `questions` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.options: ~8 rows (approximately)
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(1, 'A', 'N', 1);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(2, 'B', 'Y', 1);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(3, 'C', 'N', 1);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(4, 'D', 'N', 1);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(5, 'A', 'N', 2);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(6, 'B', 'N', 2);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(7, 'C', 'Y', 2);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(8, 'D', 'N', 2);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.questions: ~2 rows (approximately)
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(1, 'Question 1', 'multiple_choice', '2025-12-04 23:51:44', 1);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(2, 'Question 2', 'multiple_choice', '2025-12-04 23:51:44', 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Collect all quizzes';

-- Dumping data for table booknest.quizzes: ~1 rows (approximately)
INSERT INTO `quizzes` (`ID`, `TITLE`, `DESCRIPTION`, `CREATED_DATE`, `USER_ID`, `BOOK_ID`) VALUES
	(1, 'Quiz 1', 'Quiz as Test on Book One', '2025-12-04 23:51:44', 2, 12);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
