<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<div class="input-form">

<table class="data" summary="" style="width: 100%" rules="cols">
<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('atutor_patch_id');?></th>
		<th scope="col"><?php echo _AT('description');?></th>
		<th scope="col"><?php echo _AT('status');?></th>
		<th scope="col"><?php echo _AT('available_to');?></th>
		<th scope="col"><?php echo _AT('author');?></th>
		<th scope="col"><?php echo _AT('installed_date');?></th>
		<th scope="col"><?php echo _AT('view_message');?></th>
	</tr>
</thead>
	
<tbody>
<?php 
if ($this->num_of_patches == 0)
{
?>

<tr>
	<td colspan="8">
<?php 
	echo _AT('none_found');
?>
	</td>
</tr>

<?php 
}
else
{
	while ($row = mysql_fetch_assoc($this->result))
	{
			print_patch_row($row, $row['patches_id'], false);
	}
	
	$array_id = 0;
	// display un-installed patches
	if(is_array($this->patch_list_array))
	{
		foreach ($this->patch_list_array as $row_num => $new_patch)
		{
			if (!is_patch_installed($new_patch['atutor_patch_id']))
			{
				$dependent_patches_installed = true;
				$dependent_patches = "";
				
				// check if the dependent patches are installed
				if (is_array($new_patch["dependent_patches"]))
				{
					
					foreach ($new_patch["dependent_patches"] as $num => $dependent_patch)
					{
						if (!is_patch_installed($dependent_patch))
						{
							$dependent_patches_installed = false;
							$dependent_patches .= $dependent_patch. ", ";
						}
					}
					
					// remove the last comma in the string
					if ($dependent_patches <> "") $dependent_patches = substr($dependent_patches, 0, -2);
				}
	
				// display patch row
				if ($dependent_patches_installed)
					print_patch_row($new_patch, $array_id++, true);
				else
				{
					print_patch_row($new_patch, $array_id++, false);
					$dependent_patches_installed = true;
				}
			}
			else
				$array_id++;
		}
	}
?>
</tbody>
<tfoot>
<tr>
	<td colspan="8">
		<input type="submit" name="install" value="<?php echo _AT('install'); ?>" />
	</td>
</tr>
</tfoot>

<?php 
}
?>
</table>

</div>
</form>

<form name="frm_upload" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	
<div class="input-form">
		<div class="row"><?php echo _AT("upload_patch"); ?></div>

		<div class="row">
			<input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
			<input type="file" name="patchfile"  size="50" />
		</div>
		
		<div class="row buttons">
			<input type="submit" name="install_upload" value="Install" onclick="javascript: return validate_filename(); " class="submit" />
			<input type="hidden" name="uploading" value="1" />
		</div>
</div>

</form>