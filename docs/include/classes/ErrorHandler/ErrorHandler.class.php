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
* Custom ErrroHandler for php. Ability to log and send errors over e-mail
* @access	public
* @author	Jacek Materna
*/
class ErrorHandler { 
	/** 
	* The log file filenane
	* 
	* NOTE: $error_log_filename will only be used if you have log_errors Off and ;error_log filename in php.ini 
	* if log_errors is On, and error_log is set, the filename in error_log will be used. 
	* 
	* @var string 
	* @access public 
	*/ 
	var $error_log_filename; 

	/** 
	* Where to email errors to 
	* 
	* @var string 
	* @access public 
	*/ 
	var $to; 
	
	/** 
	* Storage for error report to be used by mailError() 
	* 
	* @var string 
	* @access public 
	*/ 
	var $mail_buffer; 
	
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
	* Constructor for this class
	* @return void 
	* @access public 
	*/ 
	function ErrorHandler() { 
		$this->sendMailTo( 'default', 'default@email.com' ); 
	    $this->setFilename( 'ErrorHandler_log.log' ); 
	    $this->setFlags(); 
	    set_error_handler( array( &$this, 'ERROR_HOOk' ) ); 
	
		// PHP 4 does not support destructors thus this is a workaround
	    register_shutdown_function( array( &$this, 'ErrorHandlerDestruct' ) ); 
	} 

	function ErrorHandlerDestruct() { 
		if( strlen( $this->mail_buffer ) > 0 ) { 
		    $this->mailError( $this->mail_buffer ); //Send the email 
		} 
	}
	
	/** 
	* The error handling routine set by set_error_handler(). Caqn define custom errors
	* here. Look at case 'CUSTOM':
	* 
	* @param string $error_type The type of error being handled. 
	* @param string $error_msg The error message being handled. 
	* @param string $error_file The file in which the error occurred. 
	* @param integer $error_line The line in which the error occurred. 
	* @param string $error_context The context in which the error occurred.
	* @return Boolean 
	* @access public 
	*/ 
	function ERROR_HOOK($error_type, $error_msg, $error_file, $error_line, $error_context) { 
		switch( $error_type ) {
		 
		    case E_ERROR: 
		    
			case E_USER_ERROR: 
				if (substr_count(';',$error) > 0) {
					$_error = explode(';',$error);
				} else {
					$_error = array('',$error);
				}
		
			switch($_error[0]) {
				case 'CUSTOM':
					echo 'Custom Error, not defined yet. ' . $_error[1];
					break;
				default:
					if ($this->LOG_ERR_TO_FILE) { 
					       if( $this->error_log_filename == '') { 
					         error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ') [context: ' . $error_context . ']' . chr(10), 0); 
					       } else { 
					         error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ') [context: ' . $error_context . ']' . chr(10), 3, $this->error_log_filename); 
					       } 
					} 
					
					if ($this->SEND_ERR_TO_MAIL) { 
					     $this->mail_buffer .= $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ') [context: ' . $error_context . ']' . chr(10); 
					} 
					     echo $error_msg,' (error type ',$error_type,' in ',$error_file,' on line ',$error_line,') [context: ',$error_context,']<br />'; 
					     exit; 
					     break;
					}
		      
		    case E_WARNING: 
		     
