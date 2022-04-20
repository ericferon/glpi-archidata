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
 
class PluginArchidataMenu extends CommonGLPI {
   static $rightname = 'plugin_archidata';

   static function getMenuName() {
      return _n('Data structure', 'Data structures', 2, 'archidata');
   }

   static function getMenuContent() {
      global $CFG_GLPI;

      $menu                                           = array();
      $menu['title']                                  = self::getMenuName();
      $menu['page']                                   = "/".Plugin::getWebDir("archidata", false)."/front/dataelement.php";
      $menu['links']['search']                        = PluginArchidataDataelement::getSearchURL(false);
      if (PluginArchidataDataelement::canCreate()) {
         $menu['links']['add']                        = PluginArchidataDataelement::getFormURL(false);
      }
      $menu['icon'] = self::getIcon();

      return $menu;
   }

   static function getIcon() {
      return "fas fa-table";
   }

   static function removeRightsFromSession() {
      if (isset($_SESSION['glpimenu']['assets']['types']['PluginArchidataMenu'])) {
         unset($_SESSION['glpimenu']['assets']['types']['PluginArchidataMenu']); 
      }
      if (isset($_SESSION['glpimenu']['assets']['content']['PluginArchidataMenu'])) {
         unset($_SESSION['glpimenu']['assets']['content']['PluginArchidataMenu']); 
      }
   }
}
