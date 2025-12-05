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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.books: ~3 rows (approximately)
INSERT INTO `books` (`ID`, `TITLE`, `DESCRIPTION`, `AUTHOR`, `LANGUAGE`, `AGE_GROUP`, `COVER`, `FILE_PATH`, `IS_ACTIVE`, `CREATED_DATE`) VALUES
	(12, 'Book One', 'This Book One For Test and Enjoy with Children', 'Ahmed', 'English', '7-9', 'assets/books/images/cover_693184489fe3d.jpg', 'assets/books/files/book_69318448a00d5.pdf', 'Y', '2025-12-04 14:53:28');
INSERT INTO `books` (`ID`, `TITLE`, `DESCRIPTION`, `AUTHOR`, `LANGUAGE`, `AGE_GROUP`, `COVER`, `FILE_PATH`, `IS_ACTIVE`, `CREATED_DATE`) VALUES
	(13, 'Book Two', 'This Book Two for Test and Enjoy with Children', 'Omar', 'English', '7-9', 'assets/books/images/cover_69318470bfb49.jpg', 'assets/books/files/book_69318470bfd4a.pdf', 'Y', '2025-12-04 14:54:08');
INSERT INTO `books` (`ID`, `TITLE`, `DESCRIPTION`, `AUTHOR`, `LANGUAGE`, `AGE_GROUP`, `COVER`, `FILE_PATH`, `IS_ACTIVE`, `CREATED_DATE`) VALUES
	(14, 'Book Three', 'This Book Three For Children', 'Mohamed', 'English', '7-9', 'assets/books/images/cover_6931849469742.jpg', 'assets/books/files/book_69318494698ef.pdf', 'Y', '2025-12-04 14:54:44');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.children: ~3 rows (approximately)
INSERT INTO `children` (`ID`, `CODE`, `NAME`, `DOB`, `AGE`, `AVATER`, `CREADTED_DATE`, `USER_ID`, `ROLE_ID`) VALUES
	(1, '123', 'Mo', '2012-12-03', '14', NULL, '2025-12-03 19:01:53', 1, 4);
INSERT INTO `children` (`ID`, `CODE`, `NAME`, `DOB`, `AGE`, `AVATER`, `CREADTED_DATE`, `USER_ID`, `ROLE_ID`) VALUES
	(6, 'CHILD-3-453982', 'Omar', '2019-02-01', '6', NULL, '2025-12-04 02:52:36', 3, 4);
