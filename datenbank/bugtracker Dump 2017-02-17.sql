-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.1.13-MariaDB - mariadb.org binary distribution
-- Server Betriebssystem:        Win32
-- HeidiSQL Version:             9.3.0.5049
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportiere Datenbank Struktur für bugtracker
CREATE DATABASE IF NOT EXISTS `bugtracker` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_german1_ci */;
USE `bugtracker`;

-- Exportiere Struktur von Tabelle bugtracker.attachments
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bugID` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__bugs_attachments` (`bugID`),
  CONSTRAINT `FK__bugs_attachments` FOREIGN KEY (`bugID`) REFERENCES `bugs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.attachments: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.bugs
CREATE TABLE IF NOT EXISTS `bugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoryID` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` longtext NOT NULL,
  `userID` int(11) NOT NULL,
  `assignedToID` int(11) DEFAULT NULL,
  `progress` int(11) NOT NULL DEFAULT '0',
  `statusID` int(11) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_bugs_status` (`statusID`),
  KEY `FK_bugs_users` (`userID`),
  KEY `FK_bugs_users_assigned` (`assignedToID`),
  KEY `FK_bugs_projects` (`categoryID`),
  KEY `FK_bugs_priority` (`priority`),
  CONSTRAINT `FK_bugs_priority` FOREIGN KEY (`priority`) REFERENCES `priority` (`id`),
  CONSTRAINT `FK_bugs_projects` FOREIGN KEY (`categoryID`) REFERENCES `category` (`id`),
  CONSTRAINT `FK_bugs_status` FOREIGN KEY (`statusID`) REFERENCES `status` (`id`),
  CONSTRAINT `FK_bugs_users` FOREIGN KEY (`userID`) REFERENCES `users` (`id`),
  CONSTRAINT `FK_bugs_users_assigned` FOREIGN KEY (`assignedToID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.bugs: ~1 rows (ungefähr)
/*!40000 ALTER TABLE `bugs` DISABLE KEYS */;
INSERT INTO `bugs` (`id`, `categoryID`, `priority`, `title`, `description`, `userID`, `assignedToID`, `progress`, `statusID`, `createTime`) VALUES
	(19, 9, 3, 'Implementing Project and Category Management', 'Needed Functions to create and edit Projects or Categorys in the Admin-Panel.', 5, 0, 100, 7, '2017-02-01 09:19:25');
/*!40000 ALTER TABLE `bugs` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.bug_history
CREATE TABLE IF NOT EXISTS `bug_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bugID` int(11) DEFAULT NULL,
  `content` longtext,
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_bug_history_bugs` (`bugID`),
  CONSTRAINT `FK_bug_history_bugs` FOREIGN KEY (`bugID`) REFERENCES `bugs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.bug_history: ~3 rows (ungefähr)
/*!40000 ALTER TABLE `bug_history` DISABLE KEYS */;
INSERT INTO `bug_history` (`id`, `bugID`, `content`, `createTime`) VALUES
	(97, 19, 'Bug created.', '2017-02-01 09:19:25'),
	(98, 19, '<small>User <a href=\'/user/5/nabrezzelt\'>nabrezzelt</a></small><br>- changed the status from New to Complete.', '2017-02-06 11:56:54'),
	(99, 19, '<small>User <a href=\'/user/5/nabrezzelt\'>nabrezzelt</a></small><br>- changed the status from Complete to Closed.', '2017-02-06 11:57:17'),
	(100, 19, '<small>User <a href=\'/user/5/nabrezzelt\'>nabrezzelt</a></small><br>- changed the Progress from 0 to 100. <br>- changed the status from Closed to Fixed <span class=\'glyphicon glyphicon-ok\'></span>.Change created.', '2017-02-06 11:57:27');
/*!40000 ALTER TABLE `bug_history` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectID` int(11),
  `name` varchar(50) NOT NULL,
  `parentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_category_projects` (`projectID`),
  KEY `FK_category_parentCategory` (`parentID`),
  CONSTRAINT `FK_category_parentCategory` FOREIGN KEY (`parentID`) REFERENCES `category` (`id`),
  CONSTRAINT `FK_category_projects` FOREIGN KEY (`projectID`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.category: ~3 rows (ungefähr)
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` (`id`, `projectID`, `name`, `parentID`) VALUES
	(9, 1, 'Bugtracker', NULL),
	(10, 1, 'Changelog', NULL),
	(11, 1, 'Wiki', NULL);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.changelog
CREATE TABLE IF NOT EXISTS `changelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `description` longtext,
  `changeDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_changelog_users` (`userID`),
  KEY `FK_changelog_projects` (`projectID`),
  CONSTRAINT `FK_changelog_projects` FOREIGN KEY (`projectID`) REFERENCES `projects` (`id`),
  CONSTRAINT `FK_changelog_users` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.changelog: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `changelog` DISABLE KEYS */;
