<?php
if ($this->enable_upload) {
?>
<form name="frm_upload" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	
<div class="input-form">
		<div class="row"><?php echo _AT("upload_module"); ?></div>

		<div class="row">
			<input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
			<input type="file" name="modulefile"  size="50" />
		</div>
		
		<div class="row buttons">
			<input type="submit" name="install_upload" value="<?php echo _AT('install'); ?>" onclick="javascript: return validate_filename(); " class="submit" />
			<input type="hidden" name="uploading" value="1" />
		</div>
</div>

</form>

<?php 
} // end of enable_upload

if (count($this->keys) > 0)
{
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="installform">
<table class="data" summary="">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('module_name'); ?></th>
	<th scope="col"><?php echo _AT('directory_name'); ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<td colspan="4">
		<input type="submit" name="install_manually"  value="<?php echo _AT('install'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php if (!empty($this->keys)): ?>
	<?php foreach($this->keys as $dir_name) : $module =& $this->module_list[$dir_name]; ?>
		<tr onmousedown="document.installform['m_<?php echo $dir_name; ?>'].checked = true; rowselect(this);" id="r_<?php echo $dir_name; ?>">
			<td valign="top"><input type="radio" id="m_<?php echo $dir_name; ?>" name="mod" value="<?php echo $dir_name; ?>" /></td>
			<td valign="top"><label for="m_<?php echo $row['dir_name']; ?>"><?php echo $module->getName(); ?></label></td>
			<td valign="top"><code><?php echo $dir_name; ?>/</code></td>
			<td valign="top"><?php echo $module->getDescription($_SESSION['lang']); ?></td>
		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>
<br />
<?php 
} else {
?>
<div style="border:1p solid #F6F4DA">
<p> No modules available to install</p>

</div>

<?php } // end of displaying local modules

// Disallow subsites to download and install the remote modules from update.atutor.ca
if ($this->enable_remote_installation === true) {
?>
<fieldset>
    <legend><?php echo _AT('filter'); ?></legend>
    <div class="input-form">
    <div class="row">
    <?php echo _AT('old_module_notes'); ?>
    </div>
<?php echo select_atversion(); ?>
</div>
</fieldset>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<table class="data" summary="">
<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('module_name');?></th>
		<th scope="col"><?php echo _AT('description');?></th>
		<th scope="col"><?php echo _AT('version');?></th>
		<th scope="col"><?php echo _AT('atutor_version_tested_with');?></th>
		<th scope="col"><?php echo _AT('maintainers');?></th>
		<th scope="col"><?php echo _AT('installed').'?';?></th>
	</tr>
</thead>
	
<tfoot>
<tr>
	<td colspan="7">
		<input type="submit" name="install" value="<?php echo _AT('install'); ?>" />
		<input type="submit" name="download" value="<?php echo _AT('download'); ?>" />
		<input type="submit" name="version_history" value="<?php echo _AT('version_history'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php 
$num_of_modules = count($this->module_list_array);

if ($num_of_modules == 0)
{
?>

<tr>
	<td colspan="7"><?php echo _AT('none_found'); ?></td>
</tr>

<?php 
}
else
{
	// display modules
	if(is_array($this->module_list_array))
	{
		for ($i=0; $i < $num_of_modules; $i++)
		{
			$installed = false;
			if(in_array($this->module_list_array[$i]["history"][0]["install_folder"],  '')){
			    $installed = true;
			} 
			
			if((isset($_POST['atversions']) && $_POST['atversions'] == $this->module_list_array[$i]["atutor_version"]) || $_POST['atversions'] == 0){
?>
            <tr onmousedown="document.form['m<?php echo $i; ?>'].checked = true; rowselect(this);"  id="r_<?php echo $i; ?>">
                <td><input type="radio" name="id" value="<?php echo $i; ?>" id="m<?php echo $i; ?>" <?php if ($installed) echo 'disabled="disabled"'; ?> /></td>
                <td><label for="m<?php echo $i; ?>"><?php echo $this->module_list_array[$i]["name"]; ?></label></td>
                <td style="width:45%;"><?php echo $this->module_list_array[$i]["description"]; ?></td>
                <td><?php echo $this->module_list_array[$i]["history"][0]["version"]; ?></td>
                <td><?php echo $this->module_list_array[$i]["atutor_version"]; ?></td>
                <td><?php echo $this->module_list_array[$i]["history"][0]["maintainer"]; ?></td>
                <td><?php if ($installed) echo _AT("installed"); else echo _AT("not_installed"); ?></td>
            </tr>

<?php 
            } // end if
		}
	}

?>
</tbody>

<?php 
}
?>
</table>
</form>
<?php } // end of enable_remote_installation ?>