<?php

class PluginChildticketmanagerAction {

   static function updateChildTickets_93($status, $current, $parent) {
      $children = self::getChildTickets($current);
      $retour = [];

      if ($children != null) {
         foreach($children as $tix) {
            $retour = array_merge($retour, self::updateChildTickets_93($status, $tix, $current));
         }
      }

      $currentDate   = new datetime();
      $parent_ticket = new Ticket();
      $parent_ticket->getFromDB($current);
      $updatedFields = ['status', 'solvedate', 'closedate'];
      $oldValues     = [];

      if ($parent_ticket->fields['status'] >= $status) {
         return $retour;
      }

      $parent_ticket->input   = [];
      $parent_ticket->updates = [];

      Plugin::doHook("pre_item_update", $parent_ticket);

      foreach ($updatedFields as $fld) {
         $oldValues[$fld] = $parent_ticket->fields[$fld];
      }

      $parent_ticket->fields['status'] = $status;

      if ($status == CommonITILObject::SOLVED) {
         $parent_ticket->fields['solvedate'] = $currentDate->format("Y-m-d H:i:s");

         if ($parent != null) {
            $solution = new ITILSolution();

            $fields = [
               'itemtype'           => 'Ticket',
               'items_id'           => $parent_ticket->fields['id'],
               'solutiontypes_id'   => 0,
               'solutiontype_name'  => null,
               'content'            => "RÃ©solu par le biais du billet " . $parent,
               'date_creation'      => $currentDate->format("Y-m-d H:i:s"),
               'date_mod'           => $currentDate->format("Y-m-d H:i:s"),
               'date_approval'      => null,
               'users_id'           => Session::getLoginUserID(),
               'user_name'          => null,
               'users_id_editor'    => 0,
               'users_id_approval'  => $parent_ticket->fields['users_id_recipient'],
               'user_name_approval' => null,
               'status'             => 2,
               'ticketfollowups_id' => null
            ];

            $solution->add($fields);
         }

         $mailtype = "solved";
      } else if ($status == CommonITILObject::CLOSED) {
         $parent_ticket->fields['closedate'] = $currentDate->format("Y-m-d H:i:s");

         if ($parent_ticket->fields['solvedate'] == null) {
            $parent_ticket->fields['solvedate'] = $currentDate->format("Y-m-d H:i:s");
         }

         $mailtype = "closed";
         $parent_ticket->updateInDB($updatedFields, $oldValues);
      }


      Plugin::doHook("item_update", $parent_ticket);
      Session::addMessageAfterRedirect(__("Ticket ", "childticketmanager").
                                       $parent_ticket->getID().
                                       __(" mis Ã  jour", "childticketmanager"));
      NotificationEvent::raiseEvent($mailtype, $parent_ticket);

      return array_merge([$current], $retour);
   }


   static function updateChildTickets_92($status, $current, $parent) {
      $children = self::getChildTickets($current);
      $retour   = [];

      if ($children != null) {
         foreach ($children as $tix) {
            $retour = array_merge($retour, self::updateChildTickets_92($status, $tix, $current));
         }
      }

      $currentDate   = new datetime();
      $parent_ticket = new Ticket();
      $parent_ticket->getFromDB($current);
      $oldValues     = [];
      $updatedFields = ['status', 'solvedate', 'closedate', 'solution'];

      if ($parent_ticket->fields['status'] >= $status) {
         return $retour;
      }

      $parent_ticket->input   = [];
      $parent_ticket->updates = [];

      Plugin::doHook("pre_item_update", $parent_ticket);

      foreach ($updatedFields as $fld) {
         $oldValues[$fld] = $parent_ticket->fields[$fld];
      }

      $parent_ticket->fields['status'] = $status;

      if ($status == CommonITILObject::SOLVED) {
         $parent_ticket->fields['solvedate'] = $currentDate->format("Y-m-d H:i:s");
         if ($parent_ticket->fields['solution'] == null
             && $parent != null) {
            $parent_ticket->fields['solution'] = "RÃ©solu par le biais du billet ".$parent;
         }

         $mailtype = "solved";
      } else if ($status == CommonITILObject::CLOSED) {
         $parent_ticket->fields['closedate'] = $currentDate->format("Y-m-d H:i:s");

         if ($parent_ticket->fields['solvedate'] == null) {
            $parent_ticket->fields['solvedate'] = $currentDate->format("Y-m-d H:i:s");
         }

         $mailtype = "closed";
      }

      $parent_ticket->updateInDB($updatedFields, $oldValues);

      Plugin::doHook("item_update", $parent_ticket);

      Session::addMessageAfterRedirect( __("Ticket ", "childticketmanager").
                                       $parent_ticket->getID().
                                       __(" mis Ã  jour", "childticketmanager"));
      NotificationEvent::raiseEvent($mailtype, $parent_ticket);

      return array_merge([$current], $retour);
   }


