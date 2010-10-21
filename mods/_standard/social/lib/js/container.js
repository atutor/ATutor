var Container = Class.create();
Container.prototype = {
	maxHeight: 4096,
	
	initialize: function() {
		// rpc services our container supports
		gadgets.rpc.register('resize_iframe', this.setHeight);
		gadgets.rpc.register('set_pref', this.setUserPref);
		gadgets.rpc.register('set_title', this.setTitle);
		gadgets.rpc.register('requestNavigateTo', this.requestNavigateTo);
	},
	
	setHeight: function(height) {
		if ($(this.f) != undefined) {
			// compensate for margin/padding offsets in some browsers (ugly hack but functional)
			height += 28;
			// change the height of the gadget iframe, limit to maxHeight height
			if (height > gadgets.container.maxHeight) {
				height = gadgets.container.maxHeight;
			}
			Element.setStyle($(this.f), {'height':height+'px'});
		}
	},
	
	_parseIframeUrl: function(url) {
		// parse the iframe url to extract the key = value pairs from it
		var ret = new Object();
		var hashParams = url.replace(/#.*$/, '').split('&');
		var param = key = val = '';
		for (i = 0 ; i < hashParams.length; i++) {
			param = hashParams[i];
			key = param.substr(0, param.indexOf('='));
			val = param.substr(param.indexOf('=') + 1);
			ret[key] = val;
		}
		return ret;
	},
	
	setUserPref: function(editToken, name, value) {
		// we use the security token to tell our backend who this is (app/mod/viewer)
		// since it's the only fail safe way of doing so
		if ($(this.f) != undefined) {
			var params = gadgets.container._parseIframeUrl($(this.f).src);
			//TODO use params.st to make the store request, it holds the owner / viewer / app id / mod id required
			new Ajax.Request('./mods/_standard/social/set_prefs.php', {method: 'get', parameters: { name: name, value: value, st: params.st }});
		}
	},
	
	setTitle: function(title) {
		var element = $(this.f+'_title');
		if (element != undefined) {
			// update the title, and make sure we don't break it's html
			element.update(title.replace(/&/g, '&amp;').replace(/</g, '&lt;'));
		}
	},
	
	_getUrlForView: function(view, person, app, mod) {
		if (view === 'home') {
			return './mods/_standard/social/index.php';
		} else if (view === 'profile') {
			return '/sprofile.php?id='+person;
		} else if (view === 'canvas') {
			return './mods/_standard/social/applications.php?app_id='+app;
		} else {
			return null;
		}
	},
	
	requestNavigateTo: function(view, opt_params) {
		if ($(this.f) != undefined) {
			var params = gadgets.container._parseIframeUrl($(this.f).src);
			var url = gadgets.container._getUrlForView(view, params.owner, params.aid, params.mid);
			if (opt_params) {
				var paramStr = Object.toJSON(opt_params);
				if (paramStr.length > 0) {
					url += '?appParams=' + encodeURIComponent(paramStr);
				}
			}
			if (url && document.location.href.indexOf(url) == -1) {
	 			document.location.href = url;
			}
		}
	}
}

/**
 * Create the container class on page load
 */
gadgets.container = new Container();