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

function plugin_archidata_install() {
   global $DB;
   
   include_once (Plugin::getPhpDir("archidata")."/inc/profile.class.php");
   
   $update=false;
   if (!$DB->TableExists("glpi_plugin_archidata_dataelements")) {
      
      $DB->runFile(Plugin::getPhpDir("archidata")."/sql/empty-1.0.2.sql");

   } else {
		if ($DB->TableExists("glpi_plugin_archidata_dataelements") && (!$DB->FieldExists("glpi_plugin_archidata_dataelements","plugin_archidata_masterswcomponents_id"))) {
			$DB->runFile(Plugin::getPhpDir("archidata")."/sql/update-1.0.1.sql");
		}
		$DB->runFile(Plugin::getPhpDir("archidata")."/sql/update-1.0.2.sql");
   }
   if ($DB->TableExists("glpi_plugin_archidata_profiles")) {
   
      $notepad_tables = array('glpi_plugin_archidata_dataelements');

      foreach ($notepad_tables as $t) {
         // Migrate data
         if ($DB->FieldExists($t, 'notepad')) {
            $query = "SELECT id, notepad
                      FROM `$t`
                      WHERE notepad IS NOT NULL
                            AND notepad <>'';";
            foreach ($DB->request($query) as $data) {
               $iq = "INSERT INTO `glpi_notepads`
                             (`itemtype`, `items_id`, `content`, `date`, `date_mod`)
                      VALUES ('PluginArchidataDataelement', '".$data['id']."',
                              '".addslashes($data['notepad'])."', NOW(), NOW())";
               $DB->queryOrDie($iq, "0.85 migrate notepad data");
            }
            $query = "ALTER TABLE `glpi_plugin_archidata_dataelements` DROP COLUMN `notepad`;";
            $DB->query($query);
         }
      }
   }
   
   if ($update) {
      $query_="SELECT *
            FROM `glpi_plugin_archidata_profiles` ";
      $result_=$DB->query($query_);
      if ($DB->numrows($result_)>0) {

         while ($data=$DB->fetch_array($result_)) {
            $query="UPDATE `glpi_plugin_archidata_profiles`
                  SET `profiles_id` = '".$data["id"]."'
                  WHERE `id` = '".$data["id"]."';";
            $result=$DB->query($query);

         }
      }

      $query="ALTER TABLE `glpi_plugin_archidata_profiles`
               DROP `name` ;";
      $result=$DB->query($query);

   }

   PluginArchidataProfile::initProfile();
   PluginArchidataProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);
   $migration = new Migration("2.0.0");
   $migration->dropTable('glpi_plugin_archidata_profiles');
   
   return true;
}

function plugin_archidata_uninstall() {
	global $DB;

   include_once (Plugin::getPhpDir("archidata")."/inc/profile.class.php");
   include_once (Plugin::getPhpDir("archidata")."/inc/menu.class.php");
   
	$tables = array("glpi_plugin_archidata_dataelements",
					"glpi_plugin_archidata_dataelements_items",
					"glpi_plugin_archidata_profiles",
					"glpi_plugin_archidata_dataelementcardinalities",
					"glpi_plugin_archidata_dataelementclassifications",
					"glpi_plugin_archidata_dataelementtypes");

	foreach($tables as $table)
		$DB->query("DROP TABLE IF EXISTS `$table`;");
   
	$views = ["glpi_plugin_archidata_masterswcomponents"];
				
	foreach($views as $view)
		$DB->query("DROP VIEW IF EXISTS `$view`;");

	$tables_glpi = array("glpi_displaypreferences",
					"glpi_documents_items",
					"glpi_bookmarks",
					"glpi_logs",
                    "glpi_items_tickets",
                    "glpi_notepads",
                    "glpi_dropdowntranslations");

	foreach($tables_glpi as $table_glpi)
		$DB->query("DELETE FROM `$table_glpi` WHERE `itemtype` LIKE 'PluginArchidata%';");

    if (class_exists('PluginDatainjectionModel')) {
      PluginDatainjectionModel::clean(array('itemtype'=>'PluginArchidataDataelement'));
    }

    //Delete rights associated with the plugin
    $profileRight = new ProfileRight();
   foreach (PluginArchidataProfile::getAllRights() as $right) {
      $profileRight->deleteByCriteria(array('name' => $right['field']));
   }
   PluginArchidataMenu::removeRightsFromSession();
   PluginArchidataProfile::removeRightsFromSession();
   
	return true;
}

function plugin_archidata_postinit() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['item_purge']['archidata'] = array();

   foreach (PluginArchidataDataelement::getTypes(true) as $type) {

      $PLUGIN_HOOKS['item_purge']['archidata'][$type]
         = array('PluginArchidataDataelement_Item','cleanForItem');

      CommonGLPI::registerStandardTab($type, 'PluginArchidataDataelement_Item');
   }
}

