var ned = {
	key : "ned",
	title : "Niveau éducatif détaillé",
	standard : "LOMFRv1.0",
	purpose : "educational level",
	source : "scolomfr-voc-022",
	help : "Précisez à quel(s) niveau(x) de classes la séquence se destine.",
	mandatory : true
};
var ens = {
	key : "ens",
	title : "Domaines d'enseignement",
	standard : "SCOLOMFRv1.0",
	purpose : "domaine d'enseignement",
	source : "scolomfr-voc-015",
	help : "Précisez la discipline pour laquelle la séquence est adaptée.",
	mandatory : true
};
var scc = {
	key : "scc",
	title : "Compétences du socle commun",
	standard : "LOMFRv1.0",
	purpose : "competency",
	source : "scolomfr-voc-016",
	help : "Précisez les compétences du socle commun.",
	mandatory : true
};
var classifications;
var dataSource = $("input#prefix").val() + "/data.php";
var trees = new Object();
var disableDynatreeOnSelect;
var submitButton;

function initScolomFr() {

	classifications = new Object();
	$("#ned_tree").append(ajouterChampProgramme(ned));
	$("#ens_tree").append(ajouterChampProgramme(ens));
	$("#scc_tree").append(ajouterChampProgramme(scc));
	addSubmitButton();

	$("form#formulaire_scolomfr").attr("action",
			$("form#formulaire_scolomfr").attr("action") + "/async").ajaxForm({
		dataType : 'xml',
		cache : true,
		beforeSend : function() {
			activateSubmitButton(false);
			putWaiterOnSubmitButton(true);
		},
		complete : function(xhr) {
			activateSubmitButton(false);
			putWaiterOnSubmitButton(false);
			console.log(xhr.responseXML);
		}
	});
	$("textarea#general-description").add($("input#general-title")).keyup(
			function() {
				activateSubmitButton(true);
			}).change(function() {
		activateSubmitButton(true);
	});
	$("ul", "#keyword-container").tagit({
		itemName : 'general-keywords',
		fieldName : 'general-keyword[]',
		placeholderText : 'Saisissez puis "entrée"',
		allowSpaces : true,
		afterTagAdded : function() {
			activateSubmitButton(true);
		},
		afterTagRemoved : function() {
			activateSubmitButton(true);
		}
	});

	initializeSelect("generalResourceType", "general-generalResourceType");
	initializeSelect("learningResourceType", "educational-learningResourceType");
	initializeSelect("place", "educational-place");
	initializeSelect("educationalMethod", "educational-educationalMethod");
	initializeSelect("activity", "educational-activity");
	initializeSelect("intendedEndUserRole", "educational-intendedEndUserRole");
	$("div#difficulty-container.element div select").change(function() {
		activateSubmitButton(true);
	});
	handleContributors();

}
function setDirty(bool) {
	if (bool)
		$(window)
				.bind(
						'beforeunload',
						function() {
							return "Voulez vous réellement abandonner vos modifications non enregistrées ?";
						});
	else
		$(window).unbind('beforeunload');
}
function initializeSelect(key, name) {
	var tagitContainer = $("div#" + key
			+ "-container.element div.entries-container div.elt_champ_form ul",
			"form#formulaire_scolomfr");
	$("div#" + key + "-container.element span.register-entry",
			"form#formulaire_scolomfr").button({
		icons : {
			primary : "ui-icon-add"
		},
		text : true
	}).click(function() {
		var value = $("div#" + key + "-container.element div select").val();
		addEntryListInput(tagitContainer, value, key, name);
	});
	tagitContainer.tagit({
		itemName : name,
		fieldName : name + '[]',
		allowSpaces : true,
		afterTagRemoved : function() {
			activateSubmitButton(true);
		}

	});
	tagitContainer.find("input.ui-widget-content").attr("disabled", "disabled");
}

function addEntryListInput(tagitContainer, value, key, name) {
	var yetPresent = tagitContainer.tagit("assignedTags").indexOf(value) > -1;
	$("div#" + key + "-container.element span.tagit-label").each(
			function(i, e) {
				if ($(e).text() == value)
					yetPresent = true;
			});
	$("span.ui-state-error.duplicate-alert", "div#" + key + "-container")
			.toggle(yetPresent).effect(yetPresent ? "pulsate" : "");
	if (yetPresent) {
		return;
	}
	tagitContainer.tagit("createTag", value);
	activateSubmitButton(true);
}

