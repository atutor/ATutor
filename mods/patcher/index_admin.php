<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PATCHER);
require (AT_INCLUDE_PATH.'header.inc.php');

set_time_limit(0);

/**
 * Check if the patch has been installed
 */
function is_patch_installed($patch_id)
{
	global $db;
	
	// Only displays the patches that are not installed
	$sql = "select count(*) num_of_installed from ".TABLE_PREFIX."patches " .
	       "where atutor_patch_id = '" . $patch_id ."'".
	       " and status like '%Installed'";

	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	
	if ($row["num_of_installed"] > 0) return true;
	else return false;
}

/**
 * Generate html of each patch row at main patch page
 */
function print_patch_row($patch_row, $id, $enable_radiotton)
{
	global $dependent_patches;

	if ($dependent_patches =="")
		$description = $patch_row["description"];
	else
		$description = $patch_row["description"] . _AT('patch_dependent_patch_not_installed') . "<font color='red'>" . $dependent_patches . "</font>";
?>
	<tr>
		<td><input type="radio" name="id" value="<?php echo $id; ?>" id="<?php echo $id; ?>" <?php if (!$enable_radiotton) echo 'disabled="true"'; ?> /></td>
		<td><?php echo $patch_row["atutor_patch_id"]; ?></td>
		<td><?php echo $description; ?></td>
		<td><?php if (!isset($patch_row['status'])) echo "Uninstalled"; else echo $patch_row["status"]; ?></td>
		<td><?php echo $patch_row["available_to"]; ?></td>
		<td>
		<?php 
		if (preg_match('/Installed/', $patch_row["status"]) > 0 && ($patch_row["remove_permission_files"]<> "" || $patch_row["backup_files"]<>""))
			echo '
		  <div class="row buttons">
				<input type="button" align="center" name="info" value="'._AT('view_message').'" onClick="location.href=\''. $_SERVER['PHP_SELF'] .'?patch_id='.$id.'\'" />
			</div>';
		?>
		</td>
	</tr>
<?php
}

// split a string by given delimiter and return an array
function get_array_by_delimiter($subject, $delimiter)
{
	return preg_split('/'.preg_quote($delimiter).'/', $subject, -1, PREG_SPLIT_NO_EMPTY);
}

$skipFilesModified = false;

if ($_POST['yes'])  $skipFilesModified = true;

require_once('PatchListParser.class.php');

if (trim($_POST['who']) != '') $who = trim($_POST['who']);
elseif (trim($_REQUEST['who']) != '') $who = trim($_REQUEST['who']);
else $who = "public";

// check the connection to server update.atutor.ca
$update_server = "update.atutor.ca"; 

$file = fsockopen ($update_server, 80, $errno, $errstr, 15);

if (!$file) 
{
	print "<font color='red'><b>Error: Cannot connect to patch server: ". $update_server . "</b></font>";
	exit;
}

// get patch list
$patch_folder = "http://" . $update_server . '/patch/' . str_replace('.', '_', VERSION) . '/';

$patch_list_xml = @file_get_contents($patch_folder . 'patch_list.xml');

if ($patch_list_xml === FALSE) 
{
	echo _AT('none_found');
}
else
{
	$patchListParser =& new PatchListParser();
	$patchListParser->parse($patch_list_xml);
	$patch_list_array = $patchListParser->getMyParsedArrayForVersion($who, VERSION);
}
// end of get patch list

// Installation process
if ($_POST['install'] || $_POST['yes'])
{
//	unset($_SESSION['remove_permission']);

	if ($_POST['install']) $id=$_POST['id'];
	else $id = $_REQUEST['id'];
	
	if (is_patch_installed($id))
	{
		$msg->addError('PATCH_ALREADY_INSTALLED');
	}
	else
	{
		$patchURL = $patch_folder . $patch_list_array[$id][patch_folder] . "/";
		$patch_xml = @file_get_contents($patchURL . 'patch.xml');
		
		if ($patch_xml === FALSE) 
		{
			$msg->addError('PATCH_XML_NOT_FOUND');
		}
		else
		{
			require_once('PatchParser.class.php');
			require_once('Patch.class.php');
			
			$patchParser =& new PatchParser();
			$patchParser->parse($patch_xml);

			$patch = & new Patch($patchParser->getParsedArray(), $patch_list_array[$id], $skipFilesModified);
			
			if ($patch->applyPatch())  $patch_id = $patch->getPatchID();
		}
	}
}
// end of patch installation

require_once('common.inc.php');

// display permission and backup files message
if (isSet($_REQUEST['patch_id']))  $patch_id = $_REQUEST['patch_id'];
elseif ($_POST['patch_id']) $patch_id=$_POST['patch_id'];

