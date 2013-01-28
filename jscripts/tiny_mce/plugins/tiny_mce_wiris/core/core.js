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

// Vars
var _wrs_currentPath = window.location.toString().substr(0, window.location.toString().lastIndexOf('/') + 1);
var _wrs_editMode;
var _wrs_isNewElement = true;
var _wrs_temporalImage;
var _wrs_temporalFocusElement;
var _wrs_androidRange;

var _wrs_xmlCharacters = {
	'tagOpener': '<',		// \x3C
	'tagCloser': '>',		// \x3E
	'doubleQuote': '"',		// \x22
	'ampersand': '&',		// \x26
	'quote': '\''			// \x27
};

var _wrs_safeXmlCharacters = {
	'tagOpener': '«',		// \xAB
	'tagCloser': '»',		// \xBB
	'doubleQuote': '¨',		// \xA8
	'ampersand': '§',		// \xA7
	'quote': '`',			// \xB4
	'realDoubleQuote': '¨'
};

var _wrs_safeXmlCharactersEntities = {
	'tagOpener': '&laquo;',
	'tagCloser': '&raquo;',
	'doubleQuote': '&uml;',
	'realDoubleQuote': '&quot;'
}

var _wrs_safeBadBlackboardCharacters = {
	'ltElement': '«mo»<«/mo»',
	'gtElement': '«mo»>«/mo»',
	'ampElement': '«mo»&«/mo»'
}

var _wrs_safeGoodBlackboardCharacters = {
	'ltElement': '«mo»§lt;«/mo»',
	'gtElement': '«mo»§gt;«/mo»',
	'ampElement': '«mo»§amp;«/mo»'
}

var _wrs_staticNodeLengths = {
	'IMG': 1,
	'BR': 1
}

// Retrocompatibility

if (!(window._wrs_conf_imageClassName)) {
	_wrs_conf_imageClassName = 'Wirisformula';
}

if (!(window._wrs_conf_CASClassName)) {
	_wrs_conf_CASClassName = 'Wiriscas';
}

// Functions

/**
 * Adds element events.
 * @param object target Target
 * @param function doubleClickHandler Function to run when user double clicks the element
 * @param function mousedownHandler Function to run when user mousedowns the element
 * @param function mouseupHandler Function to run when user mouseups the element
 */
function wrs_addElementEvents(target, doubleClickHandler, mousedownHandler, mouseupHandler) {
	if (doubleClickHandler) {
		wrs_addEvent(target, 'dblclick', function (event) {
			var realEvent = (event) ? event : window.event;
			var element = realEvent.srcElement ? realEvent.srcElement : realEvent.target;
			doubleClickHandler(target, element, realEvent);
		});
	}
	
	if (mousedownHandler) {
		wrs_addEvent(target, 'mousedown', function (event) {
			var realEvent = (event) ? event : window.event;
			var element = realEvent.srcElement ? realEvent.srcElement : realEvent.target;
			_wrs_temporalFocusElement = element;
			mousedownHandler(target, element, realEvent);
		});
	}
	
	if (mouseupHandler) {
		wrs_addEvent(target, 'mouseup', function (event) {
			var realEvent = (event) ? event : window.event;
			var element = realEvent.srcElement ? realEvent.srcElement : realEvent.target;
			mouseupHandler(target, element, realEvent);
		});
	}
}

/**
 * Cross-browser addEventListener/attachEvent function.
 * @param object element Element target
 * @param event event Event
 * @param function func Function to run
 */
function wrs_addEvent(element, event, func) {
	if (element.addEventListener) {
		element.addEventListener(event, func, false);
	}
	else if (element.attachEvent) {
		element.attachEvent('on' + event, func);
	}
}

/**
 * Adds iframe events.
 * @param object iframe Target
 * @param function doubleClickHandler Function to run when user double clicks the iframe
 * @param function mousedownHandler Function to run when user mousedowns the iframe
 * @param function mouseupHandler Function to run when user mouseups the iframe
 */
function wrs_addIframeEvents(iframe, doubleClickHandler, mousedownHandler, mouseupHandler) {
	wrs_addElementEvents(iframe.contentWindow.document, function (target, element, event) {
			doubleClickHandler(iframe, element, event);
		}, function (target, element, event) {
			mousedownHandler(iframe, element, event);
		}, function (target, element, event) {
			mouseupHandler(iframe, element, event);
		}
	);
}

/**
 * Adds textarea events.
 * @param object textarea Target
 * @param function clickHandler Function to run when user clicks the textarea.
 */
function wrs_addTextareaEvents(textarea, clickHandler) {
	if (clickHandler) {
		wrs_addEvent(textarea, 'click', function (event) {
			var realEvent = (event) ? event : window.event;
			clickHandler(textarea, realEvent);
		});
	}
}

/**
 * Converts applet code to img object.
 * @param object creator Object with "createElement" method
 * @param string appletCode Applet code
 * @param string image Base 64 image stream
 * @param int imageWidth Image width
 * @param int imageHeight Image height
 * @return object
 */
function wrs_appletCodeToImgObject(creator, appletCode, image, imageWidth, imageHeight) {
	var imageSrc = wrs_createImageCASSrc(image);
	var imgObject = creator.createElement('img');
	//imgObject.title = 'Double click to edit';
	imgObject.src = imageSrc;
	imgObject.align = 'middle';
	imgObject.width = imageWidth;
	imgObject.height = imageHeight;
	imgObject.setAttribute(_wrs_conf_CASMathmlAttribute, wrs_mathmlEncode(appletCode));
	imgObject.className = _wrs_conf_CASClassName;
	
	return imgObject;
}

/**
 * Checks if a determined array contains a determined element.
 * @param array stack
 * @param object element
 * @return bool
 */
function wrs_arrayContains(stack, element) {
	for (var i = stack.length - 1; i >= 0; --i) {
		if (stack[i] === element) {
			return i;
		}
	}
	
	return -1;
}

/**
 * Checks if an element contains a class.
 * @param object element
 * @param string className
 * @return bool
 */
function wrs_containsClass(element, className) {
	if (!('className' in element)){
		return false;
	}
	
	var currentClasses = element.className.split(' ');
	
	for (var i = currentClasses.length - 1; i >= 0; --i) {
		if (currentClasses[i] == className) {
			return true;
		}
	}
	
	return false;
}

/**
 * Cross-browser solution for creating new elements.
 * 
 * It fixes some browser bugs.
 *
 * @param string elementName The tag name of the wished element.
 * @param object attributes An object where each key is a wished attribute name and each value is its value.
 * @param object creator Optional param. If supplied, this function will use the "createElement" method from this param. Else, "document" will be used.
 * @return object The DOM element with the specified attributes assignated.
 */
function wrs_createElement(elementName, attributes, creator) {
	if (attributes === undefined) {
		attributes = {};
	}
	
	if (creator === undefined) {
		creator = document;
	}
	
	var element;
	
	/*
	 * Internet Explorer fix:
	 * If you create a new object dynamically, you can't set a non-standard attribute.
	 * For example, you can't set the "src" attribute on an "applet" object.
	 * Other browsers will throw an exception and will run the standard code.
	 */
	
	try {
		var html = '<' + elementName;
		
		for (var attributeName in attributes) {
			html += ' ' + attributeName + '="' + wrs_htmlentities(attributes[attributeName]) + '"';
		}
		
		html += '>';
		element = creator.createElement(html);
	}
	catch (e) {
		element = creator.createElement(elementName);
		
		for (var attributeName in attributes) {
			element.setAttribute(attributeName, attributes[attributeName]);
		}
	}
	
	return element;
}

