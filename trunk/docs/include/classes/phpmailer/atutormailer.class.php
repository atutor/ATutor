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
// $Id: atutormailer.class.php,v 1.2 2004/05/26 19:08:27 joel Exp $

if (!defined('AT_INCLUDE_PATH')) { exit; }

require('class.phpmailer.php');

class ATutorMailer extends PHPMailer {
    // Set default variables for all new objects

	// constructor
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
	}

	function Send() {
		global $_base_href;

		// attach the ATutor footer to the body first:
		$this->Body .= 	"\n\n".'----------------------------------------------'."\n";
		$this->Body .= _AT('sent_via_atutor', $_base_href);
		if ($_SESSION['course_id'] > 0) {
			$this->Body .= 'login.php?course=2 | ' . $_SESSION['course_title'];
		}

		$this->Body .= "\n"._AT('atutor_home').': http://atutor.ca';

		return parent::Send();
	}

}

?>