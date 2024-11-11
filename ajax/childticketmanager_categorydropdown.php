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

$AJAX_INCLUDE = 1;
include('../../../inc/includes.php');
header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

$entity = -1;
if (($ticket = Ticket::getById($_POST['ticket'])) != false) {
    $entity = $ticket->fields['entities_id'];
}

$options = [
    'entity' => $entity,
    'condition' => [],
    'width' => '100%',
    'addicon' => false,
    'comments' => false, // Avant de ne plus avoir de cheveux à m'arracher de sur la tête...
    'value' => 0,
    'name' => 'childticketmanager_category',
    'on_change' => (($_POST['template']??0) ? "glpi_plugin_childticketmanager_updateTemplateId($(this).val());" : ''),
];

$type = $_POST['type'] ?? Ticket::DEMAND_TYPE;
$cat = new ITILCategory();
$cat->getFromDB($_POST['value'] ?? null);

switch ($type) {
    case Ticket::INCIDENT_TYPE:
        $options['condition']['is_incident'] = 1;
        if ($cat->getField('is_incident') == 1) $options['value'] = $_POST['value'];
        break;
    case Ticket::DEMAND_TYPE:
        $options['condition']['is_request'] = 1;
        if ($cat->getField('is_request') == 1) $options['value'] = $_POST['value'];
        break;
}

ITILCategory::dropdown($options);
exit;