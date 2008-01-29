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
 * @desc	This file generates image data for the my pictures view
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
 * @desc	class Mypic
 * @see		class Pa
 */
class Mypic extends Pa{
	var $mode=POSTED_NEW;
	var $image_array=Array();
	var $page_array=Array();
	var $current_page=-1;
	var $show_page_left_buttons=false;
	var $show_page_right_buttons=false;
	var $total=NOT_SET;
	var $last_page=NOT_SET;
	
	/**
	 * @desc	class constructor
	 */
	function Mypic(){
		parent::init();
		$this->setMode();
		$this->checkCurrentPage();
		$this->setImages();
		$this->setPages();
	
	}
	
	/**
	 * @desc	This function decides whether to display left arrow and right arrow buttons for the page table
	 */
	function setPages(){
		$temp=get_page_array(MYPIC_NUMBER_OF_IMAGE, MYPIC_NUMBER_OF_IMAGE_PAGE, $this->getVariable('current_page'), $this->getVariable('last_page'));
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
	 * @desc	This function sets the mode value for the image display.  The mode value can be one of POSTED_NEW, APPROVED, DISAPPROVED
	 */
	function setMode(){
		if (isset($_GET['mode'])){
			$this->setVariable('mode', intval($_GET['mode']));
		} 
	}
	
	/**
	 * @desc	This function checks the current page is valid.  Otherwise, the current page is set to 1
	 */
	function checkCurrentPage(){
		$total=get_total_image_number(MY_PIC, $this->getVariable('course_id'), $this->getVariable('mode'));
		$last_page=get_last_page(MYPIC_NUMBER_OF_IMAGE, $total);
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
	 * @desc	This function sets the image array
	 */
	function setImages(){
		$array=get_image_array(MY_PIC, parent::getVariable('course_id'), $this->getVariable('mode'), $this->getVariable('current_page'), MYPIC_NUMBER_OF_IMAGE, $_SESSION['login']);
		$this->image_array=&$array;
	}
			
	/**
	 * @desc	This function sets the given string to a value for the class object
	 * @param	String	$string		string name to set up
	 * @param	mixed 	$value		string value
	 */
	function setVariable($string, $value){
		switch ($string){
			case 'mode':
				if ($value==APPROVED || $value==DISAPPROVED || $value==POSTED_NEW){
					$this->{$string}=$value;
				}
			break;
			case 'current_page':
			case 'last_page':
			case 'total':
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