function addSubmitButton() {
	submitButton = $(document.createElement("span")).attr("title",
			"enregistrer").button({
		icons : {
			primary : "ui-icon-save"
		},
		text : false
	}).click(function() {
		$("form#formulaire_scolomfr").submit();
	});
	$("h2.submit-button-container", "div#edit.detail-pane")
			.append(submitButton);
	activateSubmitButton(false);
	putWaiterOnSubmitButton(false);

}
function activateSubmitButton(bool) {
	submitButton.button(bool ? "enable" : "disable");
	setDirty(bool);
}
function putWaiterOnSubmitButton(bool) {
	if (bool)
		submitButton.find("span.ui-icon").removeClass("ui-icon-save").addClass(
				"ui-icon-wait");
	else
		submitButton.find("span.ui-icon").removeClass("ui-icon-wait").addClass(
				"ui-icon-save");
}
function ajouterChampProgramme(program) {
	var container = $(document.createElement("div")).addClass(
			"ui-widget-content container-editeur");
	trees[program.key] = $(document.createElement("div")).appendTo(container)
			.dynatree(
					{
						onSelect : function(select, node) {
							if (disableDynatreeOnSelect)
								return;
							var selectedNodes = node.tree.getSelectedNodes();
							updateProgramClassificationInput(selectedNodes,
									program.source, program.purpose,
									program.standard);
							activateSubmitButton(true);

						},
						checkbox : true,

						selectMode : 3,
						initAjax : {

							url : dataSource,
							ajaxDefaults : {
								cache : true,
							},
							data : {
								data : program.key
							}
						},
						onPostInit : function() {
							mettreAJourChampProgramme(program);
							var selectedNodes = trees[program.key]
									.dynatree("getSelectedNodes");
							updateProgramClassificationInput(selectedNodes,
									program.source, program.purpose,
									program.standard);
						}

					});

	return container;
}
function updateProgramClassificationInput(selectedNodes, sourceIdentifier,
		purpose, standard) {
	var classification = getOrCreateClassification(purpose, standard);
	clearTaxonPaths(classification, sourceIdentifier);
	var taxonPath;
	var index = 0;
	var node;
	while (selectedNode = selectedNodes[index]) {
		taxonPath = createTaxonPath(classification, sourceIdentifier);
		node = selectedNodes[index];
		addEntriesToTaxonPath(taxonPath, node);
		index++;
	}
	$("input#classifications").val(JSON.stringify(classifications));
}
function getOrCreateClassification(purpose, standard) {
	var classification = getClassification(purpose);
	if (!classification)
		classification = createClassification(purpose, standard);
	return classification;

}
function getClassification(purpose) {
	var classification = classifications[purpose];
	return classification;
}

function getTaxonPaths(classification, sourceIdentifier) {
	var taxonPaths = new Array();
	classification.children("taxonPath").each(function(index, elem) {
		var sourceInTaxonPath = $(elem).find("source").find("string").text();
		if (sourceIdentifier == sourceInTaxonPath)
			taxonPaths.push($(elem));
	});
	return taxonPaths;
}
function clearTaxonPaths(classification, sourceIdentifier) {
	if (!classification["taxonPaths"])
		classification["taxonPaths"] = new Array();
	classification["taxonPaths"] = jQuery.grep(classification["taxonPaths"],
			function(elem, index) {
				return (elem != "null" && elem["source"] != sourceIdentifier);
			});
}
function createTaxonPath(classification, sourceIdentifier) {
	var taxonPath = new Object();
	classification["taxonPaths"].push(taxonPath);
	taxonPath["source"] = sourceIdentifier;
	taxonPath["taxons"] = new Array();
	return taxonPath;
}
function clearTaxons(taxonPath) {
	taxonPath.children("taxon").each(function(index, elem) {
		elem.parentNode.removeChild(elem);
	});
}
function createClassification(purpose, standard) {
	classifications[purpose] = new Object();
	classifications[purpose]['standard'] = standard;
	classifications[purpose]['taxonPaths'] = new Array();
	return classifications[purpose];
}
function addEntriesToTaxonPath(taxonPath, selectedNode) {
	addTaxon(taxonPath, selectedNode.data.key, selectedNode.data.title);
	while (typeof selectedNode.getParent == 'function'
			&& selectedNode.getParent()) {
		selectedNode = selectedNode.getParent();
		if (!selectedNode.data.key)
			continue;
		addTaxon(taxonPath, selectedNode.data.key, selectedNode.data.title);
	}
}
function addTaxon(taxonPath, id, title) {
	if (!id || typeof id === undefined || id.match(/^_\d+$/))
		return;
	var taxon = {
		"id" : id,
		"entry" : title
	};
	taxonPath["taxons"].unshift(taxon);
}
function mettreAJourChampProgramme(program) {
	disableDynatreeOnSelect = true;
	trees[program.key].dynatree("getRoot").visit(
			function(node) {
				if (!node.isStatusNode() && !node.hasChildren()) {
					if (hasProgramEntry(node.data.key, program.purpose,
							program.standard, program.source))
						node.select();
				}
			});
	disableDynatreeOnSelect = false;
}
function hasProgramEntry(id, purpose, standard, source) {
	var found = false;
	$("div.purpose").each(
			function(index, elem) {
				if (source.indexOf($(elem).find(".source-data").attr(
						"data-source")) >= 0) {
					$(elem).find(".source-data").find(".entry-data").each(
							function(index, elem) {
								if ($(elem).attr("data-id") == id)
									found = true;
							});
				}
				;
			});
	return found;
}
function handleContributors() {
	$("tr.role td.vcard-string", "div#contributors-container").each(
			function(index, elem) {
				var vcardString = $(elem).text();
				$(elem).closest("tr").data(vcardString);
				$(elem).replaceWith(vCard.initialize(vcardString).to_html());
			});
	$("div#contributors-container.element span.register-entry",
			"form#formulaire_scolomfr").button({
		icons : {
			primary : "ui-icon-add"
		},
		text : true
	}).click(function() {

	});
}
