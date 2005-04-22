<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_my_uri;
global $_base_path, $include_all, $include_one;
global $savant;

ob_start(); 

if (!isset($include_all, $include_one)) {
	$include_one = ' checked="checked"';
}

echo '<form action="'.$_base_path.'search.php#search_results" method="get" name="searchform">';
echo '<input type="hidden" name="search" value="1" />';
echo '<input type="hidden" name="find_in" value="this" />';
echo '<input type="hidden" name="display_as" value="pages" />';

echo '<input type="text" name="words" class="formfield" size="20" id="words" value="'.stripslashes(htmlspecialchars($_GET['words'])).'" /><br />';
echo '<small>'._AT('search_match').': <input type="radio" name="include" value="all" id="all2"'.$include_all.' /><label for="all2">'._AT('search_all_words').'</label>, <input type="radio" name="include" value="one" id="one2"'.$include_one.' /><label for="one2">'._AT('search_any_word').'</label><br /></small>';

echo '<input type="submit" name="submit" value="  '._AT('search').'  " class="button" />';
echo '</form>';

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();


$savant->assign('title', _AT('search'));

$savant->display('include/box.tmpl.php');
?>