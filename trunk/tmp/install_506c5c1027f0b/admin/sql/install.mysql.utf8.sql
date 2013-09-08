

CREATE TABLE IF NOT EXISTS `#__fleet_driver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__fleet_entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type` int(11) NOT NULL COMMENT '1=reseller, 2=group, 3=company',
  `parent_entity_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_entity_id` (`parent_entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__fleet_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) NOT NULL,
  `weight_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `vin` varchar(255) NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `subscription_id` varchar(255) NOT NULL,
  `fuel_capacity` INT NOT NULL DEFAULT  '0',
  `serial` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_id` (`entity_id`),
  KEY `weight_id` (`weight_id`),
  KEY `driver_id` (`driver_id`)
  UNIQUE `subscription_id` (`subscription_id`),
  UNIQUE `serial` (`serial`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__fleet_user` (
  `id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `entity_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__fleet_weight` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `compensation_table_number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__fleet_entity_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__fleet_trip_driver` (
  `trip_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  PRIMARY KEY (`trip_id`),
  KEY `driver_id` (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__fleet_trip_subscription` (
  `trip_id` int(11) NOT NULL,
  `subscription_id` int(11) NOT NULL,
  PRIMARY KEY (`trip_id`),
  KEY `subscription_id` (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__fleet_device_blacklist` (
  `serial` int(11) NOT NULL,
  PRIMARY KEY (`serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/* Default data follows */

INSERT INTO `#__fleet_entity_type` (`id`, `name`) VALUES
(1, 'Reseller'),
(2, 'Company'),
(3, 'Group');

CREATE TABLE IF NOT EXISTS `fleet_severity` (
  `bin` int(11) NOT NULL,
  `severity` int(11) NOT NULL,
  UNIQUE KEY `bin` (`bin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `fleet_severity` (`bin`, `severity`) VALUES
(1, 507),
(2, 432),
(3, 363),
(4, 300),
(5, 243),
(6, 192),
(7, 147),
(8, 108),
(9, 75),
(10, 48),
(11, 27),
(12, 12),
(13, 3),
(14, 0),
(15, 0),
(16, 0),
(17, 0),
(18, 0),
(19, 3),
(20, 12),
(21, 27),
(22, 48),
(23, 75),
(24, 108),
(25, 147),
(26, 192),
(27, 243),
(28, 300),
(29, 363),
(30, 432),
(31, 507);
