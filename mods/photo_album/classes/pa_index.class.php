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
 * @desc	This file generates thumbnail image data for the index page - thumbnail view
 * @author	Dylan Cheon & Kelvin Wong
 * @copyright	2006, Institute for Assistive Technology / University of Victoria 
 * @link	http://www.canassist.ca/                                    
 * @license GNU
 */
 
require_once ('pa.class.php');

/** 
 * @desc	class Pa_index.
 * @see		class Pa
 */
class Pa_Index extends Pa {
	var $current_page=0;
	var $show_page_left_buttons=false;
	var $show_page_right_buttons=false;
	var $image_array=Array();
	var $page_array=Array();
	var $total=NOT_SET;
	var $last_page=NOT_SET;
	
	/** 
	 * @desc	class constructor
	 */
	function Pa_Index (){
		parent::init();
		$this->checkCurrentPage();
		$this->setImages();
		$this->setPages();
	}
	
	/**
	 * @desc	this function sets the image array to display the index page
	 */
	function setImages(){
		$temp=get_image_array(STUDENT, $this->getVariable('course_id'), APPROVED, $this->getVariable('current_page'), THUMB_NUMBER_OF_IMAGE);
		$this->image_array=&$temp;
	}
	
	/**
	 * @desc	This function decides whether the left arrow and right arrow button should be displayed in the page table
	 */
	function setPages(){
		$temp=get_page_array(THUMB_NUMBER_OF_IMAGE, THUMB_NUMBER_OF_IMAGE_PAGE, $this->getVariable('current_page'), $this->getVariable('last_page'));
		if ($temp['current'] >1 ){
			$this->setVariable('show_page_left_buttons', true);
		}
		if ($temp['current'] < $temp['last_page']){
			$this->setVariable('show_page_right_buttons', true);
		}
		$this->page_array=&$temp;
	}
	
	/**
	 * @desc	This function sets the given string value
	 * @param	String	$string		string name to be set up
	 * @param	mixed	$value		string value
	 */
	function setVariable($string, $value){
		switch ($string){
			case 'current_page':
			case 'total':
			case 'last_page':
				if (is_int($value)){
					$this->{$string}=$value;
				} else {
					parent::storeError("variable ".$string." is not an positive int");
				}
			break;
			case 'show_page_left_buttons':
			case 'show_page_right_buttons':
				if (is_bool($value)){
					$this->{$string}=$value;
				} else {
					parent::storeError("variable ".$string." is not boolean");
				}
			break;
		}
	}
	
	/**
	 * @desc	This function checks whether the current page value is valid.  Otherwise it sets 1 for the current page value
	 */
	function checkCurrentPage(){
		$total=get_total_image_number(STUDENT, $this->getVariable('course_id'), APPROVED);
		$last_page=get_last_page(THUMB_NUMBER_OF_IMAGE, $total);
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
}