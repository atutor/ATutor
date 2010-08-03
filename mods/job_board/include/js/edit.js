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
// $Id$

/*global jQuery*/
/*global ATutor */
/*global tinyMCE */
/*global window */

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.editor = ATutor.mods.editor || {};

(function () {
    //initialises values to show or hide them
    var setupPage = function () {
        var textArea = jQuery("#textSpan");
        var textAreaId = "jb_description";
        if (jQuery("#html").attr("checked")) {
            if (ATutor.mods.editor.editor_pref !== '1' && !tinyMCE.get(textAreaId)) {
           		tinyMCE.execCommand('mceAddControl', false, textAreaId);
            }
            textArea.show();
        } else {
            if (tinyMCE.get(textAreaId)) {
            	tinyMCE.execCommand('mceRemoveControl', false, textAreaId);
            }
            textArea.show();
        }	
    };

    //set up click handlers and show/hide appropriate tools via setupPage
    var initialize = function () {
        jQuery("#formatting_radios > input").click(setupPage);
        setupPage();
    };
    
    jQuery(document).ready(initialize);
})();