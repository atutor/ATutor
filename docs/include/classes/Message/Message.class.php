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
	
	var $error_tmpl = 'errormessage.tmpl.inc';
	
	var $feedback_tmpl = 'feedbackmessage.tmpl.inc';
	
	var $warning_tmpl = 'warningmessage.tmpl.inc';
	
	var $info_tmpl = 'infomessage.tmpl.inc';
	
	var $error_prefix = 'AT_ERROR_';
	
	var $feedback_prefix = 'AT_FEEDBACK_'
	
	var $warning_prefix = 'AT_WARNING_';
	
	var $info_prefix = 'AT_INFOS_';
	
	var $base_href;
	
	// constructor
	function Message($savant, $base_href) { 
		$this->savant = $savant;
		$this->base_href = $base_href;
	} 
	
	/**
	* Add error message to be tracked by session obj
	* @access  public
	* @param   string $code					code of the message
	* @param   array $payload				message arguments
	* @author  Jacek Materna
	*/
	function addError($code, $payload) { // common to all children
		addAbstract('error', $error_prefix . $code, $payload);
	}
	
	function printErrors() {
		printAbstract('error');
	}
	
	/**
	* Add warning message to be tracked by session obj
	* @access  public
	* @param   string $code					code of the message
	* @param   array $payload				message arguments
	* @author  Jacek Materna
	*/
	function addWarning($code, $payload) { // common to all children
		addAbstract('warning', $warning_prefix. $code, $payload);
	}
	
	function printWarnings() {
		printAbstract('warning');
	}
	
	/**
	* Add info message to be tracked by session obj
	* @access  public
	* @param   string $code					code of the message
	* @param   array $payload				message arguments
	* @author  Jacek Materna
	*/
	function addInfo($code, $payload) { // common to all children
		addAbstract('info', $info_prefix. $code, $payload);
	}
	
	function printInfos() {
		printAbstract('info');
	}
	
	/**
	* Add feedback message to be tracked by session obj
	* @access  public
	* @param   string $code					code of the message
	* @param   array $payload				message arguments
	* @author  Jacek Materna
	*/
	function addFeedback($code, $payload) { // common to all children
		addAbstract('feedback', $feedback_prefix. $code, $payload);
	}
	
	function printFeedbacks() {
		printAbstract('feedback');
	}
	
	function printAll() {
		printAbstract('all');
	}
	
	/**
	* Print message(s) of type $type
	* @access  public
	* @param   string $type					error|warning|info|feedback|all
	* @author  Jacek Materna
	*/
	function printAbstract($type) {

		savant->assign('base_href', $this->base_href);
		savant->assign('payload', $_SESSION['message'][$type]);
		
		switch($type) {
			case 'error':
				$savant->display($error_tmpl);
				break;
			case 'warning':
				$savant->display($warning_tmpl);
				break;
			case 'info':
				$savant->display($info_tmpl);
				break;
			case 'feedback':
				$savant->display($feedback_tmpl);
				break;
			case: 'all'
				$savant->display($error_tmpl);
				
				savant->assign('base_href', $base_href);
				savant->assign('payload', $_SESSION['message'][$type]);
				$savant->display($warning_tmpl);
				
				savant->assign('base_href', $base_href);
				savant->assign('payload', $_SESSION['message'][$type]);
				$savant->display($info_tmpl);
				
				savant->assign('base_href', $base_href);
				savant->assign('payload', $_SESSION['message'][$type]);
				$savant->display($feedback_tmpl);
				
			default:
		
		}
		
		if ($type === 'all') {
			unset($_SESSION['message']['error']);
			unset($_SESSION['message']['warning']);
			unset($_SESSION['message']['info']);
			unset($_SESSION['message']['feedback']);
		} else
			unset($_SESSION['message'][$type]);
	}
	
	/**
	* Add message to be tracked by session obj
	* @access  public
	* @param   string $sync					ref to type of message
	* @param   string $code					code of the message
	* @param   array $payload				message arguments
	* @author  Jacek Materna
	*/
	function addAbstract($sync, $code, $payload) {
	
		// handle bad format
		if (is_array($payload)) {
			foreach ($elem as $payload) {
				if (!is_string($elem)) {
					settype($elem, "string");
					
			endforeach;
		} else if (!is_string($code)) {
			settype($payload, "string");
		}
		
		if (!isset($_SESSION['message'][$sync]) || count($_SESSION['message'][$sync]) == 0) { // fresh
			
			// PHP 5 
			//try {
				$_SESSION['message'][$sync] = array($code => $payload);
			//} catch (Exception $e) {
			//	return false;
			//}
		} else if (isset($_SESSION['message'][$sync][$code])) { // already data there for that code, append
			
			// existing data is either a collection or a single node
			if(is_array($_SESSION['message'][$sync][$code]) {
				$_SESSION['message'][$sync][$code]->append($payload);
			} else {
				$temp = $_SESSION['message'][$sync][$code]; // grab it
				unset($_SESSION['message'][$sync][$code]); // make sure it gone
				
				$_SESSION['message'][$sync][$code] = array($temp, $payload);
			}
		} else {
		
			// Already an array there, could be empty or have something in it, append.
			// Store key = value for much faster unset as needed 
			// @see getMessageToPrint()
			
			// PHP 5
			//try {
				$_SESSION['message'][$sync]->append($code, $payload);
			//} catch (exception $e) {
			//	return false;
			//}
		}

	}
	
} // end of class

?>