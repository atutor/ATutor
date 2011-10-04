<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) exit;

/** 
 * Ajax Message
 * Returns a plain message without using the savant template.
 *
 * @author	Harris Wong
 * @date	Feb 2, 2010
 */
class AjaxMessage extends Message {
	/**
	 * Override constructor
	 */
	function AjaxMessage(){
	}

	/**
	* Overrides Message.printAbstract.  Now prings plain message
	* @access  public
	* @param   string $type					error|warning|info|feedback|help|help_pop
	* @return  string	Singular message instant
	* @author  Harris Wong
	*/
	function printAbstract($type) {
		if (!isset($_SESSION['message'][$type])) return;

		$_result = array();
		
		foreach($_SESSION['message'][$type] as $e => $item) {
			$result = '';

			// $item is either just a code or an array of argument with a particular code
			if (is_array($item)) {			
				/* this is an array with terms to replace */
				$first = array_shift($item);
				$result = _AT($first); // lets translate the code

				if ($result == '') { // if the code is not in the db lets just print out the code for easier trackdown
					$result = '[' . $first . ']';
				}
					
				$terms = $item;
			
				/* replace the tokens with the terms */
				$result = vsprintf($result, $terms);
				
			} else {
				$result = _AT($item);
				if ($result == '') // if the code is not in the db lets just print out the code for easier trackdown
					$result = '[' . $item . ']';
			}
			
			array_push($_result, $result); // append to array
		}
		//clean
		unset($_SESSION['message'][$type]);
		//return
		if (count($_result) > 0) {
			foreach ($_result as $e){
				$e = preg_replace('/<small>(.*)<\/small>/', '', $e);
				return $e;
			}
		}

	}

	//override
	function printErrors($optional=null) {
		if ($optional != null)  // shortcut
			$this->addAbstract('error', $optional);
		return $this->printAbstract('error');
	}
}
?>