/**
 * Cross-browser httpRequest creation.
 * @return object
 */
function wrs_createHttpRequest() {
	if (_wrs_currentPath.substr(0, 7) == 'file://') {
		throw 'Cross site scripting is only allowed for HTTP.';
	}
	
	if (typeof XMLHttpRequest != 'undefined') {
		return new XMLHttpRequest();
	}
			
	try {
		return new ActiveXObject('Msxml2.XMLHTTP');
	}
	catch (e) {
		try {
			return new ActiveXObject('Microsoft.XMLHTTP');
		}
		catch (oc) {
		}
	}
	
	return false;
}

/**
 * Gets CAS image src with AJAX.
 * @param string image Base 64 image stream
 * @return string
 */
function wrs_createImageCASSrc(image, appletCode) {
	var data = {
		'image': image,
		'mml': appletCode
	};
	
	return wrs_getContent(_wrs_conf_createcasimagePath, data);
}

/**
 * Gets formula image src with AJAX.
 * @param mathml Mathml code
 * @param wirisProperties
 * @return string Image src
 */
function wrs_createImageSrc(mathml, wirisProperties) {
	var data = (wirisProperties) ? wirisProperties : {};
	data['mml'] = mathml;
	
	if (window._wrs_conf_useDigestInsteadOfMathml && _wrs_conf_useDigestInsteadOfMathml) {
		data['returnDigest'] = 'true';
	}
	
	var result = wrs_getContent(_wrs_conf_createimagePath, data);
	
	if (result.indexOf('@BASE@') != -1) {
		// Replacing '@BASE@' with the base URL of createimage.
		var baseParts = _wrs_conf_createimagePath.split('/');
		baseParts.pop();
		result = result.split('@BASE@').join(baseParts.join('/'));
	}
	
	return result;
}

/**
 * Creates new object using its html code.
 * @param string objectCode
 * @return object
 */
function wrs_createObject(objectCode, creator) {
	if (creator === undefined) {
		creator = document;
	}

	// Internet Explorer can't include "param" tag when is setting an innerHTML property.
	objectCode = objectCode.split('<applet ').join('<span wirisObject="WirisApplet" ').split('<APPLET ').join('<span wirisObject="WirisApplet" ');	// It is a 'span' because 'span' objects can contain 'br' nodes.
	objectCode = objectCode.split('</applet>').join('</span>').split('</APPLET>').join('</span>');
	
	objectCode = objectCode.split('<param ').join('<br wirisObject="WirisParam" ').split('<PARAM ').join('<br wirisObject="WirisParam" ');			// It is a 'br' because 'br' can't contain nodes.
	objectCode = objectCode.split('</param>').join('</br>').split('</PARAM>').join('</br>');
	
	var container = wrs_createElement('div', {}, creator);
	container.innerHTML = objectCode;
	
	function recursiveParamsFix(object) {
		if (object.getAttribute && object.getAttribute('wirisObject') == 'WirisParam') {
			var attributesParsed = {};
			
			for (var i = 0; i < object.attributes.length; ++i) {
				if (object.attributes[i].nodeValue !== null) {
					attributesParsed[object.attributes[i].nodeName] = object.attributes[i].nodeValue;
				}
			}
			
			var param = wrs_createElement('param', attributesParsed, creator);
			
			// IE fix
			if (param.NAME) {
				param.name = param.NAME;
				param.value = param.VALUE;
			}
			
			param.removeAttribute('wirisObject');
			object.parentNode.replaceChild(param, object);
		}
		else if (object.getAttribute && object.getAttribute('wirisObject') == 'WirisApplet') {
			var attributesParsed = {};
			
			for (var i = 0; i < object.attributes.length; ++i) {
				if (object.attributes[i].nodeValue !== null) {
					attributesParsed[object.attributes[i].nodeName] = object.attributes[i].nodeValue;
				}
			}
			
			var applet = wrs_createElement('applet', attributesParsed, creator);
			applet.removeAttribute('wirisObject');
			
			for (var i = 0; i < object.childNodes.length; ++i) {
				recursiveParamsFix(object.childNodes[i]);

				if (object.childNodes[i].nodeName.toLowerCase() == 'param') {
					applet.appendChild(object.childNodes[i]);
					--i;	// When we insert the object child into the applet, object loses one child.
				}
			}

			object.parentNode.replaceChild(applet, object);
		}
		else {
			for (var i = 0; i < object.childNodes.length; ++i) {
				recursiveParamsFix(object.childNodes[i]);
			}
		}
	}
	
	recursiveParamsFix(container);
	return container.firstChild;
}

/**
 * Converts an object to its HTML code.
 * @param object object
 * @return string
 */
function wrs_createObjectCode(object) {
	if (object.nodeType == 1) {		// ELEMENT_NODE
		var output = '<' + object.tagName;

		for (var i = 0; i < object.attributes.length; ++i) {
			if (object.attributes[i].specified) {
				output += ' ' + object.attributes[i].name + '="' + wrs_htmlentities(object.attributes[i].value) + '"';
			}
		}
		
		if (object.childNodes.length > 0) {
			output += '>';
			
			for (var i = 0; i < object.childNodes.length; ++i) {
				output += wrs_createObjectCode(object.childNodes[i]);
			}
			
			output += '</' + object.tagName + '>';
		}
		else if (object.nodeName == 'DIV' || object.nodeName == 'SCRIPT') {
			output += '></' + object.tagName + '>';
		}
		else {
			output += '/>';
		}

		return output;
	}
	
	if (object.nodeType == 3) {		// TEXT_NODE
		return wrs_htmlentities(object.nodeValue);
	}
	
	return '';
}

/**
 * Parses end HTML code.
 * @param string code
 * @param object wirisProperties Extra attributes for the formula.
 * @param string language Language for the formula.
 * @return string
 */
function wrs_endParse(code, wirisProperties, language) {
	code = wrs_endParseEditMode(code, wirisProperties, language);
	return wrs_endParseSaveMode(code);
}

function wrs_regexpIndexOf(input, regexp, start) {
	var index = input.substring(start || 0).search(regexp);
    return (index >= 0) ? (index + (start || 0)) : index;
}

/**
 * Parses end HTML code depending on the edit mode.
 * @param string code
 * @param object wirisProperties Extra formula attributes.
 * @param string language Language for the formula.
 * @return string
 */
