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
 * @desc	This file generates the edit image / comment page
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
require_once (PATH.'include/data_func.php');
require_once (PATH.'include/general_func.php');
require_once (PATH.'classes/image_upload.class.php');
require_once (PATH.'HTML/Template/ITX.php');

$template= new HTML_Template_ITX("../Template");
$template->loadTemplatefile("form.tpl.php");
$template->setVariable("JAVA_SRC", BASE_PATH.'handler/fat.js');

if (isset($_POST['cancel_image'])){
	if ($_SESSION['pa']['my_pic_mode']==true){
		$action='my_photo.php';
	} else {
		$action='view.php?image_id='.$_SESSION['pa']['image_id'];
	}
	redirect($action);
} else if ((isset($_POST['upload_image']) || isset($_POST['skip_upload']) || ($_SESSION['pa']['image_checked']==true)) && ($_SESSION['pa']['mode']=='edit')){
	$_SESSION['pa']['image_owner_checked']=true;
	$image_array=get_single_data(IMAGE, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id']);
	if (isset($_POST['upload_image'])){	
		$store_folder=make_temp_folder();
		$upload=new IMAGE_UPLOAD($_FILES['input_file'], $store_folder);
		if ($upload->isError()){	//check fatal error is occured or not
			$msg->addError('pa_obj_image_upload');
			out();
		} else if ($upload->getVariable('user_input_error')!=0){	//user error is occured.
			$error_array=$upload->getVariable('user_input_array');	
			$msg->addError('pa_user_'.$error_array[0]);
			redirect('handler/file_upload.php');
		} else {	//no error is found
			$_SESSION['pa']['thumb_image_name']=$upload->getVariable('thumb_image_name');
			$_SESSION['pa']['view_image_name']=$upload->getVariable('view_image_name');
			$_SESSION['pa']['image_copy_required']=true;
			$_SESSION['pa']['temp_folder']=$store_folder;
		}
		$_SESSION['pa']['title']=$image_array['title'];
		$_SESSION['pa']['description']=$image_array['description'];
		$_SESSION['pa']['alt']=$image_array['alt'];
		$_SESSION['pa']['image_checked']=true;
	} else if (isset($_POST['skip_upload']) || (isset($_SESSION['pa']['image_copy_required']) && ($_SESSION['pa']['image_copy_required']==false))){
		//image is not changed
		$_SESSION['pa']['image_owner_checked']=true;
		$_SESSION['pa']['image_copy_required']=false;
		$_SESSION['pa']['image_checked']=true;
		$_SESSION['pa']['thumb_image_name']=$image_array['thumb_image_name'];
		$_SESSION['pa']['view_image_name']=$image_array['view_image_name'];
		$_SESSION['pa']['title']=$image_array['title'];
		$_SESSION['pa']['description']=$image_array['description'];
		$_SESSION['pa']['alt']=$image_array['alt'];
		$store_folder=$image_array['location'];
	} else {
		$store_folder=$_SESSION['pa']['temp_folder'];
	}
	
	if ($_SESSION['pa']['error']['title']==true){
		$title_fade="class=\"fade\"";
		unset($_SESSION['pa']['error']['title']);
	}
	if ($_SESSION['pa']['error']['alt']==true){
		$alt_fade="class=\"fade\"";
		unset($_SESSION['pa']['error']['alt']);
	}
	
	/* Now, display the form */

	$template->setCurrentBlock("IMAGE_DISPLAY");
	$template->setVariable("IMAGE_SRC", $get_file.$store_folder.urlencode($_SESSION['pa']['thumb_image_name']));
	$template->setVariable("ALT", _AT('pa_tag_imgage_edit_alt'));
	
	$template->setCurrentBlock("INPUT_PART");
 	$template->setVariable("MESSAGE", _AT('pa_note_image_info_edit'));
 	$template->setVariable("TEXT_FORM", "text_form");
 	$template->setVariable("TEXT_ACTION", STORE_ACTION);
 	$template->setVariable("TITLE_MESSAGE", _AT('pa_label_pic_title'));
 	$template->setVariable("TITLE_FADE", $title_fade);
 	$template->setVariable("TITLE_VALUE", $_SESSION['pa']['title']);
 	$template->setVariable("DESC_MESSAGE", _AT('pa_label_pic_description'));
 	$template->setVariable("DESC_VALUE", $_SESSION['pa']['description']);
 	if (is_admin_for_course()){
	 	$template->setCurrentBlock("ALT_PART");
	 	$template->setVariable("ALT_MESSAGE", _AT('pa_label_pic_alt'));
	 	$template->setVariable("ALT_FADE", $alt_fade);
	 	$template->setVariable("ALT_VALUE", $_SESSION['pa']['alt']);
	 	$template->parseCurrentBlock("ALT_PART");
 	}	
 	$template->setVariable("SUBMIT_MESSAGE", _AT('pa_button_update_image_info'));
 	$template->setVariable("CANCEL_STRING", _AT('cancel'));
 	
 	$template->parseCurrentBlock("IMAGE_DISPLAY");
 	$template->parseCurrentBlock();
 	$template->show();
 	
 	/* edit comment */
} else if ((($_POST['mode']=='edit') && ($_POST['choose']==COMMENT)) || (($_SESSION['pa']['mode']=='edit') && ($_SESSION['pa']['choose']==COMMENT))){
	if (($_POST['mode']=='edit') && ($_POST['choose']==COMMENT)){
		$auth=user_own(COMMENT, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id'], $_POST['comment_id']);
		if (!$auth){
			$msg->addError('pa_user_comment_not_allowed');
			redirect('view.php?image_id='.$_SESSION['pa']['image_id']);
		}
		$_SESSION['pa']['comment_id']=$_POST['comment_id'];
		unset($_SESSION['pa']['error']['comment']);
	}
	$_SESSION['pa']['choose']=COMMENT;
	$_SESSION['pa']['mode']='edit';
	
	if ($_SESSION['pa']['error']['comment']==true){
		$fade="fade";
	} 
	$comment_array=get_single_data(COMMENT, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id'], $_SESSION['pa']['comment_id']);
	
	$template->setVariable("TITLE", _AT('pa_title_comment_edit'));
	$template->setCurrentBlock("COMMENT");
	$template->setVariable("COMMENT_MESSAGE", _AT('pa_note_comment_add'));
	$template->setVariable("COMMENT_LABEL", _AT('pa_label_comment_textarea'));
	$template->setVariable("COMMENT_FORM", "comment_form");
	$template->setVariable("COMMENT_ACTION", STORE_ACTION);
	$template->setVariable("COMMENT_FADE", $fade);
	$template->setVariable("COMMENT_VALUE", $comment_array['comment']);
	$template->setVariable("SUBMIT_VALUE", _AT('pa_button_update_comment'));
	
	$template->setVariable("CANCEL_STRING", _AT('cancel'));	
	
	$template->parseCurrentBlock("COMMENT");
	$template->parseCurrentBlock();
	$template->show();
} else {
	$msg->addError('pa_var_unauthorized');
	out();
}

?>

<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>