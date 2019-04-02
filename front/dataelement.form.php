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

include ('../../../inc/includes.php');

if (!isset($_GET["id"])) $_GET["id"] = "";
if (!isset($_GET["withtemplate"])) $_GET["withtemplate"] = "";

$PluginArchidataDataelement=new PluginArchidataDataelement();
$PluginArchidataDataelement_Item=new PluginArchidataDataelement_Item();

if (isset($_POST["add"])) {

	$PluginArchidataDataelement->check(-1,'w',$_POST);
	if (isset($_POST['plugin_archidata_dataelements_id']) && $_POST['plugin_archidata_dataelements_id'] != '0') {
		// copy parent's value to child
		$PluginArchidataDataelement->getFromDB($_POST['plugin_archidata_dataelements_id']);
		foreach ($PluginArchidataDataelement->fields as $key => $value) {
			if ($key != 'id' && $key != 'sons_cache' && $key != 'ancestors_cache' && !isset($_POST[$key])) {
				$_POST[$key] = $value;
			}
		}
	}
	$newID=$PluginArchidataDataelement->add($_POST);
   if ($_SESSION['glpibackcreated']) {
      Html::redirect($swcomponent->getFormURL()."?id=".$newID);
   }
   Html::back();
	
} else if (isset($_POST["delete"])) {

	$PluginArchidataDataelement->check($_POST['id'], DELETE);
	$PluginArchidataDataelement->delete($_POST);
    $PluginArchidataDataelement->redirectToList();
	
} else if (isset($_POST["restore"])) {

	$PluginArchidataDataelement->check($_POST['id'], PURGE);
	$PluginArchidataDataelement->restore($_POST);
    $PluginArchidataDataelement->redirectToList();
	
} else if (isset($_POST["purge"])) {

	$PluginArchidataDataelement->check($_POST['id'], PURGE);
	$PluginArchidataDataelement->delete($_POST,1);
    $PluginArchidataDataelement->redirectToList();
	
} else if (isset($_POST["update"])) {

	$PluginArchidataDataelement->check($_POST['id'], UPDATE);
	$PluginArchidataDataelement->update($_POST);
    Html::back();
	
} else if (isset($_POST["additem"])) {

	if (!empty($_POST['itemtype']) && $_POST['items_id']>0) {
		$PluginArchidataDataelement_Item->check(-1, UPDATE, $_POST);
		$PluginArchidataDataelement_Item->addItem($_POST);
	}
    Html::back();

} else if (isset($_POST["deleteitem"])) {

	foreach ($_POST["item"] as $key => $val) {
      $input = array('id' => $key);
      if ($val==1) {
         $PluginArchidataDataelement_Item->check($key, UPDATE);
         $PluginArchidataDataelement_Item->delete($input);
      }
    }
    Html::back();
//unlink archidata to items of glpi from the items form
} else if (isset($_POST["deletearchidata"])) {

	$input = array('id' => $_POST["id"]);
    $PluginArchidataDataelement_Item->check($_POST["id"], UPDATE);
	$PluginArchidataDataelement_Item->delete($input);
    Html::back();
	
} else {

    $PluginArchidataDataelement->checkGlobal(READ);

    Html::header(PluginArchidataDataelement_Item::getTypeName(2), '', "assets",
                   "pluginarchidatamenu");
    $PluginArchidataDataelement->display($_GET);

    Html::footer();
}

?>