// Define dropdown relations
function plugin_archidata_getDatabaseRelations() {

	$plugin = new Plugin();

	if ($plugin->isActivated("archidata"))
		return array("glpi_plugin_archidata_dataelements"=>array("glpi_plugin_archidata_dataelements_items"=>"plugin_archidata_dataelements_id"),
					 "glpi_plugin_archidata_dataelementtypes"=>array("glpi_plugin_archidata_dataelements"=>"plugin_archidata_dataelementtypes_id"),
					 "glpi_plugin_archidata_dataelementcardinalities"=>array("glpi_plugin_archidata_dataelements"=>"plugin_archidata_dataelementcardinalities_id"),
					 "glpi_entities"=>array("glpi_plugin_archidata_dataelements"=>"entities_id"),
					 "glpi_groups"=>array("glpi_plugin_archidata_dataelements"=>"groups_id"),
					 "glpi_users"=>array("glpi_plugin_archidata_dataelements"=>"users_id")
					 );
	else
		return array();
}

// Define Dropdown tables to be managed in GLPI :
function plugin_archidata_getDropdown() {

	$plugin = new Plugin();

	if ($plugin->isActivated("archidata"))
		return array('PluginArchidataDataelementCardinality'=>__('Cardinality', 'archidata'),
                'PluginArchidataDataelementType'=>__('Type', 'archidata')
               );
	else
		return array();
}

function plugin_archidata_AssignToTicket($types) {

	if (Session::haveRight("plugin_archidata_open_ticket","1"))
		$types['PluginArchidataDataelement']=PluginArchidataDataelement::getTypeName(2);

	return $types;
}

////// SEARCH FUNCTIONS ///////() {

function plugin_archidata_getAddSearchOptions($itemtype) {
	global $LANG;
    
   $sopt=array();

    if (in_array($itemtype, PluginArchidataDataelement::getTypes(true))) {
        if (Session::haveRight("plugin_archidata", READ)) {
    
            $sopt[2510]['table']='glpi_plugin_archidata_dataelements';
            $sopt[2510]['field']='name';
            $sopt[2510]['name']=PluginArchidataDataelement::getTypeName(2)." - ".__('Name');
            $sopt[2510]['forcegroupby']  = true;
            $sopt[2510]['datatype']='itemlink';
            $sopt[2510]['itemlink_type']='PluginArchidataDataelement';
            $sopt[2510]['massiveaction'] = false;
            $sopt[2510]['joinparams']    = array('beforejoin'
                                                => array('table'      => 'glpi_plugin_archidata_dataelements_items',
                                                         'joinparams' => array('jointype' => 'itemtype_item')));
        }
    }
	
	return $sopt;
}

//for search
function plugin_archidata_addLeftJoin($type,$ref_table,$new_table,$linkfield,&$already_link_tables) {

	switch ($new_table) {

		case "glpi_plugin_archidata_dataelements_items" : //from archidata list
			return " LEFT JOIN `glpi_plugin_archidata_dataelements_items` ON (`$ref_table`.`id` = `glpi_plugin_archidata_dataelements_items`.`items_id` AND `glpi_plugin_archidata_dataelements_items`.`itemtype`= '$type') ";
			break;
		case "glpi_plugin_archidata_dataelements" : // From items
			$out=Search::addLeftJoin($type,$ref_table,$already_link_tables,"glpi_plugin_archidata_dataelements_items","plugin_archidata_dataelements_id");
			$out.= " LEFT JOIN `glpi_plugin_archidata_dataelements` ON (`glpi_plugin_archidata_dataelements`.`id` = `glpi_plugin_archidata_dataelements_items`.`plugin_archidata_dataelements_id`) ";
			return $out;
			break;
/*		case "glpi_plugin_archidata_dataelementtypes" : // From items
			$out=Search::addLeftJoin($type,$ref_table,$already_link_tables,"glpi_plugin_archidata_dataelements","plugin_archidata_dataelements_id");
			$out.= " LEFT JOIN `glpi_plugin_archidata_dataelementtypes` ON (`glpi_plugin_archidata_dataelementtypes`.`id` = `glpi_plugin_archidata_dataelements`.`plugin_archidata_dataelementtypes_id`) ";
			return $out;
			break;
*/	}

	return "";
}

