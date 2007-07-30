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
	* Reference to savant obj.
	* @access private
	* @see /include/classes/Savant/Savant.php
	* @var object	
	*/
	var $savant;
	
	/*
	* Stastic assoc. array of message types mapped to Savant template file names
	* @access private
	* @see /templates/
	* @var array
	*/
	var $tmpl = array(	'error' => 'errormessage.tmpl.php',
						'feedback' => 'feedbackmessage.tmpl.php',
						'warning' => 'warningmessage.tmpl.php',
						'info' => 'infomessage.tmpl.php',
						'help' => 'helpmessage.tmpl.php',
						'confirm' => 'confirmmessage.tmpl.php'
				);
	
	/*
	* Static assoc array of message types mapped to Language code prefixes
	* @access private
	* @see /include/lib/lang_constant.inc.php
	* @var array	
	*/
	var $prefix = array( 'error'  =>'AT_ERROR_',
						'feedback' => 'AT_FEEDBACK_',
						'warning' => 'AT_WARNING_',
						'info' => 'AT_INFOS_',
						'help' => 'AT_HELP_',
						'confirm' => 'AT_CONFIRM_'
				  );
	
	/**
	* Constructor
	* @access  public
	* @param   obj $savant Reference to Savant object
	* @author  Jacek Materna
	*/
	function Message($savant) { 
		$this->savant = $savant;
	} 
		
	/**
	* Print message(s) of type $type. Processes stored messages in session var for type $type
	* and translates them into language spec. Then passes processed data to savant template for display
	* @access  public
	* @param   string $type					error|warning|info|feedback|help|help_pop
	* @author  Jacek Materna
	*/
	function printAbstract($type) {
		if (!isset($_SESSION['message'][$type])) return;

		$_result = array();
		
		foreach($_SESSION['message'][$type] as $e => $item) {
			$result = '';

			if ($type == 'confirm') {
				// the confirm msg's have the hidden vars as the last element in the array
				$last_item = array_pop($item);
				if (count($item) == 1) {
					$item = $item[0];
				}
			}

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
		
		if (count($_result) > 0) {
			$this->savant->assign('item', $_result);	// pass translated payload to savant var for processing

			if ($type == 'confirm') {
				$this->savant->assign('hidden_vars', $last_item);
				
			} else if ($type == 'help') { // special case for help message, we need to check a few conditions
				$a = (!isset($_GET['e']) && !$_SESSION['prefs']['PREF_HELP'] && !$_GET['h']);
				$b = ($_SESSION['prefs']['PREF_CONTENT_ICONS'] == 2);
				$c = isset($_GET['e']);
				$d = $_SESSION['course_id'];
				
				$this->savant->assign('a', $a);
				$this->savant->assign('b', $b);
				$this->savant->assign('c', $c);
				$this->savant->assign('d', $d);
			}
		
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
				settype($e, "string");
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
			// existing data is either a collection or a single node
			if(is_array($_SESSION['message'][$sync][$first])) { // already an array there
				if (is_array($payload)) {
					// lets ignore the code, its already there as the first element
					$elem = array_shift($payload);
					foreach($payload as $elem) {
						array_push($_SESSION['message'][$sync][$first], $elem); // add ourselves to the chain
					}
				} else // no array here yet
					$_SESSION['message'][$sync][$first][] = $payload; // add ourselves 
				
			} else { // just a string
				if (is_array($payload)) {
					$temp = $_SESSION['message'][$sync][$first]; // grab it
					unset($_SESSION['message'][$sync][$first]); // make sure its gone
					
					$arr = array($temp);
					
					// skip first elem, we're asserting here that $first === $payload[0]
					$grb = array_shift($payload);
					foreach($payload as $elem) { // lets finish building the array
						array_push($arr, $elem);
					}
					
					$_SESSION['message'][$sync][$first] = $arr; // put it back 
				}
			}
		} else {
		
			// Already an array there, could be empty or have something in it, append.
			// Store key = value for much faster unset as needed 
			
			// PHP 5
			//try {
				$new = array($first => $payload);
				$final = array_merge((array) $_SESSION['message'][$sync], (array) $new);

				unset($_SESSION['message'][$sync]);
				$_SESSION['message'][$sync] = $final;
			//} catch (exception $e) {
			//	return false;
			//}
		}
	}
	
	/**
	* Simply check is a type $type message isset in the session obj
	* @access  public
	* @param   string $type					what type of message to check for
	* @author  Jacek Materna
	*/
	function abstractContains($type) {
		return (isset($_SESSION['message'][$type]));
	}
	
	/**
	* Deletes the tracked message code $code from the Session obj as well as all 
	* if its children
	* @access  public
	* @param   string $type					what type of message to delete
	# @param   string $code					The code to delete
	* @author  Jacek Materna
	*/
	function abstractDelete($type, $code) {
		if (!is_string($code))
			settype($code, "string");

		// Lets append the right prefic to this code for searching
		$code = $this->prefix[$type] . $code;
	
		if(isset($_SESSION['message'][$type][$code])) {
			unset($_SESSION['message'][$type][$code]); // delete it and its children
		}
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
	
	/**
	* Print error messages using Savant template
	* @access  public
	* @author  Jacek Materna
	*/
	function printErrors($optional=null) {
		if ($optional != null)  // shortcut
			$this->addAbstract('error', $optional);

		$this->printAbstract('error');
	}
	

	function addConfirm($code, $hidden_vars = '') {
		$hidden_vars_string = '';
		if (is_array($hidden_vars)) {
			foreach ($hidden_vars as $key => $value) {
				$hidden_vars_string .= '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
			}
		}
		if (!is_array($code)) {
			$code = array($code);
		}
		$code[] = $hidden_vars_string;
		$this->addAbstract('confirm', $code);
	}
	
	function printConfirm($optional=null) {
		if ($optional != null)  // shortcut
			$this->addAbstract('confirm', $optional);

		$this->printAbstract('confirm');
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
	
	/**
	* Print warning messages using Savant template
	* @access  public
	* @author  Jacek Materna
	*/
	function printWarnings($optional=null) {
		if ($optional != null)  // shortcut
			$this->addAbstract('warning', $optional);
		
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
	
	/**
	* Print info messages using Savant template
	* @access  public
	* @author  Jacek Materna
	*/
	function printInfos($optional=null) { 
		if ($optional != null)  // shortcut
			$this->addAbstract('info', $optional);

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
	
	/**
	* Print feedback messages using Savant template
	* @access  public
	* @author  Jacek Materna
	*/
	function printFeedbacks($optional=null) {
		if ($optional != null) // shortcut
			$this->addAbstract('feedback', $optional); 
			
		$this->printAbstract('feedback');
	}
	
	/**
	* Add help message to be tracked by session obj
	* @access  public
	* @param   string|array $code			code of the message or array(code, args...)
	* @author  Jacek Materna
	*/
	function addHelp($code) { 
		$this->addAbstract('help', $code);
	}
	
	/**
	* Print help messages using Savant template
	* @access  public
	* @author  Jacek Materna
	*/
	function printHelps($optional=null) {
		if ($optional != null)  // shortcut
			$this->addAbstract('help', $optional);
			
		$this->printAbstract('help');
	}
	 
	/**
	* Dump all the messages in the session to the screen in the following order
	* @access  public
	* @author  Jacek Materna
	*/
	function printAll() {
		$this->printAbstract('feedback');
		$this->printAbstract('error');
		$this->printAbstract('warning');
		$this->printAbstract('help');
		$this->printAbstract('info');
	}
	
	/**
	* Print feedback message using Savant template with no Session dialog and
	* no database dialog, straight text inside feedback box
	* @access  public
	* @param String String message to display inside feedback box
	* @author  Jacek Materna
	*/
	function printNoLookupFeedback($str) {
		if (str != null) {
			$this->savant->assign('item', array($str));	// pass string to savant var for processing
			$this->savant->display($this->tmpl['feedback']);
		}
	}
	
	/**
	 * Method which simply check if a particular message type exists in the session obj
	 */
	function containsErrors() {
		return $this->abstractContains('error');
	}
	
	function containsFeedbacks() {
		return $this->abstractContains('feedback');
	}
	
	function containsWarnings() {
		return $this->abstractContains('warning');
	}
	
	function containsInfos() {
		return $this->abstractContains('info');
	}
	
	function containsHelps() {
		return $this->abstractContains('help');
	}
	
	/**
	 * Method that allow deletion of individual Message codes form the Session obj
	 */
	function deleteError($code) {
		$this->abstractDelete('error', $code);
	}
	
	function deleteFeedback($code) {
		$this->abstractDelete('feedback', $code);
	}
	
	function deleteWarning($code) {
		$this->abstractDelete('warning', $code);
	}
	
	function deleteInfo($code) {
		$this->abstractDelete('info', $code);
	}
	
	function deleteHelp($code) {
		$this->abstractDelete('help', $code);
	}
	
} // end of class



?>