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
 * @desc	This file handles all the image upload operations
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */

require_once(PATH.'classes/phpThumb_1.7.2/phpthumb.class.php');
require_once(PATH.'classes/phpThumb_1.7.2/phpthumb.functions.php');
require_once(PATH.'classes/pa.class.php');
require_once(PATH.'define.php');

/**
 * @desc	image upload class to upload an image
 * @see		class Pa
 */

class IMAGE_UPLOAD extends PA{
	var $name='';
	var $file_size=0;
	var $file_tmp_src='';
	var $file_type='';
	var $thumb_image_name='';
	var $view_image_name='';
	var $image_copy_required=true;
	var $user_input_error=0;
	var $user_input_array=Array();
	var $temp_folder_path='';
	
	/**
	 * @desc	image upload constructor
	 * @param	Array	$file_array			array which contains the image file information (name, size, tmp_src, etc)
	 * @param	String	$temp_folder_path	temp folder path string to copy the image file in.
	 */
	function IMAGE_UPLOAD($file_array, $temp_folder_path){
		parent::init();
		$this->setVariable('temp_folder_path', $temp_folder_path);
		$this->checkImageFile($file_array);
		if (($this->getVariable('user_input_error')==0) && ($this->getVariable('image_copy_required')==true)){	//no error, so process image copy operation
			$this->setVariable('name', $file_array['name']);
			$this->copyViewImage();
			$this->copyThumbImage();
		}	
	}
	
	/**
	 * @desc	This function checks the given file array is properly set up
	 * @param	Array	$file	file array
	 */
	function checkImageFile(&$file){
		$this->checkImageEmpty($file['name']);
		$this->checkValidType($file['type']);
		$this->checkValidNameLength($file['name']);
		$this->checkValidEscape($file['name']);
		$this->checkValidSize($file['size']);		
		$this->setVariable('file_tmp_src', $file['tmp_name']);
		
	}
	
	/**
	 * @desc	This function checks whether the image is submitted empty or not
	 * @param	String	$file	image name
	 */
	function checkImageEmpty(&$file){
		if (empty($file)){
			$this->storeUserError("file_empty");
		}
	}
	
	/**
	 * @desc	This function stores the user input error 
	 * @param	String	$string		error string
	 */
	function storeUserError($string){
		$error_count=$this->getVariable('user_input_error');
		$error_array=&$this->user_input_array;
		$error_array[$error_count]=&$string;
		$this->setVariable('user_input_error', $error_count+1);
	}
	
	/**
	 * @desc	This function checks whether the image size is valid
	 * @param	int	$file	image size
	 */
	function checkValidSize($file){
		$max_size=get_max_file_size(PA::getVariable('course_id'));
		if ($max_size==-1){
			global $_config_defaults;
			$max_size=$_config_defaults['max_file_size'];
		} 
		if ($file > $max_size){
			$this->storeUserError('file_size');
		} else {
			$this->setVariable('file_size', $file);
		}
	}
	
	/**
	 * @desc	This function checks whether the image file name uses invalid escape characters or not
	 * @param	String	$file	image file name
	 */
	function checkValidEscape(&$file){
		if (ereg(INVALID_ESCAPE, $file)){
			$this->storeUserError('file_escape');
		}
	}	
	
	/**
	 * @desc	This function checks whether the image file name length is valid
	 * @param	String	$file 	image name
	 */
	function checkValidNameLength(&$file){
		if (count($file)>MAX_FILENAME_LENGTH){
			$this->storeUserError('file_length');
		}
	}
	
	/**
	 * @desc	This function checks whether the image type is valid
	 * @param	String	$type 	image type string
	 */
	function checkValidType($type){
		global $IMAGE_TYPE;
		if (in_array($type, $IMAGE_TYPE)){
			$this->setVariable('file_type', $type);
		} else {
			$this->storeUserError('file_type');
		}
	}
	
