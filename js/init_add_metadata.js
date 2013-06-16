var putBlocked;
var displayFrame;
var scannedUrl;
function secundaryInit() {
	var headerHeight = $("html>body>header.ui-helper-clearfix").height();
	var navHeight = $("div#tabs").height();
	var availableHeight = $(window).height() - headerHeight - navHeight - 20;
	$("html.js body div.add-metadata").height(availableHeight).layout({
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
	var bar = $('.bar');
	var percent = $('.percent');
	var status = $('#status');
	$('form#import-metadata').attr("action",
			$('form#import-metadata').attr("action") + "/async").ajaxForm({
		beforeSend : function() {
			if (putBlocked)
				return false;
			showProgressBar(true);
			$(this).find("input").attr("disabled", true);
			putBlocked = true;
			status.empty();
			var percentVal = '0%';
			bar.width(percentVal);
			percent.html(percentVal);

		},
		uploadProgress : function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			bar.width(percentVal);
			percent.html(percentVal);
		},
		success : function(result) {
			var percentVal = '100%';
			bar.width(percentVal);
			percent.html(percentVal);

		},
		complete : function(result) {
			displayImportResult(result.responseText);
			$(this).find("input").attr("disabled", false);
			putBlocked = false;
			showProgressBar(false);
		}
	});
	$('form#import-metadata input#metadata_submit').button().click(function() {
		$('form#import-metadata input#metadata_upload').trigger('click');
		return false;
	});
	$('form#import-metadata input#metadata_upload').change(function() {
		if (!putBlocked)
			$('form#import-metadata').submit();
	}).click(function() {
		return !putBlocked;
	});
	showProgressBar(false);
}
function showProgressBar(bool) {
	$(
			"html.js div.content div.add-metadata div.pane div.add-metadata-input-container div.progress")
			.toggle(bool);
}

function displayImportResult(result) {
	$("div#add-metadata-result").html(result);
	$("div p.imported-metadata-admin-uri a", "div#add-metadata-result")
			.button();
	$(
			"div div.ui-widget-content div.content-suggestion-wrapper form.register-url input",
			"div#add-metadata-result").button();
	$(
			"div div.ui-widget-content div.content-suggestion-wrapper form.register-url",
			"div#add-metadata-result")
			.each(
					function(index, elem) {
						$(elem)
								.attr("action",
										$(elem).attr("action") + "/async")
								.ajaxForm(
										{
											dataType : 'xml',
											beforeSend : function() {
												if (putBlocked)
													return false;
												$(elem).find("input").remove();
												putBlocked = true;
											},
											complete : function(result) {
												displayFrame = $(elem)
														.closest(
																"div.content-suggestion-wrapper")
														.empty()
														.append(
																$('<div id="upload-result-status"><img class="status-icon"/><span></span></div><div id="upload-result-message"></div>'));
												handleUrlUpdateSuccess(result.responseXML);
											}
										});
					});

}
function handleUrlUpdateSuccess(data) {
	if (!data)
		displayResult("Contenu inconnu", "Pas de réponse du serveur", "error");
	else if (data.firstChild.tagName == "error") {
		{
			displayResult($(data).find("intro").text(), $(data).find("message")
					.text(), "error");
			putBlocked = false;
		}
	} else if ($(data).find("apiscol\\:state, state").text() == "done") {
		{
			displayResult("Lien accepté",
					"Le lien a été correctement pris en compte", "success");
			putBlocked = false;

		}
	} else if ($(data).find("apiscol\\:state, state").text() == "initiated") {
		displayResult("Début du traitement",
				"Le serveur interroge le lien que vous avez fourni", "running");
		var linkElement = $(data).find("link[rel='self']");
		scannedUrl = linkElement.attr("href");
		scanForUrlParsingReports();
	}
}
function handleUrlParsing(data) {
	var state = $(data).find("apiscol\\:state, state").text();
	var link = $(data).find("link[rel='self']").attr("href");
	if (state == "done") {
		displayResult(
				"Url scannée",
				"L'url a été enregistrée et scannée sur le serveur de ressources.",
				"success");
		putBlocked = false;
	} else if (state == "aborted") {
		displayResult("Abandon", "L'url n'a pas pu être enregistrée :"
				+ $(data).find("apiscol\\:message, message").text(), "error");
		putBlocked = false;
	} else if (state == "initiated")
		displayResult("En cours", "L'url est en cours d'enregistrement",
				"running");

	if (state != "done" && state != "aborted") {
		setTimeout(scanForUrlParsingReports, 500);

	}

}
function displayResult(status, message, decoration) {
	var decorationClass = "ui-corner-all ";
	var imgsrc = "";
	switch (decoration) {
	case "error":
		decorationClass += "ui-state-error ";
		imgsrc = "warning.png"
		break;
	case "running":
		decorationClass += "ui-state-focus ";
		imgsrc = "running.gif"
		break;
	case "wait":
		decorationClass += "ui-widget-content ";
		imgsrc = "wait-icon.gif"
		break;
	case "success":
		decorationClass += "ui-state-highlight ";
		imgsrc = "success.png"
		break;
	}
	$("#upload-result-message").removeClass().addClass(decorationClass).text(
			message);
	$("#upload-result-status").removeClass().addClass(decorationClass).find(
			"span").text(status);
	$icon = $("#upload-result-status .status-icon");
	if (imgsrc == "")
		$icon.hide();
	else
		$icon.attr("src", getHiddenParameter("icon-dir") + imgsrc).fadeIn();
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
			$
			putBlocked = false;
			console.log(msg);
		},
		success : function(result) {
			handleRefreshProcessReport(result);
		}
	});
}
function scanForUrlParsingReports() {
	$.ajax({
		dataType : 'xml',
		type : "GET",
		url : window.location + "/url-parsing-report/" + scannedUrl + "/async",
		error : function(msg) {
			console.log(msg);
		},
		success : function(result) {
			handleUrlParsing(result);

		}
	});
}