function wrs_endParseEditMode(code, wirisProperties, language) {
	// Converting LaTeX to images.
	
	if (window._wrs_conf_parseModes !== undefined && wrs_arrayContains(_wrs_conf_parseModes, 'latex') != -1) {
		var output = '';
		var endPosition = 0;
		var startPosition = code.indexOf('$$');
		
		while (startPosition != -1) {
			output += code.substring(endPosition, startPosition);
			endPosition = code.indexOf('$$', startPosition + 2);
			
			if (endPosition != -1) {
				var latex = code.substring(startPosition + 2, endPosition);
				latex = wrs_htmlentitiesDecode(latex);
				var mathml = wrs_getMathMLFromLatex(latex, true);
				var imgObject = wrs_mathmlToImgObject(document, mathml, wirisProperties, language);
				output += wrs_createObjectCode(imgObject);
				endPosition += 2;
			}
			else {
				output += '$$';
				endPosition = startPosition + 2;
			}
			
			startPosition = code.indexOf('$$', endPosition);
		}
		
		output += code.substring(endPosition, code.length);
		code = output;
	}
	
	if (window._wrs_conf_defaultEditMode && _wrs_conf_defaultEditMode == 'iframes') {
		// Converting iframes to images.
		var output = '';
		var pattern = ' class="' + _wrs_conf_imageClassName + '"';
		var formulaPosition = code.indexOf(pattern);
		var endPosition = 0;
		
		while (formulaPosition != -1) {
			// Looking for the actual startPosition.
			startPosition = formulaPosition;
			var i = formulaPosition;
			var startTagFound = false;
			
			while (i >= 0 && !startTagFound) {		// Going backwards until the start tag '<' is found.
				var character = code.charAt(i);
				
				if (character == '"' || character == '\'') {
					var characterNextPosition = code.lastIndexOf(character, i);
					i = (characterNextPosition == -1) ? -1 : characterNextPosition;
				}
				else if (character == '<') {
					startPosition = i;
					startTagFound = true;
				}
				else if (character == '>') {
					i = -1;					// Break: we are inside a text node.
				}
				
				--i;
			}
			
			// Appending the previous code.
			output += code.substring(endPosition, startPosition);
			
			// Looking for the endPosition.
			
			if (startTagFound) {
				i = formulaPosition;
				var counter = 1;
				
				while (i < code.length && counter > 0) {
					var character = code.charAt(i);
					
					if (character == '"' || character == '\'') {
						var characterNextPosition = code.indexOf(character, i);
						i = (characterNextPosition == -1) ? code.length : characterNextPosition;
					}
					else if (character == '<') {
						if (i + 1 < code.length && code.charAt(i + 1) == '/') {
							--counter;
							
							if (counter == 0) {
								endPosition = code.indexOf('>', i) + 1;
								
								if (endPosition == -1) {
									// End tag stripped.
									counter = -1;		// to be != 0 and to break the loop.
								}
							}
						}
						else {
							++counter;
						}
					}
					else if (character == '>' && code.charAt(i - 1) == '/') {
						--counter;
						
						if (counter == 0) {
							endPosition = i + 1;
						}
					}
				
					++i;
				}
				
				if (counter == 0) {
					var formulaTagCode = code.substring(startPosition, endPosition);
					var formulaTagObject = wrs_createObject(formulaTagCode);
					var mathml = formulaTagObject.getAttribute(_wrs_conf_imageMathmlAttribute);
					
					if (mathml == null) {
						mathml = formulaTagObject.getAttribute('alt');
					}
					
					var imgObject = wrs_mathmlToImgObject(document, mathml, wirisProperties, language);
					output += wrs_createObjectCode(imgObject);
				}
				else {
					// Start tag found but no end tag found. No process is done. A character is appended to avoid infinite loop in the next search.
					output += code.charAt(formulaPosition);
					endPosition = formulaPosition + 1;
				}
			}
			else {
				// No start tag is found. No process is done. A character is appended to avoid infinite loop in the next search.
				output += code.charAt(formulaPosition);
				endPosition = formulaPosition + 1;
			}
			
			formulaPosition = code.indexOf(pattern, endPosition);
		}
		
		output += code.substring(endPosition, code.length);
		code = output;
	}
	
	return code;
}

/**
 * Parses end HTML code depending on the save mode.
 * @param string code
 * @return string
 */
function wrs_endParseSaveMode(code) {
	var output = '';
	var convertToXml = false;
	var convertToSafeXml = false;
	
	if (window._wrs_conf_saveMode) {
		if (_wrs_conf_saveMode == 'safeXml') {
			convertToXml = true;
			convertToSafeXml = true;
		}
		else if (_wrs_conf_saveMode == 'xml') {
			convertToXml = true;
		}
	}
	
	var endPosition = 0;
	var pattern = /<img/gi;
	var patternLength = pattern.source.length;
	
	while (pattern.test(code)) {
		var startPosition = pattern.lastIndex - patternLength;
		output += code.substring(endPosition, startPosition);
		
		var i = startPosition + 1;
		
		while (i < code.length && endPosition <= startPosition) {
			var character = code.charAt(i);
			
			if (character == '"' || character == '\'') {
				var characterNextPosition = code.indexOf(character, i + 1);
				
				if (characterNextPosition == -1) {
					i = code.length;		// End while.
				}
				else {
					i = characterNextPosition;
				}
			}
			else if (character == '>') {
				endPosition = i + 1;
			}
			
			++i;
		}
		
		if (endPosition < startPosition) {		// The img tag is stripped.
			output += code.substring(startPosition, code.length);
			return output;
		}
		
		var imgCode = code.substring(startPosition, endPosition);
		output += wrs_getWIRISImageOutput(imgCode, convertToXml, convertToSafeXml);
	}

	output += code.substring(endPosition, code.length);
	return output;
}

/**
 * Fires an element event.
 * @param object element
 * @param string event
 */
function wrs_fireEvent(element, event) {
	if (document.createEvent){
		var eventObject = document.createEvent('HTMLEvents');
		eventObject.initEvent(event, true, true);
		return !element.dispatchEvent(eventObject);
    }
    
	var eventObject = document.createEventObject();
	return element.fireEvent('on' + event, eventObject)
}

/**
 * Gets the formula mathml or CAS appletCode using its image hash code.
 * @param string variableName Variable to send on POST query to the server.
 * @param string imageHashCode
 * @return string
 */
function wrs_getCode(variableName, imageHashCode) {
	var data = {};
	data[variableName] = imageHashCode;
	return wrs_getContent(_wrs_conf_getmathmlPath, data);
}

/**
 * Gets the content from an URL.
 * @param string url
 * @param object postVariables Null if a GET query should be done.
 * @return string
 */
function wrs_getContent(url, postVariables) {
	try {
		var httpRequest = wrs_createHttpRequest();
		
		if (httpRequest) {
			if (url.substr(0, 1) == '/' || url.substr(0, 7) == 'http://' || url.substr(0, 8) == 'https://') {
				httpRequest.open('POST', url, false);
			}
			else {
				httpRequest.open('POST', _wrs_currentPath + url, false);
			}
			
			if (postVariables !== undefined) {
				httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=UTF-8');
				httpRequest.send(wrs_httpBuildQuery(postVariables));
			}
			else {
				httpRequest.send(null);
			}
			
			return httpRequest.responseText;
		}
		
		alert('Your browser is not compatible with AJAX technology. Please, use the latest version of Mozilla Firefox.');
	}
	catch (e) {
	}
	
	return '';
}

/**
 * Generates the innerHTML of an element.
 * @param object element
 * @return string
 */
function wrs_getInnerHTML(element) {
	var innerHTML = '';
	
	for (var i = 0; i < element.childNodes.length; ++i) {
		innerHTML += wrs_createObjectCode(element.childNodes[i]);
	}
	
	return innerHTML;
}

/**
 * Converts MathML to LaTeX.
 * @param string mathml
 * @return string
 */
function wrs_getLatexFromMathML(mathml) {
	var data = {
		'service': 'mathml2latex',
		'mml': mathml
	};
	
	return wrs_getContent(_wrs_conf_servicePath, data);
}

/**
 * Extracts the latex of a determined position in a text.
 * @param string text
 * @param int caretPosition
 * @return object An object with 3 keys: 'latex', 'start' and 'end'. Null if latex is not found.
 */
