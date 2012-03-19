window.moddt = (function(window, document, jQuery) {
	jQuery.cookie = function(name, value, options) { if (typeof value != 'undefined') { options = options || {}; if (value === null) { value = ''; options.expires = -1; } var expires = ''; if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) { var date; if (typeof options.expires == 'number') { date = new Date(); date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000)); } else { date = options.expires; } expires = '; expires=' + date.toUTCString(); } var path = options.path ? '; path=' + (options.path) : ''; var domain = options.domain ? '; domain=' + (options.domain) : ''; var secure = options.secure ? '; secure' : ''; document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join(''); } else { var cookieValue = null; if (document.cookie && document.cookie != '') { var cookies = document.cookie.split(';'); for (var i = 0; i < cookies.length; i++) { var cookie = $.trim(cookies[i]); if (cookie.substring(0, name.length + 1) == (name + '=')) { cookieValue = decodeURIComponent(cookie.substring(name.length + 1)); break; } } } return cookieValue; } };
	var $ = jQuery;
	var COOKIE_NAME = 'moddt';
	var moddt = {
		jQuery: jQuery,
		events: {
			ready: []
		},
		isReady: false,
		init: function() {
			$('#modDT').show();
			var current = null;
			$('#modDTPanelList li a').live('click', function() {
				if (!this.className) {
					return false;
				}
				current = $('#modDT #' + this.className);
				if (current.is(':visible')) {
					$(document).trigger('close.modDT');
					$(this).parent().removeClass('active');
				} else {
					$('.panelContent').hide(); // Hide any that are already open
					current.show();
					$('#modDTToolbar li').removeClass('active');
					$(this).parent().addClass('active');
				}
				return false;
			});
			$('#modDT a.modDTClose').live('click', function() {
				$(document).trigger('close.modDT');
				$('#modDTToolbar li').removeClass('active');
				return false;
			});
			$('#modDT a.remoteCall').live('click', function() {
				$('#modDTWindow').load(this.href, function(response, status, xhr) {
					if (status == "error") {
						var message = '<div class="moddtDebugPanelTitle"><a class="moddtDebugClose moddtDebugBack" href="">Back</a><h3>'+xhr.status+': '+xhr.statusText+'</h3></div>';
						$('#modDTWindow').html(message);
					}
					$('#modDTWindow a.modDTBack').live('click', function() {
						$(this).parent().parent().hide();
						return false;
					});
				});
				$('#modDTWindow').show();
				return false;
			});
			$('#modDTTemplatePanel a.moddtTemplateShowContext').live('click', function() {
				moddt.toggle_arrow($(this).children('.toggleArrow'));
				moddt.toggle_content($(this).parent().next());
				return false;
			});
			$('#modDT a.modDTToggle').live('click', function(e) {
				e.preventDefault();
				$(this).parent().find('.modDTCollapsed').toggle();
				$(this).parent().find('.modDTUncollapsed').toggle();
			});
			$('#modDT a.moddtToggleSwitch').live('click', function(e) {
				e.preventDefault();
				var btn = $(this);
				var id = btn.attr('data-toggle-id');
				var open_me = btn.text() == btn.attr('data-toggle-open');
				if (id === '' || !id) {
					return;
				}

				btn.parents('.modDTPanelContent').find('#sqlMain_' + id).find('.modDTCollapsed').toggle(open_me);
				btn.parents('.modDTPanelContent').find('#sqlMain_' + id).find('.modDTUncollapsed').toggle(!open_me);
				$(this).parents('.modDTPanelContent').find('.moddtToggleDetails_' + id).each(function(){
					var $this = $(this);
					if (open_me) {
						$this.addClass('moddtSelected');
						$this.removeClass('moddtUnselected');
						btn.text(btn.attr('data-toggle-close'));
						$this.find('.moddtToggleSwitch').text(btn.text());
					} else {
						$this.removeClass('moddtSelected');
						$this.addClass('moddtUnselected');
						btn.text(btn.attr('data-toggle-open'));
						$this.find('.moddtToggleSwitch').text(btn.text());
					}
				});
				return;
			});
			function getSubcalls(row) {
				var id = row.attr('id');
				return $('.modDTProfileRow[id^="'+id+'_"]');
			}
			function getDirectSubcalls(row) {
				var subcalls = getSubcalls(row);
				var depth = parseInt(row.attr('depth'), 10) + 1;
				return subcalls.filter('[depth='+depth+']');
			}
			$('.modDTProfileRow .modDTProfileToggle').live('click', function(){
				var row = $(this).closest('.modDTProfileRow');
				var subcalls = getSubcalls(row);
				if (subcalls.css('display') == 'none') {
					getDirectSubcalls(row).show();
				} else {
					subcalls.hide();
				}
			});
			$('#moddtHideToolBarButton').click(function() {
				moddt.hide_toolbar(true);
				return false;
			});
			$('#moddtShowToolBarButton').click(function() {
				moddt.show_toolbar();
				return false;
			});
			$(document).bind('close.modDT', function() {
				// If a sub-panel is open, close that
				if ($('#modDTWindow').is(':visible')) {
					$('#modDTWindow').hide();
					return;
				}
				// If a panel is open, close that
				if ($('.panelContent').is(':visible')) {
					$('.panelContent').hide();
					return;
				}
				// Otherwise, just minimize the toolbar
				if ($('#modDTToolbar').is(':visible')) {
					moddt.hide_toolbar(true);
					return;
				}
			});
			if ($.cookie(COOKIE_NAME)) {
				moddt.hide_toolbar(false);
			} else {
				moddt.show_toolbar(false);
			}
			$('#modDT .modDTHoverable').hover(function(){
				$(this).addClass('moddtDebugHover');
			}, function(){
				$(this).removeClass('moddtDebugHover');
			});
			moddt.isReady = true;
			$.each(moddt.events.ready, function(_, callback){
				callback(moddt);
			});
		},
		toggle_content: function(elem) {
			if (elem.is(':visible')) {
				elem.hide();
			} else {
				elem.show();
			}
		},
		close: function() {
			$(document).trigger('close.modDT');
			return false;
		},
		hide_toolbar: function(setCookie) {
			// close any sub panels
			$('#modDTWindow').hide();
			// close all panels
			$('.panelContent').hide();
			$('#modDTToolbar li').removeClass('active');
			// finally close toolbar
			$('#modDTToolbar').hide('fast');
			$('#modDTToolbarHandle').show();
			// Unbind keydown
			$(document).unbind('keydown.modDT');
			if (setCookie) {
				$.cookie(COOKIE_NAME, 'hide', {
					path: '/',
					expires: 10
				});
			}
		},
		show_toolbar: function(animate) {
			// Set up keybindings
			$(document).bind('keydown.modDT', function(e) {
				if (e.keyCode == 27) {
					moddt.close();
				}
			});
			$('#modDTToolbarHandle').hide();
			if (animate) {
				$('#modDTToolbar').show('fast');
			} else {
				$('#modDTToolbar').show();
			}
			$.cookie(COOKIE_NAME, null, {
				path: '/',
				expires: -1
			});
		},
		toggle_arrow: function(elem) {
			var uarr = String.fromCharCode(0x25b6);
			var darr = String.fromCharCode(0x25bc);
			elem.html(elem.html() == uarr ? darr : uarr);
		},
		ready: function(callback){
			if (moddt.isReady) {
				callback(moddt);
			} else {
				moddt.events.ready.push(callback);
			}
		}
	};
	$(document).ready(function() {
		moddt.init();
	});
	return moddt;
}(window, document, jQuery.noConflict(true)));
