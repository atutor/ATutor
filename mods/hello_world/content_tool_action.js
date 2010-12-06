/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id: login.php 10143 2010-08-19 19:26:05Z cindy $

/* The javascript is used in module.php @ $this->_content_tools["js"] */

/*global jQuery*/
/*global ATutor */
/*global tinyMCE */
/*global window */

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.hello_world = ATutor.mods.hello_world || {};

(function () {
    var helloWorldOnClick = function () {
    	alert("Clicked on hello world tool icon!");
    }
    
	//set up click handlers and show/hide appropriate tools
    var initialize = function () {
        jQuery("#helloworld_tool").click(helloWorldOnClick);
    };
    
    jQuery(document).ready(initialize);
})();