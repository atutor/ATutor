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
				if (is_dir(AT_SUBSITE_MODULE_PATH. $module_folder))
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

if (defined('IS_SUBSITE')) {
	$enable_upload = false;
	$enable_remote_installtion = false;  // Disallow subsites to download and install the remote modules from update.atutor.ca
} else {
	$enable_upload = true;
	$enable_remote_installtion = true;
}
$module_list = $moduleFactory->getModules(AT_MODULE_TYPE_EXTRA);
$keys = array_keys($module_list);
natsort($keys);

require (AT_INCLUDE_PATH.'header.inc.php');

if (defined('IS_SUBSITE') && defined('SUBSITE_SUPPORT_EMAIL')) {
	$msg->addFeedback(array('SUBSITE_INSTALL_MODULE', SUBSITE_SUPPORT_EMAIL));
}
$msg->printAll();

?>


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

<?php 
// Create a list of install modules
$installed_mods = array();
$sql = "SELECT dir_name FROM %smodules";
$rows_installed_mods = queryDB($sql, array(TABLE_PREFIX));
	foreach($rows_installed_mods as $installed){
	    array_push($installed_mods, $installed['dir_name']);
	}
	
    // Sort the data with atutor_version descending
	foreach ($module_list_array as $key => $row) {
                $version[$key]  = $row['atutor_version'];
        }
    // Add $module_list_array as the last parameter, to sort by the common key
    // Sorts by original $module_list_array by reference, then returns true|false
    $sort_by_version = array_multisort($version, SORT_DESC, $module_list_array);

$savant->assign('enable_upload', $enable_upload);
$savant->assign('enable_remote_installation', $enable_remote_installtion);
$savant->assign('keys', $keys);
$savant->assign('module_list', $module_list);
$savant->assign('installed_mods', $installed_mods);
$savant->assign('module_list_array', $module_list_array);
$savant->display('admin/modules/install_modules.tmpl.php');
require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
