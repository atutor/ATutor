<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('include/payments.lib.php');

require (AT_INCLUDE_PATH.'header.inc.php');

function is_enrolled($member_id, $course_id) {
	global $db;
	$sql = "SELECT approved FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course_id AND member_id=$member_id AND approved<>'n'";
	$result = mysql_query($sql, $db);
	return (boolean) mysql_fetch_assoc($result);
}

/// Get a list of enrolled courses or pending enrollments, and display their fee payment status 

$payment_count = 0; // num listed courses

$sql = "SELECT course_id, approved FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id]";
$result = mysql_query($sql,$db);


if (mysql_num_rows($result)) { ?>
	<table class="data static" rules="rows" summary="">
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
		while ($row = mysql_fetch_assoc($result)){
			if ($_SESSION['member_id'] == $system_courses[$row['course_id']]['member_id']) {
				continue; // this member is an instructor
			}

			$sql2 = "SELECT course_fee FROM  ".TABLE_PREFIX."ec_course_fees WHERE course_id=$row[course_id] AND course_fee>0";
			$result2 = mysql_query($sql2,$db);
			if ($this_course_fee = mysql_fetch_assoc($result2)) {
				$this_course_fee = $this_course_fee['course_fee'];
			} else {
				continue;
			}

			$payment_count++;

			echo '<tr>';
			if(is_enrolled($_SESSION['member_id'], $row['course_id'])){
				echo '<td><a href="bounce.php?course='.$row['course_id'].'">'.$system_courses[$row['course_id']]['title'].'</a></td>';

			}else{
				echo '<td>'.$system_courses[$row['course_id']]['title'].'</td>';
			}
			echo '<td align="center">'.$_config['ec_currency_symbol'].number_format($this_course_fee, 2).' '.$_config['ec_currency'].'</td>';

			$sql4 = "SELECT SUM(amount) AS total_amount FROM ".TABLE_PREFIX."payments WHERE course_id='$row[course_id]' AND member_id = '$_SESSION[member_id]' AND approved=1";
			$result4 = mysql_query($sql4,$db);
		
			$row4 = mysql_fetch_assoc($result4);
			$amount_paid = $row4['total_amount'];

			echo '<td align="center">'.$_config['ec_currency_symbol'].number_format($amount_paid, 2).'</td>';
				
			if ($row['approved'] == 'y'){
				echo '<td align="center">'._AT('yes').'</td>';
			} else {
				echo '<td align="center">'._AT('no').'</td>';
			}
			
			if ($amount_paid >= $this_course_fee){
				echo '<td align="center">'._AT('ec_full_payment_recieved').'</td>';
			}else{
				echo '<td align="center"><a href="mods/ecomm/payment.php?course_id='.$row['course_id'].'">'._AT('ec_make_payment').'</a></td>';
			}
		}	
		echo '</table>';
		if($payment_count == 0){
			$msg->printInfos('EC_NO_PAID_COURSES');
		}
} else {
	$msg->printInfos('EC_NO_PAID_COURSES');
}


 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>