function wrs_getLatexFromTextNode(textNode, caretPosition) {
	// Looking for the first textNode.
	var startNode = textNode;
	
	while (startNode.previousSibling && startNode.previousSibling.nodeType == 3) {		// TEXT_NODE
		startNode = startNode.previousSibling;
	}
	
	// Finding latex.
	
	function getNextLatexPosition(currentNode, currentPosition) {
		var position = currentNode.nodeValue.indexOf('$$', currentPosition);
		
		while (position == -1) {
			currentNode = currentNode.nextSibling;
			
			if (!currentNode || currentNode.nodeType != 3) {		// TEXT_NODE
				return null;		// Not found.
			}
			
			position = currentNode.nodeValue.indexOf('$$');
		}
		
		return {
			'node': currentNode,
			'position': position
		};
	}
	
	function isPrevious(node, position, endNode, endPosition) {
		if (node == endNode) {
			return (position <= endPosition);
		}
		
		while (node && node != endNode) {
			node = node.nextSibling;
		}
		
		return (node == endNode);
	}
	
	var start;
	
	var end = {
		'node': startNode,
		'position': 0
	};
	
	do {
		var start = getNextLatexPosition(end.node, end.position);
		
		if (start == null || isPrevious(textNode, caretPosition, start.node, start.position)) {
			return null;
		}
		
		var end = getNextLatexPosition(start.node, start.position + 2);
		
		if (end == null) {
			return null;
		}
		
		end.position += 2;
	} while (isPrevious(end.node, end.position, textNode, caretPosition));
	
	// Isolating latex.
	var latex;
	
	if (start.node == end.node) {
		latex = start.node.nodeValue.substring(start.position + 2, end.position - 2);
	}
	else {
		latex = start.node.nodeValue.substring(start.position + 2, start.node.nodeValue.length);
		var currentNode = start.node;
		
		do {
			currentNode = currentNode.nextSibling;
			
			if (currentNode == end.node) {
				latex += end.node.nodeValue.substring(0, end.position - 2);
			}
			else {
				latex += currentNode.nodeValue;
			}
		} while (currentNode != end.node);
	}
	
	return {
		'latex': latex,
		'startNode': start.node,
		'startPosition': start.position,
		'endNode': end.node,
		'endPosition': end.position
	};
}

/**
 * Converts LaTeX to MathML.
 * @param string latex
 * @return string
 */
function wrs_getMathMLFromLatex(latex, includeLatexOnSemantics) {
	var data = {
		'service': 'latex2mathml',
		'latex': latex
	};
	
	if (includeLatexOnSemantics) {
		data['saveLatex'] = '';
	}
	
	var mathML = wrs_getContent(_wrs_conf_servicePath, data);
	return mathML.split("\r").join('').split("\n").join(' ');
}

/**
 * Gets the node length in characters.
 * @param object node
 * @return int
 */
function wrs_getNodeLength(node) {
	if (node.nodeType == 3) {		// TEXT_NODE
		return node.nodeValue.length;
	}
	
	if (node.nodeType == 1) {		// ELEMENT_NODE
		var length = _wrs_staticNodeLengths[node.nodeName.toUpperCase()];
		
		if (length === undefined) {
			length = 0;
		}
		
		for (var i = 0; i < node.childNodes.length; ++i) {
			length += wrs_getNodeLength(node.childNodes[i]);
		}
		
		return length;
	}
	
	return 0;
}

/**
 * Parses the query string and returns it as a Hash table.
 * @return object
 */
function wrs_getQueryParams(windowObject) {
	var data = {};
	var start = windowObject.location.search.indexOf('?');
	start = (start == -1) ? 0 : start + 1;
	var queryStringParts = windowObject.location.search.substr(start).split('&');
	
	for (var i = 0; i < queryStringParts.length; ++i) {
		var paramParts = queryStringParts[i].split('=', 2);
		data[paramParts[0]] = wrs_urldecode(paramParts[1]);
	}
	
	return data;
}

/**
 * Gets the selected node or text.
 * If the caret is on a text node, concatenates it with all the previous and next text nodes.
 * @param object target The editable element
 * @param boolean isIframe Specifies if the target is an iframe or not
 * @return object An object with the 'node' key setted if the item is an element or the keys 'node' and 'caretPosition' if the element is text
 */
function wrs_getSelectedItem(target, isIframe) {
	var windowTarget;
	
	if (isIframe) {
		windowTarget = target.contentWindow;
		windowTarget.focus();
	}
	else {
		windowTarget = window;
		target.focus();
	}
	
	if (document.selection) {
		var range = windowTarget.document.selection.createRange();

		if (range.parentElement) {
			if (range.text.length > 0) {
				return null;
			}

			windowTarget.document.execCommand('InsertImage', false, '#');
			var temporalObject = range.parentElement();
			
			if (temporalObject.nodeName.toUpperCase() != 'IMG') {
				// IE9 fix: parentElement() does not return the IMG node, returns the parent DIV node. In IE < 9, pasteHTML does not work well.
				range.pasteHTML('<span id="wrs_openEditorWindow_temporalObject"></span>');
				temporalObject = windowTarget.document.getElementById('wrs_openEditorWindow_temporalObject');
			}
			
			var node;
			var caretPosition;
			
			if (temporalObject.nextSibling && temporalObject.nextSibling.nodeType == 3) {				// TEXT_NODE
				node = temporalObject.nextSibling;
				caretPosition = 0;
			}
			else if (temporalObject.previousSibling && temporalObject.previousSibling.nodeType == 3) {	// TEXT_NODE
				node = temporalObject.previousSibling;
				caretPosition = node.nodeValue.length;
			}
			else {
				node = windowTarget.document.createTextNode('');
				temporalObject.parentNode.insertBefore(node, temporalObject);
				caretPosition = 0;
			}
			
			temporalObject.parentNode.removeChild(temporalObject);
			
			return {
				'node': node,
				'caretPosition': caretPosition
			};
		}
		
		if (range.length > 1) {
			return null;
		}
		
		return {
			'node': range.item(0)
		};
	}
	
	var selection = windowTarget.getSelection();
	
	try {
		var range = selection.getRangeAt(0);
	}
	catch (e) {
		var range = windowTarget.document.createRange();
	}
	
	var node = range.startContainer;
	
	if (node.nodeType == 3) {		// TEXT_NODE
		if (range.startOffset != range.endOffset) {
			return null;
		}
		
		return {
			'node': node,
			'caretPosition': range.startOffset
		};
	}
	
	if (node.nodeType == 1) {	// ELEMENT_NODE
		var position = range.startOffset;
		
		if (node.childNodes[position]) {
			return {
				'node': node.childNodes[position]
			};
		}
	}
	
	return null;
}

/**
 * Converts the HTML of a image into the output code that WIRIS must return.
 * @param string imgCode
 * @return string
 */
function wrs_getWIRISImageOutput(imgCode, convertToXml, convertToSafeXml) {
	var imgObject = wrs_createObject(imgCode);
	
	if (imgObject) {
		if (imgObject.className == _wrs_conf_imageClassName) {
			if (!convertToXml) {
				return imgCode;
			}
			
			var xmlCode = imgObject.getAttribute(_wrs_conf_imageMathmlAttribute);
			
			if (xmlCode == null) {
				xmlCode = imgObject.getAttribute('alt');
			}
			
			if (!convertToSafeXml) {
				xmlCode = wrs_mathmlDecode(xmlCode);
			}
			
			return xmlCode;
		}
		else if (imgObject.className == _wrs_conf_CASClassName) {
			var appletCode = imgObject.getAttribute(_wrs_conf_CASMathmlAttribute);
			appletCode = wrs_mathmlDecode(appletCode);
			var appletObject = wrs_createObject(appletCode);
			appletObject.setAttribute('src', imgObject.src);
			var object = appletObject;
			var appletCodeToBeInserted = wrs_createObjectCode(appletObject);
			
			if (convertToSafeXml) {
				appletCodeToBeInserted = wrs_mathmlEncode(appletCodeToBeInserted);
			}
			
			return appletCodeToBeInserted;
		}
	}
	
	return imgCode;
}

