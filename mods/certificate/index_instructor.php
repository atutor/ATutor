<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: index_instructor.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CERTIFICATE);
require(AT_INCLUDE_PATH.'lib/themes.inc.php');

if (isset($_POST['remove'], $_POST['certificate_id'])) 
{
	header('Location: certificate_delete.php?certificate_id='.$_POST['certificate_id']);
	exit;
} 
else if (isset($_POST['edit'], $_POST['certificate_id'])) 
{
	header('Location: certificate_edit.php?certificate_id='.$_POST['certificate_id']);
	exit;
} 
else if (!empty($_POST) && !isset($_POST['certificate_id'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 70%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<!--<th scope="col"><?php echo _AT('certificate_id'); ?></th>-->
	<th scope="col"><?php echo _AT('test_title'); ?></th>
	<th scope="col"><?php echo _AT('pass_score'); ?></th>
	<th scope="col"><?php echo _AT('created_date'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<div class="row buttons">
		<input type="button" name="preview" value="<?php echo _AT('preview'); ?>" onClick="open_certificate_win('<?php echo dirname($_SERVER["PHP_SELF"])?>/open_certificate.php?test_id={hidden_value}&certificate_id={radio_value}', 'certificate_id', 'test_id')" />
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="remove" value="<?php echo _AT('remove'); ?>" /> 
		</div>
	</td>
</tr>
</tfoot>
<tbody>
<?php
$include_javascript = true;

require("common.inc.php");

if (is_pass_score_defined_in_base_table())
	$sql = "SELECT c.*, t.title, t.passscore, t.passpercent from ".TABLE_PREFIX."tests t, ".TABLE_PREFIX."certificate c WHERE t.test_id = c.test_id";
else
	$sql = "SELECT c.*, t.title from ".TABLE_PREFIX."tests t, ".TABLE_PREFIX."certificate c WHERE t.test_id = c.test_id";

$result = mysql_query($sql, $db) or die(mysql_error());

if (mysql_num_rows($result) == 0)
{
?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php 
}
else
{
	while ($row = mysql_fetch_assoc($result))
	{
	?>
		<tr onmousedown="document.form['m<?php echo $row['certificate_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['certificate_id']; ?>">
			<td width="10"><input type="radio" name="certificate_id" value="<?php echo $row['certificate_id']; ?>" id="m<?php echo $row['certificate_id']; ?>" <?php if ($row['certificate_id']==$_POST['certificate_id']) echo 'checked'; ?> /></td>
			<!--<td><label for="m<?php echo $row['certificate_id']; ?>"><?php echo $row['certificate_id']; ?></label></td>-->
			<td><label for="m<?php echo $row['certificate_id']; ?>"><?php echo $row['title']; ?></label></td>
			<td>
<?php 
if ($row['passscore'] <> 0) 
	echo $row['passscore']; 
elseif ($row['passpercent'] <> 0) 
	echo $row['passpercent'] . "%"; 
else echo _AT("na"); 
?></td>
			<td><?php echo $row['created_date']; ?></td>
			<input type="hidden" name="test_id" value="<?php echo $row['test_id']; ?>">
		</tr>
<?php 
	}
}
?>

</tbody>
</table>

</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
