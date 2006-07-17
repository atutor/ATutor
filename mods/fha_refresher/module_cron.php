<?php

// simple cron script to run daily

function fha_refresher_cron() {
	global $db, $system_courses, $_config;

	require_once(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

	$mail = new ATutorMailer;

	$subject = _AT('fha_ref_automatic_email_reminder');

	$sql = "SELECT * FROM ".TABLE_PREFIX."fha_refresher WHERE enabled=1";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$refresh_period_seconds = time() - ($row['refresh_period'] * 24 * 60 * 60);
		$max_refresh_period     = $row['max_refresh_period'] * 24 * 60 * 60;

		// these are used in the mail footer
		$_SESSION['course_id']    = $row['course_id'];
		$_SESSION['course_title'] = $system_courses[$row['course_id']]['title'];

		$sql = "SELECT member_id, MAX(UNIX_TIMESTAMP(date_taken)) AS date_taken FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[test_id] AND final_score >= $row[pass_score] GROUP BY member_id";
		$test_result = mysql_query($sql, $db);

		while ($test_row = mysql_fetch_assoc($test_result)) {
			$refresh_difference = $test_row['date_taken'] - $refresh_period_seconds;
			if ((abs($refresh_difference) < $max_refresh_period) && ($refresh_difference < 0)) {
				$refresh_difference = abs($refresh_difference);
				if ((round($refresh_difference / 24 / 60 / 60)  % $row['reminder_period']) == 0) {
					$sql = "SELECT login, email FROM ".TABLE_PREFIX."members WHERE member_id=$test_row[member_id]";
					$member_result = mysql_query($sql, $db);
					$member_row = mysql_fetch_assoc($member_result);
					
					$mail->From     = $_config['contact_email'];
					$mail->AddAddress($member_row['email']);
					$mail->Subject = $subject;
					$mail->Body    = _AT('fha_ref_automatic_email_body',$_SESSION['course_title'], $member_row['login']);

					$mail->Send();

					$mail->ClearAllRecipients();
				}
			}
		}
	}
}
?>