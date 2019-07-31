<?php
include ("../../../inc/includes.php");

//change mimetype
header("Content-type: application/javascript");


//not executed in self-service interface & right verification
if (isset($_SESSION['glpiactiveprofile']['interface'])
    && $_SESSION['glpiactiveprofile']['interface'] == "central"
    && Session::haveRight("ticket", CREATE)
    && Session::haveRight("ticket", UPDATE)) {

   $locale_linkedtickets = _n('Linked ticket', 'Linked tickets', 2);
   $redirect = Config::getConfigurationValues('core', ['backcreated']);
   $redirect = $redirect['backcreated'];

   $testing = GLPI_ROOT;
   $version = GLPI_VERSION;

   $JS = <<<JAVASCRIPT

   var glpi_version = "{$version}";
   glpi_version = glpi_version.replace(/\./g, "") + "000";
   glpi_version = glpi_version.substr(0,3);


   childticketmanager_addCloneLink = function(callback) {
      //only in edit form
      if (getUrlParameter('id') == undefined) {
         return;
      }

      if ($("#create_child_ticket").length > 0) {
         return;
      }
      // #3A5693
      var ticket_html = "<i class='fa fas fa-ticket fa-ticket-alt pointer' style='font-size: 20px;' id='create_child_ticket'></i>";

      $("th:contains('{$locale_linkedtickets}')>span.fa")
         .after(ticket_html);

      callback();
   };

   childticketmanager_bindClick = _.once(function() {
      $(document).on("click", "#create_child_ticket", _.once(function(e) {
         $.ajax({
            url: '../plugins/childticketmanager/ajax/childticketmanager.php',
            data: {
               'tickets_id': getUrlParameter('id')
            },
            success: function(response, opts) {
               $("[id^='linkedticket']").after(response);

               $("#childticketmanager_submit").on("click", function(e){
                  e.preventDefault();

                  $.ajax({
                     url: '../plugins/childticketmanager/ajax/childticketmanager_save.php',
                     method: 'post',
                     dataType: "json",
                     data: {
                        'tickets_id': getUrlParameter('id'),
                        'category': $("[id^='dropdown_childticketmanager_category']").val()
                     },
                     success: function(response, opts) {
                        if ($redirect == 1) {
                           window.location.href = "ticket.form.php?id=" + response["new_ticket_id"];
                        } else {
                           displayAjaxMessageAfterRedirect();
                        }
                     }
                  });

               });

               $("#childticketmanager_templ").on("click", function(e){
                  e.preventDefault();

                  $.ajax({
                     url: '../plugins/childticketmanager/ajax/childticketmanager_gettemplate.php',
                     method: 'post',
                     dataType: "json",
                     data: {
                        'tickets_id': getUrlParameter('id'),
                        'category': $("[id^='dropdown_childticketmanager_category']").val()
                     },
                     success: function(response, opts) {
                        window.location.href = "tickettemplate.form.php?id=" + response["template_id"];
                     }
                  });

               });

               $("select[id^='dropdown_type']").trigger("change");
            }
         });
      }));
   });


   /********************************************************
    * Fonction d'événement qui rafraîchi la liste du plugin
    * lorsque la catégorie du ticket change de incident
    * à demande pour GLPI 9.3.x
    *********************************************************/

   childticketmanager_change_93 = function(e){

      $("select[name='childticketmanager_category']").val(0);

      $.ajax({
         url: '../plugins/childticketmanager/ajax/childticketmanager_getcondition.php',
         method: 'post',
         datatype: 'json',
         data: {
            type: $(this).val(),
            id: getUrlParameter('id')
         },
         success: function(response, opts) {
            let result = JSON.parse(response);

            var my_param = {
               itemtype: "ITILCategory",
               display_emptychoice: 1,
               displaywith: [],
               emptylabel: "-----",
               condition: result.condition,
               used: [],
               toadd: [],
               entity_restrict: result.entity,
               permit_select_parent: 0,
               specific_tags: [],
               // searchText: term,
               page_limit: 100 // page size
               // page: page, // page number
            };

            $("select[name='childticketmanager_category']").select2({
               width: '',
               minimumInputLength: 0,
               quietMillis: 100,
               dropdownAutoWidth: true,
               minimumResultsForSearch: 10,
               ajax: {
                  url: '../../ajax/getDropdownValue.php',
                  dataType: 'json',
                  type: 'POST',
                  data: function (params) {
                     query = params;
                     return $.extend({}, my_param, {
                        searchText: params.term,
                        page_limit: 100, // page size
                        page: params.page || 1, // page number
                     });
                  },
                  processResults: function (data, params) {
                     params.page = params.page || 1;
                     var more = (data.count >= 100);

                     return {
                        results: data.results,
                        pagination: {
                           more: more
                        }
                     };
                  }
               },
               templateResult: templateResult,
               templateSelection: templateSelection

            }).bind('setValue', function(e, value) {
               $.ajax('../../ajax/getDropdownValue.php', {
                  data: $.extend({}, my_param, {
                     _one_id: value,
                  }),
                  dataType: 'json',
                  type: 'POST',
               }).done(function(data) {

                  var iterate_options = function(options, value) {
                     var to_return = false;
                     $.each(options, function(index, option) {
                        if (option.hasOwnProperty('id')
                              && option.id == value) {
                           to_return = option;
                           return false; // act as break;
                        }

                        if (option.hasOwnProperty('children')) {
                           to_return = iterate_options(option.children, value);
                        }
                     });

                     return to_return;
                  };

                  var option = iterate_options(data.results, value);
                  if (option !== false) {
                     var newOption = new Option(option.text, option.id, true, true);
                     $("select[name='childticketmanager_category']")
                        .append(newOption)
                        .trigger('change');
                  }
               });
            });
         }
      });
   };

   /********************************************************
    * Fonction d'événement qui rafraîchi la liste du plugin
    * lorsque la catégorie du ticket change de incident
    * à demande pour GLPI 9.2.x
    *********************************************************/

   childticketmanager_change_92 = function(e){

      $.ajax({
         url: '../plugins/childticketmanager/ajax/childticketmanager_getcondition.php',
         method: 'post',
         datatype: 'json',
         data: {
            type: $(this).val(),
            id: getUrlParameter('id')
         },
         success: function(response, opts) {
            let result = JSON.parse(response);

            var my_param = {
               itemtype: "ITILCategory",
               display_emptychoice: 1,
               displaywith: [],
               emptylabel: "-----",
               condition: result.condition,
               used: [],
               toadd: [],
               entity_restrict: result.entity,
               permit_select_parent: 0,
               specific_tags: [],
               // searchText: term,
               page_limit: 100 // page size
               // page: page, // page number
            };

            // console.log({$testing});

            $("input[name='childticketmanager_category']").select2({
               width: '',
               minimumInputLength: 0,
               quietMillis: 100,
               dropdownAutoWidth: true,
               minimumResultsForSearch: 10,
               ajax: {
                  url: '../../ajax/getDropdownValue.php',
                  dataType: 'json',
                  type: 'POST',
                  data: function (params) {
                     query = params;
                     return $.extend({}, my_param, {
                        searchText: params.term,
                        page_limit: 100, // page size
                        page: params.page || 1, // page number
                     });
                  },
                  results: function (data, params) {
                     console.log(data);
                     params.page = params.page || 1;
                     var more = (data.count >= 100);

                     return {
                        results: data.results,
                        pagination: {
                           more: more
                        }
                     };
                  }
               },
               initSelection: function (element, callback) {
                  var id = $(element).val();
                  var defaultid = '0';
                  if (id !== '') {
                     // No ajax call for first item
                     if (id === defaultid) {
                        callback({
                           id: 0,
                           text: "-----"
                        });
                     } else {
                        $.ajax('../../ajax/getDropdownValue.php', {
                           data: function (params) {
                           query = params;
                           return $.extend({}, my_param, {
                              searchText: params.term,
                              page_limit: 100, // page size
                              page: params.page || 1, // page number
                           });
                        },
                           dataType: 'json',
                           type: 'POST',
                        }).done(function(data) {
                           callback(data);
                        });
                     }
                  }

               },
               formatResult: function(result, container, query, escapeMarkup) {
                  container.attr('title', result.title);
                  var markup=[];
                  window.Select2.util.markMatch(result.text, query.term, markup, escapeMarkup);
                  if (result.level) {
                     var a='';
                     var i=result.level;
                     while (i>1) {
                        a = a+'&nbsp;&nbsp;&nbsp;';
                        i=i-1;
                     }
                     return a+'&raquo;'+markup.join('');
                  }
                  return markup.join('');
               }
            });
         }
      });
   };


   childticketmanager_submit = function(e, opts){
      opts = opts || {};

      /*
      * Si la liste dropdown_status est visible, c'est qu'on est sur la page d'édition du ticket
      * alors on doit prendre la valeur de cette liste pour déterminer le statut. Si elle est invisible
      * c'est qu'on est sur la page de traitement du ticket et dans ce cas, le statut est déterminé
      * par ce que l'on reçoit dans les paramètres de l'événement.
      */
      if ($("[id^='dropdown_status']").is(":visible")) {
         var status = $("[id^='dropdown_status']").val();
      } else {
         var status = e.data.ticketStatus || opts.ticketStatus;
      }


      if (typeof status == "undefined") {
         status = -1;
      }

      console.log('submit', status);

      var childrenUpdated = null;
      var childrenUpdated = e.data.childrenUpdated || opts.childrenUpdated;
      if (typeof childrenUpdated == "undefined") {
         childrenUpdated = false;
      }

      if (!childrenUpdated
            && (status == 5 || status == 6) ) {
         e.preventDefault();

         $.ajax({
            url: '../plugins/childticketmanager/ajax/childticketmanager_childtix.php',
            method: 'post',
            dataType: "json",
            data: {
               'tickets_id': getUrlParameter('id'),
               'tickets_status': status
            },
            success: function(response, opts) {
               $(e.currentTarget).trigger('click', {
                  'ticketStatus': status,
                  'childrenUpdated': true
               });
            },
            error: function(response, status, error){
               console.log(response);
               console.log(status);
               console.log(error);
            }
         });
      }
   };

   var createEvents = function() {
      // Quand on est sur la page du ticket et qu'on le résout/ferme, le bouton s'appelle "update".
      // Quand on est sur le traitement du ticket et qu'on met une solution, le bouton s'appelle "add".
      // C'est pour ça qu'on doit avoir deux bindings pour la même chose


      $("input[name='update']").on("click", {
         'childrenUpdated': false
      }, childticketmanager_submit);
      $("form[name=form_ticket], .timeline_box")
         .find("input[name='add']").on("click", {
            'ticketStatus': 5,
            'childrenUpdated': false
         }, childticketmanager_submit);
      $("input[name='add_close']").on("click", {
         'ticketStatus': 6,
         'childrenUpdated': false
      }, childticketmanager_submit);
   };

   $(document).ready(function() {

      var pageActu = window.location.pathname.split("/").slice(-1)[0];

      // Si on ne se trouve pas sur la page de gestion des tickets, on ne doit rien faire afin de ne
      // pas interférer avec le bon fonctionnement de GLPI avec des binding erronés

      if (pageActu != "ticket.form.php") {
         return;
      }

      childticketmanager_addCloneLink(childticketmanager_bindClick);
      createEvents();

      $(".glpi_tabs").on("tabsload", function(event, ui) {
         childticketmanager_addCloneLink(childticketmanager_bindClick);
         createEvents();
      });

      $(document).ajaxComplete(function(event, request, settings) {

         if (typeof(settings.data) != "undefined"
             && typeof(settings.data) != "object") {

            var params = settings.data.split("&");
            if (params[0] == "action=viewsubitem"
               && params[1] == "type=Solution") {
               createEvents();
            }
         }

         if (glpi_version < 930) {
            $("select[id^='dropdown_type']").not('.change-bound')
               .on('change', childticketmanager_change_92)
               .addClass('change-bound');
         } else {
            $("select[id^='dropdown_type']").not('.change-bound')
               .on('change', childticketmanager_change_93)
               .addClass('change-bound');
         }
      });
   });


JAVASCRIPT;

   echo $JS;
}
