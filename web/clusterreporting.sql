-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP VIEW IF EXISTS `c`;
CREATE TABLE `c` (`system` varchar(32), `system_name` varchar(64), `q_used` decimal(32,0), `q_res` decimal(32,0), `q_avail` decimal(32,0), `q_total` decimal(32,0), `q_aoacds` decimal(32,0), `q_cdsue` decimal(32,0), `fs_size` decimal(41,0), `fs_used` decimal(41,0), `fs_avail` decimal(41,0), `recorded` datetime);


DROP VIEW IF EXISTS `cfs`;
CREATE TABLE `cfs` (`system` varchar(32), `system_name` varchar(64), `size` decimal(41,0), `used` decimal(41,0), `avail` decimal(41,0), `recorded` datetime);


DROP TABLE IF EXISTS `cluster`;
CREATE TABLE `cluster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `display_name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP VIEW IF EXISTS `cq`;
CREATE TABLE `cq` (`system` varchar(32), `system_name` varchar(64), `used` decimal(32,0), `res` decimal(32,0), `avail` decimal(32,0), `total` decimal(32,0), `aoacds` decimal(32,0), `cdsue` decimal(32,0), `recorded` datetime);


DROP VIEW IF EXISTS `c_occupancy_hours`;
CREATE TABLE `c_occupancy_hours` (`system` varchar(32), `system_name` varchar(64), `used` decimal(14,4), `res` decimal(14,4), `avail` decimal(14,4), `total` decimal(14,4), `aoacds` decimal(14,4), `cdsue` decimal(14,4), `hour` int(2));


DROP VIEW IF EXISTS `c_occupancy_weekdays`;
CREATE TABLE `c_occupancy_weekdays` (`system` varchar(32), `system_name` varchar(64), `used` decimal(14,4), `res` decimal(14,4), `avail` decimal(14,4), `total` decimal(14,4), `aoacds` decimal(14,4), `cdsue` decimal(14,4), `weekday` varchar(9), `wdno` int(3));


DROP TABLE IF EXISTS `filesystem`;
CREATE TABLE `filesystem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cluster_id` int(11) NOT NULL,
  `path` varchar(32) NOT NULL,
  `size` bigint(20) DEFAULT NULL,
  `mounted` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cluster_id_path` (`cluster_id`,`path`),
  KEY `path` (`path`),
  CONSTRAINT `filesystem_ibfk_2` FOREIGN KEY (`cluster_id`) REFERENCES `cluster` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP VIEW IF EXISTS `fs`;
CREATE TABLE `fs` (`system` varchar(32), `system_name` varchar(64), `path` varchar(32), `mounted` varchar(64), `size` bigint(20), `used` bigint(20), `avail` bigint(20), `recorded` datetime);


DROP TABLE IF EXISTS `fs_occupancy`;
CREATE TABLE `fs_occupancy` (
  `filesystem_id` int(11) NOT NULL,
  `recorded` datetime NOT NULL,
  `used` bigint(20) NOT NULL,
  `avail` bigint(20) NOT NULL,
  UNIQUE KEY `filesystem_id_recorded` (`filesystem_id`,`recorded`),
  KEY `recorded` (`recorded`),
  CONSTRAINT `fs_occupancy_ibfk_1` FOREIGN KEY (`filesystem_id`) REFERENCES `filesystem` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `nodes`;
CREATE TABLE `nodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cluster_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `arch_string` varchar(256) NOT NULL,
  `num_proc` int(11) NOT NULL,
  `mem_total` varchar(256) NOT NULL,
  `swap_total` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_cluster_id` (`name`,`cluster_id`),
  KEY `cluster_id` (`cluster_id`),
  KEY `name` (`name`),
  CONSTRAINT `nodes_ibfk_1` FOREIGN KEY (`cluster_id`) REFERENCES `cluster` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP VIEW IF EXISTS `node_details`;
CREATE TABLE `node_details` (`system` varchar(32), `system_name` varchar(64), `name` varchar(256), `num_proc` int(11), `mem_total` varchar(256), `swap_total` varchar(256));


DROP VIEW IF EXISTS `q`;
CREATE TABLE `q` (`system` varchar(32), `system_name` varchar(64), `queue` varchar(32), `queue_name` varchar(64), `cpu` int(11), `ram` int(11), `scratch` int(11), `gpu` varchar(64), `cqload` double, `used` int(11), `used_p` decimal(14,4), `res` int(11), `avail` int(11), `total` int(11), `aoacds` int(11), `cdsue` int(11), `recorded` datetime);


DROP TABLE IF EXISTS `queue`;
CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cluster_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `display_name` varchar(64) NOT NULL,
  `cpu` int(11) DEFAULT NULL,
  `ram` int(11) DEFAULT NULL,
  `scratch` int(11) DEFAULT NULL,
  `gpu` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `cluster_id` (`cluster_id`),
  CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`cluster_id`) REFERENCES `cluster` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP VIEW IF EXISTS `queue_details`;
CREATE TABLE `queue_details` (`system` varchar(32), `system_name` varchar(64), `name` varchar(32), `display_name` varchar(64), `cpu` int(11), `ram` int(11), `scratch` int(11), `gpu` varchar(64));


DROP TABLE IF EXISTS `q_occupancy`;
CREATE TABLE `q_occupancy` (
  `queue_id` int(11) NOT NULL,
  `recorded` datetime NOT NULL,
  `cqload` double DEFAULT NULL,
  `used` int(11) NOT NULL,
  `res` int(11) NOT NULL,
  `avail` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `aoacds` int(11) NOT NULL,
  `cdsue` int(11) NOT NULL,
  UNIQUE KEY `queue_id_recorded` (`queue_id`,`recorded`),
  KEY `recorded` (`recorded`),
  CONSTRAINT `q_occupancy_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `queue` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `c`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `c` AS (select `cq`.`system` AS `system`,`cq`.`system_name` AS `system_name`,`cq`.`used` AS `q_used`,`cq`.`res` AS `q_res`,`cq`.`avail` AS `q_avail`,`cq`.`total` AS `q_total`,`cq`.`aoacds` AS `q_aoacds`,`cq`.`cdsue` AS `q_cdsue`,`cfs`.`size` AS `fs_size`,`cfs`.`used` AS `fs_used`,`cfs`.`avail` AS `fs_avail`,`cq`.`recorded` AS `recorded` from (`cq` join `cfs` on(((`cq`.`system` = `cfs`.`system`) and (`cq`.`recorded` = `cfs`.`recorded`)))) group by `cq`.`recorded`,`cq`.`system`);

