<?php

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require (AT_INCLUDE_PATH.'html/frameset/header.inc.php');

$sql = "SELECT * from ".TABLE_PREFIX."ec_shop WHERE shopid = '$_GET[mtid]'";
$result = mysql_query($sql,$db);

$amount = floatval($_GET['amount']);
$mtid = intval($_GET['mtid']);
$date = date("F d, Y ");
?>
<a href="javascript:void(0)" onClick="window.print()">Print Invoice</a>
<br><br>
<table width="500px" style="border:1px solid rgb(112, 161, 202); margin:10px;">
<tr><td><h3>INVOICE</h3></td><td align="right"><img src="images/at-logo.gif" alt="" height="" width="" /></td></tr>
<tr><td>Invoice# <?php echo $mtid; ?></td><td><?php echo $date;  ?></td></tr>
<tr>
	<td width="50%">
	To: <br /><?php
	while($row = mysql_fetch_assoc($result)){
		if($row['lastname']){
			$contribinfo .= ''.$row['firstname'].' ' .$row['lastname'].'<br />';
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
		if($row['postal']){
			$contribinfo .= ''.$row['postal'].'<br />';
		}
		if($row['telephone']){
			$contribinfo .= ''.$row['telephone'].'<br />';
		}
		if($row['country']){
			$contribinfo .= ''.$row['country'].'<br />';
		}
		if($row['comment']){
			$contribinfo .= ''.$row['comment'].'<br />';
		}
		if($row['course_name']){
			$course_name = $row['course_name'];
		}	
		echo $contribinfo;
		}
	?>

	</td>
	<td width="50%" valign="top">
	From:<br />
	<?php echo nl2br($_config['ec_contact_address']); 

		?>
	</td>
</tr>
</table>
<table  width="500px" style="border:1px solid rgb(112, 161, 202); margin:10px;">
	<th width="75%" align="left">Course Name</th><th width="25%" align="left">Fee</th></tr>
	<?php
			echo '<tr><td>'.$course_name.'</td>';
			echo '<td>'.$_config['ec_currency_symbol'].$amount.'</td></tr>';
			echo '<tr><td colspan="2"><hr /></td></tr>';	
			echo '<tr><td><strong>Total including taxes:</strong?></td><td>'.$_config['ec_currency_symbol'].$amount.' '.$_config['ec_currency'].'</td></tr>';
	?>
</table>

<table  width="500px" style="border:1px solid rgb(112, 161, 202); margin:10px;">
<tr><td><?php echo _AT('sent_via_atutor', $_base_href);?>
</td></tr></table>
<?php
require (AT_INCLUDE_PATH.'html/frameset/footer.inc.php');
?>