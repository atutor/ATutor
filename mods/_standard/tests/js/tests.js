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

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.tests = ATutor.mods.tests || {};

(function () {
	var activateHideAllLink = function (collapsibleElements, hideAllLink) {
		if (collapsibleElements.length > 0) {
			hideAllLink.show();
			
			hideAllLink.click(function (event) {
				var link = (event.currentTarget) ? $(event.currentTarget) : $(event.srcElement),
					linkHideText = link.find(".hideAllLabel"),
					linkShowText = link.find(".showAllLabel"),
					isAllHidden = linkShowText.is(":visible"),
					linkClass;
				
				if (isAllHidden) {
					linkHideText.show();
					linkShowText.hide();
					linkClass = ".show";
				} else {
					linkHideText.hide();
					linkShowText.show();
					linkClass = ".hide";
				}
				
				collapsibleElements.filter(linkClass).click();
				
				link.focus();
				return false;
			});
		}	
	};
	
	var makeCollapsibles = function (options) {
		options || (options = {});
		
		var collapsibleElements = options.collapsibleElements;
		
		if (!collapsibleElements || collapsibleElements.length === 0) {
			return;
		}
		
		collapsibleElements.click(function (event) {
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
				
				link.addClass("hide").removeClass("show");
	
				addRemoveClass = "removeClass";
				rowShowHide = "slideDown";
				
				fieldset[addRemoveClass](collapsedClass);
				fieldset.animate({"min-height": options.fieldsetNotCollapsedMinHeight}, "slow");
				row[rowShowHide]("slow");
			} else {
				linkHideText.hide();
				linkShowText.show();
				
				link.removeClass("hide").addClass("show");
				
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
	};
	
	var initialize = function () {
		var collapsibleElements = $(".collapsible"),
			hideAllLink = $(".hideAllRemedialLink");
		
		makeCollapsibles({
			collapsibleElements: collapsibleElements,
			collapsedClass: "collapsed",
			fieldsetNotCollapsedMinHeight: "170px",
			fieldsetCollapsedMinHeight: "5px"
		});
		
		activateHideAllLink(collapsibleElements, hideAllLink);
	};

	jQuery(document).ready(initialize);
})();