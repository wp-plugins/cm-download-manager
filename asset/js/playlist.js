(function($) {

window.CMVL.Playlist = function(container) {
	
	this.playerState = null;
	this.statsAjaxSuccessCallback = null;
	this.container = container;
	
	this.initDescriptionHandler();
	this.initSearchHandler();
	this.initNavbarHandler();
	this.initBookmarkHandler();
	this.initVideoListHandler();
	this.initVideoHandlers();
	this.initMicropaymentsHandler();
	this.initPaginationHandler();
	
};



window.CMVL.Playlist.prototype.initDescriptionHandler = function() {
	var duration = 500;
	$('figcaption', this.container)
	.mouseenter(function() {
		var inner = $(this).find('.cmvl-description-inner');
		if (!inner.data('defaultMaxHeight')) {
			inner.data('defaultMaxHeight', inner.css('max-height'));
		}
		inner.animate({"max-height" : inner[0].scrollHeight + "px"}, duration);
	})
	.mouseleave(function() {
		var inner = $(this).find('.cmvl-description-inner');
		inner.animate({"max-height" : inner.data('defaultMaxHeight')}, duration);
	});
};


window.CMVL.Playlist.prototype.initVideoHandlers = function() {
	this.initNotesHandler();
	this.initPlayerEventsHandler();
};



window.CMVL.Playlist.prototype.initMicropaymentsHandler = function() {
	var playlist = this;
	$('.cmvl-channel-micropayments', playlist.container).submit(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var form = $(this);
		if (form.find('input[type=radio]:checked').length != 0) {
			var data = form.serialize() +"&"+ $.param({action: "cmvl_channel_activate"});
			$.post(CMVLSettings.ajaxUrl, data, function(response) {
				if (response.success) {
					CMVL.Utils.toast(response.msg, 'success');
					playlist.loadURL(response.channelUrl);
				} else {
					CMVL.Utils.toast(response.msg, 'error');
				}
			});
		}
	});
};


window.CMVL.Playlist.prototype.initSearchHandler = function() {
	var playlist = this;
	window.CMVL.Utils.addSingleHandler('cmvl-search-submit', $('form.cmvl-search', playlist.container), 'submit', function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		playlist.loaderShow();
		var obj = $(this);
		obj.find(':input').blur();
		var data = obj.serialize() +"&"+ $.param({action: "cmvl_video_search"});
		$.post(CMVLSettings.ajaxUrl, data, function(response) {
			response = $(response);
			playlist.container.find('.cmvl-navbar-navigation').remove();
			playlist.container.find('.cmvl-playlist').html(response.html());
			playlist.loaderHide();
			new window.CMVL.Playlist(playlist.container);
		});
	});
};


window.CMVL.Playlist.prototype.initNotesHandler = function() {
	var playlist = this;
	$('.cmvl-notes', playlist.container).focus(function() {
		var obj = $(this);
		obj.data('defaultHeight', obj.outerHeight());
		obj.animate({height: '10em'});
	}).blur(function() {
		var obj = $(this);
		obj.animate({height: obj.data('defaultHeight') + "px"});
	}).change(function() {
		var currentVideo = $(this).parents('.cmvl-video').first();
		var data = {
				action: 'cmvl_video_set_user_note',
				channelId: currentVideo.data('channelId'),
				videoId: currentVideo.data('videoId'),
				note: $(this).val()
		};
		$.post(CMVLSettings.ajaxUrl, data, function(response) {
			// ok
		});
	});
};


window.CMVL.Playlist.prototype.initNavbarHandler = function() {
	var playlist = this;
	$('.cmvl-navbar select[name=category], .cmvl-navbar select[name=channel]').change(function(e) {
		var obj = $(this);
		if (playlist.container.data('useAjax')) {
			playlist.loadURL(obj.val());
		} else {
			location.href = obj.val();
		}
	});
};


window.CMVL.Playlist.prototype.loadURL = function(url) {
	var playlist = this;
	playlist.loaderShow();
	$.ajax({
		url: url,
		success: function(response) {
			response = $(response);
			playlist.container.html(response.find('.cmvl-channel-main-query .cmvl-widget-playlist').html());
			playlist.loaderHide();
			new window.CMVL.Playlist(playlist.container);
		}
	});
};


window.CMVL.Playlist.prototype.pause = function() {
	var iframe = this.container.find('figure iframe')[0];
	if (iframe.froogaloopHandler) {
		iframe.froogaloopHandler.api('pause');
	}
};


