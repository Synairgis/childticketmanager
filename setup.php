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
  
define('PLUGIN_CHILDTICKETMANAGER_VERSION', '2.0.4');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_childticketmanager() {
	global $PLUGIN_HOOKS;
	
	$PLUGIN_HOOKS['csrf_compliant']['childticketmanager'] = true;
	
	$PLUGIN_HOOKS['add_javascript']['childticketmanager'][] = 'js/function.js';
	$PLUGIN_HOOKS['add_javascript']['childticketmanager'][] = 'js/lodash.core.min.js';
	$PLUGIN_HOOKS['add_javascript']['childticketmanager'][] = 'js/childticketmanager.js.php';

	Plugin::registerClass('PluginChildticketmanagerConfig', array('addtabon' => 'Config'));

	$PLUGIN_HOOKS['config_page']['childticketmanager'] = 'front/config.form.php';
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
      'minGlpiVersion' => '9.3'
   ];
}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_childticketmanager_check_prerequisites() {
   // Strict version check (could be less strict, or could allow various version)
   if (version_compare(GLPI_VERSION, '9.3', 'lt')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '9.3');
      } else {
         echo __("Ce plugin requiert GLPI >= 9.3");
      }
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
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      __('Installé / non configuré', 'childticketmanager');
   }
   return false;
}