/**
 * Parses a text and replaces all HTML special characters by their entities.
 * @param string input
 * @return string
 */
function wrs_htmlentities(input) {
    return input.split('&').join('&amp;').split('<').join('&lt;').split('>').join('&gt;').split('"').join('&quot;');
}

/**
 * Parses a text and replaces all the HTML entities by their characters.
 * @param string input
 * @return string
 */
function wrs_htmlentitiesDecode(input) {
	return input.split('&quot;').join('"').split('&gt;').join('>').split('&lt;').join('<').split('&amp;').join('&');
}

/**
 * Converts a hash to a HTTP query.
 * @param hash properties
 * @return string
 */
function wrs_httpBuildQuery(properties) {
	var result = '';
	
	for (i in properties) {
		if (properties[i] != null) {
			result += wrs_urlencode(i) + '=' + wrs_urlencode(properties[i]) + '&';
		}
	}
	
	return result;
}

/**
 * Parses initial HTML code.
 * @param string code
 * @param string language Language for the formula.
 * @return string
 */
 /* Note: The code inside this function has been inverted.
 	If you invert again the code then you cannot use correctly LaTeX 
	in Moodle.
 */
function wrs_initParse(code, language) {
	code = wrs_initParseSaveMode(code, language);
	return wrs_initParseEditMode(code);
}

function wrs_initParseImgToIframes(windowTarget) {
	if (window._wrs_conf_defaultEditMode && _wrs_conf_defaultEditMode == 'iframes') {
		var imgList = windowTarget.document.getElementsByTagName('img');
		var i = 0;
		
		while (i < imgList.length) {
			if (imgList[i].className == _wrs_conf_imageClassName) {
				var mathml = imgList[i].getAttribute(_wrs_conf_imageMathmlAttribute);
				
				if (mathml == null) {
					mathml = imgList[i].getAttribute('alt');
				}
			
				var iframe = wrs_mathmlToIframeObject(windowTarget, wrs_mathmlDecode(mathml));
				imgList[i].parentNode.replaceChild(iframe, imgList[i]);
			}
			else {
				++i;
			}
		}
	}
}

/**
 * Parses initial HTML code depending on the edit mode.
 * @param string code
 * @return string
 */
function wrs_initParseEditMode(code) {
	if (window._wrs_conf_parseModes !== undefined && wrs_arrayContains(_wrs_conf_parseModes, 'latex') != -1) {
		var imgList = wrs_getElementsByNameFromString(code, 'img', true);
		var token = 'encoding="LaTeX">';
		var carry = 0;			// While replacing images with latex, the indexes of the found images changes respecting the original code, so this carry is needed.
		
		for (var i = 0; i < imgList.length; ++i) {
			var imgCode = code.substring(imgList[i].start + carry, imgList[i].end + carry);
			
			if (imgCode.indexOf(' class="' + _wrs_conf_imageClassName + '"') != -1) {
				var mathmlStartToken = ' ' + _wrs_conf_imageMathmlAttribute + '="';
				var mathmlStart = imgCode.indexOf(mathmlStartToken);
				
				if (mathmlStart == -1) {
					mathmlStartToken = ' alt="';
					mathmlStart = imgCode.indexOf(mathmlStartToken);
				}
				
				if (mathmlStart != -1) {
					mathmlStart += mathmlStartToken.length;
					var mathmlEnd = imgCode.indexOf('"', mathmlStart);
					var mathml = wrs_mathmlDecode(imgCode.substring(mathmlStart, mathmlEnd));
					var latexStartPosition = mathml.indexOf(token);
					
					if (latexStartPosition != -1) {
						latexStartPosition += token.length;
						var latexEndPosition = mathml.indexOf('</annotation>', latexStartPosition);
						var latex = mathml.substring(latexStartPosition, latexEndPosition);
						
						var replaceText = '$$' + wrs_htmlentitiesDecode(latex) + '$$';
						code = code.substring(0, imgList[i].start + carry) + replaceText + code.substring(imgList[i].end + carry);
						carry += replaceText.length - (imgList[i].end - imgList[i].start);
					}
				}
			}
		}
	}
	
	return code;
}

/**
 * Parses initial HTML code depending on the save mode.
 * @param string code
 * @param string language Language for the formula.
 * @return string
 */
function wrs_initParseSaveMode(code, language) {
	if (window._wrs_conf_saveMode) {
		var safeXml = (_wrs_conf_saveMode == 'safeXml');
		var characters = _wrs_xmlCharacters;
		
		if (safeXml) {
			characters = _wrs_safeXmlCharacters;
			code = wrs_parseSafeAppletsToObjects(code);
		}
		
		if (safeXml || _wrs_conf_saveMode == 'xml') {
			// Converting XML to tags.
			code = wrs_parseMathmlToImg(code, characters, language);
		}
	}
	
	var appletList = wrs_getElementsByNameFromString(code, 'applet', false);
	var carry = 0;			// While replacing applets with images, the indexes of the found applets changes respecting the original code, so this carry is needed.
	
	for (var i = 0; i < appletList.length; ++i) {
		var appletCode = code.substring(appletList[i].start + carry, appletList[i].end + carry);
		
		if (appletCode.indexOf(' class="' + _wrs_conf_CASClassName + '"') != -1) {
			var srcStart = appletCode.indexOf(' src="') + ' src="'.length;
			var srcEnd = appletCode.indexOf('"', srcStart);
			var src = appletCode.substring(srcStart, srcEnd);
			
			// 'Double click to edit' has been removed here.
			var imgCode = '<img align="middle" class="' + _wrs_conf_CASClassName + '" ' + _wrs_conf_CASMathmlAttribute + '="' + wrs_mathmlEncode(appletCode) + '" src="' + src + '" />';
			
			code = code.substring(0, appletList[i].start + carry) + imgCode + code.substring(appletList[i].end + carry);
			carry += imgCode.length - (appletList[i].end - appletList[i].start);
		}
	}
	
	return code;
}

/**
 * Looks for elements that match the given name in a HTML code string.
 * Important: this function is very concrete for WIRIS code. It takes as preconditions lots of behaviors that are not the general case.
 *
 * @param string code HTML code
 * @param string name Element names
 * @param boolean autoClosed True if the elements are autoClosed.
 * @return array
 */
function wrs_getElementsByNameFromString(code, name, autoClosed) {
	var elements = [];
	var code = code.toLowerCase();
	name = name.toLowerCase();
	var start = code.indexOf('<' + name + ' ');
	
	while (start != -1) {						// Look for nodes.
		var endString;
		
		if (autoClosed) {
			endString = '>';
		}
		else {
			endString = '</' + name + '>';
		}
		
		var end = code.indexOf(endString, start);
		
		if (end != -1) {
			end += endString.length;
			
			elements.push({
				'start': start,
				'end': end
			});
		}
		else {
			end = start + 1;
		}
		
		start = code.indexOf('<' + name + ' ', end);
	}
	
	return elements;
}

/**
 * Replaces a selection with an element.
 * @param object element Element
 * @param object focusElement Element to be focused
 * @param object windowTarget Target
 */
