var activeLabel;
var putBlocked;
var buttonOriginalText;
var saveApiscolLinkHtml;
var submitButton;
var displayConsole;
var scanIsRunning = false;
var awaitedRefreshProcessReports = new Array();
function secundaryInit() {
	displayConsole = $("div.pane div.console-area", "div#refresh.detail-pane");
	activeLabel = "second_menu_item_refresh";
	displayActiveLabel();
	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var navHeight = $("div#tabs").height();
	var availableHeight = $(window).height() - headerHeight - navHeight - 20;

	$("#refresh").height(availableHeight).layout({
		defaults : {
			applyDemoStyles : true
		},
		west : {
			resizable : true,
			size : "50%"
		}
	});
	initRefreshPreviewControls();
	initRefreshArchiveControls();
	initRefreshResourceIndexControls();
	initRefreshMetaIndexControls();
	initSyncTechInfosControls();
	

}
function initRefreshPreviewControls() {
	var $refreshPreviewForm = $("form#refresh-resource-preview",
			"div#refresh.detail-pane");
	$refreshPreviewForm.find("input[type=submit]").remove();
	var $refreshPreviewDisplayArea = $refreshPreviewForm
			.next("div.display-result");
	$refreshPreviewForm.attr("action",
			$refreshPreviewForm.attr("action") + "/async").ajaxForm(
			{
				dataType : 'xml',
				cache : true,
				beforeSend : function() {

					changeButtonState($refreshPreviewForm, "pending");
					displayResult("Veuillez patienter",
							"La requête de mise à jour a été envoyée.", "wait",
							$refreshPreviewDisplayArea, "blue");

				},
				complete : function(xhr) {
					handleRefreshRequestFirstResponse(xhr.responseXML,
							$refreshPreviewDisplayArea, $refreshPreviewForm,
							"blue");
				}
			});
	$refreshPreviewForm.button({
		icons : {
			primary : "ui-icon-preview",
			secondary : "ui-icon-refresh-state"
		}
	}).click(function() {
		if ($refreshPreviewForm.hasClass("ui-state-disabled"))
			return false;
		$refreshPreviewForm.submit();
	});
}
function initRefreshArchiveControls() {
	var $refreshArchiveForm = $("form#refresh-resource-archive",
			"div#refresh.detail-pane");
	$refreshArchiveForm.find("input[type=submit]").remove();
	var $refreshArchiveDisplayArea = $refreshArchiveForm
			.next("div.display-result");
	$refreshArchiveForm.attr("action",
			$refreshArchiveForm.attr("action") + "/async").ajaxForm(
			{
				dataType : 'xml',
				cache : true,
				beforeSend : function() {

					changeButtonState($refreshArchiveForm, "pending");
					displayResult("Veuillez patienter",
							"La requête de mise à jour a été envoyée.", "wait",
							$refreshArchiveDisplayArea, "red");

				},
				complete : function(xhr) {
					handleRefreshRequestFirstResponse(xhr.responseXML,
							$refreshArchiveDisplayArea, $refreshArchiveForm,
							"red");
				}
			});
	$refreshArchiveForm.button({
		icons : {
			primary : "ui-icon-archive",
			secondary : "ui-icon-refresh-state"
		}
	}).click(function() {
		if ($refreshArchiveForm.hasClass("ui-state-disabled"))
			return false;
		$refreshArchiveForm.submit();
	});
}
function initRefreshResourceIndexControls() {
	var $refreshResourceIndexForm = $("form#refresh-resource-index",
			"div#refresh.detail-pane");
	$refreshResourceIndexForm.find("input[type=submit]").remove();
	var $refreshIndexDisplayArea = $refreshResourceIndexForm
			.next("div.display-result");
	$refreshResourceIndexForm.attr("action",
			$refreshResourceIndexForm.attr("action") + "/async").ajaxForm(
			{
				dataType : 'xml',
				cache : true,
				beforeSend : function() {

					changeButtonState($refreshResourceIndexForm, "pending");
					displayResult("Veuillez patienter",
							"La requête de mise à jour a été envoyée.", "wait",
							$refreshIndexDisplayArea, "black");

				},
				complete : function(xhr) {
					handleRefreshRequestFirstResponse(xhr.responseXML,
							$refreshIndexDisplayArea,
							$refreshResourceIndexForm, "black");
				}
			});
	$refreshResourceIndexForm.button({
		icons : {
			primary : "ui-icon-solr",
			secondary : "ui-icon-refresh-state"
		}
	}).click(function() {
		if ($refreshResourceIndexForm.hasClass("ui-state-disabled"))
			return false;
		$refreshResourceIndexForm.submit();
	});
}
function initRefreshMetaIndexControls() {
	var $refreshMetaIndexForm = $("form#refresh-meta-index",
			"div#refresh.detail-pane");
	$refreshMetaIndexForm.find("input[type=submit]").remove();
	var $refreshMetaIndexDisplayArea = $refreshMetaIndexForm
			.next("div.display-result");
	$refreshMetaIndexForm.attr("action",
			$refreshMetaIndexForm.attr("action") + "/async").ajaxForm(
			{
				dataType : 'xml',
				cache : true,
				beforeSend : function() {

					changeButtonState($refreshMetaIndexForm, "pending");
					displayResult("Veuillez patienter",
							"La requête de mise à jour a été envoyée.", "wait",
							$refreshMetaIndexDisplayArea, "navy");

				},
				complete : function(xhr) {
					handleMetaIndexRefreshRequestFirstResponse(xhr.responseXML,
							$refreshMetaIndexDisplayArea,
							$refreshMetaIndexForm, "navy");
				}
			});
	$refreshMetaIndexForm.button({
		icons : {
			primary : "ui-icon-solr",
			secondary : "ui-icon-refresh-state"
		}
	}).click(function() {
		if ($refreshMetaIndexForm.hasClass("ui-state-disabled"))
			return false;
		$refreshMetaIndexForm.submit();
	});
}

