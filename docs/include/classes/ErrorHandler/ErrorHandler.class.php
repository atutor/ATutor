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

require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		
/**
* ErrorHandler
* Custom ErrorHandler for php. Ability to log and send errors over e-mail
* @access	public
* @author	Jacek Materna
*/

class ErrorHandler { 

	/** 
	* Where to email errors to 
	* 
	* @var string 
	* @access public 
	*/ 
	var $to;
	
	/** 
	* Additional addresses for multi destination e-mails
	* 
	* @var array 
	* @access public 
	*/ 
	var $cc_buf; 
	
	/** 
	* Storage for error report to be used by mailError() 
	* 
	* @var string 
	* @access public 
	*/ 
	var $mail_buffer; 
	
	/** 
	* Send errors by email? 
	* 
	* @var Boolean 
	* @access public 
	*/ 
	var $SEND_ERR_TO_MAIL; 
	
	/** 
	* Send warnings by email? 
	* 
	* @var Boolean 
	* @access public 
	*/ 
	var $SEND_WARN_TO_MAIL; 
	
	/** 
	* Send notices by email? 
	* 
	* @var Boolean 
	* @access public 
	*/ 
	var $SEND_NOTE_TO_MAIL; 
	
	/** 
	* Log errors to file?
	* 
	* @var Boolean 
	* @access public 
	*/ 
	var $LOG_ERR_TO_FILE; 
	
	/** 
	* Log warnings to file? 
	* 
	* @var Boolean 
	* @access public  
	*/ 
	var $LOG_WARN_TO_FILE; 
	
	/** 
	* Log notices to file?
	* 
	* @var Boolean 
	* @access public 
	*/ 
	var $LOG_NOTE_TO_FILE;
	
	/** 
	* ATutorMailer obj
	* 
	* @var object
	* @access public 
	*/
	var $mailer;

	/** 
	* Constructor for this class
	* @return void 
	* @access public 
	*/ 
	function ErrorHandler() {  
		$this->setFlags(); // false by default
		$this->mailer = new ATutorMailer;
		set_error_handler(array(&$this, 'ERROR_HOOK')); 
	}
	
	/** 
	* The error handling routine set by set_error_handler(). 
	* Must be as quick as possible. Note a few: '\n' -> chr(10) - avoids inline translation in php engine
	*											 'single quotes', avoid translation again
	* Ability define custom errors. See case 'CUSTOM':
	* 
	* @param string $error_type The type of error being handled. 
	* @param string $error_msg The error message being handled. 
	* @param string $error_file The file in which the error occurred. 
	* @param integer $error_ln The line in which the error occurred. 
	* @param string $error_context The context in which the error occurred.
	* @return Boolean 
	* @access public 
	*/ 
	function ERROR_HOOK($error_type, $error_msg, $error_file, $error_ln, $error_context) { 
		// lets get some info about the system used by all error codes
		ob_start();
		
		// grab usefull data from php_info
		phpinfo(INFO_GENERAL ^ INFO_CONFIGURATION ^ INFO_ENVIRONMENT ^ INFO_VARIABLES);
		$val_phpinfo .= ob_get_contents();
		ob_end_clean();
		
		/*
		 * Parse $val_phpinfo
		 */
		
		// get a substring of the php info to get rid of the html, head, title, etc.
		$val_phpinfo = substr($val_phpinfo, 554, -19);
		$val_phpinfo = substr($val_phpinfo, 552);
		$val_phpinfo .= chr(10);
		$val_phpinfo .= '$_SESSION:' . chr(10) . $this->debug($_SESSION) . chr(10);
		$val_phpinfo .= '$_GET:' . chr(10) . $this->debug($_GET) . chr(10);
		$val_phpinfo .= '$_POST:' . chr(10) . $this->debug($_POST) . chr(10);
		
		// replace the </td>'s with tabs and the $nbsp;'s with spaces
		$val_phpinfo = str_replace( '</td>', '    ', $val_phpinfo);
		$val_phpinfo = str_replace( '&nbsp;', ' ', $val_phpinfo);
		
		// strip the tags
		$val_phpinfo = strip_tags($val_phpinfo);
		$val_phpinfo .= '----------------------------------------------------------------' . chr(10);
		
		switch($error_type) {
			
			case E_ERROR:
			case E_USER_ERROR: 
				if (substr_count(';',$error) > 0) {
					$_error = explode(';', $error);
				} else {
					$_error = array('', $error);
				}
		
			switch($_error[0]) {
				case 'CUSTOM':
					echo 'Custom Error, not defined yet. ' . $_error[1];
					break;
				case 'CUSTOM2':
					echo 'Custom Error, not defined yet. ' . $_error[1];
					break;
				default:
					if ($this->LOG_ERR_TO_FILE) { 
							$this->log_to_file($error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10) . $val_phpinfo ); 
					} 					
					if ($this->SEND_ERR_TO_MAIL) {
						$this->mail_buffer .= $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10) . $val_phpinfo; 
					}
					
					exit;
					break;
			}
			
