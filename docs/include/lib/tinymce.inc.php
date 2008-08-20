<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

function load_editor($name = FALSE, $mode="textareas") {
	global $_base_path, $content_base_href;

	 if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) { 
		$course_base_href = 'get.php/'; 
	} else {  
		$course_base_href = 'content/' . $_SESSION['course_id'] . '/'; 
	}

// Note: Some tinymce buttons are removed due to lack of accessibility for disabled.
// They are:
//New Document: newdocument
//Insert Time: inserttime
//Insert Date: insertdate
//Preview: preview
//toggle guidelines: visualaid
//spellcheck: iespell
//embed media: media
//print: print
//Insert Layer: insertlayer
//move forward: moveforward
//move backward: movebackward
//toggle absolute positioning: absolute
//citation: cite
//deletion: del
//insertion: ins
//visual control characters on/off: visualchars
//insert non-breaking space character: nonbreaking
//insert predefined template: template
//insert page break: pagebreak

	echo '
<script language="javascript" type="text/javascript" src="'.AT_BASE_HREF.'jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({ 
';
	if ($name) {
		echo '  mode : "exact",';
		echo '  elements : "'.$name.'",';
	} else {
		echo '	mode : "'.$mode.'",';
	}	
	echo 'theme : "advanced",
	relative_urls : true,
	content_css :"'.$_base_path.'/themes/'.$_SESSION['prefs'][PREF_THEME].'/styles.css",
	convert_urls : true,
	convert_fonts_to_spans : true,
	accessibility_warnings : true,
	entity_encoding : "raw",
	accessibility_focus : true,
	plugins : "acheck, table,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,|,sub,sup,|,charmap,emotions,advhr,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "styleprops,|,abbr,acronym,attribs,|,acheck",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	theme_advanced_resizing : true,
	remove_linebreaks: false,
	apply_source_formatting: true, //<-- not working sad

	plugin_insertdate_dateFormat : "%Y-%m-%d",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	document_base_url: "'.AT_BASE_HREF.$course_base_href.$content_base_href.'"
	});';
	echo '</script>';
}

?>