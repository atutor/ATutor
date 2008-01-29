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
 * @desc	This file displays the view page used to display an image and its thread of comments
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
require_once ('HTML/Template/ITX.php');
require_once ('classes/pa_view.class.php');

$view=new View();
$admin_mode=$_SESSION['pa']['administrator_mode'];		//save the admin mode if it is true;
$instructor_mode=$_SESSION['pa']['instructor_mode'];	//save the instructor mode if it is true;
unset($_SESSION['pa']);
$_SESSION['pa']['administrator_mode']=$admin_mode;
$_SESSION['pa']['instructor_mode']=$instructor_mode;

if ($view->isError()!=true){	//no error is occured with the view object, so display view page.
	$_SESSION['pa']['course_id']=$view->getVariable('course_id');
 	$_SESSION['pa']['image_id']=$view->getVariable('image_id');
  
 	$image_array=$view->getVariable('image_array');			
  	$template=new HTML_Template_ITX("./Template");
  	$template->loadTemplatefile("view.tpl.php", true, true);
  	
  	$template->setVariable("IMAGE_TITLE_STRING", _AT('pa_tag_image_title'));
	$template->setVariable("IMAGE_TITLE", $image_array['title']);
	$template->setCurrentBlock("IMAGE");
		
	/* display delete and edit buttons for the image */
	if (user_own(IMAGE, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id'])==true){
		$template->setCurrentBlock("IMAGE_MODIFY_BUTTONS");
		$template->setVariable("IMAGE_CHOOSE", IMAGE);
		$template->setVariable("EDIT_FORM", "edit_form");
		$template->setVariable("EDIT_ACTION", UPLOAD_ACTION);
		$template->setVariable("EDIT_DISPLAY", _AT('pa_button_edit_image'));
		$template->setVariable("DEL_FORM", "DEL_action");
		$template->setVariable("DEL_ACTION", DELETE_CONFIRM_ACTION);	
		$template->setVariable("DEL_DISPLAY", _AT('pa_button_del_image'));
		$template->setVariable("IMAGE_ID", $image_array['image_id']);
		$template->parseCurrentBlock("IMAGE_MODIFY_BUTTONS");
	}
	
	/* display the image */
		$template->setVariable("IMAGE_SRC", $get_file.$image_array['location'].urlencode($image_array['view_image_name']));
		$template->setVariable("ALT", $image_array['alt']);
		$template->parseCurrentBlock("IMAGE");
	
	
	/* display image information */
	$template->setCurrentBlock("TABLE");
	$template->setVariable("IMAGE_DISPLAY", _AT('pa_tag_image_description'));
	$image_owner_name=get_member_name($image_array['login']);
	$template->setVariable("IMAGE_NAME_STRING", _AT('name'));
	$template->setVariable("IMAGE_NAME", $image_owner_name);
	$template->setVariable("IMAGE_DESC", convert_newlines($image_array['description']));
	$template->setVariable("IMAGE_DATE_STRING", _AT('date'));
	$template->setVariable("IMAGE_DATE", $image_array['date']);
	$template->parseCurrentBlock("TABLE");
	
	if ($view->getVariable('show_modification_buttons')==true){
		$template->setCurrentBlock("ADD_COMMENT_BUTTON");
		$template->setVariable("ADD_FORM", "add_form");
		$template->setVariable("ADD_ACTION", ADD_ACTION);
		$template->setVariable("ADD_DISPLAY", _AT('pa_button_add_comment'));
		$template->setVariable("COMMENT_CHOOSE", COMMENT);
		$template->parseCurrentBlock("ADD_COMMENT_BUTTON");
	}
		
	/* display comments */
	$comment_array=$view->comment_array;	
	if (count($comment_array) >= 1){
		$template->setCurrentBlock("COMMENT_HEAD");
		$template->setVariable("COMMENT_DISPLAY", _AT('pa_tag_comment_description'));
	
		for ($i=0; $i<count($comment_array); $i++){
			$blog_owner_name=get_member_name($comment_array[$i]['login']);
			$user_own=user_own(COMMENT, $comment_array[$i]['image_id'], $comment_array[$i]['course_id'], $comment_array[$i]['comment_id']);
			if (($comment_array[$i]['status']!=APPROVED) && ($user_own!=true)){
				continue;
			}	
			if (($view->getVariable('show_modification_buttons')) && ($user_own==true)){
				$template->setCurrentBlock("COMMENT_START");
				$color='';
				if ((is_admin_for_course()==true) || ($user_own==true)){
					if ($comment_array[$i]['status']==DISAPPROVED){
						$color="disapproved";
						$template->setVariable("MESSAGE", _AT('pa_note_comment_disapproved'));
					} else if ($comment_array[$i]['status']==POSTED_NEW){
						$color="posted_new";
						$template->setVariable("MESSAGE", _AT('pa_note_comment_posted_new'));
					}
				}
				$COMMENT_DEL_FORM="blog_del_form";
				$COMMENT_DEL_ACTION=DELETE_CONFIRM_ACTION;
				$COMMENT_DEL=_AT('pa_button_del_comment');
					
				$COMMENT_EDIT_FORM="blog_edit_form";
				$COMMENT_EDIT_ACTION=EDIT_ACTION;
				$COMMENT_EDIT=_AT('pa_button_edit_comment');
				$COMMENT_ID=$comment_array[$i]['comment_id'];
				$COMMENT_CHOOSE=COMMENT;
				
				
				$template->setVariable("COLOR", $color);
				$template->setVariable("COMMENT_NAME", $blog_owner_name);
				$template->setVariable("COMMENT_VALUE", convert_newlines($comment_array[$i]['comment']));
				$template->setVariable("COMMENT_DATE", $comment_array[$i]['date']);
				
				$control_button="<div class=\"row buttons\">";
				$control_button.="<form name=\"blog_del_form\" method=\"post\" action=\"".$COMMENT_DEL_ACTION."\">";
				$control_button.="<input type=\"submit\" name=\"delete\" value=\"".$COMMENT_DEL."\"/>";
				$control_button.="<input type=\"hidden\" name=\"mode\" value=\"delete\"/>";
				$control_button.="<input type=\"hidden\" name=\"comment_id\" value=\"".$COMMENT_ID."\"/>";
				$control_button.="<input type=\"hidden\" name=\"choose\" value=\"".$COMMENT_CHOOSE."\"/>";
				$control_button.="</form>";
			
			
				$control_button.="<form name=\"blog_edit_form\" method=\"post\" action=\"".$COMMENT_EDIT_ACTION."\">";
				$control_button.="<input type=\"submit\" name=\"edit\" value=\"".$COMMENT_EDIT."\"/>";
				$control_button.="<input type=\"hidden\" name=\"mode\" value=\"edit\"/>";
				$control_button.="<input type=\"hidden\" name=\"comment_id\" value=\"".$COMMENT_ID."\"/>";
				$control_button.="<input type=\"hidden\" name=\"choose\" value=\"".$COMMENT_CHOOSE."\"/>";
				$control_button.="</form>";
				$control_button.="</div>";
				
					
				$template->setVariable("CONTROL_BUTTONS", $control_button);
				$template->parseCurrentBlock("COMMENT_START");
			} else {
				$template->setCurrentBlock("COMMENT_START");
				$template->setVariable("COMMENT_NAME", $blog_owner_name);
				$template->setVariable("COMMENT_VALUE", convert_newlines($comment_array[$i]['comment']));
				$template->setVariable("COMMENT_DATE", $comment_array[$i]['date']);
				$template->parseCurrentBlock("COMMENT_START");
			}
		}
		$template->parseCurrentBlock("COMMENT_HEAD");
	}
	
	$template->parseCurrentBlock();
	$template->show();
} else {
	$msg->addError('pa_obj_view');
	redirect('index.php');
} 
?>

<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>
