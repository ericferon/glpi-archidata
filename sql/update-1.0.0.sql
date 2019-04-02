ALTER TABLE `glpi_plugin_archidata_dataelements`
ADD COLUMN IF NOT EXISTS `plugin_archidata_dataelementclassifications_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'dataelement classification : public, restricted, confidential, ...' AFTER `plugin_archidata_dataelementcardinalities_id`,
ADD COLUMN IF NOT EXISTS `address` VARCHAR(255) DEFAULT NULL COMMENT 'dataelement URL' AFTER `plugin_archidata_dataelementclassifications_id`;
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
