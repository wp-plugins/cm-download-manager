jQuery(function($) {
	
	$('.cmdm-settings-tabs a').click(function() {
		var match = this.href.match(/\#tab\-([^\#]+)$/);
		$('#settings .settings-category.current').removeClass('current');
		$('#settings .settings-category-'+ match[1]).addClass('current');
		$('.cmdm-settings-tabs a.current').removeClass('current');
		$('.cmdm-settings-tabs a[href=#tab-'+ match[1] +']').addClass('current');
		this.blur();
	});
	if (location.hash.length > 0) {
		$('.cmdm-settings-tabs a[href='+ location.hash +']').click();
	} else {
		$('.cmdm-settings-tabs li:first-child a').click();
	}
	
	
});