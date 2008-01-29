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
 * @desc	This file generates the image page for instructor panel
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/photo_album/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');

?>
<?php

require_once ('classes/pa_admin_image.class.php');
require_once ('HTML/Template/ITX.php');

$admin=new Pa_Admin_Image();

if ($admin->isError()!=true){	//no error is found, so display the page
	$template=new HTML_Template_ITX("./Template");
	$template->loadTemplateFile("instructor_image.tpl.php");
	$template->setVariable("ADMIN_NUMBER_OF_IMAGE", ADMIN_NUMBER_OF_IMAGE);
	
	switch ($admin->getVariable('mode')){
		case POSTED_NEW:
			$template->setVariable("TITLE", _AT('pa_title_instructor_new_pic'));
		break;
		case APPROVED:
			$template->setVariable("TITLE", _AT('pa_title_instructor_approved_pic'));
		break;
		case DISAPPROVED:
			$template->setVariable("TITLE", _AT('pa_title_instructor_disapproved_pic'));
		break;
	}
	
	$mode=get_config_mode($admin->getVariable('course_id'));
	$template->setVariable("CONFIG_STRING", _AT('pa_tag_config_string'));
	if ($mode==CONFIG_ENABLED){
		$template->setVariable("CONFIG_VALUE", _AT('pa_tag_config_enabled'));
	} else {
		$template->setVariable("CONFIG_VALUE", _AT('pa_tag_config_disabled'));
	}
	
	$template->setCurrentBlock("ADD_PART");
	$template->setVariable("ADD_FORM_NAME", "add_form");
	$template->setVariable("ADD_ACTION", UPLOAD_ACTION); 
	$template->setVariable("ADD_VALUE", _AT('pa_button_add_image'));
	$template->setVariable("ADD_HIDDEN_VALUE", IMAGE);
	$template->parseCurrentBlock("ADD_PART");
	
	$template->setCurrentBlock("SELECT_PART");
	$template->setVariable("SELECT_FORM_NAME", "select_form");
	$template->setVariable("SELECT_ACTION", $_SERVER['PHP_SELF']);
	$template->setVariable("SELECT_NAME", "mode");
	$template->setVariable("SELECT_LABEL", _AT('pa_tag_view'));
	
	$template->setCurrentBlock("OPTION_PART");
	$template->setVariable("OPTION_VALUE", POSTED_NEW);
	$template->setVariable("OPTION_STRING", _AT('pa_tag_new_pic'));
	$template->parseCurrentBlock("OPTION_PART");
	
	$template->setCurrentBlock("OPTION_PART");
	$template->setVariable("OPTION_VALUE", APPROVED);
	$template->setVariable("OPTION_STRING", _AT('pa_tag_approved_pic'));
	$template->parseCurrentBlock("OPTION_PART");
	
	$template->setCurrentBlock("OPTION_PART");
	$template->setVariable("OPTION_VALUE", DISAPPROVED);
	$template->setVariable("OPTION_STRING", _AT('pa_tag_disapproved_pic'));
	$template->parseCurrentBlock("OPTION_PART");
	
	$template->setVariable("SELECT_SUBMIT", "select_submit");
	$template->setVariable("SELECT_SUBMIT_VALUE", _AT('pa_tag_go'));
	$template->parseCurrentBlock("SELECT_PART");
	
	$template->setCurrentBlock("IMAGE_TABLE_PART");
	$template->setVariable("IMAGE_TABLE_FORM_NAME", "table_form");
	$action_string=$_SERVER['PHP_SELF']."?mode=".$admin->getVariable('mode')."&amp;current_page=".$admin->getVariable('current_page');
	$template->setVariable("IMAGE_TABLE_ACTION", $action_string);
		
	/* start display images */
	$image_array=$admin->image_array;
	
	//	print_r($image_array);
	//exit;
	for ($i=0; $i<count($image_array); $i++){
		$template->setCurrentBlock("IMAGE_TABLE_DATA");
		$template->setVariable("CHECK_NAME", "imageId".$i);
		$template->setVariable("CHECK_VALUE", $image_array[$i]['image_id']);
		$img_src=$get_file.$image_array[$i]['location'].urlencode($image_array[$i]['thumb_image_name']);
		$template->setVariable("IMAGE_TABLE_DATA1", "<img src=\"".$img_src."\" alt=\"".$image_array[$i]['alt']."\"/>");
		$template->setVariable("IMAGE_TABLE_DATA2", _AT('title').": ".$image_array[$i]['title']);
		$template->setVariable("IMAGE_TABLE_DATA3", _AT('login').": ".$image_array[$i]['login']);		
		$template->setVariable("IMAGE_TABLE_DATA4", _AT('pa_tag_alt').": ".$image_array[$i]['alt']);
		$template->setVariable("IMAGE_TABLE_DATA5", _AT('date').": ".$image_array[$i]['date']);
		$template->setVariable("IMAGE_TABLE_DATA6", "<a href=\"".BASE_PATH."view.php?image_id=".$image_array[$i]['image_id']."\">"._AT('pa_tag_view_image_link')."</a>");
		$template->parseCurrentBlock("IMAGE_TABLE_DATA");
	}
	$template->setCurrentBlock("IMAGE_BUTTON");
	$template->setVariable("IMAGE_BUTTON_NAME", "button_disapprove");
	$template->setVariable("IMAGE_BUTTON_VALUE", _AT('pa_button_set_disapproved_pic'));
	$template->parseCurrentBlock("IMAGE_BUTTON");
	
	$template->setCurrentBlock("IMAGE_BUTTON");
	$template->setVariable("IMAGE_BUTTON_NAME", "button_approve");
	$template->setVariable("IMAGE_BUTTON_VALUE", _AT('pa_button_set_approved_pic'));
	$template->parseCurrentBlock("IMAGE_BUTTON");
	
	$template->setCurrentBlock("IMAGE_BUTTON");
	$template->setVariable("IMAGE_BUTTON_NAME", "button_post_new");
	$template->setVariable("IMAGE_BUTTON_VALUE", _AT('pa_button_set_new_pic'));
	$template->parseCurrentBlock("IMAGE_BUTTON");
	
	$template->setVariable("CHECK_ALL_MSG", _AT('pa_tag_check_all_image'));
	
	$template->parseCurrentBlock("IMAGE_TABLE_PART");
	
	// Display page table 
	$page_array=$admin->getVariable('page_array');
	$current=$admin->getVariable('current_page');
	if ($admin->getVariable('show_page_left_buttons')==true){
		$first_button=_AT('pa_tag_first_page_button');
		$previous_button=_AT('pa_tag_previous_page_button');
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'instructor_image.php?current_page=1&amp;mode='.$admin->getVariable('mode').'\'><img src=\''.FIRST_PAGE_IMAGE.'\' alt=\''.$first_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'instructor_image.php?current_page='.($current-1).SEP.'mode='.$admin->getVariable('mode').'\'><img src=\''.PRE_IMAGE.'\' alt=\''.$previous_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
	}
	
	for ($i=$page_array['start']; $i<=$page_array['end']; $i++){
		if ($i==$current){
			$template->setCurrentBlock("B_DATA_PART");
			$template->setVariable("B_DATA", '<li class=\'current\'>'.$i.'</li>');
			$template->parseCurrentBlock("B_DATA_PART");
		} else {
			$template->setCurrentBlock("B_DATA_PART");
			$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'instructor_image.php?current_page='.$i.SEP.'mode='.$admin->getVariable('mode').'\'>'.$i.'</a></li>');
			$template->parseCurrentBlock("B_DATA_PART");
		}
	}
		
	if ($admin->getVariable('show_page_right_buttons')==true){
		$next_button=_AT('pa_tag_next_page_button');
		$last_button=_AT('pa_tag_last_page_button');
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'instructor_image.php?current_page='.($current+1).SEP.'mode='.$admin->getVariable('mode').'\'><img src=\''.NEXT_IMAGE.'\' alt=\''.$next_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'instructor_image.php?current_page='.$page_array['last_page'].SEP.'mode='.$admin->getVariable('mode').'\'><img src=\''.LAST_PAGE_IMAGE.'\' alt=\''.$last_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
	}
		
	$template->parseCurrentBlock();
	$template->show();
	unset($_SESSION['pa']);
	$_SESSION['pa']['course_id']=$admin->getVariable('course_id');
	$_SESSION['pa']['instructor_mode']=true;
} else {
	$msg->addError('pa_obj_pa_admin');
	redirect('index.php');
}

?>
<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>