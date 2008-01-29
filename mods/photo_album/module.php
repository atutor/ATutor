<?php
/*==============================================================
  Photo Album
 ==============================================================
  Copyright (c) 2006 by Dylan Cheon & Kelvin Wong
  Institute for Assistive Technology / University of Victoria
  http://www.canassist.ca/                                    
                                                               
  This program is free software. You can redistribute it and/or
  modify it under the terms of the GNU General Public License  
  as published by the Free Software Foundation.                
 ==============================================================
 */
// $Id:

/**
 * @desc	This file generates all the photo album module configuration. Breadcrumb paths are determined here as well
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
 
if (!defined('AT_INCLUDE_PATH')) { exit; }
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

define('AT_PRIV_PHOTO_ALBUM',       $this->getPrivilege());
define('AT_ADMIN_PRIV_PHOTO_ALBUM', $this->getAdminPrivilege());

$_student_tool = 'mods/photo_album/index.php';
$photo_path='mods/photo_album/';
define('IMAGE', 3);
define('COMMENT', 4);

if (admin_authenticate(AT_ADMIN_PRIV_PHOTO_ALBUM, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {	//handle for administrator
	$this->_pages[AT_NAV_ADMIN] = array('mods/photo_album/index_admin.php');
	$this->_pages['mods/photo_album/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/photo_album/index_admin.php']['title_var'] = 'photo_album';
	
	$this->_pages['mods/photo_album/admin_image_list.php']['title_var']='photo_album';
	$this->_pages['mods/photo_album/admin_image_list.php']['children']=array('mods/photo_album/admin_comment_list.php', 'mods/photo_album/admin_config.php');
	
	$this->_pages['mods/photo_album/admin_comment_list.php']['title_var']='pa_title_administrator_comment';
	$this->_pages['mods/photo_album/admin_comment_list.php']['parent']='mods/photo_album/admin_image_list.php';
	
	$this->_pages['mods/photo_album/admin_config.php']['title_var']='pa_title_administrator_config';
	$this->_pages['mods/photo_album/admin_config.php']['parent']='mods/photo_album/admin_image_list.php';

	
	$link=$photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
	$this->_pages[$link]['title_var']='pa_title_view';
	$this->_pages[$link]['parent']=$photo_path.'admin_image_list.php';
		
	if (($_POST['mode']=='add') || ($_SESSION['pa']['mode']=='add')){	//mode is add
		if (($_POST['choose']==IMAGE) || ($_SESSION['pa']['choose']==IMAGE)){
			$this->_pages['mods/photo_album/handler/file_upload.php']['title_var'] = 'pa_title_add_image';
			$this->_pages['mods/photo_album/handler/file_upload.php']['parent'] = $photo_path.'admin_image_list.php';
			$this->_pages['mods/photo_album/handler/add_begin.php']['title_var'] = 'pa_title_add_image';
			$this->_pages['mods/photo_album/handler/add_begin.php']['parent']       = $photo_path.'admin_image_list.php';		
		} else {
			$this->_pages['mods/photo_album/handler/add_begin.php']['title_var'] = 'pa_title_add_comment';
			$this->_pages['mods/photo_album/handler/add_begin.php']['parent']       = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];		
		}
	} else if (($_POST['mode']=='edit') || ($_SESSION['pa']['mode']=='edit')){	//mode is edit
		if (($_POST['choose']==IMAGE) || ($_SESSION['pa']['choose']==IMAGE)){
			$this->_pages['mods/photo_album/handler/file_upload.php']['title_var'] = 'pa_title_edit_image';
			$this->_pages['mods/photo_album/handler/file_upload.php']['parent'] = $photo_path.'admin_image_list.php';
			$this->_pages['mods/photo_album/handler/edit_begin.php']['title_var'] = 'pa_title_edit_image';
			$this->_pages['mods/photo_album/handler/edit_begin.php']['parent']       = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];		
			$this->_pages['mods/photo_album/handler/delete_begin.php']['title_var'] = 'pa_title_delete_image';
			$this->_pages['mods/photo_album/handler/delete_begin.php']['parent']    = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		} else {
			$this->_pages['mods/photo_album/handler/edit_begin.php']['title_var'] = 'pa_title_edit_comment';
			$this->_pages['mods/photo_album/handler/edit_begin.php']['parent']       = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];		
			$this->_pages['mods/photo_album/handler/delete_begin.php']['title_var'] = 'pa_title_delete_comment';
			$this->_pages['mods/photo_album/handler/delete_begin.php']['parent']    = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		}
	} else {	//mode is delete
		if (($_POST['choose']==IMAGE) || ($_SESSION['pa']['choose']==IMAGE)){
			$this->_pages['mods/photo_album/handler/delete_begin.php']['title_var'] = 'pa_title_delete_image';
			$this->_pages['mods/photo_album/handler/delete_begin.php']['parent']    = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		} else {
			$this->_pages['mods/photo_album/handler/delete_begin.php']['title_var'] = 'pa_title_delete_comment';
			$this->_pages['mods/photo_album/handler/delete_begin.php']['parent']    = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		}
	}

	$this->_pages['mods/photo_album/view.php']['title_var'] = 'pa_title_view';
	$this->_pages['mods/photo_album/view.php']['parent']       = $photo_path.'admin_image_list.php';
	
} else {
	$this->_pages['mods/photo_album/instructor_image.php']['title_var'] = 'pa_title_instructor_image';
	$this->_pages['mods/photo_album/instructor_image.php']['parent']   = 'tools/index.php';
	$this->_pages['mods/photo_album/instructor_image.php']['children'] = array($photo_path.'instructor_comment.php', $photo_path.'instructor_config.php');

	$this->_pages['mods/photo_album/instructor_comment.php']['title_var'] = 'pa_title_instructor_comment';
	$this->_pages['mods/photo_album/instructor_comment.php']['parent']   = $photo_path.'instructor_image.php';

	$this->_pages['mods/photo_album/instructor_config.php']['title_var'] = 'pa_title_instructor_config';
	$this->_pages['mods/photo_album/instructor_config.php']['parent'] = $photo_path.'instructor_image.php';

	$this->_pages['mods/photo_album/index.php']['title_var'] = 'photo_album';
	$this->_pages['mods/photo_album/index.php']['img']       = $photo_path.'skins/pa_tool_icon.gif';

	$this->_pages['mods/photo_album/my_photo.php']['title_var'] = 'pa_title_my_photo';
	$this->_pages['mods/photo_album/my_photo.php']['parent'] = $photo_path.'index.php';

	$this->_pages['mods/photo_album/my_comment.php']['title_var'] = 'pa_title_my_comment';
	$this->_pages['mods/photo_album/my_comment.php']['parent'] = $photo_path.'index.php';
	
	if ($_SESSION['pa']['instructor_mode']==true){
		$this->_pages['mods/photo_album/view.php']['title_var'] = 'pa_title_view';
		$this->_pages['mods/photo_album/view.php']['parent']       = $photo_path.'instructor_image.php';
		$this->_pages['mods/photo_album/view.php']['children']       = Array();
	} else {
		$this->_pages['mods/photo_album/view.php']['title_var'] = 'pa_title_view';
		$this->_pages['mods/photo_album/view.php']['parent']       = $photo_path.'index.php';
		$this->_pages['mods/photo_album/view.php']['children']       = Array();
	}
		
	if ($_SESSION['pa']['instructor_mode']==true){
		$link=$photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		$this->_pages[$link]['title_var']='pa_title_view';
		$this->_pages[$link]['parent']=$photo_path.'instructor_image.php';
	} else {
		$link=$photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		$this->_pages[$link]['title_var']='pa_title_view';
		$this->_pages[$link]['parent']=$photo_path.'index.php';
	}
	
	if (($_POST['mode']=='add') || ($_SESSION['pa']['mode']=='add')){
		if (($_POST['choose']==IMAGE) || ($_SESSION['pa']['choose']==IMAGE)){
			if ($_SESSION['pa']['instructor_mode']==true){
				$this->_pages['mods/photo_album/handler/file_upload.php']['title_var'] = 'pa_title_add_image';
				$this->_pages['mods/photo_album/handler/file_upload.php']['parent'] = $photo_path.'instructor_image.php';
				$this->_pages['mods/photo_album/handler/add_begin.php']['title_var'] = 'pa_title_add_image';
				$this->_pages['mods/photo_album/handler/add_begin.php']['parent']       = $photo_path.'instructor_image.php';	
			} else {
				$this->_pages['mods/photo_album/handler/file_upload.php']['title_var'] = 'pa_title_add_image';
				$this->_pages['mods/photo_album/handler/file_upload.php']['parent'] = $photo_path.'index.php';
				$this->_pages['mods/photo_album/handler/add_begin.php']['title_var'] = 'pa_title_add_image';
				$this->_pages['mods/photo_album/handler/add_begin.php']['parent']       = $photo_path.'index.php';	
			}
		} else {
			$this->_pages['mods/photo_album/handler/add_begin.php']['title_var'] = 'pa_title_add_comment';
			$this->_pages['mods/photo_album/handler/add_begin.php']['parent']       = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];	
		}
	} else if (($_POST['mode']=='edit') || ($_SESSION['pa']['mode']=='edit')){
		if (($_POST['choose']==IMAGE) || ($_SESSION['pa']['choose']==IMAGE)){
			$this->_pages['mods/photo_album/handler/file_upload.php']['title_var'] = 'pa_title_edit_image';
			$this->_pages['mods/photo_album/handler/file_upload.php']['parent'] = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
			$this->_pages['mods/photo_album/handler/edit_begin.php']['title_var'] = 'pa_title_edit_image';
			$this->_pages['mods/photo_album/handler/edit_begin.php']['parent']       = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];	
			$this->_pages['mods/photo_album/handler/delete_begin.php']['title_var']='pa_title_delete_image';
			$this->_pages['mods/photo_album/handler/delete_begin.php']['parent']=$photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		} else {
			$this->_pages['mods/photo_album/handler/edit_begin.php']['title_var'] = 'pa_title_edit_comment';
			$this->_pages['mods/photo_album/handler/edit_begin.php']['parent']       = $photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];	
			$this->_pages['mods/photo_album/handler/delete_begin.php']['title_var']='pa_title_delete_comment';
			$this->_pages['mods/photo_album/handler/delete_begin.php']['parent']=$photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		}
	} else {  
		if (($_POST['choose']==IMAGE) || ($_SESSION['pa']['choose']==IMAGE)){
			$this->_pages['mods/photo_album/handler/delete_begin.php']['title_var']='pa_title_delete_image';
			$this->_pages['mods/photo_album/handler/delete_begin.php']['parent']=$photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		} else {
			$this->_pages['mods/photo_album/handler/delete_begin.php']['title_var']='pa_title_delete_comment';
			$this->_pages['mods/photo_album/handler/delete_begin.php']['parent']=$photo_path.'view.php?image_id='.$_SESSION['pa']['image_id'];
		}
	}
}

$this->_pages['mods/photo_album/handler/store.php']['title_var'] = 'Photo_Album';
$this->_pages['mods/photo_album/handler/delete.php']['title_var'] = 'Photo_Album';

?>