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
* (Abstract ? PHP 5) Class defining root of _Message hierarchy. Non-instantiable.
* @access	public
* @author	Jacek Materna
*/

// PHP 5
//abstract class Message {

class Message {
	/*
	* Where to store a Message in $_SESSION
	* @access private
	* @var string	
	*/
	var $_name;
	
	/*
	* xhtml compliant header of a Message
	* @access private
	* @var string
	*/
	var $_header = '';
	
	/*
	* xhtml compliant footer of a Message
	* @access private
	* @var string
	*/
	var $_footer = '';
	
	// Parent constructor
	function Message($_name, $_header, $_footer) { 
		$this->_name = $_name;
		$this->_header = $_header;
		$this->_footer = $_footer;
	} 
	
	/**
	* Add message to be tracked by session obj for this(type of Message). Payload must already be
	* be translated to language spec. This is a temporary solution. Once the Cache code and DB access
	* for the system is moved to OOA then this can change to have THIS class take care of translating
	* the message to a lang spec as well as take care of cacheing.
	* @access  public
	* @param   string $message				code of the message
	* @param   string(array) $payload		translated message(s) for code $message
	* @return  boolean						true|false depending on success
	* @author  Jacek Materna
	*/
	function addMessageTranslatedPayload($message, $payload) { // common to all children
		/* 
		 *	- ASSERTION -
		 *  $payload must be the appropriate language translated message for code $message
		 *  @see getTranslatedCodeStr():/include/lib/output.inc.php
		*/
		
		// handle bad format
		if (!is_string($message)) {
			settype($message, "string");
		}
		
		if (!isset($_SESSION[$this->_name]) || count($_SESSION[$this->name]) == 0) { // fresh
			
			// PHP 5 
			//try {
				$_SESSION[$this->_name] = array($message => $payload);
			//} catch (Exception $e) {
			//	return false;
			//}
		} else {
		
			// Already an array there, could be empty or have something in it, append.
			// Store key = value for much faster unset as needed 
			// @see getMessageToPrint()
			
			// PHP 5
			//try {
				$_SESSION[$this->_name]->append($message, $payload);
			//} catch (exception $e) {
			//	return false;
			//}
		}
		
		return true;
	}
	
	/**
	* Return xhtml compliant string of all this_Messages to display which are currently in session obj
	* @access  public
	* @return  string 		The xhtml compliant string of all the ErrorMessages to show
	* @author  Jacek Materna
	*/
	function getMessageToPrint() {
		// Go through session var for $name and lets construct message xhtml
		$result = '';
		$body = '';
		
		// if there are more than 1 Message's to print
		$result_app = (count($_SESSION[$this->_name]) == 1) ? '<br />' :  '';
		
		while( list($key, $item) = each($_SESSION[$this->_name]) ) {

			if (is_object($item)) {
				/* this is a PEAR::ERROR object.	*/
				/* for backwards compatability.		*/
				$body .= $item->get_message();
				$body .= '.<p>';
				$body .= '<small>';
				$body .= $item->getUserInfo();
				$body .= '</small></p>';
		
			} else if (is_array($item)) {
				/* this is an array of items */
				$body .= '<ul>';
				foreach($item as $e => $info){
					$body .= '<li><small>'.$info.'</small></li>';
				}
				$body .= '</ul>';
			} else {
				/* Single item in the message */
				$body .= '<ul>';
				$body .= '<li><small>'. $item.'</small></li>';
				$body .='</ul>';
			}
      		
      		$result .= $this->_header . $body . $this->_footer;
      		
      		/* unset it from session */
      		
      		// PHP 5
      		//try {
      		
      		unset($_SESSION[$this->_name][$key]);
      		
      		//} catch (Exception $e) {
      		//	return '';
      		//}
      		
   		}
   		
   		return $result . $result_app;
	
	}
	
}

?>