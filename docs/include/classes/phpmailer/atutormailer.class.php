<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg, Heidi Hazelton */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

require(dirname(__FILE__) . '/class.phpmailer.php');

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
	* @access  public
	* @return  boolean	whether or not the mail was sent correctly.
	* @see     parent::send()
	* @since   ATutor 1.4.1
	* @author  Joel Kronenberg
	*/
	function Send() {
		global $_base_href;

		// attach the ATutor footer to the body first:
		$this->Body .= 	"\n\n".'----------------------------------------------'."\n";
		$this->Body .= _AT('sent_via_atutor', $_base_href);
		if ($_SESSION['course_id'] > 0) {
			$this->Body .= 'login.php?course='.$_SESSION['course_id'].' | ' . $_SESSION['course_title'];
		}

		$this->Body .= "\n"._AT('atutor_home').': http://atutor.ca';

		return parent::Send();
	}

}

?>
