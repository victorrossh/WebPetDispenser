-- csgfxeu_petdispenser.Users definition

CREATE TABLE `Users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `admin` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Users_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- csgfxeu_petdispenser.Devices definition

CREATE TABLE `Devices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `owner` int NOT NULL,
  `token` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Devices_UNIQUE` (`token`),
  KEY `Devices_Users_FK` (`owner`),
  CONSTRAINT `Devices_Users_FK` FOREIGN KEY (`owner`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- csgfxeu_petdispenser.Sessions definition

CREATE TABLE `Sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `UserId` int NOT NULL,
  `token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `info` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `expireOn` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Sessions_Users_FK` (`UserId`),
  CONSTRAINT `Sessions_Users_FK` FOREIGN KEY (`UserId`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- csgfxeu_petdispenser.DeviceQueue definition

CREATE TABLE `DeviceQueue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `deviceId` int NOT NULL,
  `command` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `info` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `issuedOn` datetime NOT NULL,
  `executedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `DeviceQueue_Devices_FK` (`deviceId`),
  CONSTRAINT `DeviceQueue_Devices_FK` FOREIGN KEY (`deviceId`) REFERENCES `Devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- csgfxeu_petdispenser.DeviceScheduler definition

CREATE TABLE `DeviceScheduler` (
  `id` int NOT NULL AUTO_INCREMENT,
  `deviceId` int NOT NULL,
  `command` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `time` time NOT NULL,
  `lastExecuted` datetime NOT NULL,
  `info` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `DeviceQueue_Devices_FK` (`deviceId`) USING BTREE,
  CONSTRAINT `DeviceQueue_Devices_FK_copy` FOREIGN KEY (`deviceId`) REFERENCES `Devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;