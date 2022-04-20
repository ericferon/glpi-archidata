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

if (strpos($_SERVER['PHP_SELF'],"dropdownTypeArchidata.php")) {
	$AJAX_INCLUDE=1;
	include (Plugin::getPhpDir("archidata")."/inc/includes.php");
	header("Content-Type: text/html; charset=UTF-8");
	header_nocache();
}

Session::checkCentralAccess();

// Make a select box
if (isset($_POST["swcomponenttype"])) {
   $used = array();

   // Clean used array
   if (isset($_POST['used']) && is_array($_POST['used']) && (count($_POST['used']) > 0)) {
      $query = "SELECT `id`
                FROM `glpi_plugin_archidata_dataelements`
                WHERE `id` IN (".implode(',',$_POST['used']).")
                      AND `plugin_archidata_dataelementtypes_id` = '".$_POST["dataelementtype"]."'";

      foreach ($DB->request($query) AS $data) {
         $used[$data['id']] = $data['id'];
      }
   }

   Dropdown::show('PluginArchidaraDataelement',
                  ['name'      => $_POST['myname'],
					'used'      => $used,
                    'width'     => '50%',
                    'entity'    => $_POST['entity'],
                    'rand'      => $_POST['rand'],
                    'condition' => ["glpi_plugin_archidata_dataelements.plugin_archidata_dataelementtypes_id"=>$_POST["dataelementtype"]]]);
}
?>
