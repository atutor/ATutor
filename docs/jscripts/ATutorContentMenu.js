/* 
ATutor
Copyright (c) 2002 - 2010
Inclusive Design Institute
http://atutor.ca

This program is free software. You can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation.
*/

/* Note that this javascript calls on a js function ATutor.course.contentMenu.initContentMenu()
that is generated from php function getInitMenuJS() in ContentManager.class.php. So, must call 
the php function before including this js script. */

/* global jQuery */

var ATutor = ATutor || {};
ATutor.course = ATutor.course || {};
ATutor.course.contentMenu = ATutor.course.contentMenu || {};

(function() {

	var inlineEditsSetup = function () {
		jQuery("#editable_table").find(".inlineEdits").each(function() {
			jQuery(this).text(jQuery(this).attr("title"));
		});
		
		var tableEdit = fluid.inlineEdits("#editable_table", {
			selectors : {
				text : ".inlineEdits",
				editables : "span:has(span.inlineEdits)"
			},
			defaultViewText: "",
			applyEditPadding: false,
			useTooltip: false,
			listeners: {
				afterFinishEdit : function (newValue, oldValue, editNode, viewNode) {
					if (newValue != oldValue) 
					{
						rtn = jQuery.post(ATutor.base_path+"mods/_core/content/menu_inline_editor_submit.php", { "field":viewNode.id, "value":newValue }, 
							          function(data) {}, "json");
					}
				}
			}
		});

		jQuery(".fl-inlineEdit-edit").css("width", "80px")
	};

	ATutor.course.contentMenu.expandContentFolder = function(contentID) {
		jQuery("#folder"+contentID).show();
		jQuery("#tree_icon"+contentID).attr("src", ATutor.course.collapse_icon);
		jQuery("#tree_icon"+contentID).attr("alt", ATutor.course.text_collapse);
		jQuery("#tree_icon"+contentID).attr("title", ATutor.course.text_collapse);
	};

	ATutor.course.contentMenu.collapseContentFolder = function(contentID) {
		jQuery("#folder"+contentID).hide();
		jQuery("#tree_icon"+contentID).attr("src", ATutor.course.expand_icon);
		jQuery("#tree_icon"+contentID).attr("alt", ATutor.course.text_expand);
		jQuery("#tree_icon"+contentID).attr("title", ATutor.course.text_expand);
	};

	ATutor.course.contentMenu.switchEditMode = function() {
		title_edit = ATutor.course.text_enter_edit_mode;
		img_edit = ATutor.base_path+"images/medit.gif";
		
		title_view = ATutor.course.text_exit_edit_mode;
		img_view = ATutor.base_path+"images/mlock.gif";
		
		if (jQuery("#img_switch_edit_mode").attr("src") == img_edit)
		{
			jQuery("#img_switch_edit_mode").attr("src", img_view);
			jQuery("#img_switch_edit_mode").attr("alt", title_view);
			jQuery("#img_switch_edit_mode").attr("title", title_view);
			inlineEditsSetup();
		}
		else
		{ // refresh the content navigation to exit the edit mode
			jQuery.post(ATutor.base_path+"refresh_content_nav.php", {}, 
						function(data) {jQuery("#editable_table").replaceWith(data); ATutor.course.contentMenu.initContentMenu();});
		}
	};

	jQuery(document).ready(function () {
		ATutor.course.contentMenu.initContentMenu();
	});
})();