   static function getChildTickets($parent_id) {
      global $DB;

      $query = "SELECT `tickets_id_1` as ticket_id
                FROM `glpi_tickets_tickets`
                WHERE `tickets_id_2` = ?
                  AND `link` = ".Ticket_Ticket::SON_OF;
      $stmt  = $DB->prepare($query);

      $stmt->bind_param('i', $parent_id);
      $stmt->execute();

      $res = $stmt->get_result();

      if ($res->num_rows == 0) {
         return null;
      }

      return array_column($res->fetch_all(MYSQLI_ASSOC), 'ticket_id');

   }

   static function getCondition($params = []) {
      $retour = [];

      $ticket = new Ticket();
      $ticket->getFromDB((int) $params['id']);

      $condition = "";
      if ($params['type'] == Ticket::INCIDENT_TYPE) {
         $condition = "`is_incident` = 1";
      } else if ($params['type'] == Ticket::DEMAND_TYPE) {
         $condition = "`is_request` = 1";
      }

      $retour['condition'] = sha1($condition);
      $_SESSION['glpicondition'][$retour['condition']] = $condition;

      $retour['entity'] = $ticket->fields["entities_id"];

      return $retour;
   }


   static function getTemplate($params = []) {
      $ticket = new Ticket();
      $ticket->getFromDB((int) $params['tickets_id']);

      return $ticket->getITILTemplateToUse(0, $ticket->getField('type'), $params['category']);
   }


   static function display($tickets_id = 0) {
      $configs = PluginChildticketmanagerConfig::getConfig();
      $ticket  = new Ticket();
      $ticket->getFromDB((int) $tickets_id);

      $JS = <<<JAVASCRIPT
      if ($(this).val() == 0) {
         $("#childticketmanager_templ").hide();
      } else {
         $("#childticketmanager_templ").show();
      }
JAVASCRIPT;

      $cond = "";
      if ($ticket->fields["type"] == Ticket::INCIDENT_TYPE) {
         $cond = ["is_incident" => "1"];
      } else if ($ticket->fields["type"] == Ticket::DEMAND_TYPE) {
         $cond = ["is_request" => "1"];
      }

      ITILCategory::dropdown([
         'comments'  => false,
         'name'      => 'category',
         'condition' => $cond,
         'entity'    => $ticket->fields["entities_id"],
         'on_change' => $JS
      ]);

      echo Html::hidden("ticket_id", [
         'value' => $tickets_id
      ]);

      echo "&nbsp;";
      echo "&nbsp;";
      echo html::submit( __('Créer enfant', 'childticketmanager'), [
         'id' => 'childticketmanager_submit'
      ]);
      echo "<br>";
   }


