<?php 
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
		
/**
* ErrorHandler
* Custom ErrorHandler for php. Ability to log and send errors over e-mail
* @access	public
* @author	Jacek Materna
*/

class ErrorHandler { 

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
	 * Message object
	 *
	 * @var object
	 * @access public
	 */
	var $msg;
	
	/**
	 * Container to store errors until we decide to print them all
	 *
	 * @var array
	 * @access public
	 */
	var $container;
	 
	/** 
	* Constructor for this class
	* @return void 
	* @access public 
	*/ 
	function ErrorHandler() {  
		
		$this->setFlags(); // false by default
		set_error_handler(array(&$this, 'ERROR_HOOK')); 
		$this->container = array();
		
		/**
		 * check first if the log directory is setup, if not then create a logs dir with a+w && a-r
		 */
		if (!file_exists(AT_CONTENT_DIR . 'logs/') || !realpath(AT_CONTENT_DIR. 'logs/')) {
			$this->makeLogDir();
		} else if (!is_dir(AT_CONTENT_DIR .'logs/')) {
			$this->makeLogDir();
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
		if ($error_type == E_NOTICE || $error_type == E_USER_NOTICE) return;
		
		$val_phpinfo = '';
		$val_phpinfo_foot = '';
		$val_phpinfo_printed  = false; // used to track for the scope of this method whether the server
										// has been attached to a log file or e-mail buffer previously
		
		/**
		 * Only produce the server configuration once per file
		 */
		if ($this->todayLogFileExists() === false) {
			// lets get some info about the system used by all error codes
			ob_start();
			
			// grab usefull data from php_info
			phpinfo(INFO_GENERAL ^ INFO_CONFIGURATION);
			$val_phpinfo .= ob_get_contents();
			ob_end_clean();
			
			/*
			 * Parse $val_phpinfo
			 */
			
			// get a substring of the php info to get rid of the html, head, title, etc.
			$val_phpinfo = substr($val_phpinfo, 760, -19);
			$val_phpinfo = substr($val_phpinfo, 552);
			$val_phpinfo = substr($val_phpinfo, strpos($val_phpinfo, 'System'));
			$val_phpinfo .= chr(10);
			
			$msql_str = '';
			if (defined('MYSQL_NUM'))
				$msql_str = "Yes";
			else
				$msql_str = "No";
			
			$val_phpinfo .= 'MySQL installed? ' . $msql_str . '<br/><br/>';
			
			// replace the </td>'s with tabs and the $nbsp;'s with spaces
			$val_phpinfo = str_replace( '</td>', '    ', $val_phpinfo);
			$val_phpinfo = str_replace( '&nbsp;', ' ', $val_phpinfo);
			$val_phpinfo = str_replace( '</body>', ' ', $val_phpinfo);
			$val_phpinfo = str_replace( '</html>', ' ', $val_phpinfo);
			
			$val_phpinfo = str_replace('This program makes use of the Zend Scripting Language Engine:<br />Zend Engine v1.3.0, Copyright (c) 1998-2003 Zend Technologies', '', $val_phpinfo);
		
			$val_phpinfo_foot .= '$_ENV:' . chr(10) . $this->debug($_ENV) . '<br/><br/>';
		} 
		
		// Everytime
		$val_phpuser = '$_SESSION:' . chr(10) . $this->debug($_SESSION);
		$val_phpuser .= '$_REQUEST:' . chr(10) . $this->debug($_REQUEST);
		$val_phpuser .= '$_COOKIE:' . chr(10) . $this->debug($_COOKIE);
		$val_phpuser .= '$_GET:' . chr(10) . $this->debug($_GET);
		$val_phpuser .= '$_POST:' . chr(10) . $this->debug($_POST);
		
		// replace the </td>'s with tabs and the $nbsp;'s with spaces
		$val_phpuser = str_replace( '</td>', '    ', $val_phpuser);
		$val_phpuser = str_replace( '&nbsp;', ' ', $val_phpuser) . '<br/>';
		
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
				 * List of custom errors go here and the appropriate action is taken
				 *@
				 */
				switch($_error[0]) {
					/**
					 * Custom errors are not guaranteed to be printed for example in footer.inc.php
					 * Hanlde on a case-by-case basis
					 */
					case 'VITAL': // @see vital.inc.php
						if ($this->LOG_ERR_TO_FILE) { 
								if ($val_phpinfo_printed === true) {
									$val_phpinfo = '';
								}
								$this->log_to_files($val_phpinfo, $val_phpinfo_foot, 'ATutor v' . VERSION . '<br/>'. 'PHP ERROR MESSAGE:' . '<br/><p>'
										. $error_msg . ' (error type ' . $error_type . ' in ' 
										. $error_file . ' on line ' . $error_ln . ') [context: ' 
										. $error_context . ']</p>' . chr(10) .chr(10) . $val_phpuser );
										
								$val_phpinfo_printed = true;
								
						} 					
						
						$this->printError('<strong>ATutor has detected an Error<strong> - ' .
														$_error[1]);

						exit; // done here
						break;
						
					case 'BKP_MEMBER': // @see TableBackup.class.php
						if ($this->LOG_ERR_TO_FILE) { 
								if ($val_phpinfo_printed === true) {
									$val_phpinfo = '';
								}
								$this->log_to_files($val_phpinfo, $val_phpinfo_foot, 'ATutor v' . VERSION . '<br/>'. 'PHP ERROR MESSAGE:' . '<br/><p>'
										. $error_msg . ' (error type ' . $error_type . ' in ' 
										. $error_file . ' on line ' . $error_ln . ') [context: ' 
										. $error_context . ']</p>' . chr(10) .chr(10) . $val_phpuser );
										
								$val_phpinfo_printed = true;
								
						}
						
						$this->printError('<strong>ATutor has detected an Error<strong> - ' .
														$_error[1]);
					
						exit;
						break;
					
					default: // standard user error without custom prefix
						if ($this->LOG_ERR_TO_FILE) { 
								if ($val_phpinfo_printed === true) {
									$val_phpinfo = '';
								}
								
								$this->log_to_files($val_phpinfo, $val_phpinfo_foot, 'ATutor v' . VERSION . '<br/>'. 'PHP ERROR MESSAGE:' . '<br/><p>'
										. $error_msg . ' (error type ' . $error_type . ' in ' 
										. $error_file . ' on line ' . $error_ln . ') [context: ' 
										. $error_context . ']</p>' . chr(10) .chr(10) . $val_phpuser);
								
								$val_phpinfo_printed = true;
						} 					
				}
				
				//$this->printError('<strong>ATutor has detected an Error<strong> - ' . 'Problem spot: ' . $error_msg . ' in ' 
				//				. $this->stripbase($error_file) . ' on line ' . $error_ln);
				array_push($this->container, 'Problem spot: ' . $error_msg . ' in ' . $this->stripbase($error_file) . ' on line ' . $error_ln);
									
				break;
			
			case E_WARNING: 
			case E_USER_WARNING: 
				if ($this->LOG_WARN_TO_FILE) { 
					if ($val_phpinfo_printed === true) {
						$val_phpinfo = '';
					}
								
					$this->log_to_files($val_phpinfo, $val_phpinfo_foot, 'ATutor v' . VERSION . '<br/>'. 'PHP ERROR MESSAGE:' . '<br/><p>'
							. $error_msg . ' (error type ' . $error_type . ' in ' 
							. $error_file . ' on line ' . $error_ln . ') [context: ' 
							. $error_context . ']</p>' . chr(10) . chr(10) . $val_phpuser); 		
				
					$val_phpinfo_printed = true;
				}

				//$this->printError('<strong>ATutor has detected an Error</strong> - ' . 'Problem spot: ' . $error_msg . ' in ' 
				//				. $this->stripbase($error_file) . ' on line ' . $error_ln);
				array_push($this->container, 'Problem spot: ' . $error_msg . ' in ' . $this->stripbase($error_file) . ' on line ' . $error_ln);
	
			 	break;
			 default:
		}

		return true; 
	}
	
