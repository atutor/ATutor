<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: auto_enroll_courses.php 7208 2008-01-09 16:07:24Z cindy $

// Note: MUST set variables $member_id before calling this page.

// auto enroll into courses that link with en_id
if (isset($_REQUEST["en_id"]) && $_REQUEST["en_id"] <> "")
{

	$associate_string = validate_enid($_REQUEST["en_id"]);

	$sql_courses = "SELECT aec.course_id, c.title
	                  FROM %sauto_enroll a, 
	                  %sauto_enroll_courses aec,
                    %scourses c
	                  where a.associate_string='%s'
	                  and a.auto_enroll_id = aec.auto_enroll_id
                    and aec.course_id = c.course_id";

	$rows_courses = queryDB($sql_courses, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $associate_string));
		
	if (count($rows_courses) > 0)  $_SESSION['enroll'] = AT_ENROLL_YES;
    
  $course_names="";
  $course_registered_names = "";
	foreach($rows_courses as $row_courses){
		$course = $row_courses["course_id"];
		$sql	= "SELECT access, member_id FROM %scourses WHERE course_id=%d";
		$course_info = queryDB($sql, array(TABLE_PREFIX, $course), TRUE);	
		
    $check_already_registered = "SELECT * FROM %scourse_enrollment WHERE course_id=%d AND member_id=%d";
    $chk_registered_result = queryDB($check_already_registered, array(TABLE_PREFIX, $course, $member_id));
	if (count($chk_registered_result) > 0) {
        $course_registered_names.='<li>' . $row_courses["title"] . '</li>';
    }
    if (count($chk_registered_result) == 0) {
		if ($course_info['access'] == 'private') 
		{

			$sql	= "INSERT INTO %scourse_enrollment VALUES (%d, %d, 'n', 0, '"._AT('student')."', 0)";
			$result = queryDB($sql, array(TABLE_PREFIX, $member_id, $course));
			$course_names.='<li>' . $row_courses["title"] . '</li>';
			// send the email - if needed
			if ($system_courses[$course]['notify'] == 1) {
				$mail_list = array();	//initialize an array to store all the pending emails
	
				//Get the list of students with enrollment privilege
				$module =& $moduleFactory->getModule('_core/enrolment');
				$sql	= "SELECT email, first_name, last_name, `privileges` FROM %smembers m INNER JOIN %scourse_enrollment ce ON m.member_id=ce.member_id WHERE ce.privileges > 0 AND ce.course_id=%d";
				$rows_enrolled = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course));	
				
				foreach($rows_enrolled as $row){
					if (query_bit($row['privileges'], $module->getPrivilege()))
					{
						unset($row['privileges']);	//we don't need the privilege to flow around
						$mail_list[] = $row;
					}
				}
				
				//Get instructor information
				$ins_id = $system_courses[$course]['member_id'];
				$sql	= "SELECT email, first_name, last_name FROM %smembers WHERE member_id=%d";
				$row	= queryDB($sql, array(TABLE_PREFIX,$ins_id), TRUE);
			
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

			$sql	= "INSERT INTO %scourse_enrollment VALUES (%d, %d, 'y', 0, '"._AT('student')."', 0)";
			$result = queryDB($sql, array(TABLE_PREFIX,$member_id, $course));
            $course_names.='<li>' . $row_courses["title"] . '</li>';
		}
	}
  }
}
?>