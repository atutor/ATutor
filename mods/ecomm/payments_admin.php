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

/// Get a list of those who have made payments

	//$sql = "SELECT  s.course_id,  s.date, s.shopid, s.member_id, f.course_fee, f.auto_approve , m.login, m.first_name, m.last_name from ".TABLE_PREFIX."ec_shop AS s, ".TABLE_PREFIX."ec_course_fees AS f, ".TABLE_PREFIX."members AS m GROUP BY m.login, m.first_name, m.last_name LIMIT $offset, $results_per_page";
	$sql = "SELECT  s.course_id,  s.date, s.shopid, s.member_id, f.course_fee, f.auto_approve, m.login, m.first_name, m.last_name from ".TABLE_PREFIX."ec_shop AS s, ".TABLE_PREFIX."ec_course_fees AS f, ".TABLE_PREFIX."members AS m WHERE s.member_id = m.member_id AND  s.course_id = f.course_id ORDER BY s.date DESC LIMIT $offset, $results_per_page";
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
		<th scope="col"><?php echo _AT('ec_course_name'); ?></th>
		<th scope="col"><?php echo _AT('ec_date'); ?></th>
	</tr>
	<?php
		/// Loop through each payment
		while($row = mysql_fetch_assoc($result)){
			
			echo "\n".'<tr><td><a href="admin/edit_user.php?id='.$row['member_id'].'">'.$row['first_name'].' '.$row['last_name'].' ('.$row['login'].')</a></td>';
		
			$sql3 = "SELECT amount from ".TABLE_PREFIX."ec_shop WHERE course_id = '$row[course_id]' AND member_id = '$row[member_id]'";
			$result3 = mysql_query($sql3,$db);
	
			$amount_paid = '';
			while($row3 = mysql_fetch_assoc($result3)){
				$amount_paid = $amount_paid+$row3['amount'];
			}
			if($amount_paid != 0){
			
				echo '<td>'.$_config['ec_currency_symbol'].$amount_paid.'</td>';
			}else{
				echo '<td>'.$_config['ec_currency_symbol'].'0</td>';
			}	
		
			$sql6 = "SELECT * from ".TABLE_PREFIX."course_enrollment WHERE course_id = '$row[course_id]' AND member_id = '$row[member_id]'";
			$result6 = mysql_query($sql6, $db);

		while($row6 = mysql_fetch_assoc($result6)){
		
			if($row6['approved'] == 'y'){
				echo '<td>'._AT('yes').'</td>';
			}else{
				echo '<td>'._AT('no').' </td>';
			}
		}
		/// Get the course title	
			$sql5= "SELECT  title from ".TABLE_PREFIX."courses WHERE course_id = '$row[course_id]'";
			$result5 = mysql_query($sql5,$db);
			if($course_title  = mysql_result($result5, 0)){
				echo '<td><a href="admin/enrollment/index.php?tab=0'.SEP.'course_id='.$row['course_id'].'">'.$course_title.'</a></td>';
			}else{
				echo '<td>'._AT('na').'</td>';
			}
		/// Get the payment date
			if($row['date']){
 				echo '<td>'.$row['date'].'</td></tr>';
 			}else{
				echo '<td>'._AT('na').'</td></tr>';
 			}
		//echo '</tr>'."\n";
	}
		echo '</table>'."\n";

}else{
	$msg->printInfos('EC_NO_STUDENTS_ENROLLED');
}

require (AT_INCLUDE_PATH.'footer.inc.php');

?>