<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

require_once(AT_INCLUDE_PATH.'lib/output.inc.php'); 

/**
* Message
* Class acting as MessageHandler for various message types
* @access	public
* @author	Jacek Materna
*/

class Message {
	/*
	* Ref. to savant obj.
	* @access private
	* @var string	
	*/
	var $savant;
	
	var $tmpl = array(	'error' => 'errormessage.tmpl.php',
						'feedback' => 'feedbackmessage.tmpl.php',
						'warning' => 'warningmessage.tmpl.php',
						'info' => 'infomessage.tmpl.php'
				);
	
	var $prefix = array( 'error'  =>'AT_ERROR_',
						'feedback' => 'AT_FEEDBACK_',
						'warning' => 'AT_WARNING_',
						'info' => 'AT_INFOS_'
				  );
	
	var $base_href;
	
	// constructor
	function Message($savant, $base_href) { 
		$this->savant = $savant;
		$this->base_href = $base_href;
	} 
		
	/**
	* Print message(s) of type $type. Processes stored messages in session var for type $type
	* and translates them into language spec. Then passes processed data to savant template for display
	* @access  public
	* @param   string $type					error|warning|info|feedback
	* @author  Jacek Materna
	*/
	function printAbstract($type) {

		$this->savant->assign('base_href', $this->base_href);
		
		// first lets translate the payload to language spec.
		$payload = $_SESSION['message'][$type];
		
		while( list($key, $item) = each($payload) ) {
			$item = getTranslatedCodeStr($item);
			
			$this->savant->assign('item', $item);	// pass translated payload to savant var for processing
			$this->savant->display($this->tmpl[$type]);
		}

		unset($_SESSION['message'][$type]);
	}

	/**
	* Add message to be tracked by session obj
	* @access  public
	* @param   string $sync					ref to type of message
	* @param   string|array $code			code of the message or array(code, args...)
	* @author  Jacek Materna
	*/
	function addAbstract($sync, $code) {
	
		$first = ''; // key value for storage
		// Convert to strings
		if (is_array($code)) {
			foreach($code as $e) {
				settype(&$e, "string");
			}

			$code[0] = $this->prefix[$sync] . $code[0]; // add prefix		

			$first = $code[0];
		} else {
			if (!is_string($code))  
				settype($code, "string");
			
			$code = $this->prefix[$sync] . $code;
			$first = $code;		
		}
		
		$payload = $code;
		
		if (!isset($_SESSION['message'][$sync]) || count($_SESSION['message'][$sync]) == 0) { // fresh
			
			// PHP 5 
			//try {
				$_SESSION['message'][$sync] = array($first => $payload);
			//} catch (Exception $e) {
			//	return false;
			//}
		} else if (isset($_SESSION['message'][$sync][$first])) { // already data there for that code, append
			debug($first);
			// existing data is either a collection or a single node
			if(is_array($_SESSION['message'][$sync][$first])) {
				$_SESSION['message'][$sync][$first][] = $payload;
			} else { 
				$temp = $_SESSION['message'][$sync][$first]; // grab it
				unset($_SESSION['message'][$sync][$first]); // make sure its gone
				
				$_SESSION['message'][$sync][$first] = array($temp, $payload); // put them both back as an array
			}
		} else {
		
			// Already an array there, could be empty or have something in it, append.
			// Store key = value for much faster unset as needed 
			
			// PHP 5
			//try {
				$_SESSION['message'][$sync]->append($first, $payload);
			//} catch (exception $e) {
			//	return false;
			//}
		}
	}
	
	function abstractContains($type) {
		return (isset($_SESSION['message'][$type]));
	}
	
	/**
	* Add error message to be tracked by session obj
	* @access  public
	* @param   string|array $code			code of the message or array(code, args...)
	* @author  Jacek Materna
	*/
	function addError($code) {
		$this->addAbstract('error', $code);
	}
	
	function printErrors() {
		$this->printAbstract('error');
	}
	
	/**
	* Add warning message to be tracked by session obj
	* @access  public
	* @param   string|array $code			code of the message or array(code, args...)
	* @author  Jacek Materna
	*/
	function addWarning($code) { 
		$this->addAbstract('warning', $code);
	}
	
	function printWarnings() {
		$this->printAbstract('warning');
	}
	
	/**
	* Add info message to be tracked by session obj
	* @access  public
	* @param   string|array $code			code of the message or array(code, args...)
	* @author  Jacek Materna
	*/
	function addInfo($code) { 
		$this->addAbstract('info', $code);
	}
	
	function printInfos() {
		$this->printAbstract('info');
	}
	
	/**
	* Add feedback message to be tracked by session obj
	* @access  public
	* @param   string|array $code			code of the message or array(code, args...)
	* @author  Jacek Materna
	*/
	function addFeedback($code) { 
		$this->addAbstract('feedback', $code);
	}
	
	function printFeedbacks() {
		$this->printAbstract('feedback');
	}
	
	function printAll() {
		$this->printAbstract('error');
		$this->printAbstract('warning');
		$this->printAbstract('info');
		$this->printAbstract('feedback');
	}
	
	function containsErrors() {
		return abstractContains('error');
	}
	
	function containsFeedbacks() {
		return abstractContains('feedback');
	}
	
	function containsWarnings() {
		return abstractContains('warning');
	}
	
	function containsInfos() {
		return abstractContains('info');
	}
	
} // end of class

?>