INSERT INTO `children` (`ID`, `CODE`, `NAME`, `DOB`, `AGE`, `AVATER`, `CREADTED_DATE`, `USER_ID`, `ROLE_ID`) VALUES
	(7, 'CHILD-3-707529', 'Zaid', '2019-07-01', '6', NULL, '2025-12-04 21:31:46', 3, 4);

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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.options: ~0 rows (approximately)
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
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(9, 'Charles Dickens', 'N', 3);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(10, 'Jane Austen', 'Y', 3);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(11, 'Emily Brontë', 'N', 3);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(12, 'Thomas Hardy', 'N', 3);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(13, 'True', 'Y', 4);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(14, 'False', 'N', 4);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(15, '15th century', 'N', 5);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(16, '16th century', 'Y', 5);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(17, '17th century', 'N', 5);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(18, '18th century', 'N', 5);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(19, 'Isaac Asimov', 'N', 6);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(20, 'Arthur C. Clarke', 'N', 6);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(21, 'Frank Herbert', 'Y', 6);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(22, 'Robert Heinlein', 'N', 6);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(23, '1931', 'N', 7);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(24, '1932', 'Y', 7);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(25, '1933', 'N', 7);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(26, '1934', 'N', 7);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(27, 'True', 'N', 8);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(28, 'False', 'Y', 8);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(29, 'Stephenie Meyer', 'N', 9);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(30, 'Veronica Roth', 'N', 9);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(31, 'Suzanne Collins', 'Y', 9);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(32, 'J.K. Rowling', 'N', 9);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(33, 'True', 'Y', 10);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(34, 'False', 'N', 10);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(35, 'Romance', 'N', 11);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(36, 'Thriller/Mystery', 'Y', 11);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(37, 'Science Fiction', 'N', 11);
INSERT INTO `options` (`ID`, `OPTION`, `IS_CORRECT`, `QUESTION_ID`) VALUES
	(38, 'Historical Non-fiction', 'N', 11);

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.questions: ~0 rows (approximately)
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(1, 'Question 1', 'multiple_choice', '2025-12-04 23:51:44', 1);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(2, 'Question 2', 'multiple_choice', '2025-12-04 23:51:44', 1);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(3, 'Who wrote "Pride and Prejudice"?', 'multiple_choice', '2025-12-05 00:08:47', 2);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(4, '"Moby-Dick" was written by Herman Melville', 'true_false', '2025-12-05 00:08:47', 2);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(5, 'In which century was "Don Quixote" first published?', 'multiple_choice', '2025-12-05 00:08:47', 2);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(6, 'Who wrote "Dune"?', 'multiple_choice', '2025-12-05 00:08:47', 3);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(7, 'What year was "Brave New World" published?', 'multiple_choice', '2025-12-05 00:08:47', 3);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(8, 'Philip K. Dick wrote "The Martian Chronicles"', 'true_false', '2025-12-05 00:08:47', 3);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(9, 'Who wrote "The Hunger Games"?', 'multiple_choice', '2025-12-05 00:08:47', 4);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(10, '"Gone Girl" was written by Gillian Flynn', 'true_false', '2025-12-05 00:08:47', 4);
INSERT INTO `questions` (`ID`, `QUESTION`, `TYPE`, `CREATED_DATE`, `QUIZ_ID`) VALUES
	(11, 'Which genre does "The Da Vinci Code" belong to?', 'multiple_choice', '2025-12-05 00:08:47', 4);

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Collect all quizzes';

-- Dumping data for table booknest.quizzes: ~0 rows (approximately)
INSERT INTO `quizzes` (`ID`, `TITLE`, `DESCRIPTION`, `CREATED_DATE`, `USER_ID`, `BOOK_ID`) VALUES
	(1, 'Quiz 1', 'Quiz as Test on Book One', '2025-12-04 23:51:44', 2, 12);
INSERT INTO `quizzes` (`ID`, `TITLE`, `DESCRIPTION`, `CREATED_DATE`, `USER_ID`, `BOOK_ID`) VALUES
	(2, 'Classic Literature Basics', 'Test your knowledge of classic literary works', '2025-12-05 00:08:47', 2, 14);
INSERT INTO `quizzes` (`ID`, `TITLE`, `DESCRIPTION`, `CREATED_DATE`, `USER_ID`, `BOOK_ID`) VALUES
	(3, 'Science Fiction Masters', 'A quiz about sci-fi authors and their works', '2025-12-05 00:08:47', 2, 13);
