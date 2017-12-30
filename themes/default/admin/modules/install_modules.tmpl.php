<?php
if ($this->enable_upload) {
?>
<form name="frm_upload" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	
<div class="input-form">
		<div class="row"><?php echo _AT("upload_module"); ?></div>

		<div class="row">
			<input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
			<input type="hidden" name="csrftoken"  value="<?php echo $_SESSION['token'];?>" />
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
<input type="hidden" name="csrftoken"  value="<?php echo $_SESSION['token'];?>" />
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
?>