<?php
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

// PREPARE MAIL
  $sql = "SELECT email FROM ".TABLE_PREFIX."members WHERE member_id=".$_SESSION['member_id'];
  $user_email = mysql_fetch_assoc(mysql_query($sql));
  require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
	
  $mail = new ATutorMailer;
	$mail->AddAddress($user_email['email'], get_display_name($_SESSION['member_id']));
	$mail->FromName = $_config['site_name'];
	$mail->From     = $_config['contact_email'];

// SUBSCRIBE OR UNSUBSCRIBE? TAKE APPROPRIATE ACTION
  if ($_GET['a'] == "subscribe"){
    // CHECK FOR EXISTING TABLE ENTRY
    $check = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM ".TABLE_PREFIX."courses_members_subscription WHERE course_id=".$_SESSION['course_id']." AND member_id=".$_SESSION['member_id']));
      if(empty($check[0])){
        $sql = "INSERT INTO ".TABLE_PREFIX."courses_members_subscription (member_id,course_id,subscribe) VALUES(".$_SESSION['member_id'].",".$_SESSION['course_id'].",'1')";      
      } else {
        $sql = "UPDATE ".TABLE_PREFIX."courses_members_subscription SET subscribe='1' WHERE course_id=".$_SESSION['course_id']." AND member_id=".$_SESSION['member_id'];      
      }    
    $mail->Subject = _AT('announcement_subscribe_subject');	
    $body = _AT('announcement_subscribe_body', $_SESSION['course_title'], AT_BASE_HREF.'bounce.php?course='.$_SESSION['course_id']);
    $msg->addFeedback('ANNOUNCEMENTSUB_SUBSCRIBE');
  }elseif ($_GET['a'] == "unsubscribe"){
    $sql = "UPDATE ".TABLE_PREFIX."courses_members_subscription SET subscribe='0' WHERE course_id=".$_SESSION['course_id']." AND member_id=".$_SESSION['member_id'];
    $mail->Subject = _AT('announcement_subscribe_subject');	
    $body = _AT('announcement_unsubscribe_body', $_SESSION['course_title'], AT_BASE_HREF.'bounce.php?course='.$_SESSION['course_id']);
    $msg->addFeedback('ANNOUNCEMENTSUB_UNSUBSCRIBE');
  }else {
    die("Error: action not defined");
  }

// FIX SUBSCRIPTION IN DB
  mysql_query($sql) or die(mysql_error());
  
// FINISH UP MAIL AND SHIP IT OFF

  $mail->Body    = $body;

  // UNCOMMENT THE FOLLOWING 3 LINES IF YOU WANT ATUTOR TO SEND AN EMAIL TO THE USER WHEN THE USER SUBSCRIBES/UNSUBSCRIBES TO A NEWSFEED
	//if(!$mail->Send()) {
	//	$msg->addError('SENDING_ERROR');
	//}

	unset($mail);
  
// GO HOME

header('Location: ../../index.php');

?>
