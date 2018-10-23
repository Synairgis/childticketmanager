<?php
include ("../../../inc/includes.php");

//change mimetype
header("Content-type: application/javascript");

//not executed in self-service interface & right verification
if ($_SESSION['glpiactiveprofile']['interface'] == "central"
	&& Session::haveRight("ticket", CREATE)
	&& Session::haveRight("ticket", UPDATE)
) {
	
	$locale_linkedtickets = _n('Linked ticket', 'Linked tickets', 2);
	$redirect = Config::getConfigurationValues('core', ['backcreated']);
	$redirect = $redirect['backcreated'];
	
	$JS = <<<JAVASCRIPT
		
		childticketmanager_addCloneLink = function(callback) {
	  	//only in edit form
			if (getUrlParameter('id') == undefined) {
				return;
			}
			
			if ($("#create_child_ticket").length > 0) { return; }
			// #3A5693
			var ticket_html = "<i class='fa fa-ticket pointer' style='font-size: 20px;' id='create_child_ticket'></i>";
				
			$("th:contains('$locale_linkedtickets')>span.fa")
				.after(ticket_html);
			
			callback();
			
		};
		
		childticketmanager_bindClick = _.once(function(){
			$(document).on("click", "#create_child_ticket",
			_.once(function(e){
				$.ajax({
				url:     '../plugins/childticketmanager/ajax/childticketmanager.php',
				data:    { 'tickets_id': getUrlParameter('id') },
				success: function(response, opts) {
						$("[id^='linkedticket']").after(response);
						
						$("#childticketmanager_submit").on("click", function(e){
							e.preventDefault();
							
							$.ajax({
								url: '../plugins/childticketmanager/ajax/childticketmanager_save.php',
								method: 'post',
								dataType: "json",
								data: {	'tickets_id': getUrlParameter('id'), 
										'category': $("[id^='dropdown_childticketmanager_category']").val()
									  },
								success: function(response, opts) {
									if($redirect == 1)
										window.location.href = "ticket.form.php?id=" + response["new_ticket_id"];
									else
										displayAjaxMessageAfterRedirect();
								}
							});
							
						});
						
						$("#childticketmanager_templ").on("click", function(e){
							e.preventDefault();
							
							$.ajax({
								url: '../plugins/childticketmanager/ajax/childticketmanager_gettemplate.php',
								method: 'post',
								dataType: "json",
								data: {	'tickets_id': getUrlParameter('id'), 
										'category': $("[id^='dropdown_childticketmanager_category']").val()
									  },
								success: function(response, opts) {
									window.location.href = "tickettemplate.form.php?id=" + response["template_id"];
								}
							});
							
						});
					}
				});
		   	}));
			
			
		});
	
	childticketmanager_getActiveTabId = function(){
		return $("div[id^='tabs'] > ul > li[class*='ui-tabs-active ui-state-active']")[0].firstChild.id;
	};
	
	childticketmanager_change = function(e){


		$.ajax({
			url: '../plugins/childticketmanager/ajax/childticketmanager_getcondition.php',
			method: 'post',
			datatype: 'json',
			data: {
				type: $(this).val()
			},
			success: function(response, opts)
			{
				
				var param = {
							itemtype: "ITILCategory",
							display_emptychoice: 1,
							displaywith: [],
							emptylabel: "-----",
							condition: response,
							used: [],
							toadd: [],
							entity_restrict: "2",
							permit_select_parent: 0,
							specific_tags: [],
							// searchText: term,
							page_limit: 100 // page size
							// page: page, // page number
						};

				$.ajax({
					url: '../ajax/getDropdownValue.php',
					method: 'post',
					datatype: 'json',
					data: param,
					success: function(response, opts){
						console.log("getDropdownValue réponse");
						console.log(JSON.parse(response));

						$("input[name='childticketmanager_category']").select2({
							dropdownAutoWidth: true,
							data: function(){
								return JSON.parse(response);
							}
						});

					}
				});
			}
		});
	};

	childticketmanager_submit = function(e, opts){
			opts = opts || {};
			
			var activeTab = childticketmanager_getActiveTabId();
			
			// ui-id-3 = Onglet Ticket. Dans ce cas, la valeur du statut est définie par la zone 
			// de liste sur l'interface
			
			// ui-id-4 = Onglet Traitement du ticket. Dans ce cas, la valeur du statut arrive
			// en data dans l'événement.
			
			// Dans tous les autres cas, on met un statut à -1. De toute façon, on n'a rien à faire
			// si on se trouve sur un autre onglet que les deux spécifiés plus haut. 
			
			if(activeTab == "ui-id-3")
				var status = $("[id^='dropdown_status']").val();
			else if(activeTab == "ui-id-4")
				var status = e.data.ticketStatus || opts.ticketStatus;
			else
				var status = -1;

			var childrenUpdated = e.data.childrenUpdated || opts.childrenUpdated;

			if(!childrenUpdated && (status == 5 || status == 6) )
			{
				e.preventDefault();

				$.ajax({
					url: '../plugins/childticketmanager/ajax/childticketmanager_childtix.php',
					method: 'post',
					dataType: "json",
					data: {	'tickets_id': getUrlParameter('id'), 'tickets_status': status  },
					success: function(response, opts) {
						$(e.currentTarget).trigger('click', {'ticketStatus': status, 'childrenUpdated': true });
					},
					error: function(response, status, error){
						console.log(response);
						console.log(status);
						console.log(error);
					}
				});
			}
		};
		
	$(document).ready(function() {
		
		childticketmanager_addCloneLink(childticketmanager_bindClick);
		
		$(".glpi_tabs").on("tabsload", function(event, ui) {
			childticketmanager_addCloneLink(childticketmanager_bindClick);
			
			$("input[name='update']").on("click", {'childrenUpdated': false}, childticketmanager_submit);
			$("input[name='add_close']").on("click", {'ticketStatus': 6, 'childrenUpdated': false}, childticketmanager_submit);
		});
		
		$("input[name='update']").on("click", {'childrenUpdated': false}, childticketmanager_submit);
		$("input[name='add_close']").on("click", {'ticketStatus': 6, 'childrenUpdated': false}, childticketmanager_submit);

		$("select[id^='dropdown_type']").on("change", childticketmanager_change);
			
		
		$(document).ajaxComplete(function(event, request, settings) {
			
			if(typeof settings.data != "undefined")
			{
				var params = settings.data.split("&");
				if(params[0] == "action=viewsubitem" && params[1] == "type=Solution")
				{
					$("input[name='update']").on("click", {'ticketStatus': 5, 'childrenUpdated': false}, childticketmanager_submit);
					$("input[name='add_close']").on("click", {'ticketStatus': 6, 'childrenUpdated': false}, childticketmanager_submit);
				}
			}
		});
		
		
	});
		
JAVASCRIPT;
	
	echo $JS;
}