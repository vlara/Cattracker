/* SQLEditor (MySQL (2))*/
DROP TABLE IF EXISTS location;
CREATE TABLE location
(
id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
lat DECIMAL(24,20),
lng DECIMAL(24,20),
description VARCHAR(255),
`name` VARCHAR(255),
PRIMARY KEY (id)
)ENGINE = INNODB;

DROP TABLE IF EXISTS line;
CREATE TABLE line
(
id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
name VARCHAR(255),
PRIMARY KEY (id)
)ENGINE = INNODB;

DROP TABLE IF EXISTS session;
CREATE TABLE session
(
id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
description VARCHAR(255),
active BOOLEAN,
PRIMARY KEY (id)
)ENGINE = INNODB;

DROP TABLE IF EXISTS arrival;
CREATE TABLE arrival
(
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location` int(10) unsigned NOT NULL,
  `time` time DEFAULT NULL,
  `line` int(10) unsigned NOT NULL,
  `sessionID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `location_idxfk` (`location`),
  KEY `line_idxfk` (`line`),
  KEY `sessionID_idxfk` (`sessionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

create table if not exists `daysofoperation` (
 `lineID` int(10) unsigned NOT NULL,
 `day` int(1) NOT NULL,
 UNIQUE KEY `day`(`lineID`,`day`)
) engine=innodb

ALTER TABLE arrival ADD FOREIGN KEY location_idxfk (location) REFERENCES location (id) ON DELETE CASCADE;

ALTER TABLE arrival ADD FOREIGN KEY line_idxfk (line) REFERENCES line (id) ON DELETE CASCADE;

ALTER TABLE arrival ADD FOREIGN KEY sessionID_idxfk (sessionID) REFERENCES session (id) ON DELETE CASCADE;
