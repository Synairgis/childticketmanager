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

require_once(__DIR__ . '/install/install.php');

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_childticketmanager_install()
{
	global $DB;

	$migration = new Migration(PLUGIN_CHILDTICKETMANAGER_VERSION);

	$install = new PluginChildticketmanagerInstall();
	if (!$install->isPluginInstalled()) {
		return $install->install($migration);
	}

	return $install->upgrade($migration);
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_childticketmanager_uninstall()
{
	global $DB;

	$query = "DELETE FROM glpi_configs WHERE context = 'plugin:childticketmanager'";
	$DB->queryOrDie($query);
	return true;
}
