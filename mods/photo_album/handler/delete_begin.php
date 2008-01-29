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
 * @desc	This file generates delete confirm message before deleting the image/comment
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */ 
define('AT_INCLUDE_PATH', '../../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/photo_album/module.css'; // use a custom stylesheet
require_once (AT_INCLUDE_PATH.'header.inc.php');
?>

<?php
   	define('PATH', '../');
	require_once (PATH.'define.php');
	require_once (PATH.'include/general_func.php');
	require_once (PATH.'include/data_func.php');
	require_once (PATH.'HTML/Template/ITX.php');
	
	$template= new HTML_Template_ITX("../Template");
	if ($_POST['mode']=='delete'){
		/* delete image */
		if ($_POST['choose']==IMAGE){
			$auth=user_own(IMAGE, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id']);
			if (!$auth){
				$msg->addError('pa_user_image_not_allowed');
				redirect('../view.php?image_id='.$_SESSION['pa']['image_id']);
			} else {
				$_SESSION['pa']['mode']='delete';
				$_SESSION['pa']['choose']=IMAGE;
				$data_array=get_single_data(IMAGE,$_SESSION['pa']['image_id'], $_SESSION['pa']['course_id']);
						
				/* display the image delete confirm here. */
				$template->loadTemplatefile("delete_confirm.tpl.php");
				$template->setVariable("MESSAGE", _AT('pa_tag_image_delete_confirm'));
				$template->setVariable("CONFIRM_FORM", "delete_image");
				$template->setVariable("CONFIRM_ACTION", DELETE_ACTION);
				$template->setVariable("CONFIRM_DISPLAY", _AT('yes'));
				$template->setVariable("CANCEL_FORM", "cancel_image");
				$template->setVariable("CANCEL_ACTION", BASE_PATH.'view.php?image_id='.$_SESSION['pa']['image_id']);
				$template->setVariable("CANCEL_DISPLAY", _AT('no'));
				
				$template->setCurrentBlock("IMAGE_DISPLAY");
				$template->setVariable("ALT", $data_array['alt']);
				$image_src=$get_file.$data_array['location'].urlencode($data_array['thumb_image_name']);
				$template->setVariable("IMAGE_SRC", $image_src);
				$template->parseCurrentBlock("IMAGE_DISPLAY");
				
				$member_name=get_member_name($data_array['login']);
				$template->setCurrentBlock("TABLE");
				$template->setVariable("NAME_STRING", _AT('name'));
				$template->setVariable("NAME", $member_name);
				$template->setVariable("DESC", convert_newlines($data_array['description']));
				$template->setVariable("DATE_STRING", _AT('date'));
				$template->setVariable("DATE", $data_array['date']);
				$template->parseCurrentBlock("TABLE");
				$template->parseCurrentBlock();
				$template->show();
			}
			/* delete comment */
		} else if ($_POST['choose']==COMMENT){
			$auth=user_own(COMMENT, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id'], $_POST['comment_id']);
			If (!$auth){
				$msg->addError('pa_user_comment_not_allowed');
				redirect('view.php?image_id='.$_SESSION['pa']['image_id']);
			} else {
				$_SESSION['pa']['mode']='delete';
				$_SESSION['pa']['choose']=COMMENT;
				$_SESSION['pa']['comment_id']=$_POST['comment_id'];
				$data_array=get_single_data(COMMENT, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id'], $_SESSION['pa']['comment_id']);
				
				/* display comment delete confirm here */
				$template->loadTemplatefile("delete_confirm.tpl.php");
				$template->setVariable("MESSAGE", _AT('pa_tag_comment_delete_confirm'));
				$template->setVariable("CONFIRM_FORM", "blog_confirm");
				$template->setVariable("CONFIRM_ACTION", DELETE_ACTION);
				$template->setVariable("CONFIRM_DISPLAY", _AT('yes'));
				$template->setVariable("CANCEL_FORM", "blog_cancel");
				$template->setVariable("CANCEL_ACTION", BASE_PATH.'view.php?image_id='.$_SESSION['pa']['image_id']);
				$template->setVariable("CANCEL_DISPLAY", _AT('no'));
				
				$member_name=get_member_name($data_array['login']);
				$template->setCurrentBlock("TABLE");
				$template->setVariable("NAME_STRING", _AT('name'));
				$template->setVariable("NAME", $member_name);
				$template->setVariable("DESC", convert_newlines($data_array['comment']));	
				$template->setVariable("DATE_STRING", _AT('date'));
				$template->setVariable("DATE", $data_array['date']);
				$template->parseCurrentBlock("TABLE");
				$template->parseCurrentBlock();
				$template->show();
			}
		} else {
			$msg->addError('pa_var_unauthorized');
			redirect('index.php');
		}
	} else {
		$msg->addError('pa_var_unauthorized');
		redirect('index.php');
	}
?>

<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>