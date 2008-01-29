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
 * @desc	This file defines some general purpose functions used on almost every page
 * @author	Dylan Cheon
 * @copyright	2006 Institute for Assistive Technology / University of Victoria
 * @link	http://www.canassist.ca/                                     
 * @license	GNU
 */
 
 
/**
 * @desc	The function prevents file name collision. It also converts spaces into underscores and renames files to jpeg extensions
 * @param	String	$location		location string
 * @param	String	$image_name		image name
 * @return	String					new image name
 */
function modify_image_name($location, $image_name){
	$image_name=ereg_replace(" ", "_", $image_name);
	$temp_image=without_ext($image_name);
	$new_name=$temp_image.'.jpg';
	$temp=$new_name;
	$i=0;
	while (image_name_duplicate_check($location, $temp)!=true){
		$temp=insert_into_image_name($new_name, $i);
		$i++;
	}
	return $temp;
}


/**
 * @desc	This helper function checks if there is an image with the same name in the folder 
 * @param	String	$location		location folder name
 * @param	String	$image name		image file name
 * @return	Boolean					true if the image name is unique to the folder
 */	
function image_name_duplicate_check($location, $image_name){
	$image_file=AT_CONTENT_DIR.$location.$image_name;
	if (is_file($image_file)){
		return false;
	} else {
		return true;
	}
}
	
	
/**
 * @desc	This function inserts the given string into the image file name string. It is used to prevent file name collisions
 * @param	String	$image name 	image name string
 * @param	String	$insert			insert string
 * @return	String					new image name
 */
function insert_into_image_name($image_name, $insert){
	$position=strpos($image_name, '.');
	$string1=substr($image_name, 0, $position);
	$string2=substr($image_name, $position+1);
	$result=$string1.'_'.$insert.'.'.$string2;
	return $result;
}
	
/**
 * @desc	This function checks whether the user is an instructor or administrator.  If the user is neither student nor guest, it returns true
 * @return	Boolean		true if the user is either administrator or instructor
 */
function is_admin_for_course(){
  if ($_SESSION['privileges'] != NORMAL_USER){ 
    return true;
  } else if ($_SESSION['is_admin']==true){
    return true;
  } else {
    return false;
  }
}
	
/**
 * @desc	This function copies the image file to given location
 * @param	String	$image 			 	filename to be named
 * @param	String	$temp_image_file 	image source to be copied
 * @param	String	$location			location folder name
 */	
function image_to_this_location($image, $temp_image_file, $location){
	$store_folder=AT_CONTENT_DIR.$location;
	$store_image=$store_folder.$image;
	
	if (!@copy($temp_image_file, $store_image)){
		global $msg;
		$msg->addError('pa_func_copy');
		redirect('index.php');
	} 
	@chmod($store_image, 0777);
}
	
	
/**
 * @desc	This function deletes the files of the given Array (array should be mysql resource)
 * @param	Array	$array	array which contains the file names and locations
 */
function delete_image_files($array){
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
	while ($row=mysql_fetch_array($array)){	
		$location=AT_CONTENT_DIR.$row['location'];
		$image_path=$location.$row['view_image_name'];
		$thumb_path=$location.$row['thumb_image_name'];
		unlink($image_path);
		unlink($thumb_path);
	}
}
	
	
/**
 * @desc	This function convert the newline character to <br>
 * @param	String	$string		string to be converted
 * @return	String				the new string
 */
function convert_newlines($string){
	$input=eregi_replace("\n", "<br/>", $string);
	return $input;
}	
	
	
/**
 * @desc	This function redirect the user to the requested address
 * @param	String	$addr 	redirection destination URI
 */
