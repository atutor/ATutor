//
//  Copyright (c) 2011, Maths for More S.L. http://www.wiris.com
//  This file is part of WIRIS Plugin.
//
//  WIRIS Plugin is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  any later version.
//
//  WIRIS Plugin is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with WIRIS Plugin. If not, see <http://www.gnu.org/licenses/>.
//


var wrs_int_opener;
var appletObject;
var initialXML = '';
var closeFunction;

if (window.opener) {							// For popup mode.
	wrs_int_opener = window.opener;
	closeFunction = window.close;
}
// FCKeditor integration begin
else {											// For iframe mode.
	wrs_int_opener = window.parent;
	
	while (wrs_int_opener.InnerDialogLoaded) {
		wrs_int_opener = wrs_int_opener.parent;
	}
}

if (window.parent.InnerDialogLoaded) {			// Iframe mode.
	window.parent.InnerDialogLoaded();
	closeFunction = window.parent.Cancel;
}
else if (window.opener.parent.FCKeditorAPI) {	// Popup mode.
	wrs_int_opener = window.opener.parent;
}
// FCKeditor integration end

function getMathmlFromAppletCode(appletCode) {
	var optionForm = document.getElementById('optionForm');
	appletObject = wrs_int_opener.wrs_createObject(appletCode, document);
	
	optionForm.width.value = parseInt(appletObject.width);
	optionForm.height.value = parseInt(appletObject.height);
	
	var params = appletObject.childNodes;
	var mathml = '';
	
	for (var i = 0; i < params.length; ++i) {
		if (params[i].name == 'xmlinitialtext') {
			mathml = params[i].value;
		}
		else if (params[i].name == 'requestfirstevaluation') {
			optionForm.executeonload.checked = (params[i].value == 'true') ? true : false;
		}
		else if (params[i].name == 'toolbar') {
			optionForm.toolbar.checked = (params[i].value == 'floating') ? false : true;
		}
		else if (params[i].name == 'requestfocus') {
			optionForm.focusonload.checked = (params[i].value == 'true') ? true : false;
		}
		else if (params[i].name == 'level') {
			optionForm.level.checked = (params[i].value == 'primary') ? true : false;
		}
	}
	
	return mathml;
}

function createIframePath(params) {
	var iframePath = wrs_int_opener._wrs_conf_CASPath;
	iframePath += ((iframePath.indexOf('?') == -1) ? '?' : '&') + 'mode=applet&';
	
	for (var i in params) {
		iframePath += wrs_int_opener.wrs_urlencode(i) + '=' + wrs_int_opener.wrs_urlencode(params[i]);
	}
	
	return iframePath;
}

function createIframe(params) {
	var iframe = document.createElement('iframe');
	iframe.id = 'appletContainerIframe';
	iframe.src = createIframePath(params);
	iframe.width = '100%';
	iframe.height = '100%';
	iframe.frameBorder = 0;
	
	wrs_int_opener.wrs_addEvent(iframe, 'load', function () {
		if (initialXML.length > 0) {
			var applet = iframe.contentWindow.document.getElementById('applet');
			
			function setAppletMathml() {
				// Internet explorer fails on "applet.isActive". It only supports "applet.isActive()".
				
				try {
					if (applet.isActive()) {
						applet.setXML(initialXML);
					}
					else {
						setTimeout(setAppletMathml, 50);
					}
				}
				catch (e) {
					setTimeout(setAppletMathml, 50);
				}
			}

			setAppletMathml();
		}
	});
	
	document.getElementById('appletContainer').appendChild(iframe);
}

function reloadIframe(params) {
	var iframe = document.getElementById('appletContainerIframe');
	var applet = iframe.contentWindow.document.getElementById('applet');
	initialXML = applet.getXML();
	iframe.src = createIframePath(params);
}

