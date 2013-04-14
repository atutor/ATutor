<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table summary="" class="data" rules="cols" align="center" style="width: 95%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('atutor_patch_id'); ?></th>
	<th scope="col"><?php echo _AT('atutor_version_to_apply'); ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
	<th scope="col"><?php echo _AT('last_modified'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="remove" value="<?php echo _AT('remove'); ?>" /> 
		</div>
	</td>
</tr>
<tr>
	<td colspan="5"></td>
</tr>
</tfoot>
<tbody>
<?php

if (mysql_num_rows($this->result) == 0)
{
?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php 
}
else
{
	while ($row = mysql_fetch_assoc($this->result))
	{
	?>
		<tr onmousedown="document.form['m<?php echo $row['myown_patch_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['myown_patch_id']; ?>">
			<td width="10"><input type="radio" name="myown_patch_id" value="<?php echo $row['myown_patch_id']; ?>" id="m<?php echo $row['myown_patch_id']; ?>" <?php if ($row['myown_patch_id']==$_POST['myown_patch_id']) echo 'checked'; ?> /></td>
			<td><label for="m<?php echo $row['myown_patch_id']; ?>"><?php echo $row['atutor_patch_id']; ?></label></td>
			<td><?php echo $row['applied_version']; ?></td>
			<td><?php echo $row['description']; ?></td>
			<td><?php echo $row['last_modified']; ?></td>
		</tr>
<?php 
	}
}
?>

</tbody>
</table>

</form>
