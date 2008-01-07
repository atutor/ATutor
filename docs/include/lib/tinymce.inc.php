<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

function load_editor($name = FALSE) {
	global $_base_path, $content_base_href;

	 if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) { 
		$course_base_href = 'get.php/'; 
	} else {  
		$course_base_href = 'content/' . $_SESSION['course_id'] . '/'; 
	}

	echo '<script language="javascript" type="text/javascript" src="'.AT_BASE_HREF.'jscripts/tiny_mce/tiny_mce.js"></script>';
	echo '<script language="javascript" type="text/javascript">';
	echo '	tinyMCE.init({ ';
	if ($name) {
		echo '  mode : "exact",';
		echo '  elements : "'.$name.'",';
	} else {
		echo '	mode : "textareas",';
	}	
	echo 'theme : "advanced",
	relative_urls : true,
	convert_urls : true,
	convert_fonts_to_spans : true,
	accessibility_warnings : true,
	entity_encoding : "raw",
	accessibility_focus : true,
	plugins : "table,acheck,advhr,advimage,advlink,emotions,iespell,preview,zoom,flash,print,contextmenu",
	theme_advanced_buttons1_add : "fontselect,fontsizeselect",
	theme_advanced_buttons2_add : "separator,preview,zoom,separator,forecolor,backcolor",
	theme_advanced_buttons2_add_before: "cut",
	theme_advanced_buttons3_add_before : "tablecontrols,separator",
	theme_advanced_buttons3_add : "emotions,iespell,flash,advhr,separator,print,acheck",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	plugin_insertdate_dateFormat : "%Y-%m-%d",
	plugin_insertdate_timeFormat : "%H:%M:%S",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	external_link_list_url : "example_data/example_link_list.js",
	external_image_list_url : "example_data/example_image_list.js",
	flash_external_list_url : "example_data/example_flash_list.js",
	document_base_url: "'.AT_BASE_HREF.$course_base_href.$content_base_href.'"
	});';
	echo '</script>';
}

?>