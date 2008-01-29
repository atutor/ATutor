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
 * @desc	This file generates all the comment data to be used for the admin / instructor pages
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */

require_once ('define.php');
require_once ('classes/pa.class.php');
require_once ('include/general_func.php');
require_once ('include/data_func.php');

/** 
 * @desc	class pa_admin_comment generates all the comment data to be used for admin/instructor
 * @see		class Pa
 */
 
class Pa_Admin_Comment extends PA{
	var $mode=POSTED_NEW;
	var $comment_array=Array();
	var $page_array=Array();
	var $current_page=-1;
	var $show_page_left_buttons=false;
	var $show_page_right_buttons=false;
	var $total=NOT_SET;
	var $last_page=NOT_SET;
	
	/**
	 * @desc	constructor
	 */	
	function Pa_Admin_Comment(){
		$this->checkAuthority();
		parent::init();
		$this->checkRequest();
		$this->setMode();
		$this->checkCurrentPage();
		$this->setComments();
		$this->setPages();
	
	}
	
	/**
	 * @desc	This function checks requests to change the comment status.  The request could be one of make approved, make disapproved, or make new
	 */
	function checkRequest(){
		if (isset($_POST['button_disapprove'])){
		  for ($i=0; $i < ADMIN_NUMBER_OF_COMMENT; $i++){
		    $string="commentId".$i;
		    if (isset($_POST[$string])){
				modify_comment_status($_POST[$string], $this->getVariable('course_id'), DISAPPROVED);  
		    }
		  }
		} else if (isset($_POST['button_approve'])){
		  for ($i=0; $i< ADMIN_NUMBER_OF_COMMENT; $i++){
		    $string="commentId".$i;
		    if (isset($_POST[$string])){
			  modify_comment_status($_POST[$string], $this->getVariable('course_id'), APPROVED);
			}
		  }
		} else if (isset($_POST['button_post_new'])){
		  for ($i=0; $i < ADMIN_NUMBER_OF_COMMENT; $i++){
		  	$string="commentId".$i;  
		  	if (isset($_POST[$string])){
			    modify_comment_status($_POST[$string], $this->getVariable('course_id'), POSTED_NEW);
			}
		  }  
		}
	}
	
	/**
	 * @desc	This function checks whether the user is instructor or administrator.  If the user is neither instructor nor administrator, it redirects the user to the index page
	 */
	function checkAuthority(){
		if (is_admin_for_course()!=true){
			redirect('index.php');
		}
	}
	
	/**
	 * @desc	This function decides whether to display left arrow and right arrow buttons in the page table
	 */
	function setPages(){
		$temp=get_page_array(ADMIN_NUMBER_OF_COMMENT,ADMIN_NUMBER_OF_COMMENT_PAGE, $this->getVariable('current_page'), $this->getVariable('last_page'));
		$current=$this->getVariable('current_page');
		if ($current > 1){
			$this->setVariable('show_page_left_buttons', true);
		} 
		if ($current < $temp['last_page']){
			$this->setVariable('show_page_right_buttons', true);
		}
		$this->page_array=&$temp;
		
	}	
	
	/**
	 * @desc	This function sets the mode value for the comment display.  The mode value can be one of POSTED_NEW, APPROVED, DISAPPROVED
	 */
	function setMode(){
		if (isset($_GET['mode'])){
			$this->setVariable('mode', intval($_GET['mode']));
		} 
	}
	
	/**
	 * @desc	This function checks if the current page is valid.  Otherwise, the current page is set to 1
	 */
	function checkCurrentPage(){
		$total=get_total_comment_number(ADMIN_PANEL, $this->getVariable('course_id'), $this->getVariable('mode'));
		$last_page=get_last_page(ADMIN_NUMBER_OF_COMMENT, $total);
		$this->setVariable('total', $total);
		$this->setVariable('last_page', $last_page);
		
		if (!isset($_GET['current_page'])){
			$this->setVariable('current_page', FIRST_PAGE);
		} else {
			$current_page=to_pos_int($_GET['current_page']);
			if ($current_page > $last_page){
				$this->setVariable('current_page',$last_page);
			} else {
				$this->setVariable('current_page', $current_page);
			}
		}
	}
	
	/**
	 * @desc	This function sets the data for the comment array
	 */
	function setComments(){
		$temp=get_comment_array(ADMIN_PANEL, parent::getVariable('course_id'), $this->getVariable('mode'), NOT_SET, ADMIN_NUMBER_OF_COMMENT, $this->getVariable('current_page'));
		$this->comment_array=&$temp;
	}
			
	/**
	 * @desc	This function sets the given string value for the class object
	 * @param	String	$string		string name to be set
	 * @param	mixed	$value		string value
	 */
	function setVariable($string, $value){
		switch ($string){
			case 'mode':
				if ($value==APPROVED || $value==DISAPPROVED || $value==POSTED_NEW){
					$this->{$string}=$value;
				}
			break;
			case 'current_page':
			case 'total':
			case 'last_page':
				if (is_int($value)){
					$this->{$string}=$value;
				} else {
					parent::storeError("value ".$string." is not integer");
				}
			break;
			case 'show_page_left_buttons':
			case 'show_page_right_buttons':
				if (is_bool($value)){
					$this->{$string}=$value;
				} else {
					parent::storeError("value ".$string." is not boolean");
				}
			break;
		}
	}
}