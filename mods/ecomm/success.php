<?php

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
//$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet

require (AT_INCLUDE_PATH.'header.inc.php');

?>

<h3><?php echo _AT('ec_payment_confirmation'); ?></h3>
<br /><br />
<div style="border:3px solid green; padding: 1em;  margin-left: auto; margin-right: auto; width: 80%; background-color: #e5ffe9;" >
<?php echo _AT('ec_payment_confirmation_text'); ?>
</div><br /><br />
<?php


/// Only run this query if MiraPay loads this page
if($_GET['MTID']){
		$sql = "UPDATE ".TABLE_PREFIX."shop  set  miraid='$_GET[MiraID]', amount ='$_GET[Amount1]' WHERE shopid ='$_GET[MTID]'";
		$result = mysql_query($sql, $db);

	/// If course is set to auto approve on when full payment is recieved, set enrollment approval to approved

	$sql = "SELECT amount from ".TABLE_PREFIX."ec_shop WHERE member_id = '$_GET[mid]' AND course_id = '$_GET[cid]'";
	$result = mysql_query($sql, $db);
	
	while($row = mysql_fetch_array($result)){
		$total_amount_paid = $total_amount_paid+$row['0'];
	}
	
	$sqla = "SELECT * from ".TABLE_PREFIX."ec_course_fees WHERE course_id = '$_GET[cid]'";
	$resulta = mysql_query($sqla,$db);
;
	while($row = mysql_fetch_row($resulta)){
		if($row['1'] = '1' && $total_amount_paid >= $row['0']){
			$sql2 = "UPDATE ".TABLE_PREFIX."course_enrollment set approved = 'y' WHERE member_id = $_GET[mid] AND course_id = '$_GET[cid]'";
			if($result2 = mysql_query($sql2,$db)){
				$msg->printFeedbacks(_AT('ec_payment_confirmed_auto'));
			}
		}else{
				$msg->printFeedbacks(_AT('ec_payment_confirmed_manual'));
		}

		/// If auto email when payment is made, send an email to the instructor (maybe this should be an admin option)
		if($row['2'] = '1'){

			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			
			/// Get the course title	
			$sql2= "SELECT  title from ".TABLE_PREFIX."courses WHERE course_id = '$_GET[cid]'";
			$result2 = mysql_query($sql2,$db);
			$course_title  = mysql_result($result2, 0);

			/// Get the sender's name and email address
			$sql3= "SELECT login, email, first_name, last_name  from ".TABLE_PREFIX."members WHERE member_id = '$_GET[mid]'";

			$result3 = mysql_query($sql3,$db);
			while($row3 = mysql_fetch_array($result3)){
				$sender_name = $row3['2'].' '.$row3['3'].' ('.$row3['0'].')';
				$sender_email = $row3['1'];
			}

			/// Get the instructor's email address
			$sql4= "SELECT c.member_id, m.email  from ".TABLE_PREFIX."courses AS c, ".TABLE_PREFIX."members as m WHERE c.course_id = '$_GET[cid]' AND c.member_id = m.member_id";

			$result4 = mysql_query($sql4,$db);
			while($row4 = mysql_fetch_array($result4)){
				$instructor_email = $row4['1'];	
			}

			$mail = new ATutorMailer;
	
			$mail->From     = $sender_email;
			$mail->FromName = $sender_name;
			$mail->AddAddress($instructor_email);
			$mail->Subject = _AT('ec_payment_made'); 
			$mail->Body    = _AT('ec_payment_mail_instruction', $cid);
	
			if(!$mail->Send()) {
				$msg->printErrors('SENDING_ERROR');
				exit;
			}
			unset($mail);


		}
	}

}
debug($_GET);


 require (AT_INCLUDE_PATH.'footer.inc.php'); 

?>