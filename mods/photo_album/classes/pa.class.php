<?php
/*===============================================================
  Photo Album                                                  
  ===============================================================
  Copyright (c) 2006 by Dylan Cheon & Kelvin Wong              
  Institute for Assistive Technology / University of Victoria  
  http://www.canassist.ca/                                     
                                                              
  This program is free software. You can redistribute it and/or
  modify it under the terms of the GNU General Public License  
  as published by the Free Software Foundation.                
  ===============================================================
 */
// $Id:

/**
 * @desc	This class structure is the general class to be used for every other class.  This class defines the course id, guest check, enrollment check, etc.
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
 
if (!defined('PATH')){
	define('PATH', './');
}

require_once (PATH.'define.php');
require_once (PATH.'include/data_func.php');
require_once (PATH.'include/general_func.php');
require_once (PATH.'HTML/Template/ITX.php');

/**
 * @desc	This class is called Pa.  This class is used by every other class
 */
class Pa {
	var $course_id=-1;
	var $error=0;
	var $error_array=Array();
	
	/**
	 * @desc	constructor.  
	 */
	function Pa (){
		Pa::init();
	}
	
	/**
	 * @desc	This function initializes the class object 
	 */
	function init(){
		Pa::checkGuest();
		Pa::checkEnrolled();
		Pa::setCourseId();
	}
	
	/**
	 * @desc	This function checks whether the user is enrolled in the course or not
	 */
	function checkEnrolled(){
		if ($_SESSION['privileges']>0){
			Pa::setVariable('show_modification_buttons', true);
		} else if (isset($_SESSION['enroll']) && ($_SESSION['enroll']==true)){
			Pa::setVariable('show_modification_buttons', true);
		}
	}
	
	/**
	 * @desc	This function checks whether the user is guest.  If the user is guest, it redirects the user to login page
	 */
	function checkGuest(){
		if (isset($_SESSION['is_guest']) && ($_SESSION['is_guest']==true)){
			redirect('../../'.PATH.'login.php');
		}
	}
		
	/**
	 * @desc	This function sets the course id
	 */
	function setCourseId(){
		if (isset($_POST['course_id']) && course_exist($_POST['course_id'])){
			Pa::setVariable('course_id', intval($_POST['course_id']));
		} else if ($_SESSION['course_id']==-1){
			Pa::setVariable('course_id', intval($_SESSION['pa']['course_id']));
		} else {
			Pa::setVariable('course_id', intval($_SESSION['course_id']));
		}
	}
	
	/**
	 * @desc	This function sets the given input string for the class object variable
	 * @param	String	$string		string name to be set
	 * @param	mixed	$value		string value
	 */ 
	function setVariable($string, $value){
		switch ($string){
			case 'course_id':
				if (is_int($value) && ($value > 0) && course_exist($value)){
					$this->{$string}=$value;
				} else {
					$this->storeError("course value is not valid");
				}
			break;
			case 'error':
				if (is_int($value) && ($value > 0)){
					$this->{$string}=$value;
				} else {
					$this->storeError("value ".$value." is not int");
				}
			break;
			case 'show_modification_buttons':
				if (is_bool($value)){
				  $this->{$string}=$value;
				} else {
				  $this->storeError("value ".$value." is not boolean");
				}
			break;
		}
	}
	
	/**
	 * @desc	This function returns the given variable value
	 * @param	String	$string		name of string to be returned
	 * @return	mixed				the required variable value 
	 */
	function getVariable($string){
		return $this->{$string};
	}
	
				
	/**
	 * @desc	This function stores the fatal error for the class object.  This function is only called when a fatal error is occured.
	 * @param	String	$string		error string to be stored
	 */
	function storeError($string){			
		$error=&Pa::getVariable('error');
		$array=&$this->error_array;
		$array[$error]=$string;
		Pa::setVariable('error', $error+1);
	}
	
	/**
	 * @desc	This function checks if a fatal error has occurred or not in the class object
	 * @return	boolean		true if there is fatal error
	 */
	function isError(){
	  if (Pa::getVariable('error')==0){
	    return false;
	  } else {
	    return true;
	  }
	}
}