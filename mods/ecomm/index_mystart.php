<?php
$page = 'ec_payments';
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

?>

<?php

/// If a reply from  payment processing, update the shop table to confirm the payment

if($_GET['MTID']){
		
		$sql = "UPDATE ".TABLE_PREFIX."ec_shop  set  miraid='$_GET[MiraID]', amount ='$_GET[Amount1]' WHERE shopid ='$_GET[MTID]'";
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
	while($row2 = mysql_fetch_assoc($resulta)){
		if($row2['auto_approve'] == '1' && $total_amount_paid >= $row2['course_fee']){
			$sql2 = "UPDATE ".TABLE_PREFIX."course_enrollment set approved = 'y' WHERE member_id = $_GET[mid] AND course_id = '$_GET[cid]'";
			if($result2 = mysql_query($sql2,$db)){
				$msg->printFeedbacks('EC_PAYMENT_CONFIRMED_AUTO');
			}
			$sql2 = "UPDATE ".TABLE_PREFIX."ec_shop set approval = '1' WHERE member_id = $_GET[mid] AND course_id = '$_GET[cid]'";
			$result2 = mysql_query($sql2,$db);
		}else{
				$msg->printFeedbacks('EC_PAYMENT_CONFIRMED_MANUAL');
		}
		/// If auto email when payment is made, send an email to the instructor (maybe this should be an admin option)
		if($row2['auto_email'] == '1' || $_config['ec_email_admin'] == '1'){

			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			
			/// Get the course title
			$sql2= "SELECT  title from ".TABLE_PREFIX."courses WHERE course_id = '$_GET[cid]'";
			$result2 = mysql_query($sql2,$db);
			while($row2 = mysql_fetch_array($result2)){
				$course_title  = $row2['0'];
			}


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
			
			
			/// Email Instructror if set

			$sql = "SELECT m.member_id, f.auto_email from ".TABLE_PREFIX."courses AS m,  ".TABLE_PREFIX."ec_course_fees AS f WHERE m.course_id = '$_GET[cid]'";
				
			$mail = new ATutorMailer;
				if($result = mysql_query($sql,$db)){
		
				//	$mail = new ATutorMailer;
			
					$mail->From     = $sender_email;
					$mail->FromName = $sender_name;
					$mail->AddAddress($instructor_email);
					$mail->Subject = _AT('ec_payment_made'); 
					$mail->Body    = _AT('ec_payment_mail_instruction', $course_title);
			
					if(!$mail->Send()) {
						$msg->printErrors('SENDING_ERROR');
						exit;
					}
					$mail->ClearAddresses();
					
					//unset($mail);
				}
			/// Email Administrator  if set
				if($_config['ec_email_admin']){
					If($_config['ec_contact_email']){
						$contact_admin_email = $_config['ec_contact_email'];
					}else{
						$contact_admin_email = $_config['contact_email'];
					}

					$mail->From     = $sender_email;
					$mail->FromName = $sender_name;
					$mail->AddAddress($contact_admin_email);
					$mail->Subject = _AT('ec_payment_made'); 
					$mail->Body    = _AT('ec_admin_payment_mail_instruction', $course_title);
			
					if(!$mail->Send()) {
						$msg->printErrors('SENDING_ERROR');
						exit;
					}
				}
			unset($mail);

		}
	}
}


/// Get a list of enrolled courses or pending enrollments, and display their fee payment status 

$sql = "SELECT course_id from  ".TABLE_PREFIX."course_enrollment WHERE member_id = '$_SESSION[member_id]'";

$result = mysql_query($sql,$db);

if(@mysql_num_rows($result) >=1){ ?>
	<table class="data" rules="cols" summary="">
	<thead>
		<tr>
			<th scope="col"><?php echo _AT('ec_course_name'); ?></th>
			<th scope="col"><?php echo _AT('ec_this_course_fee'); ?></th>
			<th scope="col"><?php echo _AT('ec_payment_made'); ?></th>
			<th scope="col"><?php echo _AT('ec_enroll_approved'); ?></th>
			<th scope="col"><?php echo _AT('ec_action'); ?></th>
		</tr>
	</thead>
	<?php
		while($row = mysql_fetch_assoc($result)){
			if($_SESSION['member_id'] != $system_courses[$row['course_id']]['member_id']){
				$payment_count++;
				$sql7 = "SELECT member_id from ".TABLE_PREFIX."courses WHERE course_id = '$row[course_id]' ";
				$result7 = mysql_query($sql7,$db);
				$this_course_instructor = mysql_result($result7,0);
				if($system_courses[$row['course_id']]['member_id'] != $_SESSION['member_id']){
					$sql2 = "SELECT course_fee from ".TABLE_PREFIX."ec_course_fees WHERE course_id = '$row[course_id]'";
					if($result2 = mysql_query($sql2,$db)){
						$this_course_fee = mysql_result($result2,0);
					}
					$sql3 = "SELECT title from ".TABLE_PREFIX."courses WHERE course_id = '$row[course_id]' ";
					$result3 = mysql_query($sql3,$db);
					$this_course_title = mysql_result($result3,0);

					echo '<tr><td>'.$this_course_title.'</td><td>'.$_config['ec_currency_symbol'].$this_course_fee.' '.$_config['ec_currency'].'</td>';
				
					$sql4 = "SELECT amount from ".TABLE_PREFIX."ec_shop WHERE course_id = '$row[course_id]' AND member_id = '$_SESSION[member_id]' AND course_id = '$row[course_id]'";
					$result4 = mysql_query($sql4,$db);
		
					$amount_paid = '';
					while($row4 = mysql_fetch_array($result4)){
						$amount_paid = $amount_paid+$row4['0'];
					}
					if($amount_paid != 0){
					
						echo '<td>'.$_config['ec_currency_symbol'].$amount_paid.'</td>';
					}else{
						echo '<td>'.$_config['ec_currency_symbol'].'0</td>';
					}
				
					$sql4 = "SELECT * from ".TABLE_PREFIX."course_enrollment WHERE course_id = '$row[course_id]' AND member_id = '$_SESSION[member_id]'";
			
					$result4 = mysql_query($sql4, $db);
					while($row4 = mysql_fetch_array($result4)){
					$miraid = $row4['miraid'];
					if($row4['approved'] == 'y'){
							echo '<td>'._AT('yes').' (<a href="bounce.php?course='.$row['course_id'].'">'._AT('ec_login').'</a>)</td>';
						}else{
							echo '<td>'._AT('no').'</td>';
						}
					}
			
					if($amount_paid >= $this_course_fee && $miraid !=''){
						echo '<td>'._AT('ec_full_payment_recieved').' | <a href="users/remove_course.php?course='.$row['course_id'].'">'._AT('ec_remove').'</a></td>';
					}else{
						echo '<td> <a href="mods/ecomm/payment.php?course_id='.$row['course_id'].'">'._AT('ec_make_payment').'</a> | <a href="users/remove_course.php?course='.$row['course_id'].'">'._AT('ec_remove').'</a></td>';
					}
	
				}
			}
		}	
		echo '</table>';
		if($payment_count == 0){
		
			$msg->printInfos('EC_NO_PAID_COURSES');
		}
	}else{
		$msg->printInfos('EC_NO_PAID_COURSES');
	}

 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>