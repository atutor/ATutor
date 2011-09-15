<?php 
//$this->module_list_array[$id]['history']
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<div class="input-form">
<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
<table class="data" summary="" style="width: 100%" rules="cols">
<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('version');?></th>
		<th scope="col"><?php echo _AT('publish_date');?></th>
		<th scope="col"><?php echo _AT('state');?></th>
		<th scope="col"><?php echo _AT('maintainers');?></th>
		<th scope="col"><?php echo _AT('notes');?></th>
	</tr>
</thead>

<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="download" value="<?php echo _AT('download'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php 
$num_of_versions = count($this->module_list_array[$this->id]['history']);

if ($num_of_versions == 0)
{
?>

<tr>
	<td colspan="7">
<?php 
	echo _AT('none_found'); 
?>
	</td>
</tr>

<?php 
}
else
{
	// display version list
	if(is_array($this->module_list_array[$this->id]['history']))
	{
		for ($i=0; $i < $num_of_versions; $i++)
		{
?>
	<tr onmousedown="document.form['m<?php echo $i; ?>'].checked = true; rowselect(this);"  id="r_<?php echo $i; ?>">
		<td><input type="radio" name="vid" value="<?php echo $i; ?>" id="m<?php echo $i; ?>" /></td>
		<td><label for="m<?php echo $i; ?>"><?php echo $this->module_list_array[$this->id]["name"] . ' ' .$this->module_list_array[$this->id]['history'][$i]["version"]; ?></label></td>
		<td><?php echo $this->module_list_array[$this->id]['history'][$i]["date"]; ?></td>
		<td><?php echo $this->module_list_array[$this->id]['history'][$i]["state"]; ?></td>
		<td><?php echo $this->module_list_array[$this->id]['history'][$i]["maintainer"]; ?></td>
		<td><?php echo $this->module_list_array[$this->id]['history'][$i]["notes"]; ?></td>
	</tr>

<?php 
		}
	}

?>
</tbody>

<?php 
}
?>
</table>

</div>
</form>