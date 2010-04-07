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
/************************************************************************/
// $Id: $

ATutor = ATutor || {};
ATutor.mods = ATutor.mods || {};
ATutor.mods.editor = ATutor.mods.editor || {};

(function () {
	var hiddenClass = "hidden";
	
	var headId = "#head";
	var headtoolId = "#headtool";
	var displayheadId = "#displayhead";
	
	var displaytoolsId = "#displaytools";
	var toolsId = "#tools";
	
	var isWeblinkId = "#weblink";
	var isHTMLId = "#html";
	
	var pasteToolId = "#pastetool";
	var filemanToolId = "#filemantool";
	
	var textAreaId = "#textSpan";
	var weblinkId = "#weblinkSpan";

    //hides the custom head button and custom head tools
    var hideHead = function () {    	
        jQuery(headtoolId).hide();
    	var head = jQuery(headId);
        if (!head.hasClass(hiddenClass)) {
            doToggleTools(head, jQuery(displayheadId));
        }
    };

    //hides or shows tool (toggle) and sets hidden input value appropriately.
    var doToggleTools = function (theElement, hiddenElement) {
        if (theElement.hasClass(hiddenClass)) {
            theElement.removeClass(hiddenClass);
            hiddenElement.val("1");
        } else {
            theElement.addClass(hiddenClass);
            hiddenElement.val("0");
        }       
    };

    //initializes values to show or hide them on page load
	ATutor.mods.editor.on_load = function (ed_pref) {	
		if (jQuery(displayheadId).val() === '0') {
			jQuery(headId).addClass(hiddenClass);
		}

		if (jQuery(displaytoolsId).val() === '0') {
			jQuery(toolsId).addClass(hiddenClass);
		}

        if (jQuery(isWeblinkId).attr("checked")) {
            hideHead();
			jQuery(pasteToolId).hide();
			jQuery(filemanToolId).hide();
            jQuery(textAreaId).hide();
        } else if (jQuery(isHTMLId).attr("checked")) {
            if (ed_pref !== '1') {
                tinyMCE.execCommand('mceAddControl', false, 'body_text');
            }
            jQuery(weblinkId).hide();
	    } else {
	        hideHead();
	        jQuery(weblinkId).hide();
	        jQuery(filemanToolId).hide();
	    }	
	};

	//toggles head visible and hides tools if they are not already hidden
    ATutor.mods.editor.toggleHead = function () {
        doToggleTools(jQuery(headId), jQuery(displayheadId));
        var tools = jQuery(toolsId);
        if (!tools.hasClass(hiddenClass)) {
            doToggleTools(tools, jQuery(displaytoolsId));
        }
    };

	//toggles tools visible and hides head if it is not already hidden
	ATutor.mods.editor.toggleTools = function () {
		doToggleTools(jQuery(toolsId), jQuery(displaytoolsId));
		var head = jQuery(headId);
		if (!head.hasClass(hiddenClass)) {
		    doToggleTools(head, jQuery(displayheadId));
		}
	};
	
	//switch between content types.
	ATutor.mods.editor.switch_content_type = function (formatting, ed_pref) {
		if (formatting === '0') { //text type
			hideHead();
			jQuery(filemanToolId).hide();
            jQuery(weblinkId).hide();
            tinyMCE.execCommand('mceRemoveControl', false, 'body_text');
			jQuery(textAreaId).show();
            jQuery(pasteToolId).show();
		}
		else if (formatting === '2') { //weblink type
			hideHead();
			jQuery(pasteToolId).hide();
			jQuery(filemanToolId).hide();
			jQuery(textAreaId).hide();
            tinyMCE.execCommand('mceRemoveControl', false, 'body_text');
            jQuery(weblinkId).show();
		}
		else { //html type
			jQuery(headtoolId).show();
			jQuery(pasteToolId).show();
			jQuery(filemanToolId).show();
			jQuery(textAreaId).show();
            if (ed_pref !== '1') {
                tinyMCE.execCommand('mceAddControl', false, 'body_text');
            }
            jQuery(weblinkId).hide();
 		}
	};
})();