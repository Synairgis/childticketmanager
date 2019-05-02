<?php
require_once ('../../../inc/includes.php');

// Check if current user have config right
Session::checkRight("config", UPDATE);

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isActivated('childticketmanager')) {
   Html::displayNotFoundError();
}

//Search::show('PluginchildticketmanagerConfig');
Html::redirect($CFG_GLPI["root_doc"]."/front/config.form.php?forcetab=PluginChildticketmanagerConfig\$1");

Html::footer();