function redirect($addr){
	$url=ATUTOR_PREFIX.BASE_PATH.$addr;
	echo ("
		<META http-equiv='refresh' content='0;URL=$url'>
	");	
	exit;	
}
	
	
	
/**
 * @desc	This function returns the positive int value of the given input.  If the given input is not numeric, it returns 1
 * @param	int	$input	input value
 * @return	int			positive int value
 */
function to_pos_int($input){
	$result;
	if (is_numeric($input)){
		$temp=intval($input);
		$result=max(1, $temp);
	} else {
		$result=1;
	}
	return $result;
}	
	
/**
 * @desc	This function returns the file name without an extention
 * @param	String	$input	file name 
 * @return	String			file name without extention
 */
function without_ext($input){
	$pos=strpos($input, ".");
	$string=substr($input, 0, $pos);
	if (empty($string)){
		return "unknown_image";
	} else {
		return $string;
	}
}

/**
 * @desc	This function creates a folder.
 * @param	String	$folder		folder name
 */	
function create_folder($folder){
	$folder=AT_CONTENT_DIR.$folder;
	if (!is_dir($folder)){
		if (!mkdir($folder)){
			global $msg;
			$msg->addError('pa_func_mkdir');
			redirect('index.php');
		}
		
	}
}

/**
 * @desc	This function makes a temp folder under the users folder
 * @return 	String	temp folder path
 */
function make_temp_folder(){
	$my_dir=ALBUM_IMAGE_STORE.$_SESSION['login'].'/';
	create_folder($my_dir);
	if(!is_writable($my_dir) && @chmod($my_dir, 0777)){
		$msg->addError('pa_func_make_temp_folder');
		redirect('index.php');
	}
	$temp_folder=$my_dir.TEMP_FOLDER_NAME;
	create_folder($temp_folder);
	if(!is_writable($temp_folder) && @chmod($temp_folder, 0777)){
		$msg->addError('pa_func_make_temp_folder');
		redirect('index.php');
	}
	return $temp_folder;
}


/**
 * @desc	This function deletes all the files in the temp folder directory
 */
function clear_temp_folder(){
	global $msg;
	$temp_dir=AT_CONTENT_DIR.ALBUM_IMAGE_STORE.$_SESSION['login'].'/temp/';
	if (is_dir($temp_dir)){	
		$temp=substr($temp_dir, 0, -1);
		if (!$files=@opendir($temp)){
			$msg->addError('pa_func_clear_temp_folder_open');
			redirect('../../index.php');
		}
		while ($obj=readdir($files)){
			if ($obj == '.' || $obj=='..' || $obj=='.svn'){
				continue;
			} else {
				if (!@unlink($temp_dir.$obj)){
					$msg->addError('pa_func_clear_temp_folder_unlink');
					redirect('../../index.php');
				}
			}
		}
	}
}


/**
 * @desc	This function redirects to the appropriate page when an error occurs depending on the user type
 */
function out(){
	if ($_SESSION['pa']['choose']==IMAGE){
		if ($_SESSION['pa']['mode']=='edit'){
			if ($_SESSION['pa']['my_pic_mode']==true){
				redirect('my_photo.php');
			} else if ((!is_admin_for_course()) && (get_config_mode($_SESSION['pa']['course_id'])==CONFIG_ENABLED)){
				redirect('index.php');
			} else {
				redirect('view.php?image_id='.$_SESSION['pa']['image_id']);
			}
		} else if ($_SESSION['pa']['mode']=='add'){
			if ($_SESSION['pa']['instructor_mode']==true){
				redirect('instructor_image.php');
			} else if ($_SESSION['pa']['administrator_mode']==true){
				redirect('admin_image_list.php');
			} else {
				redirect('index.php');
			}
		} else {	//mode is delete
			if ($_SESSION['pa']['instructor_mode']==true){
				redirect('instructor_image.php');
			} else if ($_SESSION['pa']['administrator_mode']==true){
				redirect('admin_image_list.php');
			} else {
				redirect('index.php');
			}
		}
	} else {	//choose is comment
		redirect('view.php?image_id='.$_SESSION['pa']['image_id']);
	}
}

			
?>