INSERT INTO `quizzes` (`ID`, `TITLE`, `DESCRIPTION`, `CREATED_DATE`, `USER_ID`, `BOOK_ID`) VALUES
	(4, 'Modern Bestsellers', 'Test your knowledge of recent bestselling books', '2025-12-05 00:08:47', 2, NULL);

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
  `CHILD_ID` int DEFAULT NULL COMMENT 'If score is for a child',
  `QUIZ_ID` int NOT NULL,
  `SCORE_PERCENTAGE` decimal(5,2) NOT NULL DEFAULT '0.00',
  `TOTAL_QUESTIONS` int NOT NULL DEFAULT '0',
  `CORRECT_ANSWERS` int NOT NULL DEFAULT '0',
  `DATE_COMPLETED` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`) USING BTREE,
  KEY `FK_quiz_scores_quizzes` (`QUIZ_ID`) USING BTREE,
  KEY `FK_quiz_scores_children` (`CHILD_ID`) USING BTREE,
  CONSTRAINT `FK_quiz_scores_children` FOREIGN KEY (`CHILD_ID`) REFERENCES `children` (`ID`) ON DELETE CASCADE,
  CONSTRAINT `FK_quiz_scores_quizzes` FOREIGN KEY (`QUIZ_ID`) REFERENCES `quizzes` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table booknest.scores: ~19 rows (approximately)
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(3, NULL, 1, 0.00, 2, 0, '2025-12-05 00:51:03');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(4, NULL, 1, 0.00, 2, 0, '2025-12-05 00:51:11');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(5, NULL, 1, 50.00, 2, 1, '2025-12-05 00:51:16');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(6, NULL, 1, 0.00, 2, 0, '2025-12-05 00:51:23');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(7, NULL, 1, 50.00, 2, 1, '2025-12-05 00:52:51');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(8, NULL, 1, 50.00, 2, 1, '2025-12-05 00:54:14');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(9, NULL, 1, 0.00, 2, 0, '2025-12-05 00:54:28');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(10, NULL, 1, 100.00, 2, 2, '2025-12-05 00:54:36');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(11, NULL, 2, 66.67, 3, 2, '2025-12-05 01:28:51');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(12, NULL, 2, 0.00, 3, 0, '2025-12-05 01:30:50');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(13, NULL, 2, 0.00, 3, 0, '2025-12-05 01:30:58');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(14, NULL, 2, 33.33, 3, 1, '2025-12-05 01:31:06');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(15, NULL, 2, 66.67, 3, 2, '2025-12-05 01:31:13');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(16, NULL, 3, 33.33, 3, 1, '2025-12-05 01:35:25');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(17, NULL, 2, 33.33, 3, 1, '2025-12-05 01:35:52');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(18, NULL, 2, 33.33, 3, 1, '2025-12-05 01:36:37');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(19, NULL, 3, 33.33, 3, 1, '2025-12-05 01:38:30');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(20, NULL, 2, 66.67, 3, 2, '2025-12-05 01:38:56');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(21, 7, 2, 0.00, 3, 0, '2025-12-05 01:44:19');
INSERT INTO `scores` (`ID`, `CHILD_ID`, `QUIZ_ID`, `SCORE_PERCENTAGE`, `TOTAL_QUESTIONS`, `CORRECT_ANSWERS`, `DATE_COMPLETED`) VALUES
	(22, 7, 2, 100.00, 3, 3, '2025-12-05 01:46:36');

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

-- Dumping data for table booknest.users: ~4 rows (approximately)
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(1, 'Adham', 'Altonsy', 'admin@gmail.com', '$2y$10$9U7SjlvjVqOIwE/13LA5P.5d3TyXYpedTX7RWSuRECsGm.mII8yai', NULL, '0123456789', 'Y', '2025-11-22 23:27:54', 1);
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(2, 'Edu', 'Institue', 'edu@gmail.com', '$2y$10$9U7SjlvjVqOIwE/13LA5P.5d3TyXYpedTX7RWSuRECsGm.mII8yai', NULL, '123546880', 'N', '2025-12-01 11:19:07', 2);
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(3, 'Adham', 'Altonsy', 'at@gmail.com', '$2y$10$xevwzZJKmUm802fhPfBIceg5NVn0SLjjFeN9QCVU1pMRvko552aMa', 948990, '1278769920', 'Y', '2025-11-30 03:49:17', 3);
INSERT INTO `users` (`ID`, `FIRST_NAME`, `LAST_NAME`, `USERNAME`, `PASSWORD`, `PASSKEY`, `PHONE`, `IS_SUBSCRIBED`, `CREATED_DATE`, `ROLE_ID`) VALUES
	(17, 'Karem', 'Mohamed', 'km@gmail.com', '$2y$10$0EqXdY5ZvGv08SwjPxFgA.DL9hKEKy6ghhXW6jYoFt/O4Wt.giDCC', NULL, '8237639298', 'Y', '2025-12-02 19:48:51', 3);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
