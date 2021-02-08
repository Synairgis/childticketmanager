<?php
/*
 -------------------------------------------------------------------------
 childticketmanager plugin for GLPI
 Copyright (C) 2018 by the childticketmanager Development Team.

 https://github.com/pluginsGLPI/childticketmanager
 -------------------------------------------------------------------------

 LICENSE

 This file is part of childticketmanager.

 childticketmanager is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 childticketmanager is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with childticketmanager. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

define('PLUGIN_CHILDTICKETMANAGER_VERSION', '2.2.0');

// Minimal GLPI version, inclusive
define("PLUGIN_CHILDTICKETMANAGER_MIN_GLPI_VERSION", "9.5");

// Maximum GLPI version, exclusive
define("PLUGIN_CHILDTICKETMANAGER_MAX_GLPI_VERSION", "9.6");


/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_childticketmanager() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['childticketmanager'] = true;

   if (class_exists('PluginChildticketmanagerConfig')) {
      // load javascript files
      $PLUGIN_HOOKS['add_javascript']['childticketmanager'] = [
         'js/function.js',
         'js/lodash.core.min.js',
         'js/childticketmanager.js.php',
      ];

      Plugin::registerClass('PluginChildticketmanagerConfig', [
         'addtabon' => 'Config'
      ]);

      $PLUGIN_HOOKS['config_page']['childticketmanager'] = 'front/config.form.php';
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
      'name'           => __('Gestionnaire de tickets enfant', 'childticketmanager'),
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
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_childticketmanager_check_prerequisites() {
   $version = preg_replace('/^((\d+\.?)+).*$/', '$1', GLPI_VERSION);
   $min = version_compare($version, PLUGIN_CHILDTICKETMANAGER_MIN_GLPI_VERSION, '>=');
   $max = version_compare($version, PLUGIN_CHILDTICKETMANAGER_MAX_GLPI_VERSION, '<');

   if (!$min || !$max) {
      echo vsprintf('This plugin requires GLPI >= %1$s and < %2$s.', [
         PLUGIN_CHILDTICKETMANAGER_MIN_GLPI_VERSION,
         PLUGIN_CHILDTICKETMANAGER_MAX_GLPI_VERSION,
      ]);
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
