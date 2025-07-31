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

use Glpi\Toolbox\Sanitizer;

header("Content-Type: text/html; charset=UTF-8");
Html::header_nocache();

Session::checkLoginUser();

$ticket_id      = filter_var($_POST['ticket'], FILTER_VALIDATE_INT) ?: 0;
$type           = filter_var($_POST['type'], FILTER_VALIDATE_INT) ?: Ticket::DEMAND_TYPE;
$category_id    = filter_var($_POST['category'], FILTER_VALIDATE_INT) ?: 0;

if (($parent = Ticket::getById($ticket_id)) === false) exit();

$child = new Ticket;
$input = $child->getITILTemplateToUse(0, $type, $category_id)->predefined;

// Get following values from template, if exist, otherwise from parent
foreach ([
    'entities_id',
    'type',
    'urgency',
    'impact',
    'requesttypes_id',
    'locations_id',
    'name',
    'content',
] as $key) {
    $input[$key] = $input[$key] ?? $parent->getField($key);
}
// Get values from template, if exist, otherwise use default values
$input['status'] = $input['status'] ?? CommonItilObject::INCOMING;
$input['requesttypes_id'] = $input['requesttypes_id'] ?? 1; // Helpdesk
if (!isset($input['_actors']['requester'])) {
    $input['_actors']['requester'][] = [
        'itemtype' => \User::getType(),
        'items_id' => Session::getLoginUserID(),
    ];
}
// Set values
$input['itilcategories_id'] = $category_id;
$input['_add'] = true; // This adds the standard redirect message
// Sanitize strings and arrays
foreach ($input as $key => $val) {
    $input[$key] = Sanitizer::dbEscapeRecursive([$val])[0];
}

$child->add($input);
(new Ticket_Ticket)->add([
    'tickets_id_1'  => $child->getID(),
    'tickets_id_2'  => $parent->getID(),
    'link'          => Ticket_Ticket::SON_OF,
]);

foreach ($input['_documents_id']??[] as $doc) {
    (new Document_Item)->add([
        'documents_id'  => $doc,
        'itemtype'      => Ticket::class,
        'items_id'      => $child->getID(),
    ]);
}

exit(json_encode(['tickets_id' => $child->getID()]));