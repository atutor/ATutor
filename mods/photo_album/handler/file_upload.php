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
 * @desc	This file generates the image file upload page
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
	require_once ('../define.php');
	require_once ('../include/general_func.php');
	require_once ('../include/data_func.php');
	require_once ('../HTML/Template/ITX.php');
	
	$template= new HTML_Template_ITX("../Template");
	$template->loadTemplatefile("file_upload.tpl.php");
	$template->setVariable("JAVA_SRC", BASE_PATH.'handler/fat.js');
	
	/* mode add */	
	if ((($_POST['mode']=='add') && ($_POST['choose']==IMAGE)) || (($_SESSION['pa']['mode']=='add') && ($_SESSION['pa']['choose']==IMAGE))){	
		if (!isset($_POST['mode']) && ($_SESSION['pa']['mode']=='add')){
			$template->setVariable("FILE_FADE", "class=\"fade\"");
		}
		$template->setVariable("MESSAGE", _AT('pa_note_file_upload_add'));
		$template->setCurrentBlock("UPLOAD_PART");
		$template->setVariable("REQUIRED_SYMBOL", "<div class=\"required\">*</div>");
		$template->setVariable("FILE_LABEL", _AT('pa_label_file'));
		$template->setVariable("UPLOAD_FORM", "upload_form");
		$template->setVariable("UPLOAD_ACTION", ADD_ACTION);
		$template->setVariable("SUBMIT_MESSAGE", _AT('pa_button_upload_image'));
		
		if ($_SESSION['pa']['administrator_mode']==true){
			$action=ATUTOR_PREFIX.BASE_PATH.'admin_image_list.php';
		} else if ($_SESSION['pa']['instructor_mode']==true){
			$action=ATUTOR_PREFIX.BASE_PATH.'instructor_image.php';
		} else {
			$action=ATUTOR_PREFIX.BASE_PATH.'index.php';
		}	
		
		$template->setVariable("CANCEL_STRING", _AT('cancel'));
		
		$template->parseCurrentBlock("UPLOAD_PART");
		$template->parseCurrentBlock();
		$template->show();
		$_SESSION['pa']['mode']='add';
		$_SESSION['pa']['choose']=IMAGE;
		
	/* mode edit */
	} else if ((($_POST['mode']=='edit') && ($_POST['choose']==IMAGE)) || (($_SESSION['pa']['mode']=='edit') && ($_SESSION['pa']['choose']==IMAGE))){
		if (($_POST['mode']=='edit') && ($_POST['choose']==IMAGE)){
			$auth=user_own(IMAGE, $_POST['image_id'], $_SESSION['pa']['course_id']);
			if (!$auth){
				$msg->addError('pa_user_image_not_allowed');
				redirect('view.php?image_id='.$_POST['image_id']);
			} else {
				$_SESSION['pa']['image_id']=intval($_POST['image_id']);
			}
		}
		if (!isset($_POST['mode']) && ($_SESSION['pa']['mode']=='edit')){
			$template->setVariable("FILE_FADE", "class=\"fade\"");
		}	
		$template->setVariable("MESSAGE", _AT('pa_note_file_upload_edit'));
		$image_array=get_single_data(IMAGE, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id']);
		$template->setCurrentBlock("IMAGE_DISPLAY");
		$img_src=$get_file.$image_array['location'].urlencode($image_array['thumb_image_name']);
		$template->setVariable("IMAGE_SRC", $img_src);
		$template->setVariable("ALT", $image_array['alt']);
		$template->parseCurrentBlock("IMAGE_DISPLAY");
		
		$template->setCurrentBlock("UPLOAD_PART");
		$template->setVariable("FILE_LABEL", _AT('pa_label_file'));
		$template->setVariable("UPLOAD_FORM", "upload_form");
		$template->setVariable("UPLOAD_ACTION", EDIT_ACTION);
		$template->setVariable("SUBMIT_MESSAGE", _AT('pa_button_update_image'));
		
		//display image skip button
		$template->setCurrentBlock("SKIP_UPLOAD");
		$template->setVariable("SUBMIT_MESSAGE2", _AT('pa_button_skip_upload_image'));
		$template->parseCurrentBlock("SKIP_UPLOAD");
		
		$template->setVariable("CANCEL_STRING", _AT('cancel'));
		
		$template->parseCurrentBlock("UPLOAD_PART");
		$template->parseCurrentBlock();
		$template->show();	
		$_SESSION['pa']['mode']='edit';
		$_SESSION['pa']['choose']=IMAGE;
		
	} else {
		$msg->addError('pa_var_unauthorized');
		out();
	}
?>
	
<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>