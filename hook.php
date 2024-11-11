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

use Glpi\Toolbox\Sanitizer;

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_childticketmanager_install() {
   PluginChildticketmanagerConfig::initConfig();

   return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_childticketmanager_uninstall() {
   $config = new Config;
   return $config->deleteByCriteria([
      'context' => PluginChildticketmanagerConfig::CONTEXT,
   ]);
}

/**
 * Plugin Hooks ITEM_UPDATE for Ticket
 * Resolves and closes child tickets, if configured.
 *
 * @param Ticket $ticket
 * @return void
 */
function plugin_childticketmanager_ticket_update(Ticket $ticket)
{
   global $DB;
   if (!in_array('status', $ticket->updates)) return; // status didn't change
   if (!in_array($ticket->fields['status'], [
      ...Ticket::getSolvedStatusArray(),
      ...Ticket::getClosedStatusArray(),
   ])) return; // not closing or solving
   
   $conf = PluginChildticketmanagerConfig::getConfig();
   $do_close = $conf['childticketmanager_close_child'];
   $do_solve = $conf['childticketmanager_resolve_child'];
   if (!$do_close && !$do_solve) return; // no actions to do

   foreach ($DB->request(Ticket_Ticket::getTable(),[
      'FIELDS' => ['tickets_id_1 AS id'],
      'WHERE'  => [
         'tickets_id_2' => $ticket->getID(),
         'link'         => Ticket_Ticket::SON_OF,
      ]
   ]) ?: [] as $row) {
      $child = Ticket::getById($row['id']);
      if (!$child->canUpdateItem()) continue; // TODO: Display an error?
      if ($do_solve && $ticket->isSolved() && $child->isNotSolved()) {
         (new ITILSolution)->add([
            'itemtype'  => Ticket::getType(),
            'items_id'  => $child->getID(),
            'content'   => Sanitizer::sanitize(sprintf(__("Solved through ticket %s", 'childticketmanager'), $ticket->getID())), //Résolution par le biais du billet %s
         ]);
         Session::addMessageAfterRedirect(sprintf(__('%1$s: %2$s'), __('Child ticket successfully resolved', 'childticketmanager'), $child->getLink()));
      } elseif ($do_close && $ticket->isClosed() && !$child->isClosed()) {
         (new ITILFollowup)->add([
            'itemtype'          => $child::getType(),
            'items_id'          => $child->getID(),
            'requesttypes_id'   => $child->getID(),
            'content'           => Sanitizer::sanitize(sprintf(__("Closed through ticket %s", 'childticketmanager'), $ticket->getID())),//Fermeture par le biais du billet %s
            'add_close'         => true, // Close if already solved
         ]);
         if ($child->isNotSolved()) { // Manually close since it wasn't awaiting for an approval to close
            $child->update([
               'id'  => $child->getID(),
               'status' => Ticket::CLOSED,
               '_accepted' => true, // ?
               'closedate' => $_SESSION['glpi_currenttime'],
               'update'   => true,
            ]);
         }
         Session::addMessageAfterRedirect(sprintf(__('%1$s: %2$s'), __('Child ticket successfully closed', 'childticketmanager'), $child->getLink()));
      }
   }
}