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

if (!defined('GLPI_ROOT')){
   die("Sorry. You can't access directly to this file");
}

/**
 * Class PluginDatainjectionDataelementInjection
 */
class PluginDatainjectionDataelementInjection extends PluginArchidataDataelement
   implements PluginDatainjectionInjectionInterface {

   function __construct() {
      //Needed for getSearchOptions !
      $this->table = getTableForItemType('PluginArchidataDataelement');
   }

   function isPrimaryType() {
      return true;
   }

   function connectedTo() {
      return array();
   }

   /**
    * @param string $primary_type
    *
    * @return array
    */
   function getOptions($primary_type = '') {

      $tab = Search::getOptions(get_parent_class($this));

      //Specific to location
      $tab[8]['linkfield'] = 'locations_id';

      //$blacklist = PluginDatainjectionCommonInjectionLib::getBlacklistedOptions();
      //Remove some options because some fields cannot be imported
      $notimportable            = [13, 30, 80];
      $options['ignore_fields'] = $notimportable;
      $options['displaytype']   = ["dropdown"       => [4, 5, 6, 11, 12],
                                   "user"           => [11],
                                   "multiline_text" => [2],
                                   "date"           => [16],
                                   "bool"           => []];

      return PluginDatainjectionCommonInjectionLib::addToSearchOptions($tab, $options, $this);

   }

   /**
    * Standard method to delete an object into glpi
    * WILL BE INTEGRATED INTO THE CORE IN 0.80
    *
    * @param array         $values
    * @param array|options $options
    *
    * @return an
    * @internal param fields $fields to add into glpi
    * @internal param options $options used during creation
    */
   function deleteObject($values = [], $options = []) {
      $lib = new PluginDatainjectionCommonInjectionLib($this, $values, $options);
      $lib->deleteObject();
      return $lib->getInjectionResults();
   }

   /**
    * Standard method to add an object into glpi
    * WILL BE INTEGRATED INTO THE CORE IN 0.80
    *
    * @param array|fields  $values
    * @param array|options $options
    *
    * @return an array of IDs of newly created objects : for example array(Computer=>1, Networkport=>10)
    * @internal param fields $values to add into glpi
    * @internal param options $options used during creation
    */
   function addOrUpdateObject($values = [], $options = []) {
      $lib = new PluginDatainjectionCommonInjectionLib($this, $values, $options);
      $lib->processAddOrUpdate();
      return $lib->getInjectionResults();
   }

}

?>
