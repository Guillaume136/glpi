<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2011 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Julien Dombre
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

// CLASSES Ocslink
class Ocslink extends CommonDBTM {

   static function getTypeName($nb=0) {
      global $LANG;

      if ($nb>1) {
         return $LANG['ocsng'][53];
      }
      return $LANG['ocsng'][58];
   }


   function canCreate() {
      return Session::haveRight('ocsng', 'w');
   }


   function canView() {
      return Session::haveRight('ocsng', 'r');
   }


   /**
   * Show OcsLink of an item
   *
   * @param $item CommonDBTM object
   * @param $withtemplate integer : withtemplate param
   * @return nothing
   **/
   static function showForItem(CommonDBTM $item, $withtemplate='') {
      global $DB, $LANG;

      if (in_array($item->getType(),array('Computer'))) {
         $items_id = $item->getField('id');

         $query = "SELECT `glpi_ocslinks`.`tag` AS tag
                   FROM `glpi_ocslinks`
                   WHERE `glpi_ocslinks`.`computers_id` = '$items_id' ".
                         getEntitiesRestrictRequest("AND","glpi_ocslinks");

         $result = $DB->query($query);
         if ($DB->numrows($result) > 0) {
            $data = $DB->fetch_assoc($result);
            $data = Toolbox::clean_cross_side_scripting_deep(Toolbox::addslashes_deep($data));

            echo "<div class='center'>";
            echo "<table class='tab_cadre_fixe'>";
            echo "<tr><th>" . $LANG['ocsng'][0] . "</th>";
            echo "<tr class='tab_bg_2'>";
            echo "<td class='center'>".$LANG['ocsconfig'][39]."&nbsp;: ".$data['tag']."</td></tr>";
         }
      }
   }


   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      global $LANG, $CFG_GLPI;

      if (!$withtemplate && $CFG_GLPI["use_ocs_mode"]) {
         switch ($item->getType()) {
            case 'Computer' :
               if (Session::haveRight('sync_ocsng', 'w') || Session::haveRight('computer', 'w')) {
                  return $LANG['ocsconfig'][0];
               }
               break;
         }
      }
      return '';
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      if ($item->getType()=='Computer') {
         OcsLink::showForItem($item);
         OcsServer::editLock($item);
      }
      return true;
   }
}

?>