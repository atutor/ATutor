<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ECOMM);

if (!$_config['ec_allow_instructors']){
	$msg->printInfos('EC_PAYMENTS_TURNED_OFF');
	require (AT_INCLUDE_PATH.'header.inc.php');
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if (isset($_POST['submit'])) {
	$_POST['ec_course_fee']   = floatval($_POST['ec_course_fee']);
	$_POST['ec_auto_approve'] = intval($_POST['ec_auto_approve']);
	$_POST['ec_auto_email']   = intval($_POST['ec_auto_email']);

	$sql = "REPLACE INTO ".TABLE_PREFIX."ec_course_fees VALUES ($_SESSION[course_id], '{$_POST['ec_course_fee']}', {$_POST['ec_auto_approve']}, {$_POST['ec_auto_email']})";
	if ($result = mysql_query($sql,$db)) {
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}else{
		$msg->addError('EC_COURSE_PAYMENT_SETTINGS_NOT_SAVED');
	}

	header('Location: '.$_SERVER['PHP_SELF']);
	exit;
}

$sql = "SELECT * from ".TABLE_PREFIX."ec_course_fees WHERE course_id='$_SESSION[course_id]'";
$result = mysql_query($sql,$db);
if ($row = mysql_fetch_assoc($result)){
	$this_course_fee   = $row['course_fee'];
	$this_auto_approve = $row['auto_approve'];
	$this_auto_email   = $row['auto_email'];
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<label for="ec_course_fee"><?php echo _AT('ec_course_fee'); ?></label><br/>
			<?php echo $_config['ec_currency_symbol'] ?><input type="text" name="ec_course_fee" size="3" value="<?php echo $this_course_fee; ?>" id="ec_course_fee" size="10"  /> (<?php echo  $_config['ec_currency'] ?>)
		</div>
		<div class="row">
			<?php echo _AT('ec_auto_approve'); ?><br/>
			<input type="radio" name="ec_auto_approve" value="1" id="auto1" <?php if($this_auto_approve){ echo 'checked="checked"'; } ?>/><label for="auto1"><?php echo _AT('enable'); ?></label>
			<input type="radio" name="ec_auto_approve" value="0" id="auto0" <?php if(!$this_auto_approve){ echo 'checked="checked"'; } ?>/><label for="auto0"><?php echo _AT('disable'); ?></label>
		</div>
		<div class="row">
			<?php echo _AT('ec_auto_email'); ?><br/>
			<input type="radio" name="ec_auto_email" value="1" id="email1" <?php if ($this_auto_email) { echo 'checked="checked"'; } ?>/><label for="email1"><?php echo _AT('enable'); ?></label>
			<input type="radio" name="ec_auto_email" value="0" id="email0" <?php if (!$this_auto_email) { echo 'checked="checked"'; } ?>/><label for="email0"><?php echo _AT('disable'); ?></label>
		</div>
		
		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  class="button"/>
		</div>
	</div>
</form>

<?php
$sql2 = "SELECT  P.course_id,  P.member_id,  f.course_fee, f.auto_approve , m.login, m.first_name, m.last_name from ".TABLE_PREFIX."payments AS P, ".TABLE_PREFIX."ec_course_fees AS f, ".TABLE_PREFIX."members AS m WHERE f.course_id = '$_SESSION[course_id]' AND P.course_id = '$_SESSION[course_id]' AND P.member_id = m.member_id GROUP BY m.login, m.first_name, m.last_name";

$result = mysql_query($sql2,$db);
if (mysql_num_rows($result)) { ?>
	<table class="data static" summary="" border="1">
	<tr>
		<th scope="col"><?php echo _AT('ec_student_name'); ?></th>
		<th scope="col"><?php echo _AT('ec_payment_made'); ?></th>
		<th scope="col"><?php echo _AT('ec_enroll_approved'); ?></th>
	</tr>
	<?php
		while($row = mysql_fetch_assoc($result2)){
			echo '<tr><td>'.$row['first_name'].' '.$row['last_name'].' ('.$row['login'].')</td>';
			
			$sql3 = "SELECT amount from ".TABLE_PREFIX."ec_shop WHERE course_id = '$row[course_id]' AND member_id = '$row[member_id]'";
			$result3 = mysql_query($sql3,$db);
		
			$amount_paid = '';
			while($row3 = mysql_fetch_assoc($result3)){
				//$amount_paid = $amount_paid.'+'.$row3['amount'];
				$amount_paid = $amount_paid+$row3['amount'];
			}
			if($amount_paid != 0){
				echo '<td>'.$amount_paid.'</td>';
			}else{
				echo '<td>0</td>';
			}	
			
			$sql4 = "SELECT * from ".TABLE_PREFIX."course_enrollment WHERE course_id = '$_SESSION[course_id]' AND member_id = '$row[member_id]'";
			if($result4 = mysql_query($sql4, $db)){
				if(mysql_num_rows($result4) >= '1'){
					while($row4 = mysql_fetch_assoc($result4)){
	
						if($row4['approved'] == 'y'){
							echo '<td>'._AT('yes').'<small> (<a href="tools/enrollment/enroll_edit.php?id0='.$row['member_id'].';func=unenroll;tab=0;course_id='.$_SESSION['course_id'].'">'._AT('unenroll').'</a>)</small></td>';
						}else{
							echo '<td>'._AT('no').' <small>( <a href="tools/enrollment/enroll_edit.php?id0='.$row['member_id'].';func=enroll;tab=0;course_id='.$_SESSION['course_id'].'">'._AT('enroll').'</a>)</small>';
						}
					}
				}else{
					echo '<td>'._AT('no').'<small> (<a href="tools/enrollment/enroll_edit.php?id0='.$row['member_id'].';func=enroll;tab=0;course_id='.$_SESSION['course_id'].'">'._AT('enroll').'</a>)</small></td>';
				}
			} else {
				echo '<td>'._AT('no').'</td>';
			}
		}
		echo '</tr></table>';
} else {
	$msg->printInfos('EC_NO_STUDENTS_ENROLLED');
}


require (AT_INCLUDE_PATH.'footer.inc.php'); ?>