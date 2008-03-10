<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: auto_enroll_courses.php 7208 2008-01-09 16:07:24Z cindy $

// Note: MUST set variables $member_id before calling this page.

// auto enroll into courses that link with en_id
if (isset($_REQUEST["en_id"]) && $_REQUEST["en_id"] <> "")
{

	$associate_string = $_REQUEST["en_id"];
	
	$sql_courses = "SELECT aec.course_id
	                  FROM " . TABLE_PREFIX."auto_enroll a, " . 
	                           TABLE_PREFIX."auto_enroll_courses aec 
	                 where a.associate_string='".$associate_string ."'
	                   and a.auto_enroll_id = aec.auto_enroll_id";

	$result_courses = mysql_query($sql_courses, $db) or die(mysql_error());
	
	if (mysql_num_rows($result_courses) > 0)  $_SESSION['enroll'] = AT_ENROLL_YES;
	
	while ($row_courses = mysql_fetch_assoc($result_courses))
	{
		$course = $row_courses["course_id"];
		
		$sql	= "SELECT access, member_id FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		$course_info = mysql_fetch_assoc($result);
		
		if ($course_info['access'] == 'private') 
		{
			$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($member_id, $course, 'n', 0, '"._AT('student')."', 0)";
			$result = mysql_query($sql, $db);
	
			// send the email - if needed
			if ($system_courses[$course]['notify'] == 1) {
				$mail_list = array();	//initialize an array to store all the pending emails
	
				//Get the list of students with enrollment privilege
				$module =& $moduleFactory->getModule('_core/enrolment');
				$sql	= "SELECT email, first_name, last_name, privileges FROM ".TABLE_PREFIX."members m INNER JOIN ".TABLE_PREFIX."course_enrollment ce ON m.member_id=ce.member_id WHERE ce.privileges > 0 AND ce.course_id=$course";
				$result = mysql_query($sql, $db);
				while ($row	= mysql_fetch_assoc($result))
				{
					if (query_bit($row['privileges'], $module->getPrivilege()))
					{
						unset($row['privileges']);	//we don't need the privilege to flow around
						$mail_list[] = $row;
					}
				}
				
				//Get instructor information
				$ins_id = $system_courses[$course]['member_id'];
				$sql	= "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$ins_id";
				$result = mysql_query($sql, $db);
				$row	= mysql_fetch_assoc($result);
				$mail_list[] = $row;
	
				//Send email notification to both assistants with privileges & Instructor
				foreach ($mail_list as $row)
				{
					$to_email = $row['email'];
					$tmp_message  = $row['first_name']  .' ' . $row['last_name']."\n\n";
					$tmp_message .= _AT('enrol_messagenew', $system_courses[$course]['title'], AT_BASE_HREF );
					if ($to_email != '') {
						require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
	
						$mail = new ATutorMailer;
						$mail->From     = $_config['contact_email'];
						$mail->FromName = $_config['site_name'];
						$mail->AddAddress($to_email);
						$mail->Subject = _AT('enrol_message3');
						$mail->Body    = $tmp_message;
	
						if (!$mail->Send()) 
						{
						   $msg->addError('SENDING_ERROR');
						}
						unset($mail);
					}
				}
			}
		} else {
			$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES ($member_id, $course, 'y', 0, '"._AT('student')."', 0)";
			$result = mysql_query($sql, $db);
		}
	}
	
}
?>