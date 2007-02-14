<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');



$sql = "SELECT 
	e.*,
	c.title,
	f.course_fee
	from
	".TABLE_PREFIX."course_enrollment AS e,
	".TABLE_PREFIX."courses AS c,
	".TABLE_PREFIX."ec_course_fees AS f
	WHERE
	e.member_id = '$_SESSION[member_id]' ";

	$result = mysql_query($sql,$db)

?>

<h2><?php echo _AT('ec_payments'); ?></h2>


<?php

if(mysql_num_rows($result) >=1){ ?>

<table class="data static" summary="">
<tr>
	<th scope="col">Course Name</th>
	<th scope="col">Course Fee</th>
	<th scope="col">Payment Received</th>
	<th scope="col">Enrollment Approved</th>
	<th scope="col">Make Payment</th>
</tr>
<?php
	while($row = mysql_fetch_assoc($result)){
		
		echo '<tr><td>'.$row['title'].'</td><td>'.$row['course_fee'].'</td>';
	
		$sql2 = "SELECT amount from ".TABLE_PREFIX."ec_shop WHERE course_id = '$row[course_id]' AND member_id = '$_SESSION[member_id]'";
		$result2 = mysql_query($sql2,$db);
		$amount_paid = '';
		while($row2 = mysql_fetch_array($result2)){
			$amount_paid = $amount_paid+$row2['0'];
		}
		if($amount_paid != 0){
		
			echo '<td>'.$amount_paid.'</td>';
		}else{
			echo '<td>0</td>';
		}	
		$sql4 = "SELECT * from ".TABLE_PREFIX."course_enrollment WHERE course_id = '$row[course_id]' AND member_id = '$_SESSION[member_id]'";
echo $sql4;
		$result4 = mysql_query($sql4, $db);
		while($row4 = mysql_fetch_array($result4)){
		
			if($row4['approved'] == 'y'){
				echo '<td>yes</td>';
			}else{
				echo '<td>no</td>';
			}
		}

		
 		if($amount_paid >= $row['course_fee']){
			echo '<td>yes</td>';
 		}else{
 			echo '<td>no <a href="users/remove_course.php?course='.$row['course_id'].'">'._AT('unenroll').'</a></td>';
 		}
		if($amount_paid >= $row['course_fee']){
			echo '<td>Full Payment Received</td>';
		}else{
			echo '<td><a href="mods/ecomm/payment.php?course_id='.$row['course_id'].'">Make Payment</a></td>';
		}
	}
	echo '</table>';
}else{
	$msg->printInfos('EC_NO_PAID_COURSES');
}


?>



<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>