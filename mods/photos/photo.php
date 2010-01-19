<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institution  */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
include (AT_PA_INCLUDE.'lib.inc.php');
$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet
$_custom_head .= '<script type="text/javascript" src="'.AT_PA_BASENAME.'include/imageReorderer.js"></script>';

$aid = intval($_GET['aid']);
$pid = intval($_GET['pid']);

$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['title']    = _AT('albums');
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['parent']   = AT_PA_BASENAME.'index.php';
//$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['children'] = array(AT_PA_BASENAME.'photo.php');
$_pages[AT_PA_BASENAME.'photo.php']['parent']    = AT_PA_BASENAME.'albums.php?id='.$aid;

$pa = new PhotoAlbum($aid);

//get details
$photos = $pa->getAlbumPhotos();
$photo_info = $pa->getPhotoInfo($pid);
$comments = $pa->getComments($pid, true);
if($pa->checkPhotoPriv($pid, $_SESSION['member_id']) || $pa->checkAlbumPriv($_SESSION['member_id'])){
	$action_permission = true;
} else {
	$action_permission = false;
}

//run a quick query to get the next and previous id
if (sizeof($photos) > 1){
	$sql = 'SELECT id, ordering FROM '.TABLE_PREFIX.'pa_photos WHERE album_id='.$aid.' AND (ordering='.($photo_info['ordering']-1).' OR ordering='.($photo_info['ordering']+1).') ORDER BY ordering';
	$result = mysql_query($sql, $db);
	if ($result){
		$prev = mysql_fetch_assoc($result);
		$next = mysql_fetch_assoc($result);

		//then reassign prev and next
		if (empty($next)){
			if ($prev['ordering'] > $photo_info['ordering']){
				$next = $prev;
				unset($prev);
			} else {
				unset($next);
			}
		}
	}
}

include (AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('total_photos', sizeof($photos));
$savant->assign('prev', $prev);
$savant->assign('next', $next);
$savant->assign('aid', $aid);
$savant->assign('photo_info', $photo_info);
$savant->assign('comments', $comments);
$savant->assign('action_permission', $action_permission);
$savant->display('pa_photo.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>