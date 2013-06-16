var outerLayout;
var innerLayout;
var queryBox;
var DEFAULTS = {
	NORTH_PANE : 45,
	WEST_PANE : 350,
	SOUTH_PANE : 45,
	START_PAGE : 0,
	ROWS : 20
};
var globalCheckBox;
function secundaryInit() {
	globalCheckBox = $("input#select-all-resources.css-checkbox");
	queryBox = $("div.content div.pane div.query-container form input#query");
	var $centerContainer = $("div.content>div.pane.ui-layout-center");
	if ($centerContainer.length == 0)
		return;
	var resize = function() {
		$("div.inner-layout").width("100%").height("100%");
	};
	var outerLayoutOptions = {
		defaults : {
			applyDemoStyles : true
		},
		north : {
			resizable : true
		},
		west : {
			resizable : true,
			onresize_end : resize
		}
	};
	var innerLayoutOptions = {
		defaults : {
			applyDemoStyles : true
		},
		south : {
			resizable : true
		}
	};
	if ($("input#north-pane").length == 0)
		outerLayoutOptions.north.size = DEFAULTS.NORTH_PANE;
	else if (parseInt($("input#north-pane").val()) == 0)
		outerLayoutOptions.north.initClosed = true;
	else
		outerLayoutOptions.north.size = parseInt($("input#north-pane").val());
	if ($("input#west-pane").length == 0)
		outerLayoutOptions.west.size = DEFAULTS.WEST_PANE;
	else if (parseInt($("input#west-pane").val()) == 0)
		outerLayoutOptions.west.initClosed = true;
	else
		outerLayoutOptions.west.size = parseInt($("input#west-pane").val());
	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var screenHeight = $(window).height() - headerHeight;
	$('div.content').height(screenHeight);
	outerLayout = $('div.content').layout(outerLayoutOptions);
	resize();
	if ($("input#south-pane").length == 0)
		innerLayoutOptions.south.size = DEFAULTS.SOUTH_PANE;
	else if (parseInt($("input#south-pane").val()) == 0)
		innerLayoutOptions.south.initClosed = true;
	else
		innerLayoutOptions.south.size = parseInt($("input#south-pane").val());
	innerLayout = $("div.inner-layout").height($centerContainer.innerHeight())
			.width($centerContainer.innerWidth()).layout(innerLayoutOptions);
	$centerContainer.css("padding", 0).css("overflow", "visible");
	var activeLabel = $("#active-tab").val();
	var active = 0;
	var counter = 0;
	$("#facets h3").each(function(indel, elem) {
		if ($(elem).children("a").first().text() == activeLabel)
			active = counter;
		counter++;
	});
	var minHeight = (parseInt($("#facets h3").length) + 3) * 30;
	var westPaneHeight = $("div.content div.pane.ui-layout-west").height()
			- $("div.content div.pane div.query-container").height();
	$("#facets").height(Math.max(minHeight, westPaneHeight));
	$("#facets").accordion({
		heightStyle : "fill",
		active : active
	});
	var selected = function(event, ui) {
		$(this).popup("close");
	}
	$("#rows-per-page-open").button({
		icons : {
			primary : "ui-icon-list",
			secondary : "ui-icon-triangle-1-s"
		}
	}).popup({
		popup : $('#rows-per-page'),
		position : {
			my : "right top",
			at : "right bottom"
		}
	}).next().menu({
		// select : selected,
		trigger : $("#rows-per-page-open")
	});
	outerLayout.allowOverflow($("#rows-per-page"));
	$("div.ui-accordion-content ul li a", "#facets")
			.add("div.filters-list a.ui-state-default")
			.add("div.filters-list span.facet a")
			.add(
					"div.content div.pane div.inner-layout div.ui-layout-center table thead tr th ul li a")
			.add(
					"div.content div.pane div.inner-layout div.ui-layout-south a.pagination")
			.add(
					"div.content div.pane ul#rows-per-page.ui-popup li.ui-menu-item a.ui-corner-all")
			.click(
					function(e) {
						$target = $(e.target).closest("a");
						var urlend = "";
						if (!$target.hasClass("pagination")
								&& !$target.hasClass("rows-per-page"))
							urlend += getPaginationParameters();
						urlend += getDisplayParameters();
						urlend += getQueryParameter();
						$target.attr("href", $target.attr("href") + urlend);
					});
	$("div.content div.pane div.query-container form input#query").keypress(
			function(e) {
				if (e.which == 13) {
					submitQuery();
					return false;
				}
			});
	var spellcheckUrl = $("input#query").attr("data-suggest");
	$("input#query")
			.autocomplete(
					{
						source : function(req, add) {
							var queryTerms = req.term.split(/\s+/);
							var queryTerm = queryTerms.pop();
							var stub = queryTerms.join(" ");
							$
									.ajax({
										type : "GET",
										url : spellcheckUrl + "/"
												+ encodeURI(queryTerm),
										headers : {
											accept : "application/atom+xml"
										},
										error : function(msg) {
											console.log("Error !: " + msg);
										},
										success : function(xmlData) {
											var suggestions = new Array();
											var suggestion;
											$(xmlData)
													.find(
															"apiscol\\:word, word")
													.each(
															function(index,
																	elem) {
																suggestion = $(
																		elem)
																		.text()
																		.replace(
																				/~\d.?\d*/g,
																				"")
																		.replace(
																				/\*/g,
																				"");
																if ($
																		.inArray(
																				suggestion,
																				suggestions) == -1) {
																	var concat = stub
																			+ " "
																			+ suggestion;
																	suggestions
																			.push({
																				label : concat,
																				value : concat,
																				sourceData : $(
																						elem)
																						.attr(
																								"source") == "data"
																						|| $(
																								elem)
																								.parent()
																								.attr(
																										"source") == "data"
																			});
																}

															});
											add(suggestions);
										}
									});

						},
						select : function(event, ui) {
							submitQuery(ui.item.value)
						}
					});
	$(
			"div.pane div.inner-layout div.ui-layout-center table form.delete-control")
			.each(
					function(index, elem) {
						$(elem)
								.button({
									icons : {
										primary : "ui-icon-trash"
									},
									text : false
								})
								.click(
										function() {
											$
													.confirm(
															"Vous vous apprêtez à supprimer définitivement cette ressource ainsi que son contenu.",
															"Avertissement de suppression",
															function() {
																$(elem)
																		.attr(
																				"action",
																				completeUri($(
																						elem)
																						.attr(
																								"action")));
																$(elem)
																		.submit();
															});
										});
					});
	$(
			"div.pane div.inner-layout div.ui-layout-center table form.refresh-control")
			.each(
					function(index, elem) {
						$(elem)
								.button({
									icons : {
										primary : "ui-icon-arrowrefresh-1-w"
									},
									text : false
								})
								.click(
										function() {
											$
													.confirm(
															"Cette action reconstruira l'archive téléchargeable, les prévisualisations, réindexera la ressource et ses métadonnées dans l'index de SolR et signalera les problèmes éventuels.",
															"Non implémenté",
															function() {
																$(elem)
																		.attr(
																				"action",
																				completeUri($(
																						elem)
																						.attr(
																								"action")));
																$(elem)
																		.submit();
															});
										});
					});
	$(
			"html.js body div.content div.pane div.inner-layout div.ui-layout-center table thead tr th span.delete-selection-button")
			.button({
				icons : {
					primary : "ui-icon-trash"
				},
				text : false
			})
			.click(
					function() {
						$
								.confirm(
										"Cette action supprimera les ressources sélectionnées.",
										"Non implémenté", function() {

										});
					});
	;
	$(
			"html.js body div.content div.pane div.inner-layout div.ui-layout-center table thead tr th span.refresh-selection-button")
			.button({
				icons : {
					primary : "ui-icon-arrowrefresh-1-w"
				},
				text : false
			})
			.click(
					function() {
						$
								.confirm(
										"Cette action lancera le processus de mise à jour (archive, index du moteur de recherche, prévisualisation) pour les ressources sélectionnées.",
										"Non implémenté", function() {

										});
					});
	$(
			"html.js body div.content div.pane div.inner-layout div.ui-layout-center table tbody tr td a.resource-download-link")
			.button({
				icons : {
					primary : "ui-icon-extlink"
				},
				text : false
			});
	globalCheckBox.change(function(e) {
		var mainCheckboxIsChecked = e.target.checked;
		$("div.ui-layout-center table tbody tr td input.css-checkbox").attr(
				"checked", mainCheckboxIsChecked);
		globalCheckBox.removeClass("semi-checked");
	});
	$("div.ui-layout-center table tbody tr td input.css-checkbox").change(
			function(event) {
				var nothingIsChecked = true;
				var everythingIsChecked = true;
				$("div.ui-layout-center table tbody tr td input.css-checkbox")
						.each(
								function(index, elem) {
									var checked = $(elem).is(':checked');
									nothingIsChecked = nothingIsChecked
											&& !checked;
									everythingIsChecked = everythingIsChecked
											&& checked;
								});
				if (nothingIsChecked) {
					globalCheckBox.removeClass("semi-checked");
					globalCheckBox.removeAttr("checked");
				} else if (everythingIsChecked) {
					globalCheckBox.removeClass("semi-checked");
					globalCheckBox.attr("checked", "checked");
				} else {
					globalCheckBox.addClass("semi-checked");
					globalCheckBox.removeAttr("checked");
				}
			});

}
function submitQuery(query) {
	var href = $("div.content div.pane div.query-container form")
			.attr("action");
	href = completeUri(href, query);
	window.location = href;
}
function completeUri(href, query) {
	if (!query)
		query = queryBox.val();
	if (query.match(/^\s*$/))
		query = "~"
	href += getPaginationParameters();
	href += getDisplayParameters();
	href += "/query/" + query;
	return href;
}
function getDisplayParameters() {
	var west;
	if (outerLayout.state.west.isClosed)
		west = 0;
	else
		west = outerLayout.state.west.size;
	var north;
	if (outerLayout.state.north.isClosed)
		north = 0;
	else
		north = outerLayout.state.north.size;
	var south;
	if (innerLayout.state.south.isClosed)
		south = 0;
	else
		south = innerLayout.state.south.size;
	var active = $("#facets").accordion("option", "active");
	if (!active || isNaN(active))
		active = 0;
	var activeLabel = $("#ui-accordion-facets-header-" + active + " a").text();
	if (activeLabel.match(/^\s*$/))
		activeLabel = "~";
	if (isNaN(north))
		north = DEFAULTS.NORTH_PANE;
	if (isNaN(west))
		west = DEFAULTS.WEST_PANE;
	if (isNaN(south))
		south = DEFAULTS.SOUTH_PANE;
	return "/active-tab/[" + encodeURIComponent(activeLabel) + "]/west-pane/"
			+ west + "/north-pane/" + north + "/south-pane/" + south;
}
function getPaginationParameters() {
	var start = DEFAULTS.START_PAGE;
	var rows = DEFAULTS.ROWS;
	if ($("input#start").length > 0)
		start = parseInt($("input#start").val());
	if ($("input#rows").length > 0)
		rows = parseInt($("input#rows").val());
	return "/start/" + start + "/rows/" + rows;
}
function getQueryParameter() {
	var query = $("input#query.ui-autocomplete-input").val();
	return "/query/" + (query.match(/^\s*$/) ? "~" : query);
}