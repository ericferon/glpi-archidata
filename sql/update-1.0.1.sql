ALTER TABLE `glpi_plugin_archidata_dataelements`
ADD COLUMN IF NOT EXISTS `plugin_archidata_masterswcomponents_id` INT(11) NOT NULL DEFAULT '0' COMMENT 'master swcomponent' AFTER `plugin_archidata_dataelementclassifications_id`;
-- -----------------------------------------------------
-- View `glpi_plugin_archidata_masterswcomponents`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `glpi_plugin_archidata_masterswcomponents`;
CREATE OR REPLACE VIEW `glpi_plugin_archidata_masterswcomponents` (`id`, `entities_id`, `name`, `comment`) AS
SELECT `id`, `entities_id`, `completename`, `comment` from `glpi_plugin_archisw_swcomponents` where level = '1';