function wrs_insertElementOnSelection(element, focusElement, windowTarget) {
	try {
		focusElement.focus();
		
		if (_wrs_isNewElement) {
			if (document.selection) {
				var range = windowTarget.document.selection.createRange();
				windowTarget.document.execCommand('InsertImage', false, element.src);
				
				if (!('parentElement' in range)) {
					windowTarget.document.execCommand('delete', false);
					range = windowTarget.document.selection.createRange();
					windowTarget.document.execCommand('InsertImage', false, element.src);
				}
				
				if ('parentElement' in range) {
					var temporalObject = range.parentElement();
					
					if (temporalObject.nodeName.toUpperCase() == 'IMG') {
						temporalObject.parentNode.replaceChild(element, temporalObject);
					}
					else {
						// IE9 fix: parentNode() does not return the IMG node, returns the parent DIV node. In IE < 9, pasteHTML does not work well.
						range.pasteHTML(wrs_createObjectCode(element));
					}
				}
			}
			else {
				var isAndroid = false;
				if (_wrs_androidRange){
					var isAndroid = true;
					var range = _wrs_androidRange;
				}else{
					var selection = windowTarget.getSelection();

					try {
						var range = selection.getRangeAt(0);
					}
					catch (e) {
						var range = windowTarget.document.createRange();
					}
					selection.removeAllRanges();
				}

				range.deleteContents();				
				
				var node = range.startContainer;
				var position = range.startOffset;
				
				if (node.nodeType == 3) {		// TEXT_NODE
					node = node.splitText(position);
					node.parentNode.insertBefore(element, node);
					node = node.parentNode;
				}
				else if (node.nodeType == 1) {	// ELEMENT_NODE
					node.insertBefore(element, node.childNodes[position]);
				}
				
				if (!isAndroid){
					//Fix to set the caret after the inserted image
					range.selectNode(element);
					position = range.endOffset;
					selection.collapse(node, position);
				}
			}
		}
		else if (_wrs_temporalRange) {
			if (document.selection) {
				_wrs_isNewElement = true;
				_wrs_temporalRange.select();
				wrs_insertElementOnSelection(element, focusElement, windowTarget);
			}
			else {
				var parentNode = _wrs_temporalRange.startContainer;
				_wrs_temporalRange.deleteContents();
				_wrs_temporalRange.insertNode(element);
			}
		}
		else {
			_wrs_temporalImage.parentNode.replaceChild(element, _wrs_temporalImage);
		}
	}
	catch (e) {
	}
}

/**
 * Checks if the mathml at position i is inside an HTML attribute or not.
 * @param string content
 * @param string i
 * @return bool True if is inside an HTML attribute. In other case, false.
 */
function wrs_isMathmlInAttribute(content, i) {
	//regex = '^[\'"][\\s]*=[\\s]*[\\w-]+([\\s]*("[^"]*"|\'[^\']*\')[\\s]*=[\\s]*[\\w-]+[\\s]*)*[\\s]+gmi<';
	var math_att = '[\'"][\\s]*=[\\s]*[\\w-]+';  						// "=att OR '=att
	var att_content = '"[^"]*"|\'[^\']*\'';		 						// "blabla" OR 'blabla'
	var att = '[\\s]*(' + att_content + ')[\\s]*=[\\s]*[\\w-]+[\\s]*';	// "blabla"=att OR 'blabla'=att
	var atts = '(' + att + ')*';										// "blabla"=att1 "blabla"=att2
	var regex = '^' + math_att + atts + '[\\s]+gmi<';					// "=att "blabla"=att1 "blabla"=att2 gmi<
	var expression = new RegExp(regex);
	
	var actual_content = content.substring(0, i);
	var reversed = actual_content.split('').reverse().join('');
	var exists = expression.test(reversed);
	
	return exists;
}

/**
 * WIRIS special encoding.
 * We use these entities because IE doesn't support html entities on its attributes sometimes. Yes, sometimes.
 * @param string input
 * @return string
 */
function wrs_mathmlDecode(input) {
	// Decoding entities.
	input = input.split(_wrs_safeXmlCharactersEntities.tagOpener).join(_wrs_safeXmlCharacters.tagOpener);
	input = input.split(_wrs_safeXmlCharactersEntities.tagCloser).join(_wrs_safeXmlCharacters.tagCloser);
	input = input.split(_wrs_safeXmlCharactersEntities.doubleQuote).join(_wrs_safeXmlCharacters.doubleQuote);
	//Added to fix problem due to import from 1.9.x
	input = input.split(_wrs_safeXmlCharactersEntities.realDoubleQuote).join(_wrs_safeXmlCharacters.realDoubleQuote);

	//Blackboard
	if ('_wrs_blackboard' in window && window._wrs_blackboard){
		input = input.split(_wrs_safeBadBlackboardCharacters.ltElement).join(_wrs_safeGoodBlackboardCharacters.ltElement);
		input = input.split(_wrs_safeBadBlackboardCharacters.gtElement).join(_wrs_safeGoodBlackboardCharacters.gtElement);
		input = input.split(_wrs_safeBadBlackboardCharacters.ampElement).join(_wrs_safeGoodBlackboardCharacters.ampElement);
		
		/*var regex = /«mtext».*[<>&].*«\/mtext»/;

		var result = regex.exec(input);
		while(result){
			var changedResult = result[0].split(_wrs_xmlCharacters.tagOpener).join('§lt;');
			changedResult = changedResult.split(_wrs_xmlCharacters.tagCloser).join('§gt;');
			changedResult = changedResult.split(_wrs_xmlCharacters.ampersand).join('§amp;');
			input = input.replace(result, changedResult);
			result = regex.exec(input);
		}*/
	}
	
	// Decoding characters.
	input = input.split(_wrs_safeXmlCharacters.tagOpener).join(_wrs_xmlCharacters.tagOpener);
	input = input.split(_wrs_safeXmlCharacters.tagCloser).join(_wrs_xmlCharacters.tagCloser);
	input = input.split(_wrs_safeXmlCharacters.doubleQuote).join(_wrs_xmlCharacters.doubleQuote);
	input = input.split(_wrs_safeXmlCharacters.ampersand).join(_wrs_xmlCharacters.ampersand);
	input = input.split(_wrs_safeXmlCharacters.quote).join(_wrs_xmlCharacters.quote);
	
	// We are replacing $ by & for retrocompatibility. Now, the standard is replace § by &
	input = input.split('$').join('&');
	
	return input;
}

/**
 * WIRIS special encoding.
 * We use these entities because IE doesn't support html entities on its attributes sometimes. Yes, sometimes.
 * @param string input
 * @return string
 */
function wrs_mathmlEncode(input) {
	input = input.split(_wrs_xmlCharacters.tagOpener).join(_wrs_safeXmlCharacters.tagOpener);
	input = input.split(_wrs_xmlCharacters.tagCloser).join(_wrs_safeXmlCharacters.tagCloser);
	input = input.split(_wrs_xmlCharacters.doubleQuote).join(_wrs_safeXmlCharacters.doubleQuote);
	input = input.split(_wrs_xmlCharacters.ampersand).join(_wrs_safeXmlCharacters.ampersand);
	input = input.split(_wrs_xmlCharacters.quote).join(_wrs_safeXmlCharacters.quote);
	
	return input;
}

/**
 * Converts special symbols (> 128) to entities.
 * @param string mathml
 * @return string
 */
function wrs_mathmlEntities(mathml) {
	var toReturn = '';
	
	for (var i = 0; i < mathml.length; ++i) {
		//parsing > 128 characters
		if (mathml.charCodeAt(i) > 128) {
			toReturn += '&#' + mathml.charCodeAt(i) + ';';
		}
		else {
			toReturn += mathml.charAt(i);
		}
	}
	
	return toReturn;
}

