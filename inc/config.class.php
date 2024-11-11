<?php
/**
 *  -------------------------------------------------------------------------
 *  childticketmanager plugin for GLPI
 *  Copyright (C) 2018 by the childticketmanager Development Team.
 *
 *  https://github.com/pluginsGLPI/childticketmanager
 *  -------------------------------------------------------------------------
 *
 *  LICENSE
 *
 *  This file is part of childticketmanager.
 *
 *  childticketmanager is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  childticketmanager is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with childticketmanager. If not, see <http://www.gnu.org/licenses/>.
 *  --------------------------------------------------------------------------
 */

use Glpi\Application\View\TemplateRenderer;

class PluginChildticketmanagerConfig extends Config {
   const CONTEXT = 'plugin:childticketmanager';

   static function getTypeName($nb = 0) {
      return _n("Child ticket", "Child tickets", $nb, "childticketmanager");
   }

   static function getConfig() {
      return Config::getConfigurationValues(self::CONTEXT);
   }

   static function initConfig() {
      return Config::setConfigurationValues(self::CONTEXT, [
         'childticketmanager_close_child'       => 0,
         'childticketmanager_resolve_child'     => 0,
         'childticketmanager_display_tmpl_link' => 0,
      ]);
   }

   function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
      return ($item instanceof Config) ? self::createTabEntry(self::getTypeName()) : '';
   }

   static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
      return ($item instanceof Config) ? self::showForConfig() : true;
   }

   static function showForConfig() { global $DB;
      if (!($canedit = Session::haveRight(self::$rightname, UPDATE))) {
         return false;
      }

      TemplateRenderer::getInstance()->display('@childticketmanager/config.html.twig', [
         'current_config'  => self::getConfig(),
         'canedit'         => $canedit,
         // 'config_class'    => __CLASS__, // only required if self::configUpdate($input) exists.
         'config_context'  => self::CONTEXT,
      ]);
   }
}
