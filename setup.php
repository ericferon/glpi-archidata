<?php
/*
 -------------------------------------------------------------------------
 Archidata plugin for GLPI
 Copyright (C) 2009-2018 by Eric Feron.
 -------------------------------------------------------------------------

 LICENSE
      
 This file is part of Archidata.

 Archidata is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 at your option any later version.

 Archidata is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Archidata. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

// Init the hooks of the plugins -Needed
function plugin_init_archidata() {
	global $PLUGIN_HOOKS;
   
    $PLUGIN_HOOKS['csrf_compliant']['archidata'] = true;
   $PLUGIN_HOOKS['change_profile']['archidata'] = array('PluginArchidataProfile', 'initProfile');
   $PLUGIN_HOOKS['assign_to_ticket']['archidata'] = true;
   
   $PLUGIN_HOOKS['assign_to_ticket_dropdown']['archidata'] = true;
   $PLUGIN_HOOKS['assign_to_ticket_itemtype']['archidata'] = array('PluginArchidataDataelement_Item');
   
   Plugin::registerClass('PluginArchidataDataelement', array(
         'linkgroup_tech_types'   => true,
         'linkuser_tech_types'    => true,
         'document_types'         => true,
         'ticket_types'           => true,
         'helpdesk_visible_types' => true//,
//         'addtabon'               => 'Supplier'
   ));
   Plugin::registerClass('PluginArchidataProfile',
                         array('addtabon' => 'Profile'));
                         
// Add links to other plugins
   $types = array('PluginArchimapGraph');
   foreach ($types as $itemtype) {
      if (class_exists($itemtype)) {
         $itemtype::registerType('PluginArchidataDataelement');
      }
   }
// Add other plugin associations
   $associatedtypes = array(
                    'PluginDatabasesDatabase',
					'PluginArchiswSwcomponent',
                    'PluginArchidataDataelement'/*,
					'PluginWebapplicationsWebapplication'*/);
   if (class_exists('PluginArchidataDataelement'))
	  foreach ($associatedtypes as $itemtype) {
		if (class_exists($itemtype)) {
			$itemtype::registerType('PluginArchidataDataelement');
		}
	  }

   if (Session::getLoginUserID()) {

      $plugin = new Plugin();
      if (Session::haveRight("plugin_archidata", READ)) {

         $PLUGIN_HOOKS['menu_toadd']['archidata'] = array('assets'   => 'PluginArchidataMenu');
      }

      if (Session::haveRight("plugin_archidata", UPDATE)) {
         $PLUGIN_HOOKS['use_massive_action']['archidata']=1;
      }

      if (class_exists('PluginArchidataDataelement_Item')) { // only if plugin activated
         $PLUGIN_HOOKS['plugin_datainjection_populate']['archidata'] = 'plugin_datainjection_populate_archidata';
      }
      // End init, when all types are registered
      $PLUGIN_HOOKS['post_init']['archidata'] = 'plugin_archidata_postinit';

      // Import from Data_Injection plugin
      $PLUGIN_HOOKS['migratetypes']['archidata'] = 'plugin_datainjection_migratetypes_archidata';
   }
}

// Get the name and the version of the plugin - Needed
function plugin_version_archidata() {

	return array (
		'name' => _n('Data structure', 'Data structures', 2, 'archidata'),
		'version' => '1.0.11',
		'author'=>'Eric Feron',
        'license' => 'GPLv2+',
        'homepage'=>'https://github.com/ericferon/glpi-archidata',
        'requirements' => [
         'glpi' => [
            'min' => '10.0',
            'dev' => false
         ]
      ]
	);

}

// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_archidata_check_prerequisites() {
	global $DB;
   if (version_compare(GLPI_VERSION, '10.0', 'lt')
       || version_compare(GLPI_VERSION, '10.1', 'ge')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '10.0');
      }
      return false;
   }
   return true;
}

// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function plugin_archidata_check_config() {
	return true;
}

/*function plugin_archidata_haveRight($module,$right) {
	$matches=array(
			""  => array("","r","w"), // ne doit pas arriver normalement
			"r" => array("r","w"),
			"w" => array("w"),
			"1" => array("1"),
			"0" => array("0","1"), // ne doit pas arriver non plus
		      );
	if (isset($_SESSION["glpi_plugin_archidata_profile"][$module])&&in_array($_SESSION["glpi_plugin_archidata_profile"][$module],$matches[$right]))
		return true;
	else return false;
}
*/
function plugin_datainjection_migratetypes_archidata($types) {
   $types[9991] = 'PluginArchidataDataelement';
   return $types;
}

?>