/**
 * Gets the accessible text of a given formula.
 * @param string mathml
 * @param string language
 * @return string
 */
function wrs_mathmlToAccessible(mathml, language) {
	var data = {
		'service': 'mathml2accessible',
		'mml': mathml
	};
	
	if (language) {
		data['lang'] = language;
	}
	
	return wrs_getContent(_wrs_conf_servicePath, data);
}

/**
 * Converts mathml to an iframe object.
 * @return object
 */
function wrs_mathmlToIframeObject(windowTarget, mathml) {
	if (window.navigator.userAgent.toLowerCase().indexOf('webkit') != -1) {
		// In WebKit, the formula is represented by a div instead of an iframe.
		var container = windowTarget.document.createElement('span');
		container.className = _wrs_conf_imageClassName;
		container.setAttribute(_wrs_conf_imageMathmlAttribute, mathml);
		container.setAttribute('height', '1');
		container.setAttribute('width', '1');
		container.style.display = 'inline-block';
		container.style.cursor = 'pointer';
		container.style.webkitUserModify = 'read-only';
		container.style.webkitUserSelect = 'all';
		
		var formulaContainer = windowTarget.document.createElement('span');
		formulaContainer.style.display = 'inline';
		container.appendChild(formulaContainer);

		function waitForViewer() {
			if (windowTarget.com && windowTarget.com.wiris) {
				if (!('_wrs_viewer' in windowTarget)) {
					windowTarget._wrs_viewer = new windowTarget.com.wiris.jsEditor.JsViewerMain(_wrs_conf_pluginBasePath + '/integration/editor');
					windowTarget._wrs_viewer.insertCSS(null, windowTarget.document);
				}
				
				windowTarget._wrs_viewer.paintFormulaOnContainer(mathml, formulaContainer, null);
				
				function prepareDiv() {
					if (windowTarget._wrs_viewer.isReady()) {
						container.style.height = formulaContainer.style.height;
						container.style.width = formulaContainer.style.width;
						container.style.verticalAlign = formulaContainer.style.verticalAlign;
					}
					else {
						setTimeout(prepareDiv, 100);
					}
				};
				
				prepareDiv();
			}
			else {
				setTimeout(waitForViewer, 100);
			}
		}
		
		if (!('_wrs_viewerAppended' in windowTarget)) {
			var viewerScript = windowTarget.document.createElement('script');
			viewerScript.src = _wrs_conf_pluginBasePath + '/integration/editor/viewer.js';
			windowTarget.document.getElementsByTagName('head')[0].appendChild(viewerScript);
			windowTarget._wrs_viewerAppended = true;
		}
		
		waitForViewer();
		
		return container;
	}
	
	windowTarget.document.wrs_assignIframeEvents = function (myIframe) {
		wrs_addEvent(myIframe.contentWindow.document, 'click', function () {
			wrs_fireEvent(myIframe, 'dblclick');
		});
	};

	var iframe = windowTarget.document.createElement('iframe');
	iframe.className = _wrs_conf_imageClassName;
	iframe.setAttribute(_wrs_conf_imageMathmlAttribute, mathml);
	iframe.style.display = 'inline';
	iframe.style.border = 'none';
	iframe.setAttribute('height', '1');
	iframe.setAttribute('width', '1');
	iframe.setAttribute('scrolling', 'no');
	iframe.setAttribute('frameBorder', '0');
	iframe.src = _wrs_conf_pluginBasePath + '/core/iframe.html#' + _wrs_conf_imageMathmlAttribute;
	return iframe;
}

/**
 * Converts mathml to img object.
 * @param object creator Object with the "createElement" method
 * @param string mathml MathML code
 * @return object
 */
function wrs_mathmlToImgObject(creator, mathml, wirisProperties, language) {
	var imgObject = creator.createElement('img');
	//imgObject.title = 'Double click to edit';
	imgObject.align = 'middle';
	
	if (window._wrs_conf_enableAccessibility && _wrs_conf_enableAccessibility) {
		imgObject.alt = wrs_mathmlToAccessible(mathml, language);
	}
	
	imgObject.className = _wrs_conf_imageClassName;
	
	var result = wrs_createImageSrc(mathml, wirisProperties);
	
	if (window._wrs_conf_useDigestInsteadOfMathml && _wrs_conf_useDigestInsteadOfMathml) {
		var parts = result.split(':', 2);
		imgObject.setAttribute(_wrs_conf_imageMathmlAttribute, parts[0]);
		imgObject.src = parts[1];
	}
	else {
		imgObject.setAttribute(_wrs_conf_imageMathmlAttribute, wrs_mathmlEncode(mathml));
		imgObject.src = result;
	}
	
	return imgObject;
}

/**
 * Opens a new CAS window.
 * @param object target The editable element
 * @param boolean isIframe Specifies if target is an iframe or not
 * @param string language
 * @return object The opened window
 */
function wrs_openCASWindow(target, isIframe, language) {
	if (isIframe === undefined) {
		isIframe = true;
	}
	
	_wrs_temporalRange = null;
	
	if (target) {
		var selectedItem = wrs_getSelectedItem(target, isIframe);
		
		if (selectedItem != null && selectedItem.caretPosition === undefined && selectedItem.node.nodeName.toUpperCase() == 'IMG' && selectedItem.node.className == _wrs_conf_CASClassName) {
			_wrs_temporalImage = selectedItem.node;
			_wrs_isNewElement = false;
		}
	}
	
	var path = _wrs_conf_CASPath;
	
	if (language) {
		path += '?lang=' + language;
	}

	return window.open(path, 'WIRISCAS', _wrs_conf_CASAttributes);
}

/**
 * Opens a new editor window.
 * @param string language Language code for the editor
 * @param object target The editable element
 * @param boolean isIframe Specifies if the target is an iframe or not
 * @return object The opened window
 */
function wrs_openEditorWindow(language, target, isIframe) {
	var ua = navigator.userAgent.toLowerCase();
	var isAndroid = ua.indexOf("android") > -1; 
	if(isAndroid) {
		var selection = target.contentWindow.getSelection();
		_wrs_androidRange = selection.getRangeAt(0);
	}

	if (isIframe === undefined) {
		isIframe = true;
	}
	
	var path = _wrs_conf_editorPath;

	if (language) {
		path += '?lang=' + language;
	}

	var availableDirs = new Array('rtl', 'ltr');
	if (typeof _wrs_int_directionality != 'undefined' && wrs_arrayContains(availableDirs, _wrs_int_directionality) != -1){
		path += '&dir=' + _wrs_int_directionality;
	}
	
	_wrs_editMode = (window._wrs_conf_defaultEditMode) ? _wrs_conf_defaultEditMode : 'images';
	_wrs_temporalRange = null;
	
	if (target) {
		var selectedItem = wrs_getSelectedItem(target, isIframe);
		
		if (selectedItem != null) {
			if (selectedItem.caretPosition === undefined) {
				if (selectedItem.node.className == _wrs_conf_imageClassName) {
					if (selectedItem.node.nodeName.toUpperCase() == 'IMG') {
						_wrs_editMode = 'images';
					}
					else if (selectedItem.node.nodeName.toUpperCase() == 'IFRAME') {
						_wrs_editMode = 'iframes';
					}
					
					_wrs_temporalImage = selectedItem.node;
					_wrs_isNewElement = false;
				}
			}
			else {
				var latexResult = wrs_getLatexFromTextNode(selectedItem.node, selectedItem.caretPosition);

				if (latexResult != null) {
					_wrs_editMode = 'latex';
					
					var mathml = wrs_getMathMLFromLatex(latexResult.latex);
					_wrs_isNewElement = false;
					
					_wrs_temporalImage = document.createElement('img');
					_wrs_temporalImage.setAttribute(_wrs_conf_imageMathmlAttribute, wrs_mathmlEncode(mathml));
					var windowTarget = (isIframe) ? target.contentWindow : window;
					
					if (document.selection) {
						var leftOffset = 0;
						var previousNode = latexResult.startNode.previousSibling;
						
						while (previousNode) {
							leftOffset += wrs_getNodeLength(previousNode);
							previousNode = previousNode.previousSibling;
						}
					
						_wrs_temporalRange = windowTarget.document.selection.createRange();
						_wrs_temporalRange.moveToElementText(latexResult.startNode.parentNode);
						_wrs_temporalRange.move('character', leftOffset + latexResult.startPosition);
						_wrs_temporalRange.moveEnd('character', latexResult.latex.length + 4);		// +4 for the '$$' characters.
					}
					else {
						_wrs_temporalRange = windowTarget.document.createRange();
						_wrs_temporalRange.setStart(latexResult.startNode, latexResult.startPosition);
						_wrs_temporalRange.setEnd(latexResult.endNode, latexResult.endPosition);
					}
				}
			}
		}
	}
	
	return window.open(path, 'WIRISeditor', _wrs_conf_editorAttributes);
}

