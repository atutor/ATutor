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
 * @desc	This file handles the delete image / comment operations
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
	/* checks whether the user typed 'Yes' button or not 
	 * If the user clicked 'Yes' to delete the data, delete it
	 */
	if (($_POST['confirm']=='Yes') && ($_SESSION['pa']['mode']=='delete')){
		if ($_SESSION['pa']['completed']==true){
			if ($_SESSION['pa']['choose']==IMAGE){
				if ($_SESSION['pa']['administrator_mode']==true){
					$msg->addFeedback('pa_delete_image_success');
					redirect('admin_image_list.php');
				} else {
					$msg->addFeedback('pa_delete_image_success');
					redirect('index.php');
				}
			} else {
				redirect('view.php?image_id='.$_SESSION['pa']['image_id']);
			}
		} else {
		
		if ($_SESSION['pa']['choose']==IMAGE){
			$delete=delete_image($_SESSION['pa']['image_id'], $_SESSION['pa']['course_id']);
			if ($delete==true){
				$_SESSION['pa']['completed']=true;
				if ($_SESSION['pa']['administrator_mode']==true){
					$msg->addFeedback('pa_delete_image_success');
					redirect('admin_image_list.php');
				} else {
					$msg->addFeedback('pa_delete_image_success');
					redirect('index.php');
				}
			} else {
				$msg->addError('pa_func_delete_image');
				redirect('index.php');
			}
		} else if ($_SESSION['pa']['choose']==COMMENT){
			$delete=delete_blog($_SESSION['pa']['image_id'], $_SESSION['pa']['course_id'], $_SESSION['pa']['comment_id']);
			if ($delete==true){
				$_SESSION['pa']['completed']=true;
				$msg->addFeedback('pa_delete_comment_success');
				redirect('view.php?image_id='.$_SESSION['pa']['image_id']);
			} else {
				$msg->addError('pa_func_delete_blog');
				redirect('index.php');
			}
		} else {
			$msg->addError('pa_var_unauthorized');
			redirect('index.php');
		}
	}
	} else {
		$msg->addError('pa_var_unauthorized');
		redirect('index.php');
	}
	

?>


<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>
