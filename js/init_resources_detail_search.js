var searchQueryUrl;
var textInput;
var resultSearchArea;
function secundaryInit() {
	activeLabel = "second_menu_item_search";
	resultSearchArea = $("div.search-results");
	textInput = $("form#search_test_request input[type=text]");
	searchQueryUrl = $("form#search_test_request").attr("action");
	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var navHeight = $("div#tabs").height();
	var availableHeight = $(window).height() - headerHeight - navHeight - 20;

	$("#search").height(availableHeight).layout({
		defaults : {
			applyDemoStyles : true
		},
		west : {
			resizable : true,
			size : "200px"
		}
	});
	displayActiveLabel();

	textInput.blur(function() {
		$(this).removeClass("focusField");
	}).focus(function() {
		$(this).addClass("focusField");
	});
	var spellcheckUrl = textInput.attr("data-suggest");
	$("form#search_test_request input[type=submit]").button().click(function() {
		submitQuery()
	});
	$("form#search_test_request").submit(function() {
		return false;
	});
	textInput
			.autocomplete({
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
											.find("apiscol\\:word, word")
											.each(
													function(index, elem) {
														suggestion = $(elem)
																.text()
																.replace(
																		/~\d.?\d*/g,
																		"")
																.replace(/\*/g,
																		"");
														if ($.inArray(
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
}
function submitQuery(query) {
	if (!query)
		query = textInput.val();
	href = searchQueryUrl + "?mode=async&query=" + query;
	$.ajax({
		dataType : 'html',
		type : "GET",
		url : href,
		error : function(msg) {
			$('form#send_file').find("input").removeClass("ui-state-disabled");
			$
			putBlocked = false;
			console.log(msg);
		},
		success : function(result) {
			resultSearchArea.html(result);
			listenToLinks();
		}
	});
}
function listenToLinks() {
	$("div.spellcheck-suggest a.ui-state-focus", "div#search.detail-pane")
			.click(function() {
				var link = $(this).attr("href");
				var analyze = /\/search\?query=(.+)$/.exec(link);
				if (analyze && analyze.length > 1) {
					textInput.val(analyze[1]);
					submitQuery();
				}
				return false;
			})
}
