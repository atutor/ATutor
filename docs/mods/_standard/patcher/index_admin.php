<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: index_admin.php 7208 2008-02-08 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_PATCHER);

set_time_limit(0);

/**
 * Generate html of each patch row at main patch page
 */
function print_patch_row($patch_row, $row_id, $enable_radiotton)
{
	global $id, $patch_id;   // current selected patch
	global $dependent_patches;

	if ($dependent_patches =="")
		$description = $patch_row["description"];
	else
		$description = $patch_row["description"] . _AT('patch_dependent_patch_not_installed') . "<span style='color: red'>" . $dependent_patches . "</span>";
?>
	<tr <?php if ($enable_radiotton) echo 'onmousedown="document.form[\'m'. $row_id.'\'].checked = true; rowselect(this);" id="r_'. $row_id .'"'; ?>>
		<td><input type="radio" name="id" value="<?php echo $row_id; ?>"<?php if ($enable_radiotton) echo 'id="m'. $row_id.'"'; ?> <?php if (!$enable_radiotton) echo 'disabled="disabled" '; if (strcmp($row_id, $id) == 0 || strcmp($row_id, $patch_id) == 0) echo "checked "?> /></td>
		<td><label <?php if ($enable_radiotton) echo 'for="m'.$row_id.'"'; ?>><?php echo $patch_row["atutor_patch_id"]; ?></label></td>
		<td><?php echo $description; ?></td>
		<td><?php if (!isset($patch_row['status'])) echo "Uninstalled"; else echo $patch_row["status"]; ?></td>
		<td><?php echo $patch_row["available_to"]; ?></td>
		<td>
		<?php 
		if (preg_match('/Installed/', $patch_row["status"]) > 0 && ($patch_row["remove_permission_files"]<> "" || $patch_row["backup_files"]<>"" || $patch_row["patch_files"]<> ""))
			echo '
		  <div class="row buttons">
				<input type="button" align="middle" name="info" value="'._AT('view_message').'" onclick="location.href=\''. $_SERVER['PHP_SELF'] .'?patch_id='.$row_id.'\'" />
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

if ($_POST['no'])
{
	unset($_SESSION['remove_permission']);
	$msg->addFeedback('CANCELLED');
	header('Location: index_admin.php');
	exit;
}

require_once('classes/PatchListParser.class.php');
require_once('include/common.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');

if (trim($_POST['who']) != '') $who = trim($_POST['who']);
elseif (trim($_REQUEST['who']) != '') $who = trim($_REQUEST['who']);
else $who = "public";

// check the connection to server update.atutor.ca
$update_server = "update.atutor.ca"; 
$connection_test_file = "http://" . $update_server . '/index.php';
$connection = @file_get_contents($connection_test_file);

if (!$connection) 
{
	print '<span style="color: red"><b>Error: Cannot connect to patch server: '. $update_server . '</b></span>';
	exit;
}

// get patch list
$patch_folder = "http://" . $update_server . '/patch/' . str_replace('.', '_', VERSION) . '/';

$patch_list_xml = @file_get_contents($patch_folder . 'patch_list.xml');

if ($patch_list_xml) 
{
	$patchListParser =& new PatchListParser();
	$patchListParser->parse($patch_list_xml);
	$patch_list_array = $patchListParser->getMyParsedArrayForVersion(VERSION);
}
// end of get patch list

$module_content_folder = AT_CONTENT_DIR . "patcher";
		
if ($_POST['install_upload'] && $_POST['uploading'])
{
	include_once(AT_INCLUDE_PATH . '/classes/pclzip.lib.php');
	
	// clean up module content folder
	clear_dir($module_content_folder);
	
	// 1. unzip uploaded file to module's content directory
	$archive = new PclZip($_FILES['patchfile']['tmp_name']);

	if ($archive->extract(PCLZIP_OPT_PATH, $module_content_folder) == 0)
	{
    clear_dir($module_content_folder);
    $msg->addError('CANNOT_UNZIP');
  }
}

// Installation process
if ($_POST['install'] || $_POST['install_upload'] && !isset($_POST["not_ignore_version"]))
{
	
	if (isset($_POST['id'])) $id=$_POST['id'];
	else $id = $_REQUEST['id'];

	if ($_POST['install'] && $id == "")
	{
		$msg->addError('CHOOSE_UNINSTALLED_PATCH');
	}
	else
	{
		if ($_POST['install'])
		{
			$patchURL = $patch_folder . $patch_list_array[$id][patch_folder] . "/";
		}
		else if ($_POST['install_upload'])
		{
			$patchURL = $module_content_folder . "/";
		}
			
		$patch_xml = @file_get_contents($patchURL . 'patch.xml');
		
		if ($patch_xml === FALSE) 
		{
			$msg->addError('PATCH_XML_NOT_FOUND');
		}
		else
		{
			require_once('classes/PatchParser.class.php');
			require_once('classes/Patch.class.php');
			
			$patchParser =& new PatchParser();
			$patchParser->parse($patch_xml);
			
			$patch_array = $patchParser->getParsedArray();

			if ($_POST["ignore_version"]) $patch_array["applied_version"] = VERSION;
			
			if ($_POST["install_upload"])
			{
				$current_patch_list = array('atutor_patch_id' => $patch_array['atutor_patch_id'],
																		'applied_version' => $patch_array['applied_version'],
																		'patch_folder' => $patchURL,
																		'available_to' => 'private',
																		'description' => $patch_array['description'],
																		'dependent_patches' => $patch_array['dependent_patches']);
			}

			if ($_POST["install"])
			{
				$current_patch_list = $patch_list_array[$id];
			}

			if ($_POST["install_upload"] && is_patch_installed($patch_array["atutor_patch_id"]))
				$msg->addError('PATCH_ALREADY_INSTALLED');
			else
			{
				$patch = & new Patch($patch_array, $current_patch_list, $skipFilesModified, $patchURL);
			
				if ($patch->applyPatch())  $patch_id = $patch->getPatchID();
			}
		}
	}
}
// end of patch installation

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
			if ($_POST['done']) $msg->printErrors('REMOVE_WRITE_PERMISSION');
			else $msg->printInfos('PATCH_INSTALLED_AND_REMOVE_PERMISSION');
			
			$feedbacks[] = _AT('remove_write_permission');
			
			foreach($remove_permission_files as $remove_permission_file)
				if ($remove_permission_file <> "") $feedbacks[count($feedbacks)-1] .= "<strong>" . $remove_permission_file . "</strong><br />";

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
		$msg->printFeedbacks('PATCH_INSTALLED_SUCCESSFULLY');
		
		if ($row["backup_files"]<> "")
		{
			$backup_files = get_array_by_delimiter($row["backup_files"], "|");
	
			if (count($backup_files) > 0)
			{
				$feedbacks[] = _AT('patcher_show_backup_files');
				
				foreach($backup_files as $backup_file)
					if ($backup_file <> "") $feedbacks[count($feedbacks)-1] .= "<strong>" . $backup_file . "</strong><br />";
			}
		}

		if ($row["patch_files"]<> "")
		{
			$patch_files = get_array_by_delimiter($row["patch_files"], "|");
	
			if (count($patch_files) > 0)
			{
				$feedbacks[] = _AT('patcher_show_patch_files');
				
				foreach($patch_files as $patch_file)
					if ($patch_file <> "") $feedbacks[count($feedbacks)-1] .= "<strong>" . $patch_file . "</strong><br />";
					
			}
		}
		
		if (count($feedbacks)> 0)
			print_feedback($feedbacks);
		else
			print_feedback(array());
	}
}

$msg->printErrors();

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
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
	while ($row = mysql_fetch_assoc($result))
	{
			print_patch_row($row, $row['patches_id'], false);
	}
	
	$array_id = 0;
	// display un-installed patches
	if(is_array($patch_list_array))
	{
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

<script language="JavaScript">
<!--

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

// This function validates if and only if a zip file is given
function validate_filename() {
  // check file type
  var file = document.frm_upload.patchfile.value;
  if (!file || file.trim()=='') {
    alert('Please give a zip file!');
    return false;
  }
  
  if(file.slice(file.lastIndexOf(".")).toLowerCase() != '.zip') {
    alert('Please upload ZIP file only!');
    return false;
  }
}

//  End -->
//-->
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
