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
/* This file is used by both the photo album and the my start page->profile picture */
if (!defined('AT_INCLUDE_PATH')) { exit; }
include (AT_PA_INCLUDE.'lib.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');
//$_custom_css = $_base_path . AT_PA_BASENAME . 'module.css'; // use a custom stylesheet
$_custom_head .= '<script src="'.$_base_path . AT_PA_BASENAME . 'include/ajaxupload.js" type="text/javascript"></script>';
$member_id = intval($_GET['member_id']);
$member_id = ($member_id==0)? $_SESSION['member_id']: $member_id;

//run a check to see if any personal album exists, if not, create one.
$sql = 'SELECT * FROM '.TABLE_PREFIX.'pa_albums WHERE member_id='.$_SESSION['member_id'].' AND type_id='.AT_PA_TYPE_PERSONAL;
$result = mysql_query($sql, $db);
if ($result){
	$rows = mysql_num_rows($result);
	if ($rows==0){
		//create one.
		$pa = new PhotoAlbum();
		$result = $pa->createAlbum(_AT('pa_profile_album'), '', '', AT_PA_TYPE_PERSONAL, AT_PA_PRIVATE_ALBUM, $_SESSION['member_id']);
		$id = mysql_insert_id();
	} else {
		$row = mysql_fetch_assoc($result);	//album info.
		$id = $row['id'];
	}
}

// Delete profile picture
if($_POST['delete'] == '1'){
    require ('../profile_pictures/save_profile_picture.php');	//handle POST request
}

//instantiate obj
$pa = new PhotoAlbum($id);
$info = $pa->getAlbumInfo();

//paginator settings
$page = intval($_GET['p']);
$photos_count = sizeof($pa->getAlbumPhotos());
$last_page = ceil($photos_count/AT_PA_PHOTOS_PER_PAGE);

if (!$page || $page < 0) {
	$page = 1;
} elseif ($page > $last_page){
	$page = $last_page;
}

$count  = (($page-1) * AT_PA_PHOTOS_PER_PAGE) + 1;
$offset = ($page-1) * AT_PA_PHOTOS_PER_PAGE;

//get details
$photos = $pa->getAlbumPhotos($offset);
$comments = $pa->getComments($id, false);
//TODO: Can improve performance by adding this to a session variable
$memory_usage = memoryUsage($_SESSION['member_id']);	

include (AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('album_info', $info);
$savant->assign('photos', $photos);
$savant->assign('comments', $comments);
$savant->assign('page', $page);
$savant->assign('num_rows', $photos_count);
$savant->assign('memory_usage', $memory_usage/(1024*1024));	//mb
$savant->assign('allowable_memory_usage', $_config['pa_max_memory_per_member']);	//mb
$savant->assign('action_permission', $pa->checkAlbumPriv($_SESSION['member_id']));
$savant->display('photos/pa_profile_albums.tmpl.php');
include (AT_INCLUDE_PATH.'footer.inc.php'); 
?>