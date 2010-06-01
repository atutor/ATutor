<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'lib.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet

//instantiate obj
$pa = new PhotoAlbum();
$album_count = sizeof($pa->getSharedAlbums(true));

//paginator settings
$page = intval($_GET['p']);
$last_page = ceil($album_count/AT_PA_ALBUMS_PER_PAGE);

if (!$page || $page < 0) {
	$page = 1;
} elseif ($page > $last_page){
	$page = $last_page;
}

$count  = (($page-1) * AT_PA_ALBUMS_PER_PAGE) + 1;
$offset = ($page-1) * AT_PA_ALBUMS_PER_PAGE;

$albums = $pa->getSharedAlbums(true, $offset);

include (AT_INCLUDE_PATH.'header.inc.php'); 
$savant->assign('albums', $albums);
$savant->assign('isSharedAlbum', true);
$savant->assign('page', $page);
$savant->assign('num_rows', $album_count);
$savant->display('photos/pa_index.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>
