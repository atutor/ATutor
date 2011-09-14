<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_MODULES);
require(AT_INCLUDE_PATH.'../mods/_core/modules/classes/ModuleListParser.class.php');
require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

// delete all folders and files in $dir
function clear_dir($dir)
{
	if ($dh = opendir($dir)) 
	{
		while (($file = readdir($dh)) !== false)
		{
			if (($file == '.') || ($file == '..'))
				continue;

			if (is_dir($dir.$file)) 
				clr_dir($dir.$file);
			else 
				unlink($dir.$file);
		}
		
		closedir($dh);
	}
}

set_time_limit(0);

// check the connection to server update.atutor.ca
$update_server = "http://update.atutor.ca"; 
$connection_test_file = $update_server . '/index.php';
$connection = @file_get_contents($connection_test_file);

if (!$connection) 
{
	$msg->addInfo(array('CANNOT_CONNECT_MOD_SERVER'));
}
else
{
	// get module list
	$module_folder = $update_server . '/modules/';
	
	$module_list_xml = @file_get_contents($module_folder . 'module_list.xml');
	
	if ($module_list_xml) 
	{
		$moduleListParser = new ModuleListParser();
		$moduleListParser->parse($module_list_xml);
		$module_list_array = $moduleListParser->getParsedArray();
	}
	// end of get module list
	
	$module_content_folder = AT_CONTENT_DIR . "module/";
	
	if (!is_dir($module_content_folder)) mkdir($module_content_folder);
}
// end of get module list

$module_content_folder = AT_CONTENT_DIR . "module/";

if (!is_dir($module_content_folder)) mkdir($module_content_folder);

// Installation process
if ((isset($_POST['install']) || isset($_POST["download"]) || isset($_POST["version_history"])) && !isset($_POST["id"]))
{
	$msg->addError('NO_ITEM_SELECTED');
}
else if (isset($_POST['install']) || isset($_POST["download"]) || isset($_POST["version_history"]) || isset($_POST["install_upload"]))
{
	if ($_POST['version_history'])
	{
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/version_history.php?id='.$_POST["id"]);
		exit;
	}

	// install and download
	if ($_POST["install_upload"])
		$module_zip_file = $_FILES['modulefile']['tmp_name'];
	else
		$module_zip_file = $module_folder . $module_list_array[$_POST["id"]]['history'][0]['location'].$module_list_array[$_POST["id"]]['history'][0]['filename'];
		
	$file_content = file_get_contents($module_zip_file);

	if (!$file_content & ($_POST['install'] || $_POST['download']))
	{
		$msg->addError('FILE_NOT_EXIST');
	}
	else
	{
		if ($_POST['install'] || $_POST['install_upload'])
		{
			clear_dir($module_content_folder);
			
			// download zip file from update.atutor.ca and write into module content folder
			if ($_POST["install_upload"])
				$local_module_zip_file = $module_content_folder . $_FILES['modulefile']['name'];
			else
				$local_module_zip_file = $module_content_folder. $module_list_array[$_POST["id"]]['history'][0]['filename'];
			
			$fp = fopen($local_module_zip_file, "w");
			fwrite($fp, $file_content);
			fclose($fp);
			
			// unzip uploaded file to module's content directory
			include_once(AT_INCLUDE_PATH . '/classes/pclzip.lib.php');
			
			$archive = new PclZip($local_module_zip_file);
		
			if ($archive->extract(PCLZIP_OPT_PATH, $module_content_folder) == 0)
			{
		    clear_dir($module_content_folder);
		    $msg->addError('CANNOT_UNZIP');
		  }
		
		  if (!$msg->containsErrors())
		  {
				// find unzip module folder name
				clearstatcache();
				
				if ($dh = opendir($module_content_folder)) 
				{
					while (($module_folder = readdir($dh)) !== false)
					{
						if ($module_folder <> "." && $module_folder <> ".." && is_dir($module_content_folder.$module_folder)) break;
					}
					
					closedir($dh);
				}

				if ($module_folder == "." || $module_folder == ".." || !isset($module_folder))
					$msg->addError('EMPTY_ZIP_FILE');
			}
		
		  // check if the same module exists in "mods" folder. If exists, it has been installed
		  if (!$msg->containsErrors())
		  {
		  	if (is_dir("../../../mods/". $module_folder))
		  		$msg->addError('ALREADY_INSTALLED');
		  }

		  if (!$msg->containsErrors())
		  {
				header('Location: module_install_step_1.php?mod='.urlencode($module_folder).SEP.'new=1');
				exit;
			}
		}
		
		if ($_POST['download'])
		{
			$id = intval($_POST['id']);
		
			header('Content-Type: application/x-zip');
			header('Content-transfer-encoding: binary'); 
			header('Content-Disposition: attachment; filename="'.htmlspecialchars($module_list_array[$id]['history'][0]['filename']).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: '.strlen($file_content));
		
			echo $file_content;
			exit;
		}
	}
}

if (isset($_POST['mod'])) {
	$dir_name = str_replace(array('.','..'), '', $_POST['mod']);

	if (isset($_POST['install_manually'])) {
		header('Location: '.AT_BASE_HREF.'mods/_core/modules/module_install_step_2.php?mod='.urlencode($dir_name).SEP.'new=1'.SEP.'mod_in=1');
		exit;
	}

} else if (isset($_POST['install_manually'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

$module_list = $moduleFactory->getModules(AT_MODULE_STATUS_UNINSTALLED | AT_MODULE_STATUS_MISSING | AT_MODULE_STATUS_PARTIALLY_UNINSTALLED, AT_MODULE_TYPE_EXTRA);
$keys = array_keys($module_list);
natsort($keys);

require (AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

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
if (count($keys) > 0)
{
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="installform">
<table class="data" summary="" rules="cols">
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
<?php if (!empty($keys)): ?>
	<?php foreach($keys as $dir_name) : $module =& $module_list[$dir_name]; ?>
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
}
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php 
?>
<table class="data" summary="" rules="cols">
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
$num_of_modules = count($module_list_array);

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
	if(is_array($module_list_array))
	{
		for ($i=0; $i < $num_of_modules; $i++)
		{
			// check if the module has been installed
			$sql = "SELECT * FROM ".TABLE_PREFIX."modules WHERE dir_name = '" . $module_list_array[$i]["history"][0]["install_folder"] . "'";
			$result = mysql_query($sql, $db) or die(mysql_error());

			if (mysql_num_rows($result) == 0) $installed = false;
			else $installed = true;

?>
	<tr onmousedown="document.form['m<?php echo $i; ?>'].checked = true; rowselect(this);"  id="r_<?php echo $i; ?>">
		<td><input type="radio" name="id" value="<?php echo $i; ?>" id="m<?php echo $i; ?>" <?php if ($installed) echo 'disabled="disabled"'; ?> /></td>
		<td><label for="m<?php echo $i; ?>"><?php echo $module_list_array[$i]["name"]; ?></label></td>
		<td><?php echo $module_list_array[$i]["description"]; ?></td>
		<td><?php echo $module_list_array[$i]["history"][0]["version"]; ?></td>
		<td><?php echo $module_list_array[$i]["atutor_version"]; ?></td>
		<td><?php echo $module_list_array[$i]["history"][0]["maintainer"]; ?></td>
		<td><?php if ($installed) echo _AT("installed"); else echo _AT("not_installed"); ?></td>
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
</form>

<script language="JavaScript">
<!--

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

// This function validates if and only if a zip file is given
function validate_filename() {
  // check file type
  var file = document.frm_upload.modulefile.value;
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