DROP TABLE IF EXISTS `cfs`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `cfs` AS (select `fs`.`system` AS `system`,`fs`.`system_name` AS `system_name`,sum(`fs`.`size`) AS `size`,sum(`fs`.`used`) AS `used`,sum(`fs`.`avail`) AS `avail`,`fs`.`recorded` AS `recorded` from `fs` group by `fs`.`recorded`,`fs`.`system`);

DROP TABLE IF EXISTS `cq`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `cq` AS (select `q`.`system` AS `system`,`q`.`system_name` AS `system_name`,sum(`q`.`used`) AS `used`,sum(`q`.`res`) AS `res`,sum(`q`.`avail`) AS `avail`,sum(`q`.`total`) AS `total`,sum(`q`.`aoacds`) AS `aoacds`,sum(`q`.`cdsue`) AS `cdsue`,`q`.`recorded` AS `recorded` from `q` group by `q`.`recorded`,`q`.`system`);

DROP TABLE IF EXISTS `c_occupancy_hours`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `c_occupancy_hours` AS (select `q`.`system` AS `system`,`q`.`system_name` AS `system_name`,avg(`q`.`used`) AS `used`,avg(`q`.`res`) AS `res`,avg(`q`.`avail`) AS `avail`,avg(`q`.`total`) AS `total`,avg(`q`.`aoacds`) AS `aoacds`,avg(`q`.`cdsue`) AS `cdsue`,hour(`q`.`recorded`) AS `hour` from `q` group by `q`.`system`,`hour` order by `hour`);

DROP TABLE IF EXISTS `c_occupancy_weekdays`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `c_occupancy_weekdays` AS (select `q`.`system` AS `system`,`q`.`system_name` AS `system_name`,avg(`q`.`used`) AS `used`,avg(`q`.`res`) AS `res`,avg(`q`.`avail`) AS `avail`,avg(`q`.`total`) AS `total`,avg(`q`.`aoacds`) AS `aoacds`,avg(`q`.`cdsue`) AS `cdsue`,dayname(`q`.`recorded`) AS `weekday`,((case dayofweek(`q`.`recorded`) when 1 then 8 else dayofweek(`q`.`recorded`) end) - 1) AS `wdno` from `q` group by `q`.`system`,`weekday`,`wdno` order by `wdno`);

DROP TABLE IF EXISTS `fs`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `fs` AS (select `c`.`name` AS `system`,`c`.`display_name` AS `system_name`,`b`.`path` AS `path`,`b`.`mounted` AS `mounted`,`b`.`size` AS `size`,`a`.`used` AS `used`,`a`.`avail` AS `avail`,`a`.`recorded` AS `recorded` from ((`fs_occupancy` `a` join `filesystem` `b` on((`a`.`filesystem_id` = `b`.`id`))) join `cluster` `c` on((`b`.`cluster_id` = `c`.`id`))));

DROP TABLE IF EXISTS `node_details`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `node_details` AS (select `c`.`name` AS `system`,`c`.`display_name` AS `system_name`,`b`.`name` AS `name`,`b`.`num_proc` AS `num_proc`,`b`.`mem_total` AS `mem_total`,`b`.`swap_total` AS `swap_total` from (`nodes` `b` join `cluster` `c` on((`b`.`cluster_id` = `c`.`id`))));

DROP TABLE IF EXISTS `q`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `q` AS (select `c`.`name` AS `system`,`c`.`display_name` AS `system_name`,`b`.`name` AS `queue`,`b`.`display_name` AS `queue_name`,`b`.`cpu` AS `cpu`,`b`.`ram` AS `ram`,`b`.`scratch` AS `scratch`,`b`.`gpu` AS `gpu`,`a`.`cqload` AS `cqload`,`a`.`used` AS `used`,(`a`.`used` / `a`.`total`) AS `used_p`,`a`.`res` AS `res`,`a`.`avail` AS `avail`,`a`.`total` AS `total`,`a`.`aoacds` AS `aoacds`,`a`.`cdsue` AS `cdsue`,`a`.`recorded` AS `recorded` from ((`q_occupancy` `a` join `queue` `b` on((`a`.`queue_id` = `b`.`id`))) join `cluster` `c` on((`b`.`cluster_id` = `c`.`id`))));

DROP TABLE IF EXISTS `queue_details`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `queue_details` AS (select `c`.`name` AS `system`,`c`.`display_name` AS `system_name`,`b`.`name` AS `name`,`b`.`display_name` AS `display_name`,`b`.`cpu` AS `cpu`,`b`.`ram` AS `ram`,`b`.`scratch` AS `scratch`,`b`.`gpu` AS `gpu` from (`queue` `b` join `cluster` `c` on((`b`.`cluster_id` = `c`.`id`))));

-- 2016-10-10 19:08:19
