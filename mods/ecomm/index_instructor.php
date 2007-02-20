<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_HELLO_WORLD);
require (AT_INCLUDE_PATH.'header.inc.php');


if($_config['ec_allow_instructors'] != 1 && !$_SESSION['is_super_admin']){

$msg->printInfos('EC_PAYMENTS_TURNED_OFF');
require (AT_INCLUDE_PATH.'footer.inc.php'); 
exit;
}

$this_course_id = intval($_POST['course_id']);
$this_course_fee = addslashes($_POST['ec_course_fee']);
$this_auto_approve = addslashes($_POST['ec_auto_approve']);
$this_auto_email = addslashes($_POST['ec_auto_email']);

if($this_auto_approve){
	$insert_approve = '1';
}else{
	$insert_approve = '0';
}
if($this_auto_email){
	$insert_email = '1';
}else{
	$insert_email = '0';
}
if($_POST['submit']){

	$sql = "REPLACE into ".TABLE_PREFIX."ec_course_fees
		set
		course_id = '$this_course_id',
		course_fee =  '$this_course_fee',
		auto_approve =   '$insert_approve',
		auto_email =   '$insert_email'";

		if($result = mysql_query($sql,$db)){
			$msg->addFeedback('EC_COURSE_PAYMENT_SETTINGS_SAVED');

		}else{
			$msg->addError('EC_COURSE_PAYMENT_SETTINGS_NOT_SAVED');
		}
}

$sql2 = "SELECT * from ".TABLE_PREFIX."ec_course_fees WHERE course_id='$_SESSION[course_id]'";
if($result2 = mysql_query($sql2,$db)){
	while($row = mysql_fetch_array($result2)){
		$this_course_fee = $row['1'];
		$this_auto_approve = $row['2'];
		$this_auto_email = $row['3'];
	}	
}
$msg->printAll();
?>

<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="course_id" value="<?php echo $_SESSION['course_id']; ?> "/>
	<div class="input-form">
		<div class="row">
			<p><label for="ec_course_fee"><?php echo _AT('ec_course_fee'); ?></label></p>
	
			<?php echo $_config['ec_currency_symbol'] ?><input type="text" name="ec_course_fee" value="<?php echo $this_course_fee; ?>" id="ec_course_fee" size="10"  /> (<?php echo  $_config['ec_currency'] ?>)
		</div>
		<div class="row">
			<p><label for="ec_auto_approve"><?php echo _AT('ec_auto_approve'); ?></label></p>				
			<input type="checkbox" name="ec_auto_approve"  
			<?php
				if($this_auto_approve == '1'){
					echo 'checked="checked"  value="1"';
				}
			?>
			 id="ec_auto_approve" />
		</div>
		<div class="row">
			<p><label for="ec_auto_email"><?php echo _AT('ec_auto_email'); ?></label></p>				
			<input type="checkbox" name="ec_auto_email"  
			<?php
				if($this_auto_email == '1'){
					echo 'checked="checked"  value="1"';
				}
			?>
			 id="ec_auto_email" />
		</div>
		
		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  class="button"  />
		</div>
	</div>
</form>

<?php

	$sql2 = "SELECT  s.course_id,  s.member_id,  f.course_fee, f.auto_approve , m.login, m.first_name, m.last_name from ".TABLE_PREFIX."ec_shop AS s, ".TABLE_PREFIX."ec_course_fees AS f, ".TABLE_PREFIX."members AS m WHERE f.course_id = '$_SESSION[course_id]' AND s.course_id = '$_SESSION[course_id]' AND s.member_id = m.member_id GROUP BY m.login, m.first_name, m.last_name";

	if($result2 = mysql_query($sql2,$db)){
		if(mysql_num_rows($result2) >=1){ ?>
		
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
				}else{
						echo '<td>'._AT('no').'xxx</td>';
				}

			}
			echo '</tr></table>';
		}else{
		//echo '</tr></table>';
			$infos = "EC_NO_STUDENTS_ENROLLED";			
		}
	}else{
		echo '<td>'._AT('no').'</td>';
		echo '</tr></table>';
	}
	//echo '</tr></table>';
$msg->printInfos($infos);
 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>