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

use Glpi\Plugin\Hooks;

define('PLUGIN_CHILDTICKETMANAGER_VERSION', '4.0.0');

// Minimal GLPI version, inclusive
define("PLUGIN_CHILDTICKETMANAGER_MIN_GLPI_VERSION", "11.0");

// Maximum GLPI version, exclusive
define("PLUGIN_CHILDTICKETMANAGER_MAX_GLPI_VERSION", "12.0");


/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_childticketmanager() {
   global $PLUGIN_HOOKS;

   // Note: Hooks::CSRF_COMPLIANT was deprecated in GLPI 11.0 and should not be used.

   if (class_exists('PluginChildticketmanagerConfig')) {
      // load javascript files
      $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['childticketmanager'] = [
         'js/ticket_form_linked_tickets.js.php',
      ];

      Plugin::registerClass('PluginChildticketmanagerConfig', [
         'addtabon' => 'Config'
      ]);

      $PLUGIN_HOOKS['config_page']['childticketmanager'] = 'front/config.form.php';
      $PLUGIN_HOOKS[Hooks::ITEM_UPDATE]['childticketmanager'] = [Ticket::class => 'plugin_childticketmanager_ticket_update'];
   }
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_childticketmanager() {
   return [
      'name'           => __('Child Tickets Manager', 'childticketmanager'),
      'version'        => PLUGIN_CHILDTICKETMANAGER_VERSION,
      'author'         => '<a href="http://www.synairgis.com">Synairgis</a>',
      'license'        => '<a href="../plugins/childticketmanager/LICENSE" target="_blank">GPLv3</a>',
      'homepage'       => '',
      'requirements'   => [
         'glpi' => [
            'min' => PLUGIN_CHILDTICKETMANAGER_MIN_GLPI_VERSION,
            'max' => PLUGIN_CHILDTICKETMANAGER_MAX_GLPI_VERSION,
         ]
      ]
   ];
}

/**
 * Check pre-requisites before install
 * OPTIONAL, but recommended
 *
 * @return boolean
 */
function plugin_childticketmanager_check_prerequisites() {
   $version = preg_replace('/^((\d+\.?)+).*$/', '$1', GLPI_VERSION);
   $min = version_compare($version, PLUGIN_CHILDTICKETMANAGER_MIN_GLPI_VERSION, '>=');
   $max = version_compare($version, PLUGIN_CHILDTICKETMANAGER_MAX_GLPI_VERSION, '<');

   if (!$min || !$max) {
      Plugin::messageIncompatible(
         'core',
         PLUGIN_CHILDTICKETMANAGER_MIN_GLPI_VERSION,
         PLUGIN_CHILDTICKETMANAGER_MAX_GLPI_VERSION
      );
      return false;
   }

   return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_childticketmanager_check_config($verbose = false) {
   return true;
}