//force groupby for multible links to items
/*
function plugin_archidata_forceGroupBy($type) {

	return true;
	switch ($type) {
		case 'PluginArchidataDataelement':
			return true;
			break;

	}
	return false;
}
*/
//display custom fields in the search
/*function plugin_archidata_giveItem($type,$ID,$data,$num) {
	global $CFG_GLPI,$LANG,$DB;

	$searchopt=&Search::getOptions($type);
	$table=$searchopt[$ID]["table"];
	$field=$searchopt[$ID]["field"];

	switch ($table.'.'.$field) {
		//display associated items with archidata
		case "glpi_plugin_archidata_dataelements_items.items_id" :
			$query_device = "SELECT DISTINCT `itemtype`
							FROM `glpi_plugin_archidata_dataelements_items`
							WHERE `plugin_archidata_dataelements_id` = '".$data['id']."'
							ORDER BY `itemtype`";
			$result_device = $DB->query($query_device);
			$number_device = $DB->numrows($result_device);
			$out='';
			$archidata=$data['id'];
			if ($number_device > 0) {
				for ($i=0 ; $i < $number_device ; $i++) {
					$column = "name";
					$itemtype = $DB->result($result_device, $i, "itemtype");
					
					if (!class_exists($itemtype)) {
                  continue;
               }
               $item = new $itemtype();
					if ($item->canView()) {
                  $table_item = getTableForItemType($itemtype);

                  if ($itemtype!='Entity') {
                     $query = "SELECT `".$table_item."`.*, `glpi_plugin_archidata_dataelements_items`.`id` AS table_items_id, `glpi_entities`.`id` AS entity "
              ." FROM `glpi_plugin_archidata_dataelements_items`, `".$table_item
              ."` LEFT JOIN `glpi_entities` ON (`glpi_entities`.`id` = `".$table_item."`.`entities_id`) "
              ." WHERE `".$table_item."`.`id` = `glpi_plugin_archidata_dataelements_items`.`items_id`
              AND `glpi_plugin_archidata_dataelements_items`.`itemtype` = '$itemtype'
              AND `glpi_plugin_archidata_dataelements_items`.`plugin_archidata_dataelements_id` = '".$archidata."' "
              . getEntitiesRestrictRequest(" AND ",$table_item,'','',$item->maybeRecursive());

                     if ($item->maybeTemplate()) {
                        $query.=" AND ".$table_item.".is_template='0'";
                     }
                     $query.=" ORDER BY `glpi_entities`.`completename`, `".$table_item."`.`$column` ";
                  } else {
                     $query = "SELECT `".$table_item."`.*, `glpi_plugin_archidata_dataelements_items`.`id` AS table_items_id "
              ." FROM `glpi_plugin_archidata_dataelements_items`, `".$table_item
              ."` WHERE `".$table_item."`.`id` = `glpi_plugin_archidata_dataelements_items`.`items_id`
              AND `glpi_plugin_archidata_dataelements_items`.`itemtype` = '$itemtype'
              AND `glpi_plugin_archidata_dataelements_items`.`plugin_archidata_dataelements_id` = '".$archidata."' "
              . getEntitiesRestrictRequest(" AND ",$table_item,'','',$item->maybeRecursive());

                     if ($item->maybeTemplate()) {
                        $query.=" AND ".$table_item.".is_template='0'";
                     }
                     $query.=" ORDER BY `glpi_entities`.`completename`, `".$table_item."`.`$column` ";
                  }
        
               if ($result_linked=$DB->query($query))
                  if ($DB->numrows($result_linked)) {
                     $item = new $itemtype();
                     while ($data=$DB->fetchAssoc($result_linked)) {
                        if ($item->getFromDB($data['id'])) {
                           $out .= $item->getTypeName()." - ".$item->getLink()."<br>";
                        }
                     }
                  } else
                     $out.=' ';
               } else
                  $out.=' ';
				}
			}
		return $out;
		break;
	}
	return "";
}
*/

////// SPECIFIC MODIF MASSIVE FUNCTIONS ///////

function plugin_archidata_MassiveActions($type) {
  
	switch ($type) {
		case 'PluginArchidataDataelement':
			return array(
				// association with glpi items
				"plugin_archidata_install"=>__('Associate', 'archidata'),
				"plugin_archidata_desinstall"=>__('Dissociate', 'archidata'),
				//tranfer archidata to another entity
				"plugin_archidata_transfert"=>__('Transfer'),
				//duplicate a dataelement
				"plugin_archidata_duplicate"=>__('Duplicate', 'archidata'),
				);
			break;
		default:
			//adding archidata from items lists
			if (in_array($type, PluginArchidataDataelement::getTypes(true))) {
				return array("plugin_archidata_add_item"=>__('Associate to the Data Structure', 'archidata'));
			}
			break;
	}
	return array();
}

function plugin_datainjection_populate_archidata() {
   global $INJECTABLE_TYPES;
   $INJECTABLE_TYPES['PluginArchidataDataelementInjection'] = 'archidata';
}

?>
