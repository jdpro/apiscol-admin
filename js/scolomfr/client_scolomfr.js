//***************************************************************
// Fonctions nécessaires au bon fonctionnement d'un formulaire
// SCOLOMFR généré dynamiquement par l'application de maquettage
//***************************************************************

// Charge l'arbre dynatree d'un vocabulaire contrôlé
// dans un élément de formulaire
// Entrées :
//    - numero : niméro du vocabulaire controlé (16,21,22)
//    - données : tableau d'objets dynatree
function init_arbre_dynatree(numero, donnees) {
	var id_tree = parseInt(numero);
	// vide entièrement le div contenant l'arbre
	$("#tree_" + id_tree).empty();
	// génère l'arbre dynaTree
	$("#tree_" + id_tree).dynatree({
		title : "Arbre " + id_tree, // pour debug seulement
		minExpandLevel : 1,
		autoCollapse : false,
		clickFolderMode : 3, // activate et expand
		checkbox : true,
		selectMode : 3, // multi ascendant au clique sur checkbox
		imagePath : './_css',
		noLink : true,
		strings : {
			loading : "Chargement…",
			loadError : "Erreur de chargement!"
		},
		children : donnees,
		debugLevel : 1,
		generateIds : false,
		idPrefix : "tree_" + id_tree, // pour gérer plusieurs arbres
		cookieId : "tree_" + id_tree, // pour gérer plusieurs arbres
		onSelect : function(flag, node) {
			// récupère les noeuds sélecitonnés
			var selectedNodes = node.tree.getSelectedNodes();
			var selectedKeys = $.map(selectedNodes, function(node) {
				return node.data.key;
			});
			// Ajoute dans le champ input la liste des éléments scolomfr avec
			// séparateur ';'
			$("#tree_input_" + id_tree).val(selectedKeys.join(";"));
		}
	});
	// on vide la liste d'élément sélectionnés
	$("#tree_input_" + id_tree).val('');
}

// **********************************************
// CALENDRIER
// **********************************************
// Affiche en html un calendrier dans un span et mise à jour des champs textes
// contenant les valeurs sélectionnées
// Entrees :
// annee mois jour courants
// id du span du calendrier à afficher
// id de l'input de l'année, mois et jour (pour maj des valeurs)
function calendrier(annee, mois, jour, id_span_calendrier, id_annee, id_mois,
		id_jour) {
	// Convertir en entier
	annee = parseInt(annee);
	mois = parseInt(mois);
	jour = parseInt(jour);
	// Données
	var monthNames = [ "Janvier", "F\351vrier", "Mars", "Avril", "Mai", "Juin",
			"Juillet", "Ao\373t", "Septembre", "Octobre", "Novembre",
			"D\351cembre" ];
	var monthDays = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

	// cas du mois de février pour année bisextile
	if (((annee % 4 == 0) && (annee % 100 != 0)) || (annee % 400 == 0)) {
		monthDays[1] = 29;
	}

	// test si le jour passé est supérieur au nombre de jour du mois
	// pour le cas où l'utilisateur a changé de mois
	if (jour > monthDays[mois - 1]) {
		jour = monthDays[mois - 1];
	}

	var nb_jours_du_mois = monthDays[mois - 1];
	var jour_de_debut = (new Date(annee, mois - 1, 1)).getDay(); // 0
	// dimanche
	// 6 :
	// samedi

	var donneesHTML = "<table class=\"calendrier\">";

	// En tête 1 :
	// pour sélectionner le mois précédent
	donneesHTML = donneesHTML + "<tr><th><a href=\"javascript:calendrier(";
	if (mois == 1) {
		donneesHTML = donneesHTML + (annee - 1);
	} else {
		donneesHTML = donneesHTML + annee;
	}
	donneesHTML = donneesHTML + ", ";
	if (mois == 1) {
		donneesHTML = donneesHTML + 12;
	} else {
		donneesHTML = donneesHTML + (mois - 1);
	}
	donneesHTML = donneesHTML + ", " + jour + ", '" + id_span_calendrier
			+ "', '" + id_annee + "', '" + id_mois + "', '" + id_jour
			+ "');\" class=\"mois_precedent\">&lt;</a></th>";
	// le mois et l'année sont affichées dans la barre du haut
	donneesHTML = donneesHTML + "<th colspan = 5>" + monthNames[mois - 1] + " "
			+ annee + "</th>";

	// pour sélectionner le mois suivant
	donneesHTML = donneesHTML + "<th><a href=\"javascript:calendrier(";
	if (mois == 12) {
		donneesHTML = donneesHTML + (annee + 1);
	} else {
		donneesHTML = donneesHTML + annee;
	}
	donneesHTML = donneesHTML + ", ";
	if (mois == 12) {
		donneesHTML = donneesHTML + 1;
	} else {
		donneesHTML = donneesHTML + (mois + 1);
	}
	donneesHTML = donneesHTML + ", " + jour + ", '" + id_span_calendrier
			+ "', '" + id_annee + "', '" + id_mois + "', '" + id_jour
			+ "');\" class=\"mois_suivant\">&gt;</a></th>";

	// En tête 2 : les jours de la semaine
	donneesHTML = donneesHTML
			+ "<tr><th>Dim</th><th>Lun</th><th>Mar</th><th>Mer</th><th>Jeu</th><th>Ven</th><th>Sam</th></tr>";

	// Jours du mois
	var colonne = 0;
	var i = 0;
	// décalage des jours vides dé début de mois
	for (i = 0; i < jour_de_debut; i++) {
		donneesHTML = donneesHTML + "<td>&nbsp;</td>";
		colonne++;
	}
	// données du calendrier
	for (i = 1; i <= nb_jours_du_mois; i++) {
		donneesHTML = donneesHTML + "<td align=\"center\">";
		if (i == jour) {
			donneesHTML = donneesHTML
					+ "<font class=\"jour_selectionne\"><strong>" + i
					+ "</strong></font>";
		} else {
			donneesHTML = donneesHTML + "<a href=\"javascript:calendrier("
					+ annee + ", " + mois + ", " + i + ", '"
					+ id_span_calendrier + "', '" + id_annee + "', '" + id_mois
					+ "', '" + id_jour + "');\" class=\"jour_calendrier\">" + i
					+ "</a>";
		}
		donneesHTML = donneesHTML + "</td>";
		colonne++;
		if (colonne == 7) {
			donneesHTML = donneesHTML + "</tr><tr>";
			colonne = 0;
		}
	}
	// comble les jours vides en fin de mois
	if (colonne != 0) {
		for (i = colonne; i < 7; i++) {
			donneesHTML = donneesHTML + "<td>&nbsp;</td>";
		}
	}
	donneesHTML = donneesHTML + "</tr></table>";

	// écrit les données dans le span
	document.getElementById(id_span_calendrier).innerHTML = donneesHTML;

	// écrit jour/mois/annee dans donnée formulaire
	document.getElementById(id_jour).value = jour;
	document.getElementById(id_mois).value = mois;
	document.getElementById(id_annee).value = annee;
}

// Ouvre ou Ferme le calendrier d'un élément de formulaire
// Entrée :
// - ordre : numero d'élément de formulaire (0..n)
// - numero : si champ multivalué numéro du champ à traiter (0..n)
function ouvrir_calendrier(ordre, numero) {
	if (document.getElementById('calendrier_' + ordre + '_' + numero).style.display != '') {
		var annee = document.getElementById('annee_c_' + ordre + '_' + numero).value;
		var mois = document.getElementById('mois_c_' + ordre + '_' + numero).value;
		var jour = document.getElementById('jour_c_' + ordre + '_' + numero).value;
		var date_jour = new Date();
		if (annee == null || annee == 0) {
			annee = date_jour.getFullYear();
		}
		if (mois == null || mois == 0) {
			mois = date_jour.getMonth() + 1;
		}
		if (jour == null || jour == 0) {
			jour = date_jour.getDate();
		}
		// mise à jour du calendrier
		calendrier(annee, mois, jour, 'calendrier_' + ordre + '_' + numero,
				'annee_c_' + ordre + '_' + numero, 'mois_c_' + ordre + '_'
						+ numero, 'jour_c_' + ordre + '_' + numero);
		// affiche le calendrier
		document.getElementById('calendrier_' + ordre + '_' + numero).style.display = '';
	} else {
		document.getElementById('calendrier_' + ordre + '_' + numero).style.display = 'none';
	}
}