	/**
	 * @desc	This function sets the string value for the class object
	 * @param	String	$string		string name to be set
	 * @param	mixed	$value		string value 
	 */
	function setVariable($string, $value){
		switch ($string){
			case 'mode_edit':
			case 'image_copy_required':
				if (is_bool($value)){
					$this->{$string}=$value;
				} else {
					parent::storeError("string ".$string." is not boolean");
				}
			break;
			case 'file_type':
			case 'file_tmp_src':
			case 'name':
			case 'thumb_image_name':
			case 'view_image_name':
			case 'temp_folder_path':
				if (is_string($value)){
					$this->{$string}=$value;
				} else {
					parent::storeError("string ".$string." is not string");
				}	
			break;
			case 'user_input_error':
				if (is_int($value)){
					$this->{$string}=$value;
				} else {
					parent::storeError("string ".$string." is not int");
				}
			break;
		}
	}
	
	/**
	 * @desc	This function creates a full-sized image of the image file and copies it to the temp folder
	 */
	function copyViewImage(){
		$temp_folder_path=$this->getVariable('temp_folder_path');
		$view_image_name=modify_image_name($temp_folder_path, $this->getVariable('name'));
		$this->setVariable('view_image_name', $view_image_name);	
		image_to_this_location($view_image_name, $this->getVariable('file_tmp_src'), $temp_folder_path);
		$thumb=new phpThumb();
		$view_image_path=AT_CONTENT_DIR.$temp_folder_path.$view_image_name;
		$thumb->setSourceFilename($view_image_path);
		$size=getimagesize($view_image_path);
		/* set size for view image */
		if ($size[0]>MAX_IMAGE_WIDTH){
			$thumb->w=MAX_IMAGE_WIDTH;
		}
		if (defined(MAX_IMAGE_HEIGHT) && $size[1]> MAX_IMAGE_HEIGHT){
			$thumb->h=MAX_IMAGE_HEIGHT;
		}
		
		$thumb->iar='l';
		$thumb->config_output_format='jpeg';
		
		/* generate for view image */	
		if ($thumb->GenerateThumbnail()){
			if ($view_image_path){
				if (!$thumb->RenderToFile($view_image_path)){
					parent::storeError("render operation failed for view image");	
				}
			} else {
				parent::storeError("view image path does not exist");
			}
		} else {
			parent::storeError("View generateThumbnail operation is failed");
		}
	}
	
	/**
	 * @desc	This function creates a thumbnail image of the image file and stores it in the temp folder
	 */	
	function copyThumbImage(){
		$temp_folder_path=$this->getVariable('temp_folder_path');
		$thumb_image_name=insert_into_image_name($this->getVariable('view_image_name'), THUMB_EXT);
		$thumb_image_name=modify_image_name($temp_folder_path, $thumb_image_name);
		$this->setVariable('thumb_image_name', $thumb_image_name);
		$thumb=new phpThumb();
		$view_image_path=AT_CONTENT_DIR.$temp_folder_path.$this->getVariable('view_image_name');
		$thumb->setSourceFilename($view_image_path);
		$size=getimagesize($view_image_path);
		/* set size for thumb image */
		$thumb->iar='l';
		$thumb->config_output_format='jpeg';
		
		/* set size for thumb image */
		$thumb->h=THUMB_IMAGE_HEIGHT;
		$thumb->w=THUMB_IMAGE_WIDTH;
		
		/* generate for thumb image */	
		$thumb_image_path=AT_CONTENT_DIR.$temp_folder_path.$thumb_image_name;
		if ($thumb->GenerateThumbnail()){
			if ($view_image_path){
				if (!$thumb->RenderToFile($thumb_image_path)){
					parent::storeError("render operation failed for thumb image");	
				}
			} else {
				parent::storeError("view image path does not exist");
			}
		} else {
			parent::storeError("Thumb generateThumbnail operation is failed");
		}
	}
}	
?>
