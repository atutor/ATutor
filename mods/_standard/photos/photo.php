<?php
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
$_user_location = 'public';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
include (AT_PA_INCLUDE.'lib.inc.php');
//$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet
$_custom_head .= '<script type="text/javascript" src="'.AT_PA_BASENAME.'include/imageReorderer.js"></script>';

$aid = intval($_GET['aid']);
$pid = intval($_GET['pid']);

//init
$pa = new PhotoAlbum($aid);

//get details
$info = $pa->getAlbumInfo();
$photos = $pa->getAlbumPhotos();
$photo_info = $pa->getPhotoInfo($pid);
$comments = $pa->getComments($pid, true);

//Set pages/submenu
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['title']    = _AT('pa_albums') .' - '.$info['name'];
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['parent']   = AT_PA_BASENAME.'index.php';
$_pages[AT_PA_BASENAME.'albums.php?id='.$aid]['children']  = array(
														AT_PA_BASENAME.'photo.php',
													);
$_pages[AT_PA_BASENAME.'photo.php']['parent'] = AT_PA_BASENAME.'albums.php?id='.$aid;

//TODO: Validate users, using permission and course album control.
if ($info['member_id'] != $_SESSION['member_id'] && $info['type_id']!=AT_PA_TYPE_PERSONAL){
	$visible_albums = $pa->getAlbums($_SESSION['member_id'], $info['type_id']);
	if(!isset($visible_albums[$aid]) && $info['permission']==AT_PA_PRIVATE_ALBUM){
		//TODO msg;
		$msg->addError("ACCESS_DENIED");
		header('location: index.php');
		exit;
	}
}

if($pa->checkPhotoPriv($pid, $_SESSION['member_id']) || $pa->checkAlbumPriv($_SESSION['member_id'])){
	$action_permission = true;
} else {
	$action_permission = false;
}

//run a quick query to get the next and previous id
if (sizeof($photos) > 1){
	$sql = 'SELECT id, ordering FROM %spa_photos WHERE album_id=%d AND (ordering=%d OR ordering=%d) ORDER BY ordering';
	$rows_next_prev = queryDB($sql, array(TABLE_PREFIX, $aid, $photo_info['ordering']-1, $photo_info['ordering']+1));

	if(count($rows_next_prev) > 0){
		$prev = $rows_next_prev[0];
		$next = $rows_next_prev[1];

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

//update photo album.
if (isset($_GET['album_cover'])){
	$result = $pa->editAlbumCover($_GET['album_cover']);
	if ($result == 0) {
	    $msg->addError('PA_EDIT_PHOTO_FAILED');
        } else {
            $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
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
$savant->display('photos/pa_photo.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>