	/** 
  	* Dump the current error into a file along with an updated profile for that error
  	* 
	* @param string the profile to log
	* @param string the bug to log 
  	* @access public
  	*/
	function log_to_files($profile, $profile_foot, $buf) {
		
		if ($profile == '' || $profile_foor = '' || $buf == '') return;
		
		/**
		 * Redundancy control for profile/error log creation
		 */
		 $profile_created = true;
		 $error_created = true;
		 
		$php_head = '<?php echo \'Only viewable as Admin user\'; exit; ?>' . chr(10);
		
		// Lets make a unqiue profile key, strip away circumventors of the md5 hashing algo. @see md5 algo src
		$temp = strip_tags($profile);
		$temp = stripslashes($temp);
		$temp = str_replace('/', ' ', $temp);
		$temp = str_replace('\$', ' ', $temp);
		$temp = str_replace('$', ' ', $temp);
		$temp = str_replace('\&' , ' ', $temp);
		$temp = str_replace('&' , ' ', $temp);
		$temp = str_replace('*' , ' ', $temp);
		$temp = str_replace('~' , ' ', $temp);
		$temp = str_replace('.' , ' ', $temp);
		$temp = str_replace(';' , ' ', $temp);
		$temp = str_replace(':' , ' ', $temp);
		$temp = str_replace('-' , ' ', $temp);
		$temp = str_replace('_' , ' ', $temp);
		$temp = str_replace('\'' , ' ', $temp);
		$temp = str_replace(',' , ' ', $temp);
		$temp = str_replace('@' , ' ', $temp);
		$temp = str_replace('#' , ' ', $temp);

		$profile_key = md5($temp);
		
		$today = getdate(); 
		
		// Uniqueness assumend to be coupled to epoch timestamp
		$timestamp_ = $today['mon'] . '-' . $today['mday'] . '-' . $today['year'];
		
		/**
		 * Lets make sure we have a log directory made for today
		 */ 
		if (!is_dir(AT_CONTENT_DIR . 'logs/' . $timestamp_)) { // create it
			$result = @mkdir(AT_CONTENT_DIR . 'logs/' . $timestamp_, 0771); // r+w for owner
	
			if ($result == 0) {
				$this->printError('Fatal. Could not create /content/logs' . '/' . $timestamp_ . '. Please resolve');
			}
		} // else already there
		
		/**
		 * Go through all the profiles in our directory and lets try and map our md5 key to one of them,
		 * if its not found then we must be dealing with a new profile, thus create it
		 */
		 $dir_ = AT_CONTENT_DIR . 'logs/' . $timestamp_;
		
		if (!($dir = opendir($dir_))) {
			$msg->printNoLookupFeedback('Could not access /content/logs/' . $timestamp_ . '. Check that the permission for the <strong>Server</string> user are r+w to it');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			
			exit;
		}
		
		/**
		 * Run through the todays logs directory and lets get all the profiles
		 */ 
		$use_profile = null;
		
		// loop through folder todays log folder and try and match a profile to our error profile md5 key
		while (($file = readdir($dir)) !== false) {
		
			/* if the name is not a directory */
			if( ($file == '.') || ($file == '..') || is_dir($file) ) {
				continue;
			}
		
			if (strpos($file, 'profile') >= 0) {
				$check_key = substr($file, strpos($file, '_') + 1);
				$check_key = substr($check_key, 0, strpos($check_key, '.log.php'));

				if ($check_key === $profile_key) { // found!
					$use_profile = $file;
					$profile_created = true;
					break;
				}
			}
		}
		closedir($dir); // clean it up
		
		// if $use_profile == null here then we must create a new profile for this error
		if ($use_profile == null) {
			$use_profile = 'profile_' . $profile_key . '.log.php';
			if ($file_handle = fopen(AT_CONTENT_DIR . 'logs/' . $timestamp_ . '/' . $use_profile, "w")) {
				if (!fwrite($file_handle, $php_head . chr(10) . $profile . $profile_foot)) { $profile_created = false; }
			} else { $profile_created = false; }
			fclose($file_handle);
		} // else just use $use_profile as the profile for this error
		
		// if the creation of the profile_created = false then creation failed and we didnt have an already
		// existant one in the dir, profile must exist
		if ($profile_created === false) return;
		
		$timestamp = $timestamp_ . '_' . $today[0];
					
		// create a unique error filename including the epoch timestamp + and the profile mapping
		$unique_error_log = $timestamp . '_pr' . $profile_key;

		if (is_file(AT_CONTENT_DIR . 'logs/' . $timestamp_ . '/' . $unique_error_log)) {
			$unique_error_log .= rand(); // should be enough
		}
		
		$unique_error_log .= '.log.php'; // append suffix
		
		/* Create error log file */
		if ($file_handle = fopen(AT_CONTENT_DIR . 'logs/' . $timestamp_ . '/' . $unique_error_log, "w")) {
			if (!fwrite($file_handle, $php_head . chr(10) . $buf)) {  $error_created = false;  }
		} else {
			$error_created = false;
		}
		fclose($file_handle);
		
		// check that we created a profile and its error or used an existing profile and created its error
		if ($profile_created === true && $error_created === true) { // ok
			chmod(AT_CONTENT_DIR . 'logs/' . $timestamp_ . '/' . $unique_error_log, 0771);
			chmod(AT_CONTENT_DIR . 'logs/' . $timestamp_ . '/' . $use_profile, 0771);
		} else if ($profile_create === true && $error_created === false) { // remove profile
			unlink(AT_CONTENT_DIR . 'logs/' . $timestamp_ . '/' . $use_profile);
		} 
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
  	* Changes the logging preferences
  	* 
	* @param Boolean $error_flag Log errors to file? 
  	* @param Boolean $warning_flag Log warnings to file? 
  	* @return void 
  	* @access public 
  	*/
	function setFlags($error_flag = true, $warning_flag = true) {				 
		
		$this->LOG_ERR_TO_FILE = $error_flag;
		$this->LOG_WARN_TO_FILE = $warning_flag;
	}
	
	/**
	 * Construct a nicely formatted tree view of a variable
	 * @param var String is the varialbe to construct the output from
	 * @access private
	 */
	function debug($var) {		
		$str_ = '<pre>';
		
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
		
		$str = $str_ . $str;
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
		if (!AT_DEVEL) return;
		
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
	
	/**
	 * Create restricted access logs dir
	 */
	function makeLogDir() {
		
		$result = @mkdir(AT_CONTENT_DIR . 'logs', 0771); // r+w for owner		
		if ($result == 0) {
			$this->printError('Fatal. Could not create /content/logs. Please resolve');
		}
	
	}
	
	/**
	 * Determine wheter a log file exists for today
	 * @access private
	 */
	function todayLogFileExists() {
		$today = getdate(); 

		$timestamp = $today['mon'] . '-' . $today['mday'] . '-' . $today['year'];
		
		return (is_file(AT_CONTENT_DIR . 'logs/' . $timestamp . '.log'));
	}
	
	/**
	 * Run through $container and print all the errors on this page.
	 * Used to prevent errors from breaking content on the page
	 * @access public
	 */
	function showErrors() {

		foreach($this->container as $elem) {
			$this->printError('<strong>ATutor has detected an Error<strong> - ' .
															$elem);
			unset($elem);
		}
	}
} 
?> 