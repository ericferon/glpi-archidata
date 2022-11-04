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

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginArchidataDataelement extends CommonTreeDropdown {

   public $dohistory=true;
   static $rightname = "plugin_archidata";
   protected $usenotepad         = true;
   
   static $types = array('Software', 'Project');

   static function getTypeName($nb=0) {

      return _n('Data structure', 'Data structures', $nb, 'archidata');
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

   switch ($item->getType()) {
        case 'Supplier' :
//      if ($item->getType()=='Supplier') {
			if ($_SESSION['glpishow_count_on_tabs']) {
				return self::createTabEntry(self::getTypeName(2), self::countForItem($item));
			}
			return self::getTypeName(2);
        case 'PluginArchidataDataelement' :
			return $this->getTypeName(Session::getPluralNumber());
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      switch ($item->getType()) {
        case 'Supplier' :
//      if ($item->getType()=='Supplier') {
			$self = new self();
			$self->showPluginFromSupplier($item->getField('id'));
            break;
        case 'PluginArchidataDataelement' :
            $item->showChildren();
            break;
      }
      return true;
   }

   static function countForItem(CommonDBTM $item) {

      $dbu = new DbUtils();
      return $dbu->countElementsInTable('glpi_plugin_archidata_dataelements',
                                  "`suppliers_id` = '".$item->getID()."'");
   }

   //clean if dataelement are deleted
   function cleanDBonPurge() {

//      $temp = new PluginArchidataDataelement_Item();
//      $temp->deleteByCriteria(array('plugin_archidata_dataelements_id' => $this->fields['id']));
   }

   // search fields from GLPI 9.3 on
   function rawSearchOptions() {

      $tab = [];
      if (version_compare(GLPI_VERSION,'9.2','le')) return $tab;

      $tab[] = [
         'id'   => 'common',
         'name' => self::getTypeName(2)
      ];

      $tab[] = [
         'id'            => '1',
         'table'         => $this->getTable(),
         'field'         => 'name',
         'name'          => __('Name'),
         'datatype'      => 'itemlink',
         'itemlink_type' => $this->getType()
      ];

      $tab[] = [
         'id'       => '2',
         'table'    => $this->getTable(),
         'field'    => 'level',
         'name'     => __('Level'),
         'datatype' => 'text'
      ];

      $tab[] = [
         'id'       => '4',
         'table'    => 'glpi_plugin_archidata_masterswcomponents',
         'field'    => 'name',
//         'name'     => PluginArchidataMasterSwcomponent::getTypeName(1),
         'name'      => __('Master application', 'archidata'),
         'datatype' => 'dropdown'
      ];

      $tab[] = [
         'id'        => '5',
         'table'     => 'glpi_plugin_archidata_dataelementtypes',
         'field'     => 'name',
         'linkfield' => 'plugin_archidata_dataelementtypes_id',
         'name'      => __('Type'),
         'datatype'  => 'dropdown'
      ];

      $tab[] = [
         'id'        => '6',
         'table'     => 'glpi_plugin_archidata_dataelementclassifications',
         'field'     => 'name',
         'linkfield' => 'plugin_archidata_dataelementclassifications_id',
         'name'      => __('Classification level', 'archidata'),
         'datatype'  => 'dropdown'
      ];

      $tab[] = [
         'id'        => '11',
         'table'     => 'glpi_users',
         'field'     => 'name',
         'linkfield' => 'users_id',
         'name'      => __('Data Expert', 'archidata'),
         'datatype'  => 'dropdown',
         'right'     => 'interface'
      ];

      $tab[] = [
         'id'        => '12',
         'table'     => 'glpi_groups',
         'field'     => 'name',
         'linkfield' => 'groups_id',
         'name'      => __('Data Owner', 'archidata'),
         'condition' => '`is_assign`',
         'datatype'  => 'dropdown'
      ];

      $tab[] = [
         'id'            => '16',
         'table'         => $this->getTable(),
         'field'         => 'date_mod',
         'massiveaction' => false,
         'name'          => __('Last update'),
         'datatype'      => 'datetime'
      ];

      $tab[] = [
         'id'            => '71',
         'table'         => 'glpi_plugin_archidata_dataelements_items',
         'field'         => 'items_id',
         'nosearch'      => true,
         'massiveaction' => false,
         'name'          => _n('Associated item', 'Associated items', 2),
         'forcegroupby'  => true,
         'joinparams'    => [
            'jointype' => 'child'
         ]
      ];

      $tab[] = [
         'id'            => '72',
         'table'         => $this->getTable(),
         'field'         => 'id',
         'name'          => __('ID'),
         'datatype'      => 'number'
      ];

      $tab[] = [
         'id'            => '80',
         'table'         => $this->getTable(),
         'field'    => 'completename',
         'name'     => __('Data Structure', 'archidata'),
         'datatype' => 'dropdown'
      ];

      $tab[] = [
         'id'    => '81',
         'table' => 'glpi_entities',
         'field' => 'entities_id',
         'name'  => __('Entity') . "-" . __('ID')
      ];

      return $tab;
   }

   //define header form
   function defineTabs($options=array()) {

      $ong = array();
      $this->addDefaultFormTab($ong);
      $this->addStandardTab('PluginArchidataDataelement', $ong, $options);
//      $this->addStandardTab('PluginArchidataDataelement_Item', $ong, $options);
      $this->addImpactTab($ong, $options);
      $this->addStandardTab('Notepad', $ong, $options);
      $this->addStandardTab('Log', $ong, $options);

      return $ong;
   }

   /*
    * Return the SQL command to retrieve linked object
    *
    * @return a SQL command which return a set of (itemtype, items_id)
    */
/*   function getSelectLinkedItem () {
      return "SELECT `itemtype`, `items_id`
              FROM `glpi_plugin_archidata_dataelements_items`
              WHERE `plugin_archidata_dataelements_id`='" . $this->fields['id']."'";
   }
*/
   function showForm ($ID, $options=array()) {

      $this->initForm($ID, $options);
      $this->showFormHeader($options);

      echo "<tr class='tab_bg_1'>";
      //name of dataelement
      echo "<td>".__('Name')."</td>";
      echo "<td>";
      echo Html::input('name',['value' => $this->fields['name'], 'id' => "name" , 'size' => 50]);
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //completename of dataelement
      echo "<td>".__('As child of').": </td>";
      echo "<td>";
      Dropdown::show('PluginArchidataDataelement', array('value' => $this->fields["plugin_archidata_dataelements_id"]));
      echo "</td>";
      //level of dataelement
      echo "<td>".__('Level').": </td>";
      echo "<td>";
      echo Html::input('level',['value' => $this->fields['level'], 'id' => "level" , 'size' => 2, 'readonly' => true]);
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //description of dataelement
      echo "<td>".__('Description').":	</td>";
      echo "<td class='top center' colspan='6'>";
      echo Html::input('description',['value' => $this->fields['description'], 'id' => "description" , 'width' => "100%"]);
      echo "</td>";
      echo "</tr>";
      echo "<tr class='tab_bg_1'>";
      //comment about dataelement
      echo "<td>".__('Comment').":	</td>";
      echo "<td class='top center' colspan='5'><textarea cols='100' rows='6' name='comment' >".$this->fields["comment"]."</textarea>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //type
      echo "<td>".__('Type')."</td><td>";
      Dropdown::show('PluginArchidataDataelementType', array('value' => $this->fields["plugin_archidata_dataelementtypes_id"]));
      echo "</td>";
      //cardinality
      echo "<td>".__('Cardinality', 'archidata')."</td><td>";
      Dropdown::show('PluginArchidataDataelementCardinality', array('value' => $this->fields["plugin_archidata_dataelementcardinalities_id"]));
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //master application
      echo "<td>".__('Master application', 'archidata').": </td><td>";
      Dropdown::show('PluginArchidataMasterSwcomponent', ['name' => "plugin_archidata_masterswcomponents_id", 'value' => $this->fields["plugin_archidata_masterswcomponents_id"],'entity' => $this->fields["entities_id"]]);
      echo "</td>";
      //classification
      echo "<td>".__('Classification level', 'archidata')."</td><td>";
      Dropdown::show('PluginArchidataDataelementClassification', array('value' => $this->fields["plugin_archidata_dataelementclassifications_id"]));
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //url of data 
      echo "<td>";
	  echo Html::link(__('URL'), $this->fields["address"]);
      echo "</td>";
      echo "<td>";
      echo Html::input('address',['value' => $this->fields['address'], 'id' => "address" , 'size' => 50]);
      echo "</td>";
      //is_helpdesk_visible
      echo "<td>" . __('Associable to a ticket') . "</td><td>";
      Dropdown::showYesNo('is_helpdesk_visible', $this->fields['is_helpdesk_visible']);
      echo "</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      //groups
      echo "<td>".__('Data Owner', 'archidata')."</td><td>";
      Group::dropdown(['name' => "groups_id", 'value' => $this->fields["groups_id"], 'entity' => $this->fields["entities_id"], 'right' => 'interface']);
      echo "</td>";
      //users
      echo "<td>".__('Data Expert', 'archidata')."</td><td>";
      User::dropdown(['name' => "users_id", 'value' => $this->fields["users_id"], 'entity' => $this->fields["entities_id"], 'right' => 'interface']);
      echo "</td>";
      echo "</tr>";



      $this->showFormButtons($options);

      return true;
   }
   
   /**
    * Make a select box for link archidata
    *
    * Parameters which could be used in options array :
    *    - name : string / name of the select (default is plugin_dataflows_dataflowtypes_id)
    *    - entity : integer or array / restrict to a defined entity or array of entities
    *                   (default -1 : no restriction)
    *    - used : array / Already used items ID: not to display in dropdown (default empty)
    *
    * @param $options array of possible options
    *
    * @return nothing (print out an HTML select box)
   **/
   static function dropdownArchidata($options=array()) {
      global $DB, $CFG_GLPI;


      $p['name']    = 'plugin_archidata_dataelements_id';
      $p['entity']  = '';
      $p['used']    = array();
      $p['display'] = true;

      if (is_array($options) && count($options)) {
         foreach ($options as $key => $val) {
            $p[$key] = $val;
         }
      }

      $where = " WHERE `glpi_plugin_archidata_dataelements`.`is_deleted` = '0' ".
                       getEntitiesRestrictRequest("AND", "glpi_plugin_archidata_dataelements", '', $p['entity'], true);

      $p['used'] = array_filter($p['used']);
      if (count($p['used'])) {
         $where .= " AND `id` NOT IN (0, ".implode(",",$p['used']).")";
      }

      $query = "SELECT *
                FROM `glpi_plugin_archidata_dataelementtypes`
                WHERE `id` IN (SELECT DISTINCT `plugin_archidata_dataelementtypes_id`
                               FROM `glpi_plugin_archidata_dataelements`
                             $where)
                ORDER BY `name`";
      $result = $DB->query($query);

      $values = array(0 => Dropdown::EMPTY_VALUE);

      while ($data = $DB->fetchAssoc($result)) {
         $values[$data['id']] = $data['name'];
      }
      $rand = mt_rand();
      $out  = Dropdown::showFromArray('_dataflowtype', $values, array('width'   => '30%',
                                                                     'rand'    => $rand,
                                                                     'display' => false));
      $field_id = Html::cleanId("dropdown__dataflowtype$rand");

      $params   = array('dataflowtype' => '__VALUE__',
                        'entity' => $p['entity'],
                        'rand'   => $rand,
                        'myname' => $p['name'],
                        'used'   => $p['used']);

      $out .= Ajax::updateItemOnSelectEvent($field_id,"show_".$p['name'].$rand,
                                            Plugin::getWebDir('archidata')."/ajax/dropdownTypeArchidata.php",
                                            $params, false);
      $out .= "<span id='show_".$p['name']."$rand'>";
      $out .= "</span>\n";

      $params['dataflowtype'] = 0;
      $out .= Ajax::updateItem("show_".$p['name'].$rand,
                               Plugin::getWebDir('archidata')."/plugins/archidata/ajax/dropdownTypeArchidata.php",
                               $params, false);
      if ($p['display']) {
         echo $out;
         return $rand;
      }
      return $out;
   }

   /**
    * For other plugins, add a type to the linkable types
    *
    * @since version 1.3.0
    *
    * @param $type string class name
   **/
   static function registerType($type) {
      if (!in_array($type, self::$types)) {
         self::$types[] = $type;
      }
   }


   /**
    * Type than could be linked to a Rack
    *
    * @param $all boolean, all type, or only allowed ones
    *
    * @return array of types
   **/
   static function getTypes($all=false) {

      if ($all) {
         return self::$types;
      }

      // Only allowed types
      $types = self::$types;

      foreach ($types as $key => $type) {
         if (!class_exists($type)) {
            continue;
         }

         $item = new $type();
         if (!$item->canView()) {
            unset($types[$key]);
         }
      }
      return $types;
   }



   /**
    * @since version 0.85
    *
    * @see CommonDBTM::getSpecificMassiveActions()
   **/
   function getSpecificMassiveActions($checkitem=NULL) {
      $isadmin = static::canUpdate();
      $actions = parent::getSpecificMassiveActions($checkitem);

      if ($_SESSION['glpiactiveprofile']['interface'] == 'central') {
         if ($isadmin) {
            $actions['PluginArchidataDataelement'.MassiveAction::CLASS_ACTION_SEPARATOR.'install']    = _x('button', 'Associate');
            $actions['PluginArchidataDataelement'.MassiveAction::CLASS_ACTION_SEPARATOR.'uninstall'] = _x('button', 'Dissociate');
            $actions['PluginArchidataDataelement'.MassiveAction::CLASS_ACTION_SEPARATOR.'duplicate'] = _x('button', 'Duplicate');

            if (Session::haveRight('transfer', READ)
                     && Session::isMultiEntitiesMode()
            ) {
               $actions['PluginArchidataDataelement'.MassiveAction::CLASS_ACTION_SEPARATOR.'transfer'] = __('Transfer');
            }
         }
      }
      return $actions;
   }
   
   /**
    * @since version 0.85
    *
    * @see CommonDBTM::showMassiveActionsSubForm()
   **/
   static function showMassiveActionsSubForm(MassiveAction $ma) {

      switch ($ma->getAction()) {
         case 'plugin_archidata_add_item':
            self::dropdownDataflow(array());
            echo "&nbsp;".
                 Html::submit(_x('button','Post'), array('name' => 'massiveaction'));
            return true;
            break;
         case "install" :
            Dropdown::showSelectItemFromItemtypes(array('items_id_name' => 'item_item',
                                                        'itemtype_name' => 'typeitem',
                                                        'itemtypes'     => self::getTypes(true),
                                                        'checkright'
                                                                        => true,
                                                  ));
            echo Html::submit(_x('button', 'Post'), array('name' => 'massiveaction'));
            return true;
            break;
         case "uninstall" :
            Dropdown::showSelectItemFromItemtypes(array('items_id_name' => 'item_item',
                                                        'itemtype_name' => 'typeitem',
                                                        'itemtypes'     => self::getTypes(true),
                                                        'checkright'
                                                                        => true,
                                                  ));
            echo Html::submit(_x('button', 'Post'), array('name' => 'massiveaction'));
            return true;
            break;
         case "duplicate" :
		    $options = array();
			$options['value'] = 1;
			$options['min'] = 1;
			$options['max'] = 20;
			$options['unit'] = "times";
            Dropdown::showNumber('repeat', $options);
            echo Html::submit(_x('button','Post'), array('name' => 'massiveaction'));
            return true;
            break;
         case "transfer" :
            Dropdown::show('Entity');
            echo Html::submit(_x('button','Post'), array('name' => 'massiveaction'));
            return true;
            break;
    }
      return parent::showMassiveActionsSubForm($ma);
   }
   
   
   /**
    * @since version 0.85
    *
    * @see CommonDBTM::processMassiveActionsForOneItemtype()
   **/
   static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item,
                                                       array $ids) {
      global $DB;
      
      $dataflow_item = new PluginDataflowsDataflow_Item();
      
      switch ($ma->getAction()) {
         case "plugin_archidata_add_item":
            $input = $ma->getInput();
            foreach ($ids as $id) {
               $input = array('plugin_archidata_dataelementtypes_id' => $input['plugin_archidata_dataelementtypes_id'],
                                 'items_id'      => $id,
                                 'itemtype'      => $item->getType());
               if ($dataflow_item->can(-1,UPDATE,$input)) {
                  if ($dataflow_item->add($input)) {
                     $ma->itemDone($item->getType(), $id, MassiveAction::ACTION_OK);
                  } else {
                     $ma->itemDone($item->getType(), $ids, MassiveAction::ACTION_KO);
                  }
               } else {
                  $ma->itemDone($item->getType(), $ids, MassiveAction::ACTION_KO);
               }
            }

            return;
         case "transfer" :
            $input = $ma->getInput();
            if ($item->getType() == 'PluginArchidataDataelement') {
            foreach ($ids as $key) {
                  $item->getFromDB($key);
                  $type = PluginArchidataDataelementType::transfer($item->fields["plugin_archidata_dataelementtypes_id"], $input['entities_id']);
                  if ($type > 0) {
                     $values["id"] = $key;
                     $values["plugin_archidata_dataelementtypes_id"] = $type;
                     $item->update($values);
                  }

                  unset($values);
                  $values["id"] = $key;
                  $values["entities_id"] = $input['entities_id'];

                  if ($item->update($values)) {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
                  } else {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
                  }
               }
            }
            return;

         case 'install' :
            $input = $ma->getInput();
            foreach ($ids as $key) {
               if ($item->can($key, UPDATE)) {
                  $values = array('plugin_archidata_dataelements_id' => $key,
                                 'items_id'      => $input["item_item"],
                                 'itemtype'      => $input['typeitem']);
                  if ($dataflow_item->add($values)) {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
                  } else {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
                  }
               } else {
                  $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_NORIGHT);
                  $ma->addMessage($item->getErrorMessage(ERROR_RIGHT));
               }
            }
            return;
            
         case 'uninstall':
            $input = $ma->getInput();
            foreach ($ids as $key) {
               if ($val == 1) {
                  if ($dataflow_item->deleteItemByDataelementsAndItem($key,$input['item_item'],$input['typeitem'])) {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_OK);
                  } else {
                     $ma->itemDone($item->getType(), $key, MassiveAction::ACTION_KO);
                  }
               }
            }
            return;

         case "duplicate" :
            $input = $ma->getInput();
            if ($item->getType() == 'PluginArchidataDataelement') {
            foreach ($ids as $key) {
				  $success = array();
				  $failure = array();
                  $item->getFromDB($key);
				  $values = $item->fields;
				  $name = $values["name"];

                  unset($values["id"]);
                  unset($values["sons_cache"]);
				  for ($i = 1 ; $i <= $input['repeat'] ; $i++) {
					$values["name"] = $name . " (Copy $i)";

					if ($item->add($values)) {
						$success[] = $key;
					} else {
						$failure[] = $key;
					}
				  }
				  if ($success) {
				    $ma->itemDone('PluginArchidataDataelement', $key, MassiveAction::ACTION_OK);
				  }
				  if ($failure) {
					$ma->itemDone('PluginArchidataDataelement', $key, MassiveAction::ACTION_KO);
				  }
               }
            }
            return;

      }
      parent::processMassiveActionsForOneItemtype($ma, $item, $ids);
   }
   
}
?>