window.CMVL.Playlist.prototype.removePlayerEventsHandler = function() {
	var iframe = this.container.find('figure iframe');
	for (var i=0; i<iframe.length; i++) {
		var frame = iframe[i];
		if (frame.froogaloopHandler) {
//			console.log('detaching');
			frame.froogaloopHandler.removeEvent('playProgress');
			frame.froogaloopHandler.removeEvent('ready');
			frame.froogaloopHandler.removeEvent('play');
			frame.froogaloopHandler.removeEvent('seek');
			frame.froogaloopHandler.removeEvent('finish');
			frame.froogaloopHandler.removeEvent('pause');
		}
	}
};

	
window.CMVL.Playlist.prototype.initVideoListHandler = function() {
	var playlist = this;
	$('.cmvl-video-list a', playlist.container).click(CMVL.Utils.leftClick(function(e) {
		var link = $(this);
		playlist.statsAjaxSuccessCallback = function() {
			playlist.statsAjaxSuccessCallback = null;
			playlist.removePlayerEventsHandler();
			$.ajax({
				url: link.attr('href'),
				success: function(response) {
					var doc = $(response);
	//				playlist.container.find('figure.cmvl-video').replaceWith(doc.find('.entry-content figure.cmvl-video'));
					playlist.container.find('.cmvl-ajax-content').html(doc.find('.entry-content .cmvl-ajax-content').html());
					link.parents('nav').first().find('li.current').removeClass('current');
					link.parents('li').first().addClass('current');
					playlist.initVideoHandlers();
					playlist.loaderHide();
				}
			});
		};
		playlist.loaderShow();
		if (playlist.playerState == 'play') {
			playlist.pause();
		} else {
			playlist.statsAjaxSuccessCallback();
		}
	}));
};
	
	
window.CMVL.Playlist.prototype.initBookmarkHandler = function() {
	var playlist = this;
	$('.cmvl-bookmark', playlist.container).click(function(ev) {
		ev.stopPropagation();
		ev.preventDefault();
		var button = $(this);
		button.parents('a').first().blur();
		var video = button.parents('.cmvl-video').first();
		var bookmark = 'add';
		if (button.hasClass('on')) bookmark = 'remove';
		var data = {
				action: 'cmvl_video_set_user_bookmark',
				channelId: video.data('channelId'),
				videoId: video.data('videoId'),
				bookmark: bookmark
		};
		$.post(CMVLSettings.ajaxUrl, data, function(response) {
			if (response.status == 'ok') {
				if (bookmark == 'add') button.addClass('on');
				else button.removeClass('on');
			}
		});
	});
};


window.CMVL.Playlist.prototype.initPaginationHandler = function() {
	var playlist = this;
	$('.cmvl-pagination a').click(function(e) {
		var obj = $(this);
		if (playlist.container.data('useAjax')) {
			playlist.loadURL(obj.attr('href'));
			return false;
		}
	});
};


window.CMVL.Playlist.prototype.initPlayerEventsHandler = function() {
	if (typeof $f == 'undefined') return;
	var playlist = this;
	this.container.find('iframe').each(function() {
		window.intervals = [];
		var lastProgressPercent = 0;
		var intervalStartPercent = 0;
		var iframe = $(this);
		var player = $f(this);
		this.froogaloopHandler = player;
		var lastAddedTimestamp = 0;
		var addInterval = function(start, stop) {
			start*=100;
			stop*=100;
//			console.log('add interval: '+ start +' - '+ stop);
			lastAddedTimestamp = (new Date()).getTime();
			var currentVideo = iframe.parents('.cmvl-video').first();
			var data = {action: 'cmvl_video_watching_stats', start: start, stop: stop,
					videoId: currentVideo.data('videoId'), channelId: currentVideo.data('channelId')};
			$.post(CMVLSettings.ajaxUrl, data, function(response) {
				if (playlist.statsAjaxSuccessCallback) {
					playlist.statsAjaxSuccessCallback();
				}
			});
		};
		player.addEvent('ready', function() {
			playlist.playerState = 'ready';
			player.addEvent('play', function() {
				intervalStartPercent = lastProgressPercent;
				playlist.playerState = 'play';
			});
			player.addEvent('pause', function() {
				addInterval(intervalStartPercent, lastProgressPercent);
				if (lastProgressPercent == 1) {
					lastProgressPercent = 0;
				}
				intervalStartPercent = lastProgressPercent;
				playlist.playerState = 'pause';
			});
			player.addEvent('seek', function(data) {
				if (playlist.playerState == 'play') {
					addInterval(intervalStartPercent, lastProgressPercent);
				}
				intervalStartPercent = data.percent;
				lastProgressPercent = data.percent;
			});
			player.addEvent('playProgress', function(data, id) {
				lastProgressPercent = data.percent;
				var intervalSeconds = 30;
				var now = (new Date()).getTime();
				if ((now-lastAddedTimestamp)/1000 >= intervalSeconds && data.seconds <= data.duration-intervalSeconds) {
					// save progress after every x seconds
					addInterval(intervalStartPercent, lastProgressPercent);
					intervalStartPercent = lastProgressPercent;
				}
			});
		});
	});
};

window.CMVL.Playlist.prototype.loaderShow = function() {
	this.container.append($('<div/>', {"class":"cmvl-loader"}));
};


window.CMVL.Playlist.prototype.loaderHide = function() {
	this.container.find('.cmvl-loader').remove();
};




$(function() {
	$('.cmvl-widget-playlist').each(function() {
		new window.CMVL.Playlist($(this));
	});
});


	
})(jQuery);