INSERT INTO `changelog` (`id`, `userID`, `projectID`, `description`, `changeDate`) VALUES
	(1, 5, 1, 'Update', '2017-02-06 11:57:27');
/*!40000 ALTER TABLE `changelog` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.changelog_bug_relation
CREATE TABLE IF NOT EXISTS `changelog_bug_relation` (
  `changeID` int(11) NOT NULL,
  `bugID` int(11) NOT NULL,
  KEY `FK__changelog` (`changeID`),
  KEY `FK__bugs` (`bugID`),
  CONSTRAINT `FK__bugs` FOREIGN KEY (`bugID`) REFERENCES `bugs` (`id`),
  CONSTRAINT `FK__changelog` FOREIGN KEY (`changeID`) REFERENCES `changelog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.changelog_bug_relation: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `changelog_bug_relation` DISABLE KEYS */;
INSERT INTO `changelog_bug_relation` (`changeID`, `bugID`) VALUES
	(1, 19);
/*!40000 ALTER TABLE `changelog_bug_relation` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bugID` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `userID` int(11) NOT NULL,
  `createTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `FK_comments_users` (`userID`),
  KEY `FK_comments_bugs` (`bugID`),
  CONSTRAINT `FK_comments_bugs` FOREIGN KEY (`bugID`) REFERENCES `bugs` (`id`),
  CONSTRAINT `FK_comments_users` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.comments: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.groups
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.groups: ~1 rows (ungefähr)
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` (`id`, `name`) VALUES
	(1, 'Administrator');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.group_permissions
CREATE TABLE IF NOT EXISTS `group_permissions` (
  `groupID` int(11) NOT NULL,
  `permissionID` int(11) NOT NULL,
  KEY `FK__groups` (`groupID`),
  KEY `FK__permissions_groups` (`permissionID`),
  CONSTRAINT `FK__groups` FOREIGN KEY (`groupID`) REFERENCES `groups` (`id`),
  CONSTRAINT `FK__permissions_groups` FOREIGN KEY (`permissionID`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.group_permissions: ~34 rows (ungefähr)
/*!40000 ALTER TABLE `group_permissions` DISABLE KEYS */;
INSERT INTO `group_permissions` (`groupID`, `permissionID`) VALUES
	(1, 1),
	(1, 2),
	(1, 3),
	(1, 5),
	(1, 6),
	(1, 7),
	(1, 8),
	(1, 9),
	(1, 10),
	(1, 11),
	(1, 12),
	(1, 13),
	(1, 14),
	(1, 15),
	(1, 17),
	(1, 18),
	(1, 19),
	(1, 20),
	(1, 21),
	(1, 22),
	(1, 23),
	(1, 24),
	(1, 25),
	(1, 26),
	(1, 27),
	(1, 28),
	(1, 29),
	(1, 30),
	(1, 31),
	(1, 32),
	(1, 33),
	(1, 34),
	(1, 35),
	(1, 36);
/*!40000 ALTER TABLE `group_permissions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.group_user_relation
CREATE TABLE IF NOT EXISTS `group_user_relation` (
  `userID` int(11) NOT NULL,
  `groupID` int(11) NOT NULL,
  KEY `FK__users_groups` (`userID`),
  KEY `FK__groups_user` (`groupID`),
  CONSTRAINT `FK__groups_user` FOREIGN KEY (`groupID`) REFERENCES `groups` (`id`),
  CONSTRAINT `FK__users_groups` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.group_user_relation: ~2 rows (ungefähr)
/*!40000 ALTER TABLE `group_user_relation` DISABLE KEYS */;
INSERT INTO `group_user_relation` (`userID`, `groupID`) VALUES
	(5, 1),
	(6, 1);