if ($patch_id > 0)
{
	// clicking on button "Done" at displaying remove permission info page
	if ($_POST['done'])
	{
		$permission_files = array();
		
		if (is_array($_SESSION['remove_permission']))
		{
			foreach ($_SESSION['remove_permission'] as $file)
			{
				if (is_writable($file))  $permission_files[] = $file;
			}
		}
		
		if (count($permission_files) == 0)
		{
			$updateInfo = array("remove_permission_files"=>"", "status"=>"Installed");
		
			updatePatchesRecord($patch_id, $updateInfo);
		}
		else
		{
			foreach($permission_files as $permission_file)
				$remove_permission_files .= $permission_file. '|';
		
			$updateInfo = array("remove_permission_files"=>preg_quote($remove_permission_files), "status"=>"Partly Installed");
			
			updatePatchesRecord($patch_id, $updateInfo);
		}
	
	}
	
	// display remove permission info
	unset($_SESSION['remove_permission']);

	$sql = "SELECT * FROM ".TABLE_PREFIX."patches 
	         WHERE patches_id = " . $patch_id;

	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	
	if ($row["remove_permission_files"]<> "")
	{
		$remove_permission_files = $_SESSION['remove_permission'] = get_array_by_delimiter($row["remove_permission_files"], "|");

		if (count($_SESSION['remove_permission']) > 0)
		{
			$feedbacks[] = _AT('remove_write_permission');
			
			foreach($remove_permission_files as $remove_permission_file)
				if ($remove_permission_file <> "") $feedbacks[count($feedbacks)-1] .= "<strong>" . $remove_permission_file . "</strong><br>";

			$notes = '<form action="'. $_SERVER['PHP_SELF'].'?patch_id='.$patch_id.'" method="post" name="remove_permission">
		  <div class="row buttons">
				<input type="hidden" name="patch_id" value="'.$patch_id.'" />
				<input type="submit" name="done" value="'._AT('done').'" accesskey="d" />
			</div>
			</form>';
		}

		print_errors($feedbacks, $notes);
	}

	// display backup file info after remove permission step
	if ($row["remove_permission_files"] == "")
	{
		if ($row["backup_files"]<> "")
		{
			$backup_files = get_array_by_delimiter($row["backup_files"], "|");
	
			if (count($backup_files) > 0)
			{
				$feedbacks[] = _AT('patcher_show_backup_files');
				
				foreach($backup_files as $backup_file)
					if ($backup_file <> "") $feedbacks[count($feedbacks)-1] .= "<strong>" . $backup_file . "</strong><br>";
			}
					
			$patch_files = get_array_by_delimiter($row["patch_files"], "|");
	
			if (count($patch_files) > 0)
			{
				$feedbacks[] = _AT('patcher_show_patch_files');
				
				foreach($patch_files as $patch_file)
					if ($patch_file <> "") $feedbacks[count($feedbacks)-1] .= "<strong>" . $patch_file . "</strong><br>";
					
			}
			
			if (count($feedbacks)> 0)
				print_feedback($feedbacks);
			else
				print_feedback(array());
		}
	}
}

$msg->printErrors();

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="get_patch_form">
<div class="input-form">
	<div class="row">
		<label for="who"><?php echo _AT('name'); ?></label>
		<input type="text" name="who" id="who" size="80" value="<?php echo $who; ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('get_my_patch'); ?>" name="get_my_patch" />
	</div>
</div>

<div class="input-form">

<?php 
?>
<table class="data" summary="" style="width: 100%" rules="cols">
<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('atutor_patch_id');?></th>
		<th scope="col"><?php echo _AT('description');?></th>
		<th scope="col"><?php echo _AT('status');?></th>
		<th scope="col"><?php echo _AT('available_to');?></th>
		<th scope="col"><?php echo _AT('view_message');?></th>
	</tr>
</thead>
	
<tbody>
<?php 
// display installed patches
$sql = "select * from ".TABLE_PREFIX."patches " .
       "where applied_version = '" . VERSION . "' ".
       "order by atutor_patch_id";

$result = mysql_query($sql, $db);
$num_of_patches = mysql_num_rows($result) + count($patch_list_array);

if ($num_of_patches == 0)
{
	echo _AT('none_found');
}
else
{
	while ($row = mysql_fetch_assoc($result))
	{
			print_patch_row($row, $row['atutor_patch_id'], false);
	}
	
	$array_id = 0;
	// display un-installed patches
	foreach ($patch_list_array as $row_num => $new_patch)
	{
		if (!is_patch_installed($new_patch['atutor_patch_id']))
		{
			$dependent_patches_installed = true;
		
			// check if the dependent patches are installed
			if (is_array($new_patch["dependent_patches"]))
			{
				$dependent_patches = "";
				foreach ($new_patch["dependent_patches"] as $num => $dependent_patch)
				{
					if (!is_patch_installed($dependent_patch))
					{
						$dependent_patches_installed = false;
						$dependent_patches .= $dependent_patch. "<br>";
					}
				}
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

?>
</tbody>
<tfoot>
<tr>
	<td colspan="7">
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

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
