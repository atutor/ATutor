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
include (AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');	//clr_dir()
//include (AT_INCLUDE_PATH.'lib/filemanager.inc.php');	//clr_dir()
include (AT_PA_INCLUDE.'lib.inc.php');
include (AT_PA_INCLUDE.'classes/PhotoAlbum.class.php');

admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM);

//init
$aid = intval($_REQUEST['aid']);
$pa = new PhotoAlbum($aid);

//handle edit/delete
if (isset($_POST['edit'])){
	//open up the edit page
	header('Location: edit_album.php?id='.$aid);
} elseif (isset($_POST['delete'])){
	//handle confirmation 
	if ($_POST['submit_no']) {
		$msg->addFeedback('CANCELLED');
		Header('Location: index_admin.php');
		exit;
	}
	if ($_POST['submit_yes']) {
		//delete
		$pa->deleteAlbum();
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		Header('Location: index_admin.php');
		exit;
	}
	//printing out the confirmation box
	$hidden_vars['aid'] = $aid;
	$hidden_vars['delete'] = 'delete';
	$msg->addConfirm(array('PA_DELETE_ALBUM', htmlentities_utf82($info['name'])), $hidden_vars);	
}

//paginator settings
$page = intval($_GET['p']);
$albums_count = sizeof($pa->getAllAlbums());
$last_page = ceil($albums_count/AT_PA_ADMIN_ALBUMS_PER_PAGE);

if (!$page || $page < 0) {
	$page = 1;
} elseif ($page > $last_page){
	$page = $last_page;
}
$count  = (($page-1) * AT_PA_ADMIN_ALBUMS_PER_PAGE) + 1;
$offset = ($page-1) * AT_PA_ADMIN_ALBUMS_PER_PAGE;

//get details
$albums = $pa->getAllAlbums($offset);

require (AT_INCLUDE_PATH.'header.inc.php');
$msg->printConfirm();
$savant->assign('albums', $albums);
$savant->assign('page', $page);
$savant->assign('num_rows', $photos_count);
$savant->display('admin/pa_index.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>