/*!40000 ALTER TABLE `group_user_relation` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `message` longtext COLLATE latin1_german1_ci NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_notifications_users` (`userID`),
  CONSTRAINT `FK_notifications_users` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- Exportiere Daten aus Tabelle bugtracker.notifications: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `errorCode` int(11) NOT NULL,
  `errorMessage` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.permissions: ~33 rows (ungefähr)
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` (`id`, `name`, `errorCode`, `errorMessage`) VALUES
	(1, 'User darf die Bugs einer Kategorie einsehen.', 2001, 'No Permission to see the Buglist.'),
	(2, 'User darf einen Kommentar zu einem Bug erstellen.', 6002, 'No Permission to create Comments.'),
	(3, 'User darf einen Bug einsehen.', 2003, 'No Permission to view a Bug.'),
	(5, 'User darf einen Bug erstellen.', 2005, 'No Permission to report a new Bug.'),
	(6, 'User darf Dateien herunterladen.', 4006, 'No Permission to download files.'),
	(7, 'User darf Dateien hochladen.', 4007, 'No Permission to upload files.'),
	(8, 'User darf Dateien löschen.', 4008, 'No Permission to delete Attachments.'),
	(9, 'User darf einen Bug bearbeiten.', 2009, 'No Permission to edit a existing Bug.'),
	(10, 'User darf einen Bug löschen.', 2010, 'No Permission to delete a Bug.'),
	(11, 'User darf den Changelog eines Projekts einsehen.', 3011, 'No Permission to view the Changelog of a Project.'),
	(12, 'User darf einen neuen Change einstellen.', 3012, 'No Permission to create a new Change.'),
	(13, 'User darf Einträge im Changelog bearbeiten.', 3013, 'No Permission to edit Changes.'),
	(14, 'User darf Einträge im Changelog löschen.', 3014, 'No Permission to delete Changes.'),
	(15, 'User darf Kommentare löschen.', 6015, 'No Permission to delete Comments.'),
	(16, 'User darf Kommentare bearbeiten.', 6016, 'No Permission to edit Comments.'),
	(17, 'User darf sich zugewiesene Bugs im Accountpanel einsehen.', 7017, 'No Permission to view assigned Bugs in the Accountpanel.'),
	(18, 'User darf von sich geöffnete Bugs im Accountpanel einsehen.', 7018, 'No Permission to view opened Bugs in the Accountpanel.'),
	(19, 'User darf die Daten eines Users einsehen.', 8019, 'No Permission to view Data of a User.'),
	(20, 'User darf andere Benutzer bannen.', 8020, 'No Permission to ban other Users.'),
	(21, 'User darf sehen ob andere Benutzer gebannt sind.', 8021, 'No Permission to view if a User is banned.'),
	(22, 'User darf die Email eines Benutzers einsehen', 8022, 'No Permission to see the Email of a User.'),
	(23, 'User darf die letzte IP eines Users einsehen.', 8023, 'No Permission to see the last IP of a User.'),
	(24, 'User darf den letzten Login eines Users einsehen.', 8024, 'No Permission to see the last Login of a User.'),
	(25, 'User darf das Registrierdatum eines Users einsehen.', 8025, 'No Permission to see the Register-Date of a User.'),
	(26, 'User darf Userdetails im Adminpanel einsehen', 9026, 'No Permission to view Userdetails in the Adminpanel.'),
	(27, 'User darf die Gruppen eines Benutzers im Admin-Panel einsehen', 9027, 'No Permission to view the Groups of a User.'),
	(28, 'User darf die Rechte eines Benutzers im Admin-Panel einsehen', 9028, 'No Permission to view the Permissions of a User in the Admin-Panel.'),
	(29, 'User darf andere User zu einer Gruppe hinzufügen.', 9029, 'No Permission to add Users to Groups.'),
	(30, 'User darf die Rechte eines Benutzers ändern.', 9030, 'No Permission to change the Permissions of a User.'),
	(31, 'User darf sich alle Gruppen auflisten lassen.', 9031, 'No Permission to list all Groups.'),
	(32, 'User darf sich die Mitglieder eine Gruppe anzeigen lassen.', 9032, 'No Permission to list the Members of a Group.'),
	(33, 'user darf sich die Details eine Gruppe ansehen.', 9033, 'No Permission to view the Details of a Group.'),
	(34, 'User darf sich die Rechte einer Gruppe anzeigen lassen', 9034, 'No Permission to view the Permissions of a Group.'),
	(35, 'User darf die Rechte einer Gruppe ändern.', 9035, 'No Permission to change the Permissions of a Group.'),
	(36, 'User darf eine Gruppe von einem Benutzer entfernen.', 9036, 'No Permission to remove the Group from a User.'),
	(37, 'User darf eine Gruppe erstellen.', 9037, 'No Permission to create a Group.'),
	(38, 'User darf eine Gruppe löschen.', 9038, 'No Permission to delete a Group.'),
	(39, 'User darf eine Liste aller Benutzer einsehen', 9039, 'No Permission to view the List of all Users.');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.priority
CREATE TABLE IF NOT EXISTS `priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `colorName` varchar(50) NOT NULL,
  `importantValue` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.priority: ~4 rows (ungefähr)
/*!40000 ALTER TABLE `priority` DISABLE KEYS */;
INSERT INTO `priority` (`id`, `name`, `colorName`, `importantValue`) VALUES
	(1, 'Very High', 'danger', 10),
	(2, 'High', 'warning', 9),
	(3, 'Normal', 'success', 8),
	(4, 'Low', 'info', 7),
	(5, 'Very Low', 'default', 6);
