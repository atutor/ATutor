<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

function atutor_mail($email,
					 $subject,
					 $body,
					 $from,
					 $bcc = '') {

	global $_base_href;

	$atutor_sig = "\n\n".'----------------------------------------------'."\n";
	$atutor_sig .= _AT('sent_via_atutor', $_base_href);
	$atutor_sig .= "\n"._AT('atutor_home').': http://atutor.ca';

	$body .= $atutor_sig;

	if ($bcc) {
		$bcc = "\nBcc: $bcc";
	}

	@mail($email, stripslashes($subject), stripslashes($body), 'From: '.$from."\n"."Reply-To:".$from."$bcc\nX-Mailer: PHP");
} 

?>