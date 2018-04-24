<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
        $root_path =  preg_replace ('#/get.php#','',$_base_path);
        $this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$root_path);
        require($this_path.'include/classes/phpmailer/class.phpmailer.php');

/**
* ATutorMailer extends PHPMailer and sets all the default values
* that are common for ATutor.
* @access  public
* @see     include/classes/phpmailer/class.phpmailer.php
* @since   ATutor 1.4.1
* @author  Joel Kronenberg
*/
class ATutorMailer extends PHPMailer {

	/**
	* The constructor sets whether to use SMTP or Sendmail depending
	* on the value of MAIL_USE_SMTP defined in the config.inc.php file.
	* @access  public
	* @since   ATutor 1.4.1
	* @author  Joel Kronenberg
	*/
	function ATutorMailer() {
		if (MAIL_USE_SMTP) {
			$this->IsSMTP(); // set mailer to use SMTP
			$this->Host = ini_get('SMTP');  // specify main and backup server
		} else {
			$this->IsSendmail(); // use sendmail
			$this->Sendmail = ini_get('sendmail_path');
		}

		$this->SMTPAuth = false;  // turn on SMTP authentication
		$this->IsHTML(false);

		// send the email in the current encoding:
		global $myLang;
		$this->CharSet = $myLang->getCharacterSet();
	}

	/**
	* Appends a custom ATutor footer to all outgoing email then sends the email.
	* If mail_queue is enabled then instead of sending the mail out right away, it 
	* places it in the database and waits for the cron to send it using SendQueue().
	* The mail queue does not support reply-to, or attachments, and converts all BCCs
	* to regular To emails.
	* @access  public
	* @return  boolean	whether or not the mail was sent (or queued) successfully.
	* @see     parent::send()
	* @since   ATutor 1.4.1
	* @author  Joel Kronenberg
	*/
	function Send() {
		global $_config;

		// attach the ATutor footer to the body first:
		$this->Body .= 	'<br style="float:left;clear:both;"/><hr style="float:left;clear:both;width:95%;"/><br />';
		$this->Body .= "<p>"._AT('sent_via_atutor', AT_BASE_HREF);
		if ($_SESSION['course_id'] > 0) {
			$this->Body .= 'login.php?course='.$_SESSION['course_id'].' | <a href="'.AT_BASE_HREF.'login.php?course='.$_SESSION['course_id'].'">' . $_SESSION['course_title'].'</a>';
		}

		//$this->Body .= "<br />"._AT('atutor_home').': http://atutor.ca</p>';

		// if this email has been queued then don't send it. instead insert it in the db
		// for each bcc or to or cc
		if ($_config['enable_mail_queue'] && !$this->attachment) {
			global $db;
			for ($i = 0; $i < count($this->to); $i++) {
				$this->QueueMail(addslashes($this->to[$i][0]), addslashes($this->to[$i][1]), addslashes($this->From), addslashes($this->FromName), addslashes($this->Subject), addslashes($this->Body));
			}
			for($i = 0; $i < count($this->cc); $i++) {
				$this->QueueMail(addslashes($this->cc[$i][0]), addslashes($this->cc[$i][1]), addslashes($this->From), addslashes($this->FromName), addslashes($this->Subject), addslashes($this->Body));
			}
			for($i = 0; $i < count($this->bcc); $i++) {
				$this->QueueMail(addslashes($this->bcc[$i][0]), addslashes($this->bcc[$i][1]), addslashes($this->From), addslashes($this->FromName), addslashes($this->Subject), addslashes($this->Body));
			}
			return true;
		} else {
			return parent::Send();
		}
	}

	/**
	* Adds the mail to the queue.
	* @access private
	* @return boolean whether the mail was queued successfully.
	* @since  ATutor 1.5.3
	* @author Joel Kronenberg
	*/
	function QueueMail($to_email, $to_name, $from_email, $from_name, $subject, $body) {
		global $db;
		/////
		// $sql = "INSERT INTO ".TABLE_PREFIX."mail_queue VALUES (NULL, '$to_email', '$to_name', '$from_email', '$from_name', '".addslashes($this->CharSet)."', '$subject', '$body')";
		// return mysql_query($sql, $db);
		//
		/////////////////
		//// UNTESTED, TEST WITH MAIL ENABLED SERVER
		$sql = "INSERT INTO %smail_queue VALUES (NULL, '%s', '%s', '%s', '%s', '".addslashes($this->CharSet)."', '%s', '%s')";
		return queryDB($sql, array(TABLE_PREFIX,$to_email, $to_name, $from_email, $from_name, $subject, $body));

		
	}

	/**
	* Sends all the queued mail. Called by ./admin/cron.php.
	* @access public
	* @return void
	* @since ATutor 1.5.3
	* @author Joel Kronenberg
	*/
	function SendQueue() {
		global $db;

		$mail_ids = '';
		///////
		//$sql = "SELECT * FROM ".TABLE_PREFIX."mail_queue";
		//$result = mysql_query($sql, $db);
		//
		/////////////////
		//// UNTESTED, TEST WITH MAIL ENABLED SERVER		
		$sql = "SELECT * FROM %smail_queue";
		$rows_queue = queryDB($sql, array(TABLE_PREFIX));
		foreach($rows_queue as $row){
		//////
		//while ($row = mysql_fetch_assoc($result)) {
		//////
			$this->ClearAllRecipients();

			$this->AddAddress($row['to_email'], $row['to_name']);
			$this->From     = $row['from_email'];
			$this->FromName = $row['from_name'];
			$this->CharSet  = $row['char_set'];
			$this->Subject  = $row['subject'];
			$this->Body     = $row['body'];

			parent::Send();

			$mail_ids .= $row['mail_id'].',';
		}
		if ($mail_ids) {
			$mail_ids = substr($mail_ids, 0, -1); // remove the last comma
		/////
		//	$sql = "DELETE FROM ".TABLE_PREFIX."mail_queue WHERE mail_id IN ($mail_ids)";
		//	mysql_query($sql, $db);
		/////////////////
		//// UNTESTED, TEST WITH MAIL ENABLED SERVER
		$sql = "DELETE FROM %smail_queue WHERE mail_id IN (%s)";
		queryDB($sql, array(TABLE_PREFIX, $mail_ids));
		}
	}

}

?>