/*!40000 ALTER TABLE `priority` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.projects
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.projects: ~0 rows (ungefähr)
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` (`id`, `name`) VALUES
	(1, 'DevControl');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.project_user_relation
CREATE TABLE IF NOT EXISTS `project_user_relation` (
  `projectID` int(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  KEY `FK_project_user_relation_projects` (`projectID`),
  KEY `FK_project_user_relation_users` (`userID`),
  CONSTRAINT `FK_project_user_relation_projects` FOREIGN KEY (`projectID`) REFERENCES `projects` (`id`),
  CONSTRAINT `FK_project_user_relation_users` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.project_user_relation: ~1 rows (ungefähr)
/*!40000 ALTER TABLE `project_user_relation` DISABLE KEYS */;
INSERT INTO `project_user_relation` (`projectID`, `userID`) VALUES
	(1, 5),
	(1, 6);
/*!40000 ALTER TABLE `project_user_relation` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.status
CREATE TABLE IF NOT EXISTS `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.status: ~9 rows (ungefähr)
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` (`id`, `name`) VALUES
	(1, 'New'),
	(2, 'Confirmed'),
	(3, 'Assigned'),
	(4, 'In Progress'),
	(5, 'Complete'),
	(6, 'Ready For Testing'),
	(7, 'Fixed <span class=\'glyphicon glyphicon-ok\'></span>'),
	(8, 'Closed'),
	(9, 'No Bug');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(250) NOT NULL,
  `rankName` varchar(50) NOT NULL,
  `lastLogin` datetime NOT NULL,
  `lastIP` varchar(50) NOT NULL DEFAULT '0.0.0.0',
  `registerDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `online` enum('0','1') NOT NULL DEFAULT '0',
  `profilePicture` varchar(50) NOT NULL,
  `banned` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.users: ~5 rows (ungefähr)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `rankName`, `lastLogin`, `lastIP`, `registerDate`, `online`, `profilePicture`, `banned`) VALUES
	(0, 'Nobody', 'nobody@the-fruit.de', 'NoPassword', '', '0000-00-00 00:00:00', '0.0.0.0', '0000-00-00 00:00:00', '0', 'default.png', '1'),
	(1, 'tim', 'tim@timmey.org', 'c0d19e4483571ff07cb01a4d3f5484102d7f333c4cafa64a2821f55031ea6041', '', '0000-00-00 00:00:00', '0.0.0.0', '2016-11-01 09:00:00', '0', 'default.png', '0'),
	(2, 'peter', 'peter.maier@gartenbau.de', 'c9f3795ef24a9355bb016518114cd9ff', '', '0000-00-00 00:00:00', '0.0.0.0', '2016-11-01 09:00:00', '0', 'default.png', '0'),
	(4, 'neandertaler', 'steinzeit@museum.org', 'a5c299e2fdd21869360a5e52cb764eafc7b94bd0eed0a2080c427684024242e0', '', '0000-00-00 00:00:00', '0.0.0.0', '2016-11-01 09:00:00', '0', 'default.png', '1'),
	(5, 'nabrezzelt', 'nabrezzelt@gmail.com', '1e6b2139b35df8545add0a9bf91d24368b59f3907f4ec115decfa43a3b359c39', 'Administrator', '2017-02-10 10:58:32', '127.0.0.1', '2016-11-01 09:00:00', '1', 'default.png', '0'),
	(6, 'janine', 'anonymouse@bt.crack', '50750ff115fd11d4430c5f5a1df5feeddedba0c0bac2627d8be37994c4db259b', 'Tester', '2017-01-26 14:00:23', '::1', '2016-12-02 12:24:36', '0', 'default.png', '0');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle bugtracker.user_permissions
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `userID` int(11) NOT NULL,
  `permissionID` int(11) NOT NULL,
  KEY `FK__users` (`userID`),
  KEY `FK__permissions` (`permissionID`),
  CONSTRAINT `FK__permissions` FOREIGN KEY (`permissionID`) REFERENCES `permissions` (`id`),
  CONSTRAINT `FK__users` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle bugtracker.user_permissions: ~7 rows (ungefähr)
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
INSERT INTO `user_permissions` (`userID`, `permissionID`) VALUES
	(5, 1),
	(5, 2),
	(5, 3),
	(5, 36),
	(5, 37),
	(5, 38),
	(5, 39);
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
