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
 * @desc	This file defines all constants
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */

define('ATUTOR_PREFIX', $_base_path);		
define('BASE_PATH', 'mods/photo_album/');

define('ALBUM_IMAGE_STORE', 'photo_album/');		//album images folder
define('TEMP_FOLDER_NAME', 'temp/');

define('INDEX_PAGE', 1);
define('VIEW_PAGE', 2);

define('IMAGE', 3);
define('COMMENT', 4);
define('CONFIG', 5);

define('ADMIN_SHOW_APPROVED', 5);
define('ADMIN_SHOW_DISAPPROVED', 6);
define('ADMIN_SHOW_NEW', 7);

define('POSTED_NEW', 1);	//image status is new
define('APPROVED', 2);	//image status is approved
define('DISAPPROVED', 3);	//image status is disapproved

define('CONFIG_ENABLED', 1);
define('CONFIG_DISABLED', 2);

define('THUMB_NUMBER_OF_IMAGE',10);	//number of images to display in the album image thumbnail
define('THUMB_NUMBER_OF_IMAGE_PAGE', 10);		//number of page numbers to display in the album image thumbnail

define('MYPIC_NUMBER_OF_IMAGE', 10);
define('MYPIC_NUMBER_OF_IMAGE_PAGE', 10);

define('MYCOMMENT_NUMBER_OF_COMMENT', 10);
define('MYCOMMENT_NUMBER_OF_COMMENT_PAGE', 10);

define('ADMIN_NUMBER_OF_IMAGE', 10);	//number of images to display in the admin/instructor thumbnail
define('ADMIN_NUMBER_OF_IMAGE_PAGE', 10);		//number of page numbers to display in the admin/instructor thumbnail 

define('ADMIN_NUMBER_OF_COMMENT', 10);
define('ADMIN_NUMBER_OF_COMMENT_PAGE', 10);

define('FIRST_PAGE', 1); //first page
define('NOT_SET', -1); 
define('NORMAL_USER', 0);

/* state values */
define('ADMIN_PANEL', 1);	//admin or instructor is looking at the panel page
define('ADMIN_VIEW', 2);	//admin or instructor is looking at the view page
define('MY_PIC', 3);	//the picture owner state
define('MY_COMMENT', 4); //the comment owner state
define('STUDENT', 5);	//normal student user is looking at the view page

/* page buttons */
define('SKINS_PATH', BASE_PATH.'skins/');
define('FIRST_PAGE_IMAGE', SKINS_PATH.'pa_first.gif');		
define('NEXT_IMAGE', SKINS_PATH.'pa_next.gif');
define('PRE_IMAGE', SKINS_PATH.'pa_prev.gif');
define('LAST_PAGE_IMAGE', SKINS_PATH.'pa_last.gif');

define('THUMB_IMAGE_HEIGHT', 130);
define('THUMB_IMAGE_WIDTH', 130);
define('MAX_IMAGE_WIDTH', 550);	//the max image width for view image
//define('MAX_IMAGE_HEIGHT', 550);	//uncheck this line if you want to set maximum height for view page
define('INVALID_ESCAPE', '[!@#^&*+=~`\%\$]');	//invalid escape for image file name
define('THUMB_EXT', 'thumb');	//the thumb extention string which will be concatenated to the original image file name
define('MAX_FILENAME_LENGTH', 200);	//the maximum image filename length

define('GO_BACK_TO_THUMBNAIL_ACTION', BASE_PATH.'index.php');
define('UPLOAD_ACTION', BASE_PATH.'handler/file_upload.php');
define('DELETE_CONFIRM_ACTION', BASE_PATH.'handler/delete_begin.php');
define('DELETE_ACTION', BASE_PATH.'handler/delete.php');
define('EDIT_ACTION', BASE_PATH.'handler/edit_begin.php');
define('ADD_ACTION', BASE_PATH.'handler/add_begin.php');
define('STORE_ACTION', BASE_PATH.'handler/store.php');

$IMAGE_TYPE=array('image/jpeg', 'image/pjpeg', 'image/png', 'image/bmp', 'image/gif');

if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE){
	$get_file=BASE_PATH.'get_pa.php/';
} else {
	$get_file='content/';
}

?>