function initSyncTechInfosControls() {
	var $syncTechInfosForm = $("form#sync-tech-infos",
			"div#refresh.detail-pane");
	$syncTechInfosForm.find("input[type=submit]").remove();
	var $syncTechInfosDisplayArea = $syncTechInfosForm
			.next("div.display-result");
	$syncTechInfosForm.attr("action",
			$syncTechInfosForm.attr("action") + "/async").ajaxForm(
			{
				dataType : 'xml',
				cache : true,
				beforeSend : function() {

					changeButtonState($syncTechInfosForm, "pending");
					displayResult("Veuillez patienter",
							"La requête de mise à jour a été envoyée.", "wait",
							$syncTechInfosDisplayArea, "green");

				},
				complete : function(xhr) {
					handleSyncInfosRequestFirstResponse(xhr.responseXML,
							$syncTechInfosDisplayArea, $syncTechInfosForm,
							"green");
				}
			});
	$syncTechInfosForm.button({
		icons : {
			primary : "ui-icon-tech",
			secondary : "ui-icon-refresh-state"
		}
	}).click(function() {
		if ($syncTechInfosForm.hasClass("ui-state-disabled"))
			return false;
		$syncTechInfosForm.submit();
	});
}
function changeButtonState(button, state) {
	var iconClass = "ui-icon-refresh-state";
	switch (state) {
	case "default":
		iconClass = "ui-icon-refresh-state";
		break;
	case "pending":
		iconClass = "ui-icon-refresh-state-wait";
		break;
	case "success":
		iconClass = "ui-icon-refresh-state-success";
		break;
	case "error":
		iconClass = "ui-icon-refresh-state-error";
		break;

	default:
		break;
	}
	if (button.hasClass(iconClass))
		return;
	button.find("span.ui-icon.ui-button-icon-secondary").removeClass(
			"ui-icon-refresh-state").removeClass("ui-icon-refresh-state-wait")
			.removeClass("ui-icon-refresh-state-success").removeClass(
					"ui-icon-refresh-state-error").addClass(iconClass);
	if (state == "pending")
		button.addClass("ui-state-disabled");
	else
		button.removeClass("ui-state-disabled")
}
function handleSyncInfosRequestFirstResponse(data, $displayArea,
		$refreshPreviewForm, color) {
	if (!data)
		displayResult("Problème inconnu", "Pas de réponse du serveur", "error");
	else if (data.firstChild.tagName == "infos") {
		var infos = "Les informations suivantes ont été reportées dans les métadonnées :"
		infos += "<p>-Taille : " + $(data).find("apiscol\\:size, size").text()
				+ "</p>";
		infos += "<p>-Langage : "
				+ $(data).find("apiscol\\:language, language").text() + "</p>";
		infos += "<p>-Localisation : "
				+ $(data).find("apiscol\\:location, location").text() + "</p>";
		infos += "<p>-Localisation technique : "
				+ $(data).find(
						"apiscol\\:technical-location, technical-location")
						.text() + "</p>";
		infos += "<p>-Format : "
				+ $(data).find("apiscol\\:format, format").text() + "</p>";
		infos += "<p>-Prévisualisation : "
				+ $(data).find("apiscol\\:preview, preview").text() + "</p>";

		displayResult("Mise à jour terminée", infos, "success", $displayArea,
				color);
		changeButtonState($refreshPreviewForm, "success");

	}
}
function handleMetaIndexRefreshRequestFirstResponse(data, $displayArea,
		$refreshPreviewForm, color) {
	if (!data)
		displayResult("Problème inconnu", "Pas de réponse du serveur", "error");
	else if (data.firstChild.tagName == "entry") {
		var infos = "Les métadonnées ont été réindexées dans le moteur de recherche.";

		displayResult("Mise à jour terminée", infos, "success", $displayArea,
				color);
		changeButtonState($refreshPreviewForm, "success");

	}
}
function handleRefreshRequestFirstResponse(data, $displayArea,
		$refreshPreviewForm, color) {
	if (!data)
		displayResult("Problème inconnu", "Pas de réponse du serveur", "error");
	else if (data.firstChild.tagName == "error") {
		{
			displayResult($(data).find("intro").text(), $(data).find("message")
					.text(), "error", $displayArea, color);
			changeButtonState($refreshPreviewForm, "error");
		}
	} else if ($(data).find("apiscol\\:state, state").text() == "done") {
		{
			displayResult("Processus terminé",
					"Le processus s'est terminé avec succès", "success",
					$displayArea, color);
			changeButtonState($refreshPreviewForm, "success");
		}
	} else {
		displayResult("Début du traitement",
				"Le traitement commencera dès que possible", "running",
				$displayArea, color);
		var linkElement = $(data).find("link[rel='self']");
		awaitedRefreshProcessReports.push({
			url : linkElement.attr("href"),
			element : $displayArea,
			button : $refreshPreviewForm,
			color : color
		});
		if (!scanIsRunning)
			scanForRefreshProcessReports();
	}
}
function scanForRefreshProcessReports() {
	scanIsRunning = true;
	if (awaitedRefreshProcessReports.length == 0) {
		scanIsRunning = false;
		return;
	}
	var refreshProcessReport = awaitedRefreshProcessReports[0];

	$.ajax({
		dataType : 'xml',
		type : "GET",
		url : window.location + "/refresh-process-report/"
				+ refreshProcessReport.url + "/async",
		error : function(msg) {
			console.log(msg);
		},
		success : function(result) {
			handleRefreshProcessReport(result, refreshProcessReport.element,
					refreshProcessReport.button, refreshProcessReport.color);
		}
	});
}
function handleRefreshProcessReport(data, element, button, color) {
	if (data.firstChild.tagName == "error") {
		{
			displayResult($(data).find("intro").text(), $(data).find("message")
					.text(), "error", element, color);
			changeButtonState(button, "error");
			awaitedRefreshProcessReports.shift();
			return;
		}
	}
	var state = $(data).find("apiscol\\:state,state").text();
	var message = $(data).find("apiscol\\:message,message").text();
	var link = $(data).find("link[rel='self']").attr("href");
	if (state == "done") {
		displayResult("Processus terminé",
				"Le processus s'est terminé avec succès : " + message,
				"success", element, color);
		changeButtonState(button, "success");
	} else if (state == "aborted") {
		$('form#send_file').find("input").removeClass("ui-state-disabled");
		displayResult("Abandon", "Le processus a échoué :" + message, "error",
				element, color);
		changeButtonState(button, "error");
	} else if (state == "pending") {
		displayResult("En cours", "Le processus est en cours : " + message,
				"running", element, color);
		changeButtonState(button, "pending");
	}

	if (state == "done" || state == "aborted") {
		awaitedRefreshProcessReports.shift();

	}
	setTimeout(scanForRefreshProcessReports, 500);
}
function displayResult(status, message, decoration, element, color) {
	var decorationClass = "ui-corner-all ";
	var imgsrc = "";
	switch (decoration) {
	case "error":
		decorationClass += "ui-state-error ";
		imgsrc = "warning.png";
		break;
	case "running":
		decorationClass += "ui-state-active ";
		imgsrc = "running.gif";
		break;
	case "wait":
		decorationClass += "ui-state-active ";
		imgsrc = "wait-icon.gif";
		break;
	case "success":
		decorationClass += "ui-state-active ";
		imgsrc = "success.png";
		break;
	}
	element.removeClass().addClass(decorationClass).html(
			"<h5>" + status + "</h5>");
	var moreLines = (decoration == "success") ? "<p></p>" : "";
	displayConsole.html(displayConsole.html() + '<p style="color:' + color
			+ '">' + message + "</p>" + moreLines);
	fixHeightProblems();
}
function fixHeightProblems() {
	if (displayConsole.get(0).scrollHeight > displayConsole.get(0).clientHeight) {
		var first = displayConsole.find("p").first();
		first.animate({
			"margin-top" : -first.height()
		}, {
			complete : function() {
				displayConsole.find("p").first().remove();
				fixHeightProblems();
			}
		});
	}
}
function getDisplayParameters() {
	return "";
}