			case E_USER_WARNING: 
				if($this->LOG_WARN_TO_FILE) { 
					if($this->error_log_filename == '') { 
		         		error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ') [context: ' . $error_context . ']' . chr(10), 0); 
		       		} else { 
		         		error_log( $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ') [context: ' . $error_context . ']' . chr(10), 3, $this->error_log_filename); 
		       		} 
		     	} 
		     
				if($this->SEND_WARN_TO_MAIL) { 
		         	$this->mail_buffer .= $error_msg . ' (error type ' . $error_type . ' in ' . $error_file . ' on line ' . $error_line . ') [context: ' . $error_context . ']' . chr(10); 
		     	} 
		    
				echo $error_msg,' (error type ',$error_type,' in ',$error_file,' on line ',$error_line,') [context: ',$error_context,']<br />'; 
		    	break; 
		     
			case E_NOTICE: 
		    
			case E_USER_NOTICE: 
		     	if($this->LOG_NOTE_TO_FILE) { 
		      		if($this->error_log_filename == '') { 
		         		error_log( $err_msg . ' (error type ' . $err_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10), 0); 
		       		} else { 
		        		error_log( $err_msg . ' (error type ' . $err_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10), 3, $this->error_log_filename); 
		       		} 
		     	} 
		     
				if($this->SEND_NOTE_TO_MAIL) { 
		    		$this->mail_buffer .= $err_msg . ' (error type ' . $err_type . ' in ' . $error_file . ' on line ' . $error_ln . ') [context: ' . $error_context . ']' . chr(10); 
		     	} 
		     
				echo $err_msg,' (error type ',$err_type,' in ',$error_file,' on line ',$error_ln,') [context: ',$error_context,']<br />'; 
		     	break; 
		} 
		
		return true; 
	} 

  	

  	/** 
  	* Restores the error handler to the default error handler 
  	* 
  	* @return void 
  	* @access public
  	*/ 
  	function restoreOrigHandler() { 
     	 restore_error_handler(); 
  	}

  	/** 
 	* Returns the error handler to ERROR_HOOK() 
 	* 
  	* @return void 
  	* @access public  
  	*/ 
  	function returnHandler() { 
    	set_error_handler(array(&$this, 'ERROR_HOOK')); 
 	} 

  	/** 
  	* This will print the associative array populated by backtrace data 
  	* 
  	* @return void 
  	* @access public  
  	*/ 
  	function showBacktrace() { 
      	debug_print_backtrace(); 
  	} 

  	/** 
  	* Method that is used to send error reports via email 
  	* 
  	* @param string $mail_body Error message 
  	* @return void 
  	* @access public 
  	*/ 
  	function mailError($mail_body) { 
	    $headers ='From: ErrorHandler.class.php' . "\r\n"; 
	    $headers .= 'MIME-Version: 1.0' . "\r\n"; 
	    $headers .= 'Content-type: text/plain; charset=iso-8859-1' . "\r\n"; 
	    $subject = 'Error Report'; 
	    $body = $mail_body; 
	    mail( $this->to, $subject, $body, $headers ); 
  	}

	/** 
  	* Changes the filename of the generated log file. 
  	* 
  	* @param string $filename The filename to be used for the log file. 
  	* @return void 
  	* @access public 
  	*/ 
  	function setFilename($file_name) { 
     	$this->error_log_filename = $file_name;
  	}
 
  	/** 
  	* Changes the logging preferences
  	* 
	* @param Boolean $error_flag Log errors to file? 
  	* @param Boolean $warning_flag Log warnings to file? 
  	* @param Boolean $notice_flag Log notices to file? 
  	* @param Boolean $error_mailflag Send errors via mail? 
  	* @param Boolean $warning_mailflag Send warnings via mail? 
  	* @param Boolean $notice_mailflag Send notices via mail? 
  	* @return void 
  	* @access public 
  	*/ 
  	function setFlags( $error_flag = true, $warning_flag = true, $notice_flag = true, 
				$error_mailflag = true, $warning_mailflag = true, $notice_mailflag = true ) {
				 
      	$this->LOG_ERR_TO_FILE = $error_flag;          
    	$this->LOG_WARN_TO_FILE = $warning_flag;          
    	$this->LOG_NOTE_TO_FILE = $notice_flag;            
    	$this->SEND_ERR_TO_MAIL = $error_mailflag;       
    	$this->SEND_WARN_TO_MAIL = $warning_mailflag;   
    	$this->SEND_NOTE_TO_MAIL = $notice_mailflag;     
  	}
 
  	/** 
  	* This method will setup php to send mail via mail() on a Windows server 
  	* 
  	* @param string $smtp_server Your SMTP Server address 
  	* @param string $from Your email address 
  	* @return void 
  	* @access public 
  	*/ 
  	function setupWindowsMail($smtp_server, $from) { 
      	ini_set( 'SMTP', $smtp_server ); 
      	ini_set( 'sendmail_from', $from ); 
 	}

	/** 
	* Set the e-amil destination of an error message
	* 
	* @param string $recipient The name of the recipient. 
	* @param string $recipient_add The email address of the recipient. 
	* @return void 
	* @access public  
	*/ 
	function sendMailTo($recipient, $recipient_add) { 
		$this->to = $recipient . ' <'. $recipient_add .'>'; 
	} 
  
	/** 
  	* This method will setup php to send mail via mail() on a Linux server 
  	* 
  	* Usually /usr/bin/sendmail 
  	* 
  	* @param string $sendmailpath Path to sendmail on your server, and whatever flags you wish to use with sendmail. 
  	* @return void 
  	* @access public  
  	*/ 
  
	function setupLinixMail($sendmailpath) { 
      	ini_set( 'sendmail_path', $sendmailpath ); 
  	} 

} 
?> 
