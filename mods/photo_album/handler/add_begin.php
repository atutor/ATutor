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
 * @desc	This file generates the add image / comment page
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
		if ($_SESSION['pa']['administrator_mode']==true){
			$action='admin_image_list.php';
		} else if ($_SESSION['pa']['instructor_mode']==true){
			$action='instructor_image.php';
		} else {
			$action='index.php';
		}
		redirect($action);
	} else if ((isset($_POST['upload_image']) || ($_SESSION['pa']['image_uploaded']==true)) && ($_SESSION['pa']['mode']=='add')){ /* mode add */
		if (isset($_POST['upload_image'])){	//initial add
			$temp_folder=make_temp_folder();
			$upload=new IMAGE_UPLOAD($_FILES['input_file'], $temp_folder);
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
				$_SESSION['pa']['image_uploaded']=true;
				$_SESSION['pa']['temp_folder']=$temp_folder;
			}
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
		$template->setVariable("IMAGE_SRC", $get_file.$_SESSION['pa']['temp_folder'].urlencode($_SESSION['pa']['thumb_image_name']));
		$template->setVariable("ALT", _AT('pa_tag_image_add_alt'));
		
		$template->setCurrentBlock("INPUT_PART");
	 	$template->setVariable("MESSAGE", _AT('pa_note_image_info_add'));
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
	 	$template->setVariable("SUBMIT_MESSAGE", _AT('pa_button_upload_image_info'));
	 	
		$template->setVariable("CANCEL_STRING", _AT('cancel'));
	 	
	 	$template->parseCurrentBlock("IMAGE_DISPLAY");
	 	$template->parseCurrentBlock();
	 	$template->show();
	 	
	 	/* mode add for comment */
	} else if ((($_POST['mode']=='add') && ($_POST['choose']==COMMENT)) || (($_SESSION['pa']['mode']=='add') && ($_SESSION['pa']['choose']==COMMENT))){
		$template->setVariable("TITLE", _AT('pa_title_comment_add'));
		$_SESSION['pa']['choose']=COMMENT;
		$_SESSION['pa']['mode']='add';
		if ($_SESSION['pa']['error']['comment']==true){
			$fade="fade";
		}
		$template->setCurrentBlock("COMMENT");
		$template->setVariable("COMMENT_MESSAGE", _AT('pa_note_comment_add'));
		$template->setVariable("COMMENT_LABEL", _AT('pa_label_comment_textarea'));
		$template->setVariable("COMMENT_FORM", "comment_form");
		$template->setVariable("COMMENT_ACTION", STORE_ACTION);
		$template->setVariable("COMMENT_FADE", $fade);
		$template->setVariable("SUBMIT_VALUE", _AT('pa_button_upload_comment'));
		
	 	$template->setVariable("CANCEL_STRING", _AT('cancel'));
		
		$template->parseCurrentBlock("COMMENT");
		$template->parseCurrentBlock();
		$template->show();
	} else {	//invalid way of entrance
		$msg->addError('pa_var_unauthorized');
		out();
	}

?>

<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>