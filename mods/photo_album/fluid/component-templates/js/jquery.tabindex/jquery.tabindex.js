/*
Copyright 2007 University of Toronto

Licensed under the GNU Public License or the MIT license. 
You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the GPL and MIT License at
https://source.fluidproject.org/svn/sandbox/tabindex/trunk/LICENSE.txt
*/
(function ($) {
	// Private functions.
	var normalizeTabIndexName = function () {
	    return $.browser.msie ? "tabIndex" : "tabindex";
	}

	var getValue = function (elements) {
		if (!elements.hasTabIndex ()) {
			return undefined;
		}

        // Get the attribute (.attr () doesn't work for tabIndex in IE) and return it as a number value.
		var value = elements[0].getAttribute (normalizeTabIndexName ());
		return Number (value);
	};

	var setValue = function (elements, toIndex) {
		return elements.each (function (i, item) {
			$ (item).attr (normalizeTabIndexName (), toIndex);
		});
	};

	// Public methods.
	$.fn.tabIndex = function (toIndex) {
		if (toIndex !== null && toIndex !== undefined) {
			return setValue (this, toIndex);
		} else {
			return getValue (this);
		}
	};

	$.fn.removeTabIndex = function () {
		return this.each(function (i, item) {
			$ (item).removeAttr (normalizeTabIndexName ());
		});
	};

	$.fn.hasTabIndex = function () {
	    var attributeNode = this[0].getAttributeNode (normalizeTabIndexName ());
        return attributeNode ? attributeNode.specified : false;
	};
}) (jQuery);