/**
 * Converts all occurrences of mathml code to the corresponding image.
 * @param string content
 * @return string
 */
function wrs_parseMathmlToImg(content, characters, language) {
	var output = '';
	var mathTagBegin = characters.tagOpener + 'math';
	var mathTagEnd = characters.tagOpener + '/math' + characters.tagCloser;
	var start = content.indexOf(mathTagBegin);
	var end = 0;
	
	while (start != -1) {
		output += content.substring(end, start);
		end = content.indexOf(mathTagEnd, start);
		
		if (end == -1) {
			end = content.length - 1;
		}
		else {
			end += mathTagEnd.length;
		}
		
		if (!wrs_isMathmlInAttribute(content, start)){
			var mathml = content.substring(start, end);
			mathml = (characters == _wrs_safeXmlCharacters) ? wrs_mathmlDecode(mathml) : wrs_mathmlEntities(mathml);
			output += wrs_createObjectCode(wrs_mathmlToImgObject(document, mathml, null, language));
		}
		else {
			output += content.substring(start, end);
		}
		
		start = content.indexOf(mathTagBegin, end);
	}
	
	output += content.substring(end, content.length);
	return output;
}

/**
 * Converts all occurrences of safe applet code to the corresponding code.
 * @param string content
 * @return string
 */
function wrs_parseSafeAppletsToObjects(content) {
	var output = '';
	var appletTagBegin = _wrs_safeXmlCharacters.tagOpener + 'APPLET';
	var appletTagEnd = _wrs_safeXmlCharacters.tagOpener + '/APPLET' + _wrs_safeXmlCharacters.tagCloser;
	var upperCaseContent = content.toUpperCase();
	var start = upperCaseContent.indexOf(appletTagBegin);
	var end = 0;
	
	while (start != -1) {
		output += content.substring(end, start);
		end = upperCaseContent.indexOf(appletTagEnd, start);
		
		if (end == -1) {
			end = content.length - 1;
		}
		else {
			end += appletTagEnd.length;
		}
		
		output += wrs_mathmlDecode(content.substring(start, end));
		start = upperCaseContent.indexOf(appletTagBegin, end);
	}
	
	output += content.substring(end, content.length);
	return output;
}

/**
 * Cross-browser removeEventListener/detachEvent function.
 * @param object element Element target
 * @param event event Event
 * @param function func Function to run
 */
function wrs_removeEvent(element, event, func) {
	if (element.removeEventListener) {
		element.removeEventListener(event, func, false);
	}
	else if (element.detachEvent) {
		element.detachEvent('on' + event, func);
	}
}

/**
 * Splits an HTML content in three parts: the code before <body>, the code between <body> and </body> and the code after </body>.
 * @param string code
 * @return string
 */
function wrs_splitBody(code) {
	var prefix = '';
	var sufix = '';
	var bodyPosition = code.indexOf('<body');
	
	if (bodyPosition != -1) {
		bodyPosition = code.indexOf('>', bodyPosition);
		
		if (bodyPosition != -1) {
			++bodyPosition;
			var endBodyPosition = code.indexOf('</body>', bodyPosition);
			
			if (endBodyPosition == -1) {
				endBodyPosition = code.length;
			}
			
			prefix = code.substring(0, bodyPosition);
			sufix = code.substring(endBodyPosition, code.length);
			code = code.substring(bodyPosition, endBodyPosition);
		}
	}
	
	return {
		'prefix': prefix,
		'code': code,
		'sufix': sufix
	};
}

/**
 * Inserts or modifies CAS.
 * @param object focusElement Element to be focused
 * @param object windowTarget Window where the editable content is
 * @param string appletCode Applet code
 * @param string image Base 64 image stream
 * @param int imageWidth Image width
 * @param int imageHeight Image height
 */
function wrs_updateCAS(focusElement, windowTarget, appletCode, image, imageWidth, imageHeight) {
	var imgObject = wrs_appletCodeToImgObject(windowTarget.document, appletCode, image, imageWidth, imageHeight);
	wrs_insertElementOnSelection(imgObject, focusElement, windowTarget);
}

/**
 * Inserts or modifies formulas.
 * @param object focusElement Element to be focused
 * @param object windowTarget Window where the editable content is
 * @param string mathml Mathml code
 * @param object wirisProperties Extra attributes for the formula (like background color or font size).
 * @param string editMode Current edit mode.
 * @param string language Language for the formula.
 */
function wrs_updateFormula(focusElement, windowTarget, mathml, wirisProperties, editMode, language) {
	if (editMode == 'latex') {
		var latex = wrs_getLatexFromMathML(mathml);
		var textNode = windowTarget.document.createTextNode('$$' + latex + '$$');
		wrs_insertElementOnSelection(textNode, focusElement, windowTarget);
	}
	else if (editMode == 'iframes') {
		var iframe = wrs_mathmlToIframeObject(windowTarget, mathml);
		wrs_insertElementOnSelection(iframe, focusElement, windowTarget);
	}
	else {
		var imgObject = wrs_mathmlToImgObject(windowTarget.document, mathml, wirisProperties, language);
		wrs_insertElementOnSelection(imgObject, focusElement, windowTarget);
	}
}

/**
 * Inserts or modifies formulas or CAS on a textarea.
 * @param object textarea Target
 * @param string text Text to add in the textarea. For example, if you want to add the link to the image, you can call this function as wrs_updateTextarea(textarea, wrs_createImageSrc(mathml));
 */
function wrs_updateTextarea(textarea, text) {
	if (textarea && text) {
		textarea.focus();
		
		if (textarea.selectionStart != null) {
			textarea.value = textarea.value.substring(0, textarea.selectionStart) + text + textarea.value.substring(textarea.selectionEnd, textarea.value.length);
		}
		else {
			var selection = document.selection.createRange();
			selection.text = text;
		}
	}
}

/**
 * URL decode function.
 * @param string input
 * @return string
 */
function wrs_urldecode(input) {
	return decodeURIComponent(input);
}

/**
 * URL encode function.
 * @param string clearString Input.
 * @return string
 */
function wrs_urlencode(clearString) {
	var output = '';
	//encodeURIComponent doesn't encode !'()*~
	output = encodeURIComponent(clearString).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/~/g, '%7E');	
	return output;
}
