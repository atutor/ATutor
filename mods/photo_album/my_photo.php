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
 * @desc	This file displays the my photo page
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */

define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/photo_album/module.css'; // use a custom stylesheet
require_once (AT_INCLUDE_PATH.'header.inc.php');
?>

<?php
require_once ('define.php');
require_once ('classes/pa_mypic.class.php');
require_once ('include/general_func.php');
require_once ('HTML/Template/ITX.php');

if ($_SESSION['enroll']!=true){
	$msg->addError('pa_var_unauthorized');
	redirect('index.php');
}
$my=new Mypic();
if ($my->isError()!=true){	//no error is occured
	$course_id=$_SESSION['pa']['course_id'];	//store course_id temporarily
	unset($_SESSION['pa']);						//clear pa session variables
	$_SESSION['pa']['course_id']=$course_id;
	$_SESSION['pa']['my_pic_mode']=true;
	
	$template=new HTML_Template_ITX("./Template");
	$template->loadTemplateFile("my_photo.tpl.php");
	
	switch ($my->getVariable('mode')){
		case POSTED_NEW:
			$template->setVariable("TITLE", _AT('pa_title_my_photo_new'));
		break;
		case APPROVED:
			$template->setVariable("TITLE", _AT('pa_title_my_photo_approved'));
		break;
		case DISAPPROVED:
			$template->setVariable("TITLE", _AT('pa_title_my_photo_disapproved'));
		break;
	}
	
	$template->setVariable("MAIN_URL", BASE_PATH.'index.php');
	$template->setVariable("MAIN_TITLE", _AT('pa_tag_course_photo_alt'));
	
	$template->setVariable("MY_PHOTO_URL", BASE_PATH.'my_photo.php');
	$template->setVariable("MY_PHOTO_TITLE", _AT('pa_tag_my_photo_alt'));
	
	$template->setVariable("MY_COMMENT_URL", BASE_PATH.'my_comment.php');
	$template->setVariable("MY_COMMENT_TITLE", _AT('pa_tag_my_comment_alt'));
	
	
	$template->setCurrentBlock("SELECT_PART");
	$template->setVariable("DESTINATION", BASE_PATH.'my_photo.php?mode='.POSTED_NEW.SEP.'current_page='.$my->getVariable('current_page'));
	$template->setVariable("LINK_TEXT", _AT('pa_tag_new_pic').' | ');
	$template->parseCurrentBlock("SELECT_PART");
	
	$template->setCurrentBlock("SELECT_PART");
	$template->setVariable("DESTINATION", BASE_PATH.'my_photo.php?mode='.APPROVED.SEP.'current_page='.$my->getVariable('current_page'));
	$template->setVariable("LINK_TEXT", _AT('pa_tag_approved_pic').' | ');
	$template->parseCurrentBlock("SELECT_PART");
	
	$template->setCurrentBlock("SELECT_PART");
	$template->setVariable("DESTINATION", BASE_PATH.'my_photo.php?mode='.DISAPPROVED.SEP.'current_page='.$my->getVariable('current_page'));
	$template->setVariable("LINK_TEXT", _AT('pa_tag_disapproved_pic'));
	$template->parseCurrentBlock("SELECT_PART");
	
	
		
	/* start display images */
	$image_array=$my->image_array;
	for ($i=0; $i<count($image_array); $i++){
		$template->setCurrentBlock("IMAGE_DATA");
		$template->setVariable("CHECK_NAME", "imageId".$i);
		$template->setVariable("CHECK_VALUE", $image_array[$i]['image_id']);
		$img_src=$get_file.$image_array[$i]['location'].urlencode($image_array[$i]['thumb_image_name']);
		$template->setVariable("IMAGE_DATA1", $image_array[$i]['title']);
		$template->setVariable("IMAGE_DATA2", "<img src=\"".$img_src."\" alt=\"".$image_array[$i]['alt']."\"/>");
		$template->setVariable("IMAGE_DATA3", _AT('date').": ".$image_array[$i]['date']);
	
		$template->setVariable("FORM_NAME", "edit_button");
		$template->setVariable("ACTION", UPLOAD_ACTION);
		$template->setVariable("CHOOSE_VALUE", IMAGE);
		$template->setVariable("IMAGE_ID", $image_array[$i]['image_id']);
		$template->setVariable("EDIT_VALUE", _AT('pa_button_edit_image'));
		$template->parseCurrentBlock("IMAGE_DATA");
	}
	
	// Display page table 
	$page_array=$my->getVariable('page_array');
	$current=$my->getVariable('current_page');
	$template->setCurrentBlock("PAGE_TABLE_PART");
	if ($my->getVariable('show_page_left_buttons')==true){
		$first_button=_AT('pa_tag_first_page_button');
		$previous_button=_AT('pa_tag_previous_page_button');
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'my_photo.php?current_page=1&amp;mode='.$my->getVariable('mode').'\'><img src=\''.FIRST_PAGE_IMAGE.'\' alt=\''.$first_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'my_photo.php?current_page='.($current-1).SEP.'mode='.$my->getVariable('mode').'\'><img src=\''.PRE_IMAGE.'\' alt=\''.$previous_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
	}
	
	for ($i=$page_array['start']; $i<=$page_array['end']; $i++){
		if ($i==$current){
			$template->setCurrentBlock("B_DATA_PART");
			$template->setVariable("B_DATA", '<li class=\'current\'>'.$i.'</li>');
			$template->parseCurrentBlock("B_DATA_PART");
		} else {
			$template->setCurrentBlock("B_DATA_PART");
			$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'my_photo.php?current_page='.$i.SEP.'mode='.$my->getVariable('mode').'\'>'.$i.'</a></li>');
			$template->parseCurrentBlock("B_DATA_PART");
		}
	}
		
	if ($my->getVariable('show_page_right_buttons')==true){
		$next_button=_AT('pa_tag_next_page_button');
		$last_button=_AT('pa_tag_last_page_button');
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'my_photo.php?current_page='.($current+1).SEP.'mode='.$my->getVariable('mode').'\'><img src=\''.NEXT_IMAGE.'\' alt=\''.$next_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
		$template->setCurrentBlock("B_DATA_PART");
		$template->setVariable("B_DATA", '<li><a href=\''.BASE_PATH.'my_photo.php?current_page='.$page_array['last_page'].SEP.'mode='.$my->getVariable('mode').'\'><img src=\''.LAST_PAGE_IMAGE.'\' alt=\''.$last_button.'\' width=\'30\' height=\'20\'/></a></li>');
		$template->parseCurrentBlock("B_DATA_PART");
	}
		
	$template->parseCurrentBlock("PAGE_TABLE_PART");
	$template->parseCurrentBlock();
	$template->show();
} else {
	$msg->addError('pa_obj_mypic');
	redirect('index.php');
}

?>

<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>