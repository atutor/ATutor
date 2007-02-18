<?php

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ECOMM);

/// Get pagination info
$sql	= "SELECT COUNT(shopid) AS cnt FROM ".TABLE_PREFIX."ec_shop WHERE amount > '0'";

if($result = mysql_query($sql, $db)){
	$row = mysql_fetch_assoc($result);
	$num_results = $row['cnt'];
}

$results_per_page = 25;
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);

if (!$page) {
	$page = 1;
}	

$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

/// Get a list oif those who have made payements

	$sql = "SELECT  s.course_id,  s.member_id,  f.course_fee, f.auto_approve , m.login, m.first_name, m.last_name from ".TABLE_PREFIX."ec_shop AS s, ".TABLE_PREFIX."ec_course_fees AS f, ".TABLE_PREFIX."members AS m GROUP BY m.login, m.first_name, m.last_name LIMIT $offset, $results_per_page";

	$result = mysql_query($sql,$db);
require (AT_INCLUDE_PATH.'header.inc.php'); 

	if(mysql_num_rows($result) >=1){ ?>
	
<div class="paging">
	<ul>
	<?php for ($i=1; $i<=$num_pages; $i++): ?>
		<li>
			<?php if ($i == $page) : ?>
				<a class="current" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string;?>"><em><?php echo $i; ?></em></a>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
	</ul>
</div>
	<table class="data static" summary="">
	<tr>
		<th scope="col"><?php echo _AT('ec_student_name'); ?></th>
		<th scope="col"><?php echo _AT('ec_payment_made'); ?></th>
		<th scope="col"><?php echo _AT('ec_enroll_approved'); ?></th>
	</tr>
	<?php
	
		while($row = mysql_fetch_assoc($result)){
			
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
			$result4 = mysql_query($sql4, $db);
		while($row4 = mysql_fetch_array($result4)){
		
			if($row4['approved'] == 'y'){
				echo '<td>'._AT('yes').'</td>';
			}else{
				echo '<td>'._AT('no').'</td>';
			}
		}
	}
		echo '</table>';
}else{
	$msg->printInfos('EC_NO_STUDENTS_ENROLLED');
}

require (AT_INCLUDE_PATH.'footer.inc.php');

?>