/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2010 by Laurel Williams                           */
/* Adaptive Technology Resource Centre / University of Toronto          */
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
		element_collapse_icon = ATutor.base_href + "images/mswitch_minus.gif";
		element_expand_icon = ATutor.base_href + "images/mswitch_plus.gif";
		printSubmenus();		
	};
	
	var hideMenu = function () {
		var menuImage = jQuery("#menutoggle > a > img");
		menuImage.attr("src", menu_show_icon);
		menuImage.attr("alt", ATutor.course.show);
		menuImage.attr("title", ATutor.course.show);
		
		jQuery("#side-menu").slideUp("slow");
		ATutor.setcookie("side-menu", "none", 1);
	};

	var showMenu = function () {		
		var menuImage = jQuery("#menutoggle > a > img");
		menuImage.attr("src", menu_hide_icon);
		menuImage.attr("alt", ATutor.course.hide);
		menuImage.attr("title", ATutor.course.hide);
		
		jQuery("#side-menu").slideDown("slow");
		ATutor.setcookie("side-menu", "", 1);
	};
	
	var showHideMenu = function () {
		var clickedElement = jQuery("img", this);
		if (clickedElement.attr("src") == menu_hide_icon) {
			hideMenu();
		}
		else {
			showMenu();
		}
	};

	var printMenuToggle = function () {
		var state = ATutor.getcookie("side-menu");
		if (state && state=="none") { 
			hideMenu();
		} else {
			showMenu(); 
		}
		var menuLink = jQuery("#menutoggle > a");
		menuLink.click(showHideMenu);	
	};
	
	ATutor.course.doMenuToggle = function () {
		menu_show_icon = ATutor.base_href + "images/showmenu.gif";
		menu_hide_icon = ATutor.base_href + "images/hidemenu.gif";
		printMenuToggle();
	};
	
	ATutor.course.toggleFolder = function (cid) {
		var tree_collapse_icon = ATutor.base_href + "images/tree/tree_collapse.gif";
		var tree_expand_icon = ATutor.base_href + "images/tree/tree_expand.gif";
		if (jQuery("#tree_icon"+cid).attr("src") == tree_collapse_icon) {
			jQuery("#tree_icon"+cid).attr("src", tree_expand_icon);
			jQuery("#tree_icon"+cid).attr("alt", "<?php echo _AT('expand'); ?>");
			jQuery("#tree_icon"+cid).attr("title", "<?php echo _AT('expand'); ?>");
			ATutor.setcookie("c<?php echo $this->course_id;?>_"+cid, null, 1);
		}
		else {
			jQuery("#tree_icon"+cid).attr("src", tree_collapse_icon);
			jQuery("#tree_icon"+cid).attr("alt", "<?php echo _AT('collapse'); ?>");
			jQuery("#tree_icon"+cid).attr("title", "<?php echo _AT('collapse'); ?>");
			ATutor.setcookie("c<?php echo $this->course_id;?>_"+cid, "1", 1);
		}
		
		jQuery("#folder"+cid).slideToggle();
	};

})();
