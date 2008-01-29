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
 * @desc	This file generates the image data and comment data to be displayed in the view page
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
 
require_once ('define.php');
require_once ('classes/pa.class.php');
require_once ('include/data_func.php');
require_once ('include/general_func.php');

/** 
 * @desc	class View
 * @see		class Pa
 */
class View extends Pa {
  	var $image_id=NOT_SET;
  	var $comment_array=Array();
  	var $image_array=Array();
  	
  	/**
  	 * @desc	class constructor
  	 */
	function View (){
		parent::init();
		$this->checkImageId();	
		$this->checkAuthority();
		$this->setImage();
	  	$this->setComments();
	}
	
	/**
	 * @desc	This function checks if the image has approved status.  If the image is not set to approved and user is neither admin nor instructor, it redirects user to the index page
	 */
	function checkAuthority(){
		$image_array=get_single_data(IMAGE, $this->getVariable('image_id'), parent::getVariable('course_id'));
		if (!(($_SESSION['is_admin']==true) || ($_SESSION['privileges'] > 0))){
			if ($image_array['status']!=APPROVED){
				global $msg;
				$msg->addError('pa_var_unauthorized');
				redirect('index.php');
			}
		}
	}
	
	/**
	 * @desc	This function checks whether the given image_id exists in the database
	 */
	function checkImageId(){
		global $msg;
		if (isset($_GET['image_id'])){
			if (image_exist(intval($_GET['image_id']), parent::getVariable('course_id'))){
				$this->setVariable('image_id', intval($_GET['image_id']));
			} else {
				$msg->addError('pa_var_unauthorized');
				redirect('index.php');
			}
		} else {
			$msg->addError('pa_var_unauthorized');
			redirect ('index.php');
		}
	}
	
	/**
	 * @desc	This function sets the string to a value
	 * @param	String	$string		string name to set up
	 * @param	mixed 	$value		string value
	 */
	function setVariable($string, $value){
	  switch ($string){
	    case 'image_id':
	 		if (is_int($value) && ($value > 0)){
			   $this->{$string}=$value;
			} else {
			  parent::storeError("string ".$string." is not integer");
			}
		break;  
	  }
	}
	
	/**
	 * @desc	This function sets the image array
	 */
	function setImage(){
		$this->image_array=&get_single_data(IMAGE, $this->getVariable('image_id'), parent::getVariable('course_id'));
	}
	
	/**
	 * @desc	This function sets the comment array 
	 */
	function setComments(){
		$this->comment_array=&get_comment_array(ADMIN_VIEW, $this->getVariable('course_id'), NOT_SET, $this->getVariable('image_id'));  
	}	
}