wrs_int_opener.wrs_addEvent(window, 'load', function () {
	// Getting language list <select> object.
	var languageList = document.getElementById('languageList');
	
	// When the language list <select> object changes its value, the iframe should be refreshed.
	
	wrs_int_opener.wrs_addEvent(languageList, 'change', function () {
		reloadIframe({
			'lang': languageList.value
		});
	});
	
	// Setting iframe language.
	var language;
	
	if (wrs_int_opener._wrs_isNewElement) {
		var queryParams = wrs_int_opener.wrs_getQueryParams(window);

		var availableLangs = new Array();
		for (var i = 0; i < languageList.options.length; i++){
			availableLangs[i] = languageList.options[i].value;
		}
		
		if (typeof queryParams['lang'] != 'undefined' && wrs_int_opener.wrs_arrayContains(availableLangs, queryParams['lang']) != -1){
			language = queryParams['lang'];
		}else if (typeof queryParams['lang'] != 'undefined' && wrs_int_opener.wrs_arrayContains(availableLangs, queryParams['lang'].substr(0,2)) != -1){
			language = queryParams['lang'].substr(0,2);
		}else{
			language = wrs_int_opener._wrs_int_language
		}
	}
	else {
		var appletCode = wrs_int_opener._wrs_temporalImage.getAttribute(wrs_int_opener._wrs_conf_CASMathmlAttribute);
		initialXML = getMathmlFromAppletCode(wrs_int_opener.wrs_mathmlDecode(appletCode));
		
		var language = '';
		
		// We can convert initialXML to an object and get its "lang" value. However, IE does not support this functionability, so we use string parsing.
		var languageStart = initialXML.indexOf('lang="');
		
		if (languageStart != -1) {
			var languageEnd = initialXML.indexOf('"', languageStart + 6);		// +6 because 'lang="'.length is 6.
			
			if (languageEnd != -1) {
				language = initialXML.substring(languageStart + 6, languageEnd);
			}
		}
	}
	
	// Creating the iframe.
	
	createIframe({
		'lang': language
	});
	
	// Selecting the language on the <select> object.
	
	for (var i = languageList.options.length - 1; i >= 0; --i) {
		if (languageList.options[i].value == language) {
			languageList.selectedIndex = i;
			i = 0;
		}
	}
	
	// More events.
	
	wrs_int_opener.wrs_addEvent(document.getElementById('submit'), 'click', function () {
		var applet = document.getElementById('appletContainerIframe').contentWindow.document.getElementById('applet');
		
		// Creating new applet code
		var optionForm = document.getElementById('optionForm');
		var newWidth = parseInt(optionForm.width.value);
		var newHeight = parseInt(optionForm.height.value);
		
		var appletCode = '<applet alt="WIRIS cas" class="Wiriscas" align="middle" ';
		appletCode += 'codebase="' + applet.getAttribute('codebase') + '" ';
		appletCode += 'archive="' + applet.getAttribute('archive') + '" ';
		appletCode += 'code="' + applet.getAttribute('code') + '" ';
		appletCode += 'width="' + newWidth + '" height="' + newHeight + '">';
		
		appletCode += '<param name="requestfirstevaluation" value="' + (optionForm.executeonload.checked ? 'true' : 'false') + '"></param>';
		appletCode += '<param name="toolbar" value="' + (optionForm.toolbar.checked ? 'true' : 'floating') + '"></param>';
		appletCode += '<param name="requestfocus" value="' + (optionForm.focusonload.checked ? 'true' : 'false') + '"></param>';
		appletCode += '<param name="level" value="' + (optionForm.level.checked ? 'primary' : 'false') + '"></param>';
		appletCode += '<param name="xmlinitialtext" value="' + wrs_int_opener.wrs_htmlentities(applet.getXML()) + '"></param>';
		appletCode += '<param name="interface" value="false"></param><param name="commands" value="false"></param><param name="command" value="false"></param>';
		
		appletCode += '</applet>';
		
		// Getting the image
		// First, resize applet
		applet.style.width = newWidth + 'px';
		applet.style.height = newHeight + 'px';
		
		// Waiting for applet resizing
		function finish() {
			if (applet.getSize().width != newWidth || applet.getSize().height != newHeight) {
				setTimeout(finish, 100);
			}
			else {
				// Getting the image
				var image = applet.getImageBase64('png');
				
				// FCKeditor integration begin
				if (window.parent.InnerDialogLoaded && window.parent.FCKBrowserInfo.IsIE) {			// On IE, we must close the dialog for push the caret on the correct position.
					closeFunction();
					wrs_int_opener.wrs_int_updateCAS(appletCode, image, newWidth, newHeight);
				}
				// FCKeditor integration end
				else {
					wrs_int_opener.wrs_int_updateCAS(appletCode, image, newWidth, newHeight);
					closeFunction();
				}
			}
		}
		
		finish();
	});

	wrs_int_opener.wrs_addEvent(document.getElementById('cancel'), 'click', function () {
		closeFunction();
	});

	var acceptButton = document.getElementById('submit');
	if (strings['accept'] != null){
		acceptButton.value = strings['accept'];
	}

	var cancelButton = document.getElementById('cancel');
	if (strings['cancel'] != null){
		cancelButton.value = strings['cancel'];
	}
	
	// Auto resizing
	setInterval(function () {
		document.getElementById('appletContainer').style.height = (document.getElementById('optionForm').offsetHeight - document.getElementById('controls').offsetHeight - 5) + 'px';
	}, 100);
});

wrs_int_opener.wrs_addEvent(window, 'unload', function () {
	wrs_int_opener.wrs_int_notifyWindowClosed();
});
