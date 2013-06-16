function secundaryInit() {
	$("div.home-bloc").hover(function() {
		$(this).addClass("ui-state-highlight");
	}, function() {
		$(this).removeClass("ui-state-highlight");
	});
}