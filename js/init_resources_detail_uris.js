var activeLabel;
var tabEverActivated = new Object();
var putBlocked;
var buttonOriginalText;
var saveApiscolLinkHtml;
function secundaryInit() {
	activeLabel = "second_menu_item_uris";
	refreshTabsState();
}
function refreshTabsState() {
	displayActiveLabel();

	$('html').addClass($.fn.details.support ? 'details' : 'no-details');
	$('details').details();

}