   static function save($params = []) {
      global $DB;

      $parent_ticket = new Ticket();
      $parent_ticket->getFromDB((int) $params['tickets_id']);

      $new_ticket = new Ticket();
      $template = $new_ticket->getITILTemplateToUse(0, $parent_ticket->fields['type'], $params['category']);

      $new_ticket_values = $template->predefined;

      $new_ticket_values['entities_id'] = $parent_ticket->fields['entities_id'];

      // La date d'ouverture... si elle vient du template on n'a rien à faire et si
      // le template ne la définit pas, glpi prend la date courante alors dans tous les cas, on n'a rien à faire.
      // $new_ticket_values['date'] =

      $new_ticket_values['type'] = isset($new_ticket_values['type'])
         ? $new_ticket_values['type']
         : $parent_ticket->fields['type'];
      $new_ticket_values['status'] = isset($new_ticket_values['status'])
         ? $new_ticket_values['status']
         : 1; // 1 => Nouveau
      $new_ticket_values['urgency'] = isset($new_ticket_values['urgency'])
         ? $new_ticket_values['urgency']
         : $parent_ticket->fields['urgency'];
      $new_ticket_values['impact'] = isset($new_ticket_values['impact'])
         ? $new_ticket_values['impact']
         : $parent_ticket->fields['impact'];

      //Priorité, voir si on peut la calculer
      //$new_ticket_values['impact'] = isset($new_ticket_values['impact']) ? $new_ticket_values['impact'] : $parent_ticket->fields['impact'];

      $new_ticket_values['itilcategories_id'] = $params['category'];
      $new_ticket_values['requesttypes_id'] = isset($new_ticket_values['requesttypes_id'])
         ? $new_ticket_values['requesttypes_id']
         : 1; // 1 => Helpdesk

      //$new_ticket_values['global_validation'] = Rien à faire puisqu'on prend soit la valeur du gabarit ou la valeur par défaut

      $new_ticket_values['locations_id'] = isset($new_ticket_values['locations_id'])
         ? $new_ticket_values['locations_id']
         : $parent_ticket->fields['locations_id'];
      $new_ticket_values['name'] = isset($new_ticket_values['name'])
         ? $new_ticket_values['name']
         : $parent_ticket->fields['name'];
      $new_ticket_values['content'] = isset($new_ticket_values['content'])
         ? $new_ticket_values['content']
         : $parent_ticket->fields['content'];

      foreach ($new_ticket_values as $field => $val) {
         if ($field != '_documents_id') {
            $new_ticket_values[$field] = $DB->escape($val);
         }

         // $new_ticket_values[$field] = mysqli_real_escape_string($DB->dbh, $val);
      }

      $new_ticket->add($new_ticket_values);
      $relation = new Ticket_Ticket();
      $relation->add([
         'tickets_id_1' => $new_ticket->getID(),
         'tickets_id_2' => $parent_ticket->getID(),
         'link'         => Ticket_Ticket::SON_OF
      ]);

      if (isset($new_ticket_values['_documents_id'])) {
         foreach ($new_ticket_values['_documents_id'] as $docID) {
            $docItem = new Document_Item();
            $docItem->add([
               'documents_id' => $docID,
               'itemtype'     => 'Ticket',
               'items_id'     => $new_ticket->getID()
            ]);
         }
      }

      return $new_ticket;
   }

   static function childtix($params = []) {
      $configs = PluginChildticketmanagerConfig::getConfig();

      $result = "";
      if ($configs['childticketmanager_close_child']
            && $params['tickets_status'] == CommonITILObject::CLOSED
         || $configs['childticketmanager_resolve_child']
            && $params['tickets_status'] == CommonITILObject::SOLVED) {

         if (version_compare(GLPI_VERSION, "9.3") >= 0) {
            $fonction = "updateChildTickets_93";
         } else {
            $fonction = "updateChildTickets_92";
         }

         $result = call_user_func_array([
            "PluginChildticketmanagerAction",
            $fonction
         ], [
            (int) $params['tickets_status'],
            (int) $params['tickets_id'],
            null
         ]);
      }

      return $result;
   }
}
