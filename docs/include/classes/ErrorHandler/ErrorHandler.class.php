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
	 * Message object
	 *
	 * @var object
	 * @access public
	 */
	var $msg;

	/** 
	* Constructor for this class
	* @return void 
	* @access public 
	*/ 
	function ErrorHandler() {  
		
		$this->setFlags(); // false by default
		set_error_handler(array(&$this, 'ERROR_HOOK')); 

		// Check that log system is setup		
		$to_root = AT_CONTENT_DIR;

		$pos_last = strpos($to_root, "content");
		$to_root = substr($to_root, 0, $pos_last);

		/**
		 * check first if the log directory is setup
		 */
		 if(!file_exists($to_root . 'pub/logs/') || !realpath($to_root . 'pub/logs/')) {
			$this->printError('<strong>/pub/logs</strong> does not exist. Please create.');
		} else if (!is_dir($to_root . 'pub/logs/')) {
			$this->printError('<strong>/pub/logs</strong> is not a directory. Please create.');
		} else if (!is_writable($to_root . 'pub/logs/')){
			$this->printError('<strong>/pub/logs</strong> is not writable. Please change permissions.');
		}
		
	}
	
	/** 
	* The error handling routine set by set_error_handler(). Mimicking and Exception implementation in OOA
	* Must be as quick as possible. Note a few: '\n' -> chr(10) - avoids inline translation in php engine
	*											 'single quotes', avoid translation again
	* Ability define custom errors. See case 'VITAL':
	* 
	* eg call from script, trigger_error('VITAL#There was a problem with the database.','E_USER_ERROR');
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
		
		$msql_str = '';
		if (defined('MYSQL_NUM'))
			$msql_str = "Yes";
		else
			$msql_str = "No";
		
		$val_phpinfo .= 'MySQL installed? ' . $msql_str . chr(10);
		$val_phpinfo .= '$_SESSION:' . chr(10) . $this->debug($_SESSION) . chr(10);
		$val_phpinfo .= '$_GET:' . chr(10) . $this->debug($_GET) . chr(10);
		$val_phpinfo .= '$_POST:' . chr(10) . $this->debug($_POST) . chr(10);
		
		// replace the </td>'s with tabs and the $nbsp;'s with spaces
		$val_phpinfo = str_replace( '</td>', '    ', $val_phpinfo);
		$val_phpinfo = str_replace( '&nbsp;', ' ', $val_phpinfo);
		$val_phpinfo = str_replace('This program makes use of the Zend Scripting Language Engine:<br />Zend Engine v1.3.0, Copyright (c) 1998-2003 Zend Technologies', '', $val_phpinfo);
		
		// strip the tags
		$val_phpinfo = strip_tags($val_phpinfo);
		
		$val_phpinfo .= '######################################################################' . chr(10);
		
		switch($error_type) {
			
			case E_ERROR: // caught below
			case E_USER_ERROR: 

				if (substr_count($error_msg, "#") > 0) {
					$_error = explode("#", $error_msg);
				} else {
					$_error = array('', $error_msg);
				}

				/**
				 * eg call, trigger_error('VITAL;There was a problem with the database.',E_USER_ERROR);
				 *
				 *List of custom errors go here and the appropriate action is taken
				 *@
				 */
				switch($_error[0]) {
					case 'VITAL': // if a custom type
						
						if ($this->LOG_ERR_TO_FILE) { 
								$this->log_to_file($error_msg . ' (error type ' . $error_type . ' in ' 
										. $error_file . ' on line ' . $error_ln . ') [context: ' 
										. $error_context . ']' . chr(10) .chr(10) . $val_phpinfo );
								
						} 					
						if ($this->SEND_ERR_TO_MAIL) {
							$this->mail_buffer .= $error_msg . ' (error type ' . $error_type . ' in ' 
										. $error_file . ' on line ' . $error_ln . ') [context: ' 
										. $error_context . ']' . chr(10) . chr(10) . $val_phpinfo; 
						}
						
						$this->printError('<strong>ATutor has detected an Error<strong> - ' .
														$_error[1]);

						exit; // done here
						break;
						
					default:
						if ($this->LOG_ERR_TO_FILE) { 
								$this->log_to_file($error_msg . ' (error type ' . $error_type . ' in ' 
										. $error_file . ' on line ' . $error_ln . ') [context: ' 
										. $error_context . ']' . chr(10) .chr(10) . $val_phpinfo );
								
						} 					
						if ($this->SEND_ERR_TO_MAIL) {
							$this->mail_buffer .= $error_msg . ' (error type ' . $error_type . ' in ' 
										. $error_file . ' on line ' . $error_ln . ') [context: ' 
										. $error_context . ']' . chr(10) . chr(10) . $val_phpinfo; 
						}
				}
				
				$this->printError('<strong>ATutor has detected an Error<strong> - ' . 'Problem spot: ' . $error_msg . ' in ' 
								. $this->stripbase($error_file) . ' on line ' . $error_ln);
										
				break;
			
			case E_WARNING: 
			
			/* Too much output > 2M log file in 1 script file
			case E_NOTICE: 
			case E_USER_NOTICE: 
				if ($this->LOG_WARN_TO_FILE) { 
					$this->log_to_file($error_msg . ' (error type ' . $error_type . ' in ' 
							. $error_file . ' on line ' . $error_ln . ') [context: ' 
							. $error_context . ']' . chr(10) . chr(10) . $val_phpinfo); 		
				}
				
				if ($this->SEND_WARN_TO_MAIL) {
					$this->mail_buffer .= $error_msg . ' (error type ' . $error_type . ' in ' 
							. $error_file . ' on line ' . $error_ln . ') [context: ' 
							. $error_context . ']' . chr(10) . chr(10) . $val_phpinfo; 
				}

				$this->printError('<strong>ATutor has detected an Error</strong> - ' . 'Problem spot: ' . $error_msg . ' in ' 
								. $this->stripbase($error_file) . ' on line ' . $error_ln);
				*/
			case E_USER_WARNING: 
				if ($this->LOG_WARN_TO_FILE) { 
					$this->log_to_file($error_msg . ' (error type ' . $error_type . ' in ' 
							. $error_file . ' on line ' . $error_ln . ') [context: ' 
							. $error_context . ']' . chr(10) . chr(10) . $val_phpinfo); 		
				}
				
				if ($this->SEND_WARN_TO_MAIL) {
					$this->mail_buffer .= $error_msg . ' (error type ' . $error_type . ' in ' 
							. $error_file . ' on line ' . $error_ln . ') [context: ' 
							. $error_context . ']' . chr(10) . chr(10) . $val_phpinfo; 
				}

				$this->printError('<strong>ATutor has detected an Error</strong> - ' . 'Problem spot: ' . $error_msg . ' in ' 
								. $this->stripbase($error_file) . ' on line ' . $error_ln);
	
			 	break;
			 default:
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
		
		$to_root = AT_CONTENT_DIR;

		$pos_last = strpos($to_root, "content");
		$to_root = substr($to_root, 0, $pos_last);

		$buf = 'ATutor v' . VERSION . chr(10). 'PHP ERROR MESSAGE:' . chr(10) . $buf;
		
		$today = getdate(); 

		$timestamp = $today['mon'] . '-' . $today['mday'] . '-' . $today['year'];

		$buf = $buf;

		if ($file_handle = fopen($to_root . 'pub/logs/' . $timestamp . '.log', "a")) {
			if (!fwrite($file_handle, $buf)) { /*echo 'could not write to file'; */ }
		} else {
			//echo 'could not open file';
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
		
		require_once(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		$this->mailer =& new ATutorMailer;
		
		if (isset($to)) {
			$this->mailer->From     = 'ErrorHandler';
			$this->mailer->FromName = 'ErrorHandler';
			$this->mailer->AddAddress = $to;
			
			// add to CC list
			foreach($this->cc_buf as $e)
					$this->mailer->addCC($e);
					
			$this->mailer->Subject = 'Error Report';
			$this->mailer->Body    = $mail_buffer;
	
			if(!$this->mailer->Send()) {
			   //echo 'There was an error sending the message';
			   exit;
			}
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
			
			$first = array_shift($names); // first one is 'to' address
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
	function setFlags($error_flag = true, $warning_flag = true, $notice_flag = false, 
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
	
	/**
	 * Function which strips the path base off a file URL since it is a security risk
	 * @param String str is the path string
	 * @return String only the script filename where the error occured
	 */
	function stripbase($str) {
		
		$to_root = $_SERVER["PATH_TRANSLATED"];
		
		$pos_last = strrpos($to_root, "/");
		$to_root = substr($to_root, $pos_last + 1);
		return $to_root;
	}
	
	/**
	 * Print the error to the browser, dont use any templates or css sheets for flexibility
	 * @access private
	 */
	function printError($str) {

		echo '<br />';
		echo '<table bgcolor="#FF0000" border="0" cellpadding="3" cellspacing="2" width="90%" summary="" align="center">';
		echo '<tr bgcolor="#FEF1F1" align="top">';
		echo '<td>';
		echo '<h3><span style="font-family: arial verdana">Internal Error Detected</span></small></h3>';
		echo '<ul>';
		echo '<li><small><span style="font-family: arial verdana">'. $str .'</span></small></li>';
		echo'</ul>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '<br />';
	}
} 
?> 