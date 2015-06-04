jQuery(function($) {
	
	// Choosing channel on the post-channel edit/add form
	$('#cmvl-choose-channel input[type=radio]').change(function() {
		var channel = $(this).parents('figure').first();
		$('#title').val(channel.data('name'));
		$('#title-prompt-text').hide();
		var description = JSON.parse(channel.data('description'));
		if (!description) description = '';
		$('#content').val(description);
		if (tinyMCE.activeEditor) {
			tinyMCE.activeEditor.setContent(description.replace("\n", '<br>'));
		}
	});
	
	
	$('.cmvl-report-filter select').change(function() {
		$(this).parents('form').submit();
	});
	

	$('.cmvl-report-table .cmvl-actions a[data-confirm]').click(function() {
		return confirm($(this).data('confirm'));
	});
	
	
	$('.cmvl-subscription-add-form').each(function() {
		var form = $(this);
		
		form.find('a.add').click(function() {
			form.find('.inner').show();
			$(this).blur();
			return false;
		});
		
		var loginInput = form.find('input[name=user_login]');
		loginInput.autocomplete({
			source:    ajaxurl + '?action=cmvl_user_suggest',
			delay:     500,
			minLength: 2,
//			position:  position,
			open: function() {
				$( this ).addClass( 'open' );
			},
			close: function() {
				$( this ).removeClass( 'open' );
			}
		});
		
		var postInput = form.find('input[name=post_find]');
		postInput.autocomplete({
			source:    ajaxurl + '?action=cmvl_post_suggest',
			delay:     500,
			minLength: 2,
//			position:  position,
			open: function() {
				$( this ).addClass( 'open' );
			},
			close: function() {
				$( this ).removeClass( 'open' );
			},
			select: function( event, ui ) {
				postInput.val('');
				form.find('.cmvl-subscription-add-post span').text(ui.item.label);
				form.find('.cmvl-subscription-add-post input').val(ui.item.value);
				form.find('.cmvl-subscription-add-find-post').hide();
				form.find('.cmvl-subscription-add-post').show();
			}
		});
		
		form.find('.cmvl-subscription-add-post-remove').click(function() {
			postInput.val('');
			form.find('.cmvl-subscription-add-post span').text('');
			form.find('.cmvl-subscription-add-post input').val(0);
			form.find('.cmvl-subscription-add-find-post').show();
			form.find('.cmvl-subscription-add-post').hide();
			postInput.focus();
			return false;
		});
		
		
	});
	
	
	// After submit post-channel edit/add form
	$('form#post').submit(function(e) {
		var setError = function(msg) {
			e.preventDefault();
			e.stopPropagation();
			alert(msg);
		};
		// Force to choose channel.
		if ($('#cmvl-choose-channel input[type=radio]').length > 0 && $('#cmvl-choose-channel input[type=radio]:checked').length == 0) {
			setError('Please choose the Vimeo album.');
		}
		// Force to choose at least one category for channel.
		else if ($('#cmvl_categorychecklist input[type=checkbox]').length > 0 && $('#cmvl_categorychecklist input[type=checkbox]:checked').length == 0) {
			setError('Please select at least one category.');
		}
	});
	
	
	$('.cmvl-settings-tabs a').click(function() {
		var match = this.href.match(/\#tab\-([^\#]+)$/);
		$('#settings .settings-category.current').removeClass('current');
		$('#settings .settings-category-'+ match[1]).addClass('current');
		$('.cmvl-settings-tabs a.current').removeClass('current');
		$('.cmvl-settings-tabs a[href=#tab-'+ match[1] +']').addClass('current');
		this.blur();
	});
	if (location.hash.length > 0) {
		$('.cmvl-settings-tabs a[href='+ location.hash +']').click();
	} else {
		$('.cmvl-settings-tabs li:first-child a').click();
	}
	
	
	$('.cmvl-mp-cost-add').click(function() {
		var button = $(this);
		var p = button.parents('p').first();
		p.before(button.data('template').replace(/\%s/g, ''));
		p.prev().find('.cmvl-mp-cost-remove').click(mpCostRemove);
		return false;
	});
	
	var mpCostRemove = function() {
		var button = $(this);
		button.parents('div').first().remove();
		return false;
	};
	$('.cmvl-mp-cost-remove').click(mpCostRemove);
	
});