<?php

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require (AT_INCLUDE_PATH.'html/frameset/header.inc.php');

$sql = "SELECT * from ".TABLE_PREFIX."members WHERE member_id = '$_SESSION[member_id]'";
$result = mysql_query($sql,$db);

$amount = floatval($_GET['amount']);
$mtid = intval($_GET['mtid']);
$date = date("F d, Y ");
?>
<a href="javascript:void(0)" onClick="window.print()"><?php echo _AT('ec_print_invoice'); ?></a>
<br><br>
<table width="500px" style="border:1px solid rgb(112, 161, 202); margin:10px;">
<tr><td><h3><?php echo _AT('ec_invoice'); ?></h3></td><td align="right"><img src="images/at-logo.gif" alt="" height="" width="" /></td></tr>
<tr><td><?php echo _AT('ec_invoice'); ?># AT_<?php echo $_GET['payment_id']; ?></td><td><?php echo $date;  ?></td></tr>
<tr>
	<td width="50%" valign="top">
	<?php echo _AT('to'); ?>: <br /><?php
	while($row = mysql_fetch_assoc($result)){
		if($row['last_name']){
			$contribinfo .= ''.$row['first_name'].' ' .$row['last_name'].'<br />';
		}
		if($row['organization']){
			$contribinfo .= ''.$row['organization'].'<br />';
		}
		if($row['email']){
			$contribinfo .= ''.$row['email'].'<br />';
		}
		if($row['address']){
			$contribinfo .= ''.$row['address'].'<br />';
		}
		if($row['city']){
			$contribinfo .= ''.$row['city'].'<br />';
		}
		if($row['province']){
			$contribinfo .= ''.$row['province'].'<br />';
		}
		if($row['postal']){
			$contribinfo .= ''.$row['postal'].'<br />';
		}
		if($row['phone']){
			$contribinfo .= ''.$row['phone'].'<br />';
		}
		if($row['country']){
			$contribinfo .= ''.$row['country'].'<br />';
		}
	
		echo $contribinfo;
		}
	?>

	</td>
	<td width="50%" valign="top">
	<?php echo _AT('from'); ?><br />
	<?php echo nl2br($_config['ec_contact_address']); 

		?>
	</td>
</tr>
</table>
<table  width="500px" style="border:1px solid rgb(112, 161, 202); margin:10px;">
	<th width="75%" align="left"><?php echo _AT('ec_course_name'); ?></th><th width="25%" align="left"><?php echo _AT('ec_fees'); ?></th></tr>
	<?php
			echo '<tr><td>'. $_GET['course_title'].'</td>';
			echo '<td>'.$_config['ec_currency_symbol'].$amount.'</td></tr>';
			echo '<tr><td colspan="2"><hr /></td></tr>';	
			echo '<tr><td><strong>'._AT('ec_course_name').':</strong></td><td>'.$_config['ec_currency_symbol'].$amount.' '.$_config['ec_currency'].'</td></tr>';
	?>
</table>

<table  width="500px" style="border:1px solid rgb(112, 161, 202); margin:10px;">
<tr><td><?php echo _AT('sent_via_atutor', $_base_href);?>
</td></tr></table>
<?php
require (AT_INCLUDE_PATH.'html/frameset/footer.inc.php');
?>