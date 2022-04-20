
-- -----------------------------------------------------
-- Table `glpi_plugin_archidata_dataelements`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archidata_dataelements`;
CREATE TABLE `glpi_plugin_archidata_dataelements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entities_id` int(11) NOT NULL DEFAULT '0',
  `is_recursive` tinyint(1) NOT NULL default '0',
  `plugin_archidata_dataelements_id` int(11) NOT NULL DEFAULT '0',
  `completename` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `comment` text COLLATE utf8_unicode_ci,
  `level` int(11) NOT NULL DEFAULT '0',
  `sons_cache` longtext COLLATE utf8_unicode_ci,
  `ancestors_cache` longtext COLLATE utf8_unicode_ci,
  `plugin_archidata_dataelementtypes_id` INT(11) NOT NULL default '0' COMMENT 'dataelement type : Boolean, Text, ...' ,
  `plugin_archidata_dataelementcardinalities_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'dataelement cardinality : 0, 0 or more, ...',
  `plugin_archidata_dataelementclassifications_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'dataelement classification : public, restricted, confidential, ...',
  `plugin_archidata_masterswcomponents_id` INT(11) NOT NULL default '0' COMMENT 'master swcomponent',
  `address` VARCHAR(255) DEFAULT NULL COMMENT 'dataelement URL',
  `users_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_users (id)',
  `groups_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_groups (id)',
  `date_mod` datetime default NULL,
  `is_helpdesk_visible` int(11) NOT NULL default '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unicity` (`plugin_archidata_dataelements_id`,`name`),
  KEY `plugin_archidata_dataelementcardinalities_id` (`plugin_archidata_dataelementcardinalities_id`),
  KEY `plugin_archidata_dataelementtypes_id` (`plugin_archidata_dataelementtypes_id`),
  KEY `users_id` (`users_id`),
  KEY `groups_id` (`groups_id`),
  KEY `plugin_archidata_masterswcomponents_id` (`plugin_archidata_masterswcomponents_id`),
  KEY date_mod (date_mod),
  KEY is_helpdesk_visible (is_helpdesk_visible),
  KEY `is_deleted` (`is_deleted`),
  KEY `plugin_archidata_dataelements_id` (`plugin_archidata_dataelements_id`)
) ENGINE=MyISAM AUTO_INCREMENT=756 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- ----------------------------------------------------------------
-- Table `glpi_plugin_archidata_dataelements_items`
-- ----------------------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archidata_dataelements_items`;
CREATE TABLE `glpi_plugin_archidata_dataelements_items` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`plugin_archidata_dataelements_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugin_archidata_dataelements (id)',
	`items_id` int(11) NOT NULL default '0' COMMENT 'RELATION to various tables, according to itemtype (id)',
   `itemtype` varchar(100) collate utf8_unicode_ci NOT NULL COMMENT 'see .class.php file',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `unicity` (`plugin_archidata_dataelements_id`,`items_id`,`itemtype`),
  KEY `FK_device` (`items_id`,`itemtype`),
  KEY `item` (`itemtype`,`items_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- -----------------------------------------------------
-- Table `glpi_plugin_archidata_profiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archidata_profiles`;
CREATE TABLE `glpi_plugin_archidata_profiles` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`profiles_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_profiles (id)',
	`archidata` char(1) collate utf8_unicode_ci default NULL,
	`open_ticket` char(1) collate utf8_unicode_ci default NULL,
	PRIMARY KEY  (`id`),
	KEY `profiles_id` (`profiles_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- -----------------------------------------------------
-- Table `glpi_plugin_archidata_dataelementtypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archidata_dataelementtypes`;
CREATE  TABLE `glpi_plugin_archidata_dataelementtypes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NOT NULL ,
  `comment` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `glpi_plugin_archidata_dataelementtype_name` (`name` ASC) )
ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archidata_dataelementtypes` ( `id` , `name` , `comment` )  VALUES (1,'Structure','');
INSERT INTO `glpi_plugin_archidata_dataelementtypes` ( `id` , `name` , `comment` )  VALUES (2,'Character String','');
INSERT INTO `glpi_plugin_archidata_dataelementtypes` ( `id` , `name` , `comment` )  VALUES (3,'Integer Nbr','');
INSERT INTO `glpi_plugin_archidata_dataelementtypes` ( `id` , `name` , `comment` )  VALUES (4,'Decimal Nbr','');
INSERT INTO `glpi_plugin_archidata_dataelementtypes` ( `id` , `name` , `comment` )  VALUES (5,'Boolean','');

-- -----------------------------------------------------
-- Table `plugin_archidata_dataelementcardinalities`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archidata_dataelementcardinalities`;
CREATE TABLE `glpi_plugin_archidata_dataelementcardinalities` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) collate utf8_unicode_ci default NULL,
   `comment` text collate utf8_unicode_ci,
	PRIMARY KEY  (`id`),
	KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archidata_dataelementcardinalities` VALUES ('1', '1','');
INSERT INTO `glpi_plugin_archidata_dataelementcardinalities` VALUES ('2', '1 or more','');
INSERT INTO `glpi_plugin_archidata_dataelementcardinalities` VALUES ('3', '0 or 1','');
INSERT INTO `glpi_plugin_archidata_dataelementcardinalities` VALUES ('4', '0 or more','');

-- -----------------------------------------------------
-- Table `plugin_archidata_dataelementclassifications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `glpi_plugin_archidata_dataelementclassifications`;
CREATE TABLE `glpi_plugin_archidata_dataelementclassifications` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) collate utf8_unicode_ci default NULL,
   `comment` text collate utf8_unicode_ci,
	PRIMARY KEY  (`id`),
	KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `glpi_plugin_archidata_dataelementclassifications` VALUES ('1', 'Public','');
INSERT INTO `glpi_plugin_archidata_dataelementclassifications` VALUES ('2', 'Restricted','');

INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchidataDataelement','2','2','0');
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchidataDataelement','6','3','0');
INSERT INTO `glpi_displaypreferences` VALUES (NULL,'PluginArchidataDataelement','7','4','0');
	
-- -----------------------------------------------------
-- View `glpi_plugin_archidata_masterswcomponents`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `glpi_plugin_archidata_masterswcomponents`;
CREATE OR REPLACE VIEW `glpi_plugin_archidata_masterswcomponents` (`id`, `entities_id`, `name`, `comment`) AS
SELECT `id`, `entities_id`, `completename`, `comment` from `glpi_plugin_archisw_swcomponents` where level = '1';

