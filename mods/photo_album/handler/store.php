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
 * @desc	This file stores or updates the image / comment data in the database
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
 
define('AT_INCLUDE_PATH', '../../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/photo_album/module.css'; // use a custom stylesheet
require_once (AT_INCLUDE_PATH.'header.inc.php');


   	require_once ('../define.php');
	require_once ('../include/general_func.php');
	require_once ('../include/data_func.php');
	$config_mode=get_config_mode($_SESSION['pa']['course_id']);
	
	if (isset($_POST['cancel_image'])){
		if ($_SESSION['pa']['mode']=='add'){
			if ($_SESSION['pa']['administrator_mode']==true){
				$action='admin_image_list.php';
			} else if ($_SESSION['pa']['instructor_mode']==true){
				$action='instructor_image.php';
			} else {
				$action='index.php';
			}
		} else if ($_SESSION['pa']['mode']=='edit'){
			if ($_SESSION['pa']['my_pic_mode']==true){
				$action='my_photo.php';
			} else {
				$action='view.php?image_id='.$_SESSION['pa']['image_id'];
			}
		} else {
			$msg->addError('pa_var_unauthorized');
			out();
		}	
		redirect($action);
	} else if ($_POST['cancel_comment']){
		$action='view.php?image_id='.$_SESSION['pa']['image_id'];
		redirect($action);
	} else if ((isset($_POST['submit'])) && ($_SESSION['pa']['mode']=='add')){	//mode add
		if ($_SESSION['pa']['completed']==true){
			out();
		} else {
		$goback_url='handler/add_begin.php';
		if (($_SESSION['pa']['choose']==IMAGE) && ($_SESSION['pa']['image_uploaded']==true)){
			$store_path=ALBUM_IMAGE_STORE.$_SESSION['login'].'/';
			$_SESSION['pa']['title']=$_POST['title'];
			$_SESSION['pa']['alt']=$_POST['alt'];
			$_SESSION['pa']['description']=$_POST['description'];
			unset($_SESSION['pa']['error']);
			if ((empty($_POST['title']) || (strlen($_POST['title'])==0))){
				$_SESSION['pa']['error']['title']=true;
				$msg->addError('pa_user_title_empty');
				redirect($goback_url);
			} else if (is_admin_for_course() && (empty($_POST['alt']) || (strlen($_POST['alt'])==0))){
				$_SESSION['pa']['error']['alt']=true;
				$msg->addError('pa_user_alt_empty');
				redirect($goback_url);
			} else {	//input has no error				
				$view_image_name=modify_image_name($store_path, $_SESSION['pa']['view_image_name']);
				$thumb_image_name=modify_image_name($store_path, $_SESSION['pa']['thumb_image_name']);		
				$store_dir=AT_CONTENT_DIR.$store_path;
				if (!is_dir($store_dir)){
					if (!@mkdir($store_dir)){
						$msg->addError('pa_func_mkdir');
						out();
					}
					chmod ($store_dir, 0757);
				}
					
				if (!copy(AT_CONTENT_DIR.$_SESSION['pa']['temp_folder'].$_SESSION['pa']['view_image_name'], AT_CONTENT_DIR.$store_path.$view_image_name)){
					$msg->addError('pa_func_copy');
					out();
				}
				if (!copy(AT_CONTENT_DIR.$_SESSION['pa']['temp_folder'].$_SESSION['pa']['thumb_image_name'], AT_CONTENT_DIR.$store_path.$thumb_image_name)){
					$msg->addError('pa_func_copy');
					out();
				}
				
				if (is_admin_for_course()==true){
					$store=store_image_in_database($_SESSION['pa']['course_id'], $_SESSION['login'], htmlspecialchars($_SESSION['pa']['title']), htmlspecialchars($_SESSION['pa']['description']), $view_image_name, $store_path, $thumb_image_name, htmlspecialchars($_SESSION['pa']['alt']), APPROVED);
				} else if ($config_mode==CONFIG_ENABLED){	//student image add when config is enabled
					$store=store_image_in_database($_SESSION['pa']['course_id'], $_SESSION['login'], htmlspecialchars($_SESSION['pa']['title']), htmlspecialchars($_SESSION['pa']['description']), $view_image_name, $store_path, $thumb_image_name, htmlspecialchars($_SESSION['pa']['title']), POSTED_NEW);
				} else {	//student image add when config is disabled
					$store=store_image_in_database($_SESSION['pa']['course_id'], $_SESSION['login'], htmlspecialchars($_SESSION['pa']['title']), htmlspecialchars($_SESSION['pa']['description']), $view_image_name, $store_path, $thumb_image_name, htmlspecialchars($_SESSION['pa']['title']), APPROVED);
				}

				if ($store!=true){
					$msg->addError('pa_func_store_image_in_database');
					out();
				} else {
					$_SESSION['pa']['completed']=true;
					if ((is_admin_for_course()==true) || ($config_mode==CONFIG_DISABLED)){
						$msg->addFeedback('pa_add_image_success_config_disabled');
					} else if ($config_mode==CONFIG_ENABLED){
						$msg->addFeedback('pa_add_image_success_config_enabled');
					}
					out();
				}
			}
		} else if ($_SESSION['pa']['choose']==COMMENT){	//store comment
			$comment=trim($_POST['comment']);
			if (empty($comment)){
				$_SESSION['pa']['error']['comment']=true;
				$msg->addError('pa_user_comment_empty');
				redirect($goback_url);
			}
			if ((is_admin_for_course()==true) || ($config_mode==CONFIG_DISABLED)){
				$store=store_comment_in_database($_SESSION['pa']['course_id'], $_SESSION['login'], htmlspecialchars($_POST['comment']), $_SESSION['pa']['image_id'], APPROVED);
			} else {
				$store=store_comment_in_database($_SESSION['pa']['course_id'], $_SESSION['login'], htmlspecialchars($_POST['comment']), $_SESSION['pa']['image_id'], POSTED_NEW);
			} 	
				
			if ($store==true){
				$_SESSION['pa']['completed']=true;
				if ((is_admin_for_course()==true) || ($config_mode==CONFIG_DISABLED)){
					$msg->addFeedback('pa_add_comment_success_config_disable');
				} else if ($config_mode==CONFIG_ENABLED){
					$msg->addFeedback('pa_add_comment_success_config_enabled');
				}
				redirect('view.php?image_id='.$_SESSION['pa']['image_id']);
			} else {
				$msg->addError('pa_func_store_comment_in_database');
				out();
			}
		} else {
			$msg->addError('pa_var_unauthorized');
			out();
		}
	}
		
	/* mode is edit */
	} else if (($_SESSION['pa']['mode']=='edit')&& (isset($_POST['submit']))){
		if ($_SESSION['pa']['completed']==true){
			out();
		} else {
		$goback_url='handler/edit_begin.php';	
		if (($_SESSION['pa']['choose']==IMAGE) && ($_SESSION['pa']['image_checked']==true)){
			$_SESSION['pa']['description']=$_POST['description'];
			$_SESSION['pa']['title']=$_POST['title'];
			$_SESSION['pa']['alt']=$_POST['alt'];
			if ((empty($_SESSION['pa']['title'])) || ((strlen($_SESSION['pa']['title']))==0)){
				$_SESSION['pa']['error']['title']=true;
				$msg->addError('pa_user_title_empty');
				redirect($goback_url);
			} else if (is_admin_for_course() && (empty($_SESSION['pa']['alt']) || (strlen($_SESSION['pa']['alt'])==0))){
				$msg->addError('pa_user_alt_empty');
				$_SESSION['pa']['error']['alt']=true;
				redirect($goback_url);
			}
			if ($_SESSION['pa']['image_copy_required']==true){
				$data_array=get_single_data(IMAGE, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id']);
				$store_dir=AT_CONTENT_DIR.ALBUM_IMAGE_STORE.$data_array['login'].'/';
				$old_view_image=AT_CONTENT_DIR.$data_array['location'].$data_array['view_image_name'];
				$old_thumb_image=AT_CONTENT_DIR.$data_array['location'].$data_array['thumb_image_name'];
				$image_view_name=modify_image_name($data_array['location'], $_SESSION['pa']['view_image_name']);
				$image_thumb_name=modify_image_name($data_array['location'], $_SESSION['pa']['thumb_image_name']);
				if (!copy(AT_CONTENT_DIR.$_SESSION['pa']['temp_folder'].$_SESSION['pa']['view_image_name'], $store_dir.$image_view_name)){
					$msg->addError('pa_func_copy');
					out();
				} 
				if (!copy(AT_CONTENT_DIR.$_SESSION['pa']['temp_folder'].$_SESSION['pa']['thumb_image_name'], $store_dir.$image_thumb_name)){
					$msg->addError('pa_func_copy');
					out();
				}
				if (!@unlink($old_view_image)){
					$msg->addError('pa_func_unlink');
				}
				if (!@unlink($old_thumb_image)){
					$msg->addError('pa_func_unlink');
				}
					
				if (is_admin_for_course() || $config_mode==CONFIG_DISABLED){
					$update=update_image_in_database($_SESSION['pa']['course_id'], htmlspecialchars($_SESSION['pa']['title']), htmlspecialchars($_SESSION['pa']['description']), $image_view_name, $_SESSION['pa']['image_id'], $image_thumb_name, htmlspecialchars($_SESSION['pa']['alt']), APPROVED);
				} else {
					$update=update_image_in_database($_SESSION['pa']['course_id'], htmlspecialchars($_SESSION['pa']['title']), htmlspecialchars($_SESSION['pa']['description']), $image_view_name, $_SESSION['pa']['image_id'], $image_thumb_name,  htmlspecialchars($_SESSION['pa']['title']), POSTED_NEW);
				}
					
				if ($update==false){
					$msg->addError('pa_func_update_image_in_database');
					out();
				} else {
					$_SESSION['pa']['completed']=true;
					if ((is_admin_for_course()==true) || ($config_mode==CONFIG_DISABLED)){
						$msg->addFeedback('pa_edit_image_success_config_disabled');
					} else if ($config_mode==CONFIG_ENABLED){
						$msg->addFeedback('pa_edit_image_success_config_enabled');
					}
					out();
				}
			} else {	//image copy is not required, so just update the database with title and description
				$data_array=get_single_data(IMAGE, $_SESSION['pa']['image_id'], $_SESSION['pa']['course_id']);
				if (is_admin_for_course() || $config_mode==CONFIG_DISABLED){
					$update=update_image_in_database($_SESSION['pa']['course_id'], htmlspecialchars($_SESSION['pa']['title']), htmlspecialchars($_SESSION['pa']['description']), $data_array['view_image_name'], $_SESSION['pa']['image_id'], $data_array['thumb_image_name'], htmlspecialchars($_SESSION['pa']['alt']), APPROVED);
				} else {
					$update=update_image_in_database($_SESSION['pa']['course_id'], htmlspecialchars($_SESSION['pa']['title']), htmlspecialchars($_SESSION['pa']['description']), $data_array['view_image_name'], $_SESSION['pa']['image_id'], $data_array['thumb_image_name'], htmlspecialchars($_SESSION['pa']['title']), POSTED_NEW);
				}
				
				if ($update==false){
					$msg->addError('pa_func_update_image_in_database');
					out();
				} else {
					$_SESSION['pa']['completed']=true;
					if ((is_admin_for_course()==true) || ($config_mode==CONFIG_DISABLED)){
						$msg->addFeedback('pa_edit_image_success_config_disabled');
					} else if ($config_mode==CONFIG_ENABLED){
						$msg->addFeedback('pa_edit_image_success_config_enabled');
					}
					out();
				}
			}
		} else if ($_SESSION['pa']['choose']==COMMENT){ 
			$_SESSION['pa']['comment_checked']=true;
			$comment=trim($_POST['comment']);
			if (empty($comment)){
				$_SESSION['pa']['error']['comment']=true;
				$msg->addError('pa_user_comment_empty');
				redirect($goback_url);
			}
			
			if ((is_admin_for_course()==true) || ($config_mode==CONFIG_DISABLED)){
				$update=update_comment_in_database($_SESSION['pa']['course_id'], htmlspecialchars($_POST['comment']), $_SESSION['pa']['image_id'], $_SESSION['pa']['comment_id'], APPROVED);
			} else {
				$update=update_comment_in_database($_SESSION['pa']['course_id'], htmlspecialchars($_POST['comment']), $_SESSION['pa']['image_id'], $_SESSION['pa']['comment_id'], POSTED_NEW);
			}
			
			if ($update==false){
				$msg->addError('pa_func_update_comment_in_database');
				out();
			} else {
				if ((is_admin_for_course()==true) || ($config_mode==CONFIG_DISABLED)){
					$msg->addFeedback('pa_edit_comment_success_config_disable');
				} else if ($config_mode==CONFIG_ENABLED){
					$msg->addFeedback('pa_edit_comment_success_config_enabled');
				}
				redirect('view.php?image_id='.$_SESSION['pa']['image_id']);
			}
		} else {
			$msg->addError('pa_var_unauthorized');
			out();
		}
	}
	} else {
		$msg->addError('pa_var_unauthorized');
		out();
	}
		
?>

<?php require_once(AT_INCLUDE_PATH.'footer.inc.php'); ?>