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

include ('../../../inc/includes.php');
header('Content-type: application/javascript');

function plugin_childticketmanager_ticket_form_linked_tickets()
{
    // On ticket form only
    if (isset($_SESSION['glpiactiveprofile']['interface'])
        && Session::getCurrentInterface() == 'central'
        && Session::haveRight('ticket', CREATE)
        && Session::haveRight('ticket', UPDATE)
        && parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) == Ticket::getFormURL()) {

        // For existing ticket only (not new)
        parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $params);
        if (isset($params['id'])){

            // Variables to insert in the Heredoc sections.
            $baseurl = Plugin::getWebDir('childticketmanager', false);
            $typename = PluginChildticketmanagerConfig::getTypeName();
            $template_btn_label = __("Display Template",'childticketmanager');
            $child_label = __('+Child', 'childticketmanager');
            $child_tooltip = __('Create child ticket from category', 'childticketmanager');

            // Configuration settings
            $conf = PluginChildticketmanagerConfig::getConfig();
            $is_template_shown = ($conf['childticketmanager_display_tmpl_link'] ? 1 : 0);
            $is_backcreated = Config::getConfigurationValue('core', 'backcreated'); // Go to created item after creation
            
            // Target element MUST be a SPAN for the ITILCategory::dropdown() to work correctly when Type changes.
            $category_placeholder = <<<HTML
            <span id="childticketmanager_category_placeholder"></span>
            HTML;
            
            // Optional link to the selected category's template, if one exists.
            $template_btn = $is_template_shown ? <<<HTML
            <a 
                id="childticketmanager_showtemplate"
                href="#"
                class="dropdown_tooltip btn btn btn-outline-secondary px-1"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="{$template_btn_label}"
            >
                <i class="fa-fw ti ti-template"></i>
                <span class="sr-only">{$template_btn_label}</span>
            </a>
            HTML : '';

            // HTML UI to add in the Linked Tickets section 
            // (between `` because we use jQuery to insert it, including the javascripts)
            $ticket_html = '`' . <<<HTML
            <div class="input-group mt-2">
                <span class="input-group-text border-0 ps-1 gap-1" data-bs-toggle="tooltip" data-bs-placement="left" title="{$typename}">
                    <i class="fa fa-ticket"></i>
                </span>
                {$category_placeholder}
                {$template_btn}
                <button 
                    id="childticketmanager_create"
                    type="button" 
                    class="btn btn-outline-secondary"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="{$child_tooltip}"
                >{$child_label}</button>
            </div>
            HTML . '`';

            // Function to update the URL of the "Show Template" button, if shown (config).
            $template_action = $is_template_shown ? <<<JAVASCRIPT
            var glpi_plugin_childticketmanager_updateTemplateId = function (cat) {
                $.post('/{$baseurl}/ajax/childticketmanager_template.php', // url
                { // data
                    'ticket':   $('[name=id]').val(),
                    'category': (cat === undefined ? $('[name=childticketmanager_category]').val() : cat),
                    'type':     $('[name=type]').val(),
                },
                (json)=>{ // success
                    if (json.template_id === undefined || json.template_id <= 0) {
                        $("#childticketmanager_showtemplate").hide();
                        $("#childticketmanager_showtemplate").attr('href', '#');
                    } else {
                        $("#childticketmanager_showtemplate").show();
                        $("#childticketmanager_showtemplate").attr('href', "tickettemplate.form.php?id=" + json.template_id);
                    }
                }, 'json');
            };
            JAVASCRIPT : '';

            // Main plugin JS code
            echo <<<JAVASCRIPT
            {$template_action}
            var glpi_plugin_childticketmanager_updateCategoryDropdown = function (cat) {
                $("#childticketmanager_category_placeholder").load(
                    '/{$baseurl}/ajax/childticketmanager_categorydropdown.php', // url
                    { // data
                        'ticket':   $('[name=id]').val(),
                        'value':    cat,
                        'type':     $('[name=type]').val(),
                        'template': {$is_template_shown},
                    },
                    ()=>{ // complete
                        if ({$is_template_shown}) {
                            glpi_plugin_childticketmanager_updateTemplateId();
                        }
                    }
                );
            };
            $("main").on("glpi.tab.loaded", ()=>{
                if($("[id^=tab-Ticket_main]").hasClass('show')){

                    // Insert UI to Linked Tickets section, inside Add+ (initially hidden)
                    $("#link_ticket_dropdowns >:last").after({$ticket_html});

                    // Set initial state of our Category dropdown
                    glpi_plugin_childticketmanager_updateCategoryDropdown($('[name=itilcategories_id]').val());

                    // Refresh our Category dropdown on Ticket Type change
                    $("select[id^=dropdown_type]").on('change', ()=>{
                        glpi_plugin_childticketmanager_updateCategoryDropdown($('[name=childticketmanager_category]').val());
                    });

                    // Create a new Child ticket using selected category's template
                    $("#childticketmanager_create").on('click', (e)=>{
                        e.preventDefault();
                        $.post('/{$baseurl}/ajax/childticketmanager_create.php', // url
                        { // data
                            'ticket':   $('[name=id]').val(),
                            'category': $('[name=childticketmanager_category]').val(),
                            'type':     $('[name=type]').val(),
                        },
                        (json)=>{ // success
                            if (json.tickets_id != undefined) {
                                if ({$is_backcreated}) {
                                    window.location.href = 'ticket.form.php?id=' + json.tickets_id;
                                } else {
                                    displayAjaxMessageAfterRedirect();
                                }
                            }
                        }, 'json');
                    });
                }                
            });
            JAVASCRIPT;
        }
    } 
}
plugin_childticketmanager_ticket_form_linked_tickets();

