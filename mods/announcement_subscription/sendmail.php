<?php		

  // Extract memberids of members who subscribe to newsfeed in this course
  $subscriber_list = '';
	$sql = "SELECT member_id from ".TABLE_PREFIX."courses_members_subscription WHERE subscribe = '1' AND course_id=".$_SESSION['course_id'];
	$result = mysql_query($sql, $db) or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		$subscriber_list .= $row['member_id'] . ',';
	}
	$subscriber_list = $substr($subscriber_list, 0, -1); //strip last comma from list
	
	
	// Get name and email adress for members in $subscriber_list
	$subscriber_email_list = array();
  if ($subscriber_list != '') {
  	$sql = "SELECT first_name, second_name, last_name, email, member_id FROM ".TABLE_PREFIX."members WHERE member_id IN ($subscriber_list)";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$subscriber_email_list[] = array('email'=> $row['email'], 'full_name' => $row['first_name'] . ' '. $row['second_name'] . ' ' . $row['last_name'], 'member_id'=>$row['member_id']);
		}
	}



  // SEND MAIL
	if ($subscriber_email_list) {
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		foreach ($subscriber_email_list as $subscriber){
			$mail = new ATutorMailer;
			$mail->AddAddress($subscriber['email'], get_display_name($subscriber['member_id']));
			$body = _AT('announcement_notify_body1', $_SESSION['course_title'], AT_BASE_HREF.'bounce.php?course='.$_SESSION['course_id']);
			$body .= "\n----------------------------------------------\n";
			$body .= _AT('posted_by').": ".get_display_name($_SESSION['member_id'])."\n";
			$body .= $_POST['title']."\n";
      $body .= $_POST['body_text']."\n";
			$mail->FromName = $_config['site_name'];
			$mail->From     = $_config['contact_email'];
			$mail->Subject = _AT('announcement_notify_subject');
			$mail->Body    = $body;

			if(!$mail->Send()) {
				$msg->addError('SENDING_ERROR');
			}

			unset($mail);
		}
	}
	


?>
