<?php
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

