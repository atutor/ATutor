<?php
function print_errors( $errors, $notes='' ) {
	?>
	<div class="input-form">
	<table border="0" class="errbox" cellpadding="3" cellspacing="2" width="100%" summary="" align="center">
	<tr class="errbox">
		<td>
		<h3 class="err"><img src="images/bad.gif" align="top" alt="" class="img" /> Warning</h3>
		<?php
			echo '<ul>';
			foreach ($errors as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?>
		</td>
	</tr>
	<tr>
		<td>
		<?php echo $notes; ?>
		</td>
	</tr>
	</table>
	</div>
<?php
}

function print_feedback( $feedback, $notes='' ) {
	?>
	<div class="input-form">
	<table border="0" class="fbkbox" cellpadding="3" cellspacing="2" width="100%" summary="" align="center">
	<tr class="fbkbox">
	<td><h3 class="feedback2"><img src="images/feedback.gif" align="top" alt="" class="img" /> The patch has been installed successfully!</h3>
		<?php
			echo '<ul>';
			foreach ($feedback as $p) {
				echo '<li>'.$p.'</li>';
			}
			echo '</ul>';
		?></td>
	</tr>
	<tr>
		<td>
		<?php echo $notes; ?>
		</td>
	</tr>
	</table>
	</div>
<?php
}


/**
* update patches.remove_permission_files & patches.backup_files
* @access  private
* @author  Cindy Qi Li
*/
function updatePatchesRecord($patch_id, $updateInfo)
{
	global $db;
	
	$sql_prefix = "Update ". TABLE_PREFIX. "patches set ";
	
	foreach ($updateInfo as $key => $value)
	{
		$sql_middle .= $key . "='" . $value . "', ";
	}
	
	$sql = substr($sql_prefix . $sql_middle, 0, -2) . " where patches_id = " . $patch_id;

	$result = mysql_query($sql, $db) or die(mysql_error());
	
	return true;
}

/**
* This function deletes $dir recrusively without deleting $dir itself.
* @access  public
* @param   string $charsets_array	The name of the directory where all files and folders under needs to be deleted
* @author  Cindy Qi Li
*/
function clear_dir($dir) {
	include_once(AT_INCLUDE_PATH . '/lib/filemanager.inc.php');
	
	if(!$opendir = @opendir($dir)) {
		return false;
	}
	
	while(($readdir=readdir($opendir)) !== false) {
		if (($readdir !== '..') && ($readdir !== '.')) {
			$readdir = trim($readdir);

			clearstatcache(); /* especially needed for Windows machines: */

			if (is_file($dir.'/'.$readdir)) {
				if(!@unlink($dir.'/'.$readdir)) {
					return false;
				}
			} else if (is_dir($dir.'/'.$readdir)) {
				/* calls lib function to clear subdirectories recrusively */
				if(!clr_dir($dir.'/'.$readdir)) {
					return false;
				}
			}
		}
	} /* end while */

	@closedir($opendir);
	
	return true;
}

?>