			case E_WARNING: 
			case E_USER_WARNING: 
				if ($this->LOG_WARN_TO_FILE) { 
					$this->log_to_file($error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10) . $val_phpinfo); 		
				}
				
				if ($this->SEND_WARN_TO_MAIL) {
					$this->mail_buffer .= $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10) . $val_phpinfo; 
				}

				break;
	
			case E_NOTICE: 
			
			case E_USER_NOTICE: 
				if ($this->LOG_NOTE_TO_FILE) { 
					$this->log_to_file($error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10) . $val_phpinfo); 
				}
				
				if ($this->SEND_NOTE_TO_MAIL) {
					$this->mail_buffer .= $err_msg . ' (error type ' . $err_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10) . $val_phpinfo; 
				}
				
				break; 
		}

		return true; 
	}
	
	/** 
ÊÊ	* Dump the error buffer to log file corresponding to days date
	* i.e. 10-30-2004.log will correspond to October 30th, 2004.
ÊÊ	* 
	* @param string the error buffer to log
ÊÊ	* @return void 
ÊÊ	* @access public
ÊÊ	*/
	function log_to_file($buf) {
		$buf = 'ATutor v' . VERSION . chr(10). 'PHP ERROR MESSAGE:' . chr(10) . $buf;
		
		global $_base_href;
	
		$today = getdate(); 

		$timestamp = $today['month'] . '-' . $today['mday'] . '-' . $today['year'];

		$buf = $buf;
		
		if ($file_handle = fopen($_base_href . 'logs/' . $timestamp . '.log', "a")) {
			if (!fwrite($file_handle, $buf)) echo 'could not write to file';
		} else {
			echo 'could not open file';
		}
		
		fclose($file_handle);
	}

	/** 
ÊÊ	* Restores the error handler to the default error handler 
ÊÊ	* 
ÊÊ	* @return void 
ÊÊ	* @access public
ÊÊ	*/
	function restoreOrigHandler() {
		restore_error_handler();
	}

	/** 
Ê	* Returns the error handler to ERROR_HOOK() 
Ê	* 
ÊÊ	* @return void 
ÊÊ	* @access public  
ÊÊ	*/
	function returnHandler() {
		set_error_handler(array(&$this, 'ERROR_HOOK'));
	}

	/** 
ÊÊ	* Dump the current mail_buffer into an e-mail and send to set destinations
ÊÊ	* 
ÊÊ	* @return void 
ÊÊ	* @access public 
ÊÊ	*/
	function mailError() { 
		$this->mailer->From     = 'ErrorHandler';
		$this->mailer->FromName = 'ErrorHandler';
		$this->mailer->AddAddress = $to;
		
		foreach($this->cc_buf as $e)
				$this->mailer->addCC($e);
				
		$this->mailer->Subject = 'Error Report';
		$this->mailer->Body    = $mail_buffer;

		if(!$this->mailer->Send()) {
		   echo 'There was an error sending the message';
		   exit;
		}
	}
	
	/** 
ÊÊ	* Change the destination(s) of the log e-mail
ÊÊ	* 
ÊÊ	* @param string|array $names Destination(s) of log e-mail
ÊÊ	* @return void 
ÊÊ	* @access public 
ÊÊ	*/
	function setRecipients($names) {
		if (is_array($names)) {
			
			$first = array_shift($names); // first one is to address
			$this->$to = $first;
			
			$this->cc_buf = $names; // rest is cc'd
				
		} else {
			$this->to = $names;
		}
	}

	/** 
ÊÊ	* Changes the logging preferences
ÊÊ	* 
	* @param Boolean $error_flag Log errors to file? 
ÊÊ	* @param Boolean $warning_flag Log warnings to file? 
ÊÊ	* @param Boolean $notice_flag Log notices to file? 
ÊÊ	* @param Boolean $error_mailflag Send errors via mail? 
ÊÊ	* @param Boolean $warning_mailflag Send warnings via mail? 
ÊÊ	* @param Boolean $notice_mailflag Send notices via mail? 
ÊÊ	* @return void 
ÊÊ	* @access public 
ÊÊ	*/
	function setFlags($error_flag = true, $warning_flag = true, $notice_flag = treu, 
				$error_mailflag = false, $warning_mailflag = false, $notice_mailflag = false) {				 
		
		$this->LOG_ERR_TO_FILE = $error_flag;
		$this->LOG_WARN_TO_FILE = $warning_flag;
		$this->LOG_NOTE_TO_FILE = $notice_flag;
		$this->SEND_ERR_TO_MAIL = $error_mailflag;
		$this->SEND_WARN_TO_MAIL = $warning_mailflag;
		$this->SEND_NOTE_TO_MAIL = $notice_mailflag;
	}
	
	function debug($var) {
		if (!AT_DEVEL) {
			return;
		}
		
		ob_start();
		print_r($var);
		$str = ob_get_contents();
		ob_end_clean();
	
		$str = str_replace('<', '&lt;', $str);
	
		$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
		$str = str_replace(']', ']</span>', $str);
		$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
		$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
		$str .= '</pre>';
		return $str;
	}
} 
?> 