/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2010                                            */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/** ********************************************************************* */
// $Id: $
var ATutor = ATutor || {};
ATutor.course = ATutor.course || {};

(function() {

	var element_collapse_icon;
	var element_expand_icon;
	
	var setExpandIcon = function (clickedElement, title) {
		clickedElement.attr("src", element_expand_icon)
		clickedElement.attr("alt", ATutor.course.show + " " + title);
		clickedElement.attr("title", ATutor.course.show + " " + title);
		ATutor.setcookie("m_"+title, 0, 1);
	};
	
	var setCollapseIcon = function (clickedElement, title) {
		clickedElement.attr("src", element_collapse_icon);
		clickedElement.attr("alt", ATutor.course.hide + " " + title);
		clickedElement.attr("title", ATutor.course.hide + " " + title);
		ATutor.setcookie("m_"+title, null, 1);;

	};
	
	// toggle elements in side menu (via the +/- icon in each side menu element)
	function showHideSubmenu()
	{
		var clickedElement = jQuery(this);
		var title = jQuery("span", clickedElement.parent()).html();
		if (clickedElement.attr("src") == element_collapse_icon) {
			setExpandIcon(clickedElement, title);
		}
		else {
			setCollapseIcon(clickedElement, title);
		}
		clickedElement.parent().next().slideToggle();
	}
	
	//modifies the menu html to add title, expand/collapse image, alt text. 
	var printSubmenus = function () {
		var sideMenuBoxHeadings = jQuery("h4.box");
		for (var titleIndex = 0; titleIndex < sideMenuBoxHeadings.length; titleIndex++) {
			var heading = jQuery(sideMenuBoxHeadings[titleIndex]);				
			var title = jQuery("span", heading).html();
			var inputElement = jQuery("input", heading);
			var menu = jQuery(heading.next());
			if (ATutor.getcookie("m_" + title) == "0") {
				setExpandIcon(inputElement, title);
				menu.hide();
			} else {
				setCollapseIcon(inputElement, title);
				menu.show();	
			}
			inputElement.click(showHideSubmenu);
			
		}
	};
	
	var menu_show_icon;
	var menu_hide_icon;
	
	//Initialize the submenus
	ATutor.course.doSideMenus = function () {
		element_collapse_icon = ATutor.base_href + "themes/" + ATutor.course.theme + "/images/mswitch_minus.gif";
		element_expand_icon = ATutor.base_href + "themes/" + ATutor.course.theme + "/images/mswitch_plus.gif";
		printSubmenus();		
	};
	
	var hideMenu = function (effect) {
		var menuImage = jQuery("#menutoggle > a > img");
		menuImage.attr("src", menu_show_icon);
		menuImage.attr("alt", ATutor.course.show);
		menuImage.attr("title", ATutor.course.show);
		
		if (effect) {
			jQuery("#side-menu").slideUp("slow");
		} else {
			jQuery("#side-menu").hide();
		}
			
		ATutor.setcookie("side-menu", "none", 1);
	};

	var showMenu = function (effect) {		
		var menuImage = jQuery("#menutoggle > a > img");
		menuImage.attr("src", menu_hide_icon);
		menuImage.attr("alt", ATutor.course.hide);
		menuImage.attr("title", ATutor.course.hide);
		
		if (effect) {
			jQuery("#side-menu").slideDown("slow");
		} else {
			jQuery("#side-menu").show();
		}
		ATutor.setcookie("side-menu", "", 1);
	};
	
	var showHideMenu = function () {
		var clickedElement = jQuery("img", this);
		if (clickedElement.attr("src") == menu_hide_icon) {
			hideMenu(true);
		}
		else {
			showMenu(true);
		}
	};

	var printMenuToggle = function (effect) {
		var state = ATutor.getcookie("side-menu");
		if (state && state=="none") { 
			hideMenu(effect);
		} else {
			showMenu(effect); 
		}
		var menuLink = jQuery("#menutoggle > a");
		menuLink.click(showHideMenu);	
	};
	
	ATutor.course.doMenuToggle = function (effect) {
		menu_show_icon = ATutor.base_href + "themes/" + ATutor.course.theme + "/images/showmenu.gif";
		menu_hide_icon = ATutor.base_href +  "themes/" + ATutor.course.theme + "/images/hidemenu.gif"; 
		printMenuToggle();
	};
	
	ATutor.course.toggleFolder = function (cid, expand, collapse, course_id) {
		if (jQuery("#tree_icon"+cid).attr("src") == ATutor.course.collapse_icon) {
			jQuery("#tree_icon"+cid).attr("src", ATutor.course.expand_icon);
			jQuery("#tree_icon"+cid).attr("alt", expand);
			jQuery("#tree_icon"+cid).attr("title", expand);
			ATutor.setcookie("c"+course_id+"_"+cid, null, 1);
		}
		else {
			jQuery("#tree_icon"+cid).attr("src", ATutor.course.collapse_icon);
			jQuery("#tree_icon"+cid).attr("alt", collapse);
			jQuery("#tree_icon"+cid).attr("title", collapse);
			ATutor.setcookie("c"+course_id+"_"+cid, "1", 1);
		}
		
		jQuery("#folder"+cid).slideToggle();
	};

})();
