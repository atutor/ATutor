/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2010 by Laurel Williams                                */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: $

/*global jQuery*/
/*global ATutor */
/*global tinyMCE */
/*global window */

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.tests = ATutor.mods.tests || {};

(function () {
	var makeCollapsibles = function (options) {
		options || (options = {});
		var elements = $(options.elementsJQuerySearch),
			length = elements.length,
			i;
		
		for (i=0; i < length; ++i) {
			$(elements[i]).click(function (event) {
				var link = (event.currentTarget) ? $(event.currentTarget) : $(event.srcElement),
					linkHideText = link.find(".hideLabel"),
					linkShowText = link.find(".showLabel"),
					fieldset = link.parent().parent(),
					row = fieldset.find(".row"),
					collapsedClass = options.collapsedClass,
					isCollapsed = fieldset.hasClass(collapsedClass),
					linkNewText, addRemoveClass, rowShowHide;
				
				if (row.is(":animated")) {
					return;
				}
				
				if (isCollapsed) {
					linkHideText.show();
					linkShowText.hide();
		
					addRemoveClass = "removeClass";
					rowShowHide = "slideDown";
					
					fieldset[addRemoveClass](collapsedClass);
					fieldset.animate({"min-height": options.fieldsetNotCollapsedMinHeight}, "slow");
					row[rowShowHide]("slow");
				} else {
					linkHideText.hide();
					linkShowText.show();
					
					addRemoveClass = "addClass";
					rowShowHide = "slideUp";
					
					fieldset.animate({"min-height": options.fieldsetCollapsedMinHeight}, "slow");
					
					row[rowShowHide]('slow', function() {
						fieldset[addRemoveClass](collapsedClass);
					});
				}
				
				link.focus();
				return false;
			});
		}
	};
	
	var initialize = function () {
		makeCollapsibles({
			elementsJQuerySearch: ".collapsible",
			collapsedClass: "collapsed",
			fieldsetNotCollapsedMinHeight: "170px",
			fieldsetCollapsedMinHeight: "5px"
		});
	};

	jQuery(document).ready(initialize);
})();