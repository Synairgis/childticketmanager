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

/**
 * Redirects to the Configuration tab of the Plugin
 */

require_once ('../../../inc/includes.php');

// Check if current user have config right
Session::checkRight("config", UPDATE);

// Check if plugin is activated...
if (!(new Plugin)->isActivated('childticketmanager')) Html::displayNotFoundError();

$tab = '?' . http_build_query(['forcetab' => PluginChildticketmanagerConfig::class]) . '$1';
Html::redirect(Config::getFormURL() . $tab);

