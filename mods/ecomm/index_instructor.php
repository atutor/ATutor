<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_HELLO_WORLD);
require (AT_INCLUDE_PATH.'header.inc.php');



$this_course_id = addslashes($_POST['course_id']);
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

	$sql = "UPDATE ".TABLE_PREFIX."ec_course_fees
		set
		course_id = '$this_course_id',
		course_fee =  '$this_course_fee',
		auto_approve =   '$insert_approve',
		auto_email =   '$insert_email' 
		WHERE 
		course_id = '$this_course_id'";
		$result = mysql_query($sql,$db);
}


$sql2 = "SELECT * from ".TABLE_PREFIX."ec_course_fees WHERE course_id='$_SESSION[course_id]'";
$result2 = mysql_query($sql2,$db);


while($row = mysql_fetch_array($result2)){
	$this_course_fee = $row['1'];
	$this_auto_approve = $row['2'];
	$this_auto_email = $row['3'];
}	

?>



<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="course_id" value="<?php echo $_SESSION['course_id']; ?> "/>
	<div class="input-form">
		<div class="row">
			<p><label for="ec_course_fee"><?php echo _AT('ec_course_fee'); ?></label></p>
	
			<input type="text" name="ec_course_fee" value="<?php echo $this_course_fee; ?>" id="ec_course_fee" size="10"  /> (<?php echo _AT('ec_currency'); ?>)
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
	$sql = "SELECT s.course_id, s.member_id,  f.course_fee, f.auto_approve , m.login, m.first_name, m.last_name from ".TABLE_PREFIX."ec_shop AS s, ".TABLE_PREFIX."ec_course_fees AS f, ".TABLE_PREFIX."members AS m WHERE f.course_id = '$_SESSION[course_id]' AND s.course_id = '$_SESSION[course_id]' AND s.member_id = m.member_id";

	$result = mysql_query($sql,$db);

//echo $sql."<br />";
//echo mysql_num_rows($result);
	if(mysql_num_rows($result) >=1){ ?>
	
	<table class="data static" summary="">
	<tr>
		<th scope="col">Name</th>
		<th scope="col">Course Fee</th>
		<th scope="col">Payment Received</th>
		<th scope="col">Enrollment Approved</th>
	</tr>
	<?php
	
		while($row = mysql_fetch_assoc($result)){
			
			echo '<tr><td>'.$row['first_name'].' '.$row['last_name'].' ('.$row['login'].')</td><td>'.$row['course_fee'].'</td>';
		
			$sql3 = "SELECT amount from ".TABLE_PREFIX."ec_shop WHERE course_id = '$row[course_id]' AND member_id = '$row[member_id]'";
	
			$result3 = mysql_query($sql3,$db);
	//echo $_SESSION['course_id']."<br />";
	//echo $sql3."<br />";
	
	
			$amount_paid = '';
			while($row3 = mysql_fetch_assoc($result3)){
	//echo $row3['amount']."blah";
				$amount_paid = $amount_paid+$row3['amount'];
			}
			if($amount_paid != 0){
			
				echo '<td>'.$amount_paid.'</td>';
			}else{
				echo '<td>0</td>';
			}	
		
			$sql4 = "SELECT * from ".TABLE_PREFIX."course_enrollment WHERE course_id = '$_SESSION[course_id]' AND member_id = '$row[member_id]'";
			$result4 = mysql_query($sql4, $db);
		while($row4 = mysql_fetch_array($result4)){
		
			if($row4['approved'] == 'y'){
				echo '<td>yes</td>';
			}else{
				echo '<td>no</td>';
			}
		}
	}
		echo '</table>';
}else{
	$msg->printInfos('EC_NO_STUDENTS_ENROLLED');
}






//debug($_POST);
//debug($insert_approve);
//debug($this_course_fee);
 require (AT_INCLUDE_PATH.'footer.inc.php'); ?>