var putBlocked;
var uploadScanLink;
var awaitedFileTransferReports = new Array();
var awaitedUrlParsingReports = new Array();
var urlParsingCurrentStates = new Object();
var fileTransferCurrentStates = new Object();
var sendFileButton;
function secundaryInit() {
	activeLabel = "second_menu_item_edit";
	showProgressBar(false);
	showProgressInformation(false);
	refreshTabsState();
}
function refreshTabsState() {
	displayActiveLabel();
	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var navHeight = $("div#tabs").height();
	var availableHeight = $(window).height() - headerHeight - navHeight - 20;

	$("#edit").height(availableHeight).layout({
		defaults : {
			applyDemoStyles : true
		},
		south : {
			resizable : true,
			size : 150
		},
		west : {
			resizable : true,
			size : "50%"
		}
	});
	$(
			"html.js body div.content div#edit.detail-pane div.pane form div#resource-type input[type=submit]")
			.remove();
	var buttonSetSupport = $("div#edit.detail-pane div.pane div#resource-type");
	$("div#edit.detail-pane div.pane div#resource-type")
			.buttonset()
			.find("input")
			.click(
					function() {
						if (putBlocked)
							return false;
						if (this.id == "resource-type-asset"
								&& $("form#update_url").length > 0
								&& !$("form#update_url input[type=text]").val()
										.match(/^\s*$/))
							$
									.confirm(
											"En transformant cette ressource en ressource locale, vous allez effacer l'url que vous avez enregistrée.",
											"Effacement du lien", function() {

												buttonSetSupport
														.closest("form")
														.submit();
											});
						else
							buttonSetSupport.closest("form").submit();

					});
	sendFileButton = $("form#send_file input#file_submit",
			"div#edit.detail-pane").button().click(function() {
		if (putBlocked)
			return false;
		$('form#send_file input#file_upload').trigger('click');
		return false;
	});
	$('form#send_file input#file_upload').change(function(e) {
		if (!putBlocked)
			$('form#send_file').submit();
	}).click(function() {
		return !putBlocked;
	});
	var bar = $('.bar');
	$('form#send_file').attr("action",
			$('form#send_file').attr("action") + "/async").ajaxForm({
		dataType : 'xml',
		cache : true,
		beforeSend : function() {
			if (putBlocked)
				return false;
			showProgressBar(true);
			$('form#send_file').find("input").addClass("ui-state-disabled");
			putBlocked = true;
			var percentVal = '0%';
			bar.width(percentVal);
		},
		uploadProgress : function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			bar.width(percentVal);
		},
		success : function(result) {
			var percentVal = '100%';
			bar.width(percentVal);
		},
		complete : function(xhr) {
			handleFileUploadSuccess(xhr.responseXML);
			showProgressBar(false);
		}
	});
	refreshFileList();
	$("form#update_url")
			.attr("action", $('form#update_url').attr("action") + "/async")
			.ajaxForm(
					{
						dataType : 'xml',
						cache : true,
						beforeSend : function() {
							if (putBlocked)
								return false;
							$("span.url-submit", "form#update_url").hide();
							activateUrlField(false);
							putBlocked = true;
							displayResult(
									"Envoi au serveur",
									"L'url que vous avez soumise est envoyée au serveur",
									"wait");
						},
						complete : function(xhr) {
							updateLinkButton();
							handleUrlUpdateSuccess(xhr.responseXML);
						}
					});
	$("form#update_url").find("input[type=text]").blur(function() {
		$(this).removeClass("focusField");
	}).focus(function() {
		$(this).addClass("focusField");
	}).keydown(function() {
		$("form#update_url span.url-submit").show();
	});
	$("form#update_url a").button({
		icons : {
			primary : "ui-icon-extlink"
		},
		text : false
	});
	$("span.url-submit", "form#update_url").text("Mettre à jour").button({
		icons : {
			primary : "ui-icon-valid"
		},
		text : true
	}).hide().click(function() {
		$("form#update_url").submit();
	});
	$("div.pane form.add-content input", "div#edit.detail-pane").button();
	initScolomFr();
	$('html').addClass($.fn.details.support ? 'details' : 'no-details');
	$('details').details();
}
function updateLinkButton() {
	$("a.ui-button", "form#update_url").attr("href",
			$("input", "form#update_url").val());
}
function activateUrlField(bool) {
	if (bool) {
		$("form#update_url").find("input[type=text]").removeClass(
				"ui-state-disabled").removeAttr("disabled");
	} else {
		$("form#update_url").find("input[type=text]").addClass(
				"ui-state-disabled").attr("disabled", "disabled");
	}
}
function showProgressBar(bool) {
	$("div.pane div.file-input-container form#send_file div.bar",
			"div#edit.detail-pane").toggle(bool);
}
function showProgressInformation(bool) {
	$("div.progress").toggle(bool);
}
function getDisplayParameters() {
	return "";
}
function handleFileUploadSuccess(data) {
	if (!data)
		displayResult("Contenu inconnu", "Pas de réponse du serveur", "error");
	else if (data.firstChild.tagName == "error") {
		{
			displayResult($(data).find("intro").text(), $(data).find("message")
					.text(), "error");
			$('form#send_file').find("input").removeClass("ui-state-disabled");
			putBlocked = false;
		}
	} else if ($(data).find("apiscol\\:state, state").text() == "done") {
		{
			displayResult("Fichier transféré",
					"Le fichier a été transféré sur le serveur", "running");
			$('form#send_file').find("input").removeClass("ui-state-disabled");
			putBlocked = false;
		}
	} else {
		displayResult("Début du traitement",
				"Le serveur analyse le fichier que vous avez fourni", "running");
		var linkElement = $(data).find("link[rel='self']");
		awaitedFileTransferReports.push(linkElement.attr("href"));
		scanForFileTransferReports();
	}
}
function handleUrlUpdateSuccess(data) {
	if (!data)
		displayResult("Contenu inconnu", "Pas de réponse du serveur", "error");
	else if (data.firstChild.tagName == "error") {
		{
			displayResult($(data).find("intro").text(), $(data).find("message")
					.text(), "error");
			activateUrlField(true);
			putBlocked = false;
		}
	} else if ($(data).find("apiscol\\:state, state").text() == "done") {
		{
			displayResult("Lien accepté",
					"Le lien a été correctement pris en compte", "success");
			activateUrlField(true);
			putBlocked = false;

		}
	} else if ($(data).find("apiscol\\:state, state").text() == "initiated") {
		displayResult("Début du traitement",
				"Le serveur interroge le lien que vous avez fourni", "running");
		var linkElement = $(data).find("link[rel='self']");
		awaitedUrlParsingReports.push(linkElement.attr("href"));
		scanForUrlParsingReports();
	}
}
function handleTransferReport(data) {
	var state = $(data).find("apiscol\\:state,state").text();
	var link = $(data).find("link[rel='self']").attr("href");
	if (state == "done") {
		displayResult("Fichier transféré",
				"Le fichier a été transféré et indexé sur le serveur",
				"success");
		$('form#send_file').find("input").removeClass("ui-state-disabled");
		putBlocked = false;
		requestFileList();
	} else if (state == "aborted") {
		$('form#send_file').find("input").removeClass("ui-state-disabled");
		putBlocked = false;
		displayResult("Abandon",
				"Le fichier n'a pas pu être ajouté à la ressource :"
						+ $(data).find("apiscol\\:message, message").text(),
				"error");
	} else if (state == "pending") {
		displayResult("En cours", "Le fichier est en cours d'indexation",
				"running");
	}

	if (state == "done" || state == "aborted") {
		awaitedFileTransferReports.shift();

	}
	setTimeout(scanForFileTransferReports, 500);
}
function handleUrlParsing(data) {
	var state = $(data).find("apiscol\\:state, state").text();
	var link = $(data).find("link[rel='self']").attr("href");
	if (state == "done") {
		displayResult(
				"Url scannée",
				"L'url a été enregistrée et scannée sur le serveur de ressources.",
				"success");
		activateUrlField(true);
		// maj de l'url
		putBlocked = false;
	} else if (state == "aborted") {
		displayResult("Abandon", "L'url n'a pas pu être enregistrée :"
				+ $(data).find("apiscol\\:message, message").text(), "error");
		activateUrlField(true);
		// maj de l'url
		putBlocked = false;
	} else if (state == "initiated")
		displayResult("En cours", "L'url est en cours d'enregistrement",
				"running");

	if (state == "done" || state == "aborted") {
		awaitedUrlParsingReports.shift();

	}
	setTimeout(scanForUrlParsingReports, 500);
}
function displayResult(status, message, decoration) {
	var decorationClass = "ui-corner-all ";
	var imgsrc = "";
	switch (decoration) {
	case "error":
		decorationClass += "ui-state-error ";
		imgsrc = "warning.png";
		break;
	case "running":
		decorationClass += "ui-state-focus ";
		imgsrc = "running.gif";
		break;
	case "wait":
		decorationClass += "ui-widget-content ";
		imgsrc = "wait-icon.gif";
		break;
	case "success":
		decorationClass += "ui-state-highlight ";
		imgsrc = "success.png";
		break;
	}
	showProgressInformation(true);
	$("#upload-result-message").removeClass().addClass(decorationClass).text(
			message);
	$("#upload-result-status").removeClass().addClass(decorationClass).find(
			"span").text(status);
	$icon = $("#upload-result-status .status-icon");
	if (imgsrc == "")
		$icon.hide();
	else
		$icon.attr("src", $icon.attr("data-src") + imgsrc).fadeIn();
}
function scanForFileTransferReports() {
	var transferReportUrl = awaitedFileTransferReports[0];
	if (!transferReportUrl)
		return;
	$.ajax({
		dataType : 'xml',
		type : "GET",
		url : window.location + "/file-transfer-report/" + transferReportUrl
				+ "/async",
		error : function(msg) {
			$('form#send_file').find("input").removeClass("ui-state-disabled");
			putBlocked = false;
			console.log(msg);
		},
		success : function(result) {
			handleTransferReport(result);
		}
	});
}
function scanForUrlParsingReports() {
	var urlParsingUrl = awaitedUrlParsingReports[0];
	if (!urlParsingUrl)
		return;
	$.ajax({
		dataType : 'xml',
		type : "GET",
		url : window.location + "/url-parsing-report/" + urlParsingUrl
				+ "/async",
		error : function(msg) {
			console.log(msg);
		},
		success : function(result) {
			handleUrlParsing(result);

		}
	});
}
function requestFileList() {
	var $list = $("div#edit.detail-pane div.pane ul.files-list");
	$.ajax({
		type : "GET",
		url : $list.attr("data-src"),
		dataType : 'html',
		error : function(msg) {
			console.log(msg);
		},
		success : function(result) {
			refreshFileList(result);
		}
	});
}
function refreshFileList(result) {
	var $list = $("div#edit.detail-pane div.pane ul.files-list");
	if (result)
		$list.replaceWith(result);
	$("span.do-main-file form", "div#edit.detail-pane").each(
			function(index, elem) {
				$(elem).attr("action", $(elem).attr("action") + "/async")
						.ajaxForm(
								{
									dataType : 'html',
									cache : true,
									beforeSend : function() {
										if (putBlocked)
											return false;
										putBlocked = true;
										$(elem).find("span.ui-icon")
												.removeClass("ui-icon-main")
												.addClass("ui-icon-wait");
									},
									complete : function(xhr) {
										refreshFileList(xhr.responseText);
										putBlocked = false;
									}
								}).button({
							icons : {
								primary : "ui-icon-main"
							},
							text : false
						}).click(function() {
							if (putBlocked)
								return false;
							$(this).closest("form").submit();
						});
			});

	$("span.delete-file form", "div#edit.detail-pane")
			.each(
					function(index, elem) {
						$(elem)
								.attr("action",
										$(elem).attr("action") + "/async")
								.ajaxForm(
										{
											dataType : 'html',
											cache : true,
											beforeSend : function() {
												putBlocked = true;
												$(elem)
														.find("span.ui-icon")
														.removeClass(
																"ui-icon-trash")
														.addClass(
																"ui-icon-wait");
											},
											complete : function(xhr) {
												refreshFileList(xhr.responseText);
												putBlocked = false;
											}
										})
								.button({
									icons : {
										primary : "ui-icon-trash"
									},
									text : false
								})
								.click(
										function() {
											if (putBlocked)
												return false;
											self = this;
											$
													.confirm(
															"Êtes-vous sûr(e) de vouloir supprimer ce fichier (action irréversible).",
															"Confirmer la suppression",

															function() {
																if (putBlocked)
																	return false;
																$(self)
																		.closest(
																				"form")
																		.submit();
															});
										});
					});
	$("div.pane ul.files-list li a", "div#edit.detail-pane").button({
		icons : {
			primary : "ui-icon-download"
		},
		text : false
	});
}
