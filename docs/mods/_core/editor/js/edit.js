/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2010 by Laurel Williams                                */
/* Adaptive Technology Resource Centre / University of Toronto          */
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
ATutor.mods.editor = ATutor.mods.editor || {};

(function () {
    var hiddenClass = "hidden";
    var enabledClass = "clickable";
    
    var hideIt = function (theElement, hiddenElement) {
        theElement.addClass(hiddenClass);
        hiddenElement.val("0");
    };

    var showIt = function (theElement, hiddenElement) {
        theElement.removeClass(hiddenClass);
        hiddenElement.val("1");
    };

    //hides or shows tool (toggle) and sets hidden input value appropriately.
    var doToggle = function (theElement, hiddenElement) {
        if (theElement.hasClass(hiddenClass)) {
            showIt(theElement, hiddenElement);
        } else {
            hideIt(theElement, hiddenElement);
        }
    };

    //customized head variables
    var headId = "#head";
    var displayheadId = "#displayhead";
    var headTool = {
            toolId: "#headtool",
            enabledClass: enabledClass,
            enabledImage: "/images/custom_head.png",
            clickFunction: function () {
                doToggle(jQuery(headId), jQuery(displayheadId));
            },
            disabledImage: "/images/custom_head_disabled.png",
        };

    //paste from file variables
    var pasteId = "#paste";
    var displaypasteId = "#displaypaste";
    var pasteTool = {
            toolId: "#pastetool",
            enabledClass: enabledClass,
            enabledImage: "/images/paste.png",
            clickFunction: function () {
                doToggle(jQuery(pasteId), jQuery(displaypasteId));
            },
            disabledImage: "/images/paste_disabled.png",
        };

    var base_path = window.location.protocol + "//" + window.location.host + "/" + window.location.pathname.split("/")[1];

    //click function to launch file manager window
    var launchFileManager = function () {
        window.open(base_path + '/mods/_core/file_manager/index.php?framed=1&popup=1&cp=' + ATutor.mods.editor.content_path, 'newWin1', 'menubar=0,scrollbars=1,resizable=1,width=640,height=490');
        return false;
    };

    //file manager variables
    var filemanTool = {
            toolId: "#filemantool",
            enabledClass: enabledClass,
            enabledImage: "/images/file-manager.png",
            clickFunction: function () {
                launchFileManager();
            },
            disabledImage: "/images/file-manager_disabled.png",
        };
    
    //checks hidden variable and shows/hides element accordingly
    var setDisplay = function (theElement, hiddenElement) {
        if (hiddenElement.val() === '0') {
            theElement.addClass(hiddenClass);
        } else {
            theElement.removeClass(hiddenClass);
        }
    };

    var disableTool = function (theTool) {
        var theToolElement = jQuery(theTool.toolId);
        theToolElement.removeClass(theTool.enabledClass);
        theToolElement.attr("src", base_path + theTool.disabledImage);
        theToolElement.attr("title", theTool.disabledTitle);
        theToolElement.attr("alt", theTool.disabledTitle);
        theToolElement.unbind("click");
    };
    
    var enableTool = function (theTool) {
        var theToolElement = jQuery(theTool.toolId);
        theToolElement.addClass(theTool.enabledClass);
        theToolElement.attr("src", base_path + theTool.enabledImage);
        theToolElement.attr("title", theTool.enabledTitle);
        theToolElement.attr("alt", theTool.enabledTitle);
        theToolElement.click(theTool.clickFunction);
    };	

    //initialises values to show or hide them
    var setupPage = function () {
        var head = jQuery(headId);
        var displayhead = jQuery(displayheadId);
        var paste = jQuery(pasteId);
        var displaypaste = jQuery(displaypasteId);
        var textArea = jQuery("#textSpan");
        var weblink = jQuery("#weblinkSpan");
        if (jQuery("#weblink").attr("checked")) {
            disableTool(headTool);
            disableTool(pasteTool);
            disableTool(filemanTool);
            
            hideIt(head, displayhead);
            hideIt(paste, displaypaste);
            tinyMCE.execCommand('mceRemoveControl', false, 'body_text');
            textArea.hide();
            weblink.show();
        } else if (jQuery("#html").attr("checked")) {
            enableTool(headTool);
            enableTool(pasteTool);
            enableTool(filemanTool);
            
            setDisplay(head, displayhead);
            setDisplay(paste, displaypaste);
            if (ATutor.mods.editor.editor_pref !== '1') {
                tinyMCE.execCommand('mceAddControl', false, 'body_text');
            }
            weblink.hide();
            textArea.show();
        } else {
            disableTool(headTool);
            enableTool(pasteTool);
            enableTool(filemanTool);
            
            hideIt(head, displayhead);
            setDisplay(paste, displaypaste);
            weblink.hide();
            tinyMCE.execCommand('mceRemoveControl', false, 'body_text');
            textArea.show();
        }	
    };

    //click function to launch tool window
    var launchTool = function () {
        window.open(base_path + '/mods/_core/tool_manager/index.php?framed=1&popup=1&tool_file=' + ATutor.mods.editor.tool_file + '&cid=' + ATutor.mods.editor.content_id, 'newWin2', 'menubar=0,scrollbars=1,resizable=1,width=600,height=400');
        return false;
    };

    //set up click handlers and show/hide appropriate tools via setupPage
    var initialize = function () {
        jQuery(".tool").click(launchTool);
        jQuery("#formatting_radios > input").click(setupPage);
        headTool.enabledTitle = ATutor.mods.editor.head_enabled_title;
        headTool.disabledTitle = ATutor.mods.editor.head_disabled_title;
        pasteTool.enabledTitle = ATutor.mods.editor.paste_enabled_title;
        pasteTool.disabledTitle = ATutor.mods.editor.paste_disabled_title;
        filemanTool.enabledTitle = ATutor.mods.editor.fileman_enabled_title;
        filemanTool.disabledTitle = ATutor.mods.editor.fileman_disabled_title;
        setupPage();
    };
    
    jQuery(document).ready(initialize);
})();