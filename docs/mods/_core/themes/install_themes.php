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
// $Id: 

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);
require(AT_INCLUDE_PATH.'../mods/_core/themes/classes/ThemeListParser.class.php');
require(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php');

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
	$infos = array('CANNOT_CONNECT_SERVER', $update_server);
	$msg->addError($infos);
	
	require(AT_INCLUDE_PATH.'header.inc.php');
  $msg->printAll();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// get theme list
$theme_folder = $update_server . '/themes/';
$local_theme_folder = "../../../themes/";

$theme_list_xml = @file_get_contents($theme_folder . 'theme_list.xml');

if ($theme_list_xml) 
{
	$themeListParser = new ThemeListParser();
	$themeListParser->parse($theme_list_xml);
	$theme_list_array = $themeListParser->getParsedArray();
}
// end of get theme list

$theme_content_folder = AT_CONTENT_DIR . "theme/";

// create theme content dir if not exists
if (!is_dir($theme_content_folder)) mkdir($theme_content_folder);

// Installation process
if ((isset($_POST['install']) || isset($_POST["download"]) || isset($_POST["version_history"])) && !isset($_POST["id"]))
{
	$msg->addError('NO_ITEM_SELECTED');
}
else if (isset($_POST['install']) || isset($_POST["download"]) || isset($_POST["version_history"]) || isset($_POST["import"]))
{
	if ($_POST['version_history'])
	{
		header('Location: '.AT_BASE_HREF.'mods/_core/themes/version_history.php?id='.$_POST["id"]);
		exit;
	}

	// install and download
	if ($_POST["import"])
	{
		if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) 
		{
			$file_content = file_get_contents($_POST['url']);
			$filename = pathinfo($_POST['url']);
			$filename = $filename['basename'];
		}
		else
		{
			$file_content = file_get_contents($_FILES['themefile']['tmp_name']);
			$filename = $_FILES['themefile']['name'];
		}
	}
	else
	{
		$file_content = file_get_contents($theme_folder . $theme_list_array[$_POST["id"]]['history'][0]['location'].$theme_list_array[$_POST["id"]]['history'][0]['filename']);
	}
		
	if (!$file_content & ($_POST['install'] || $_POST['download']))
	{
		$msg->addError('FILE_NOT_EXIST');
	}
	else
	{
		if ($_POST['install'] || $_POST['import'])
		{
			clear_dir($theme_content_folder);
			
			// download zip file from update.atutor.ca and write into theme content folder
			if ($_POST["import"])
				$local_theme_zip_file = $theme_content_folder . $filename;
			else
				$local_theme_zip_file = $theme_content_folder. $theme_list_array[$_POST["id"]]['history'][0]['filename'];
			
			$fp = fopen($local_theme_zip_file, "w");
			fwrite($fp, $file_content);
			fclose($fp);
			
			// unzip uploaded file to theme's content directory
			include_once(AT_INCLUDE_PATH . '/classes/pclzip.lib.php');
			
			$archive = new PclZip($local_theme_zip_file);
		
			if ($archive->extract(PCLZIP_OPT_PATH, $theme_content_folder) == 0)
			{
		    clear_dir($theme_content_folder);
		    $msg->addError('CANNOT_UNZIP');
		  }
		
		  if (!$msg->containsErrors())
		  {
				// find unzip theme folder name
				clearstatcache();
				
				if ($dh = opendir($theme_content_folder)) 
				{
					while (($this_theme_folder = readdir($dh)) !== false)
					{
						if ($this_theme_folder <> "." && $this_theme_folder <> ".." && is_dir($theme_content_folder.$this_theme_folder)) break;
					}
					
					closedir($dh);
				}

				if ($this_theme_folder == "." || $this_theme_folder == ".." || !isset($this_theme_folder))
					$msg->addError('EMPTY_ZIP_FILE');
			}
		
		  // check if the same theme exists in "themes" folder. If exists, it has been installed
		  if (!$msg->containsErrors())
		  {
		  	if (is_dir($local_theme_folder. $this_theme_folder))
		  		$msg->addError('ALREADY_INSTALLED');
		  }

		  if (!$msg->containsErrors())
		  {
				header('Location: theme_install_step_1.php?theme='.urlencode($this_theme_folder).SEP.'title='.urlencode($theme_list_array[$_POST["id"]]["name"]));
				exit;
			}
		}
		
		if ($_POST['download'])
		{
			$id = intval($_POST['id']);
		
			header('Content-Type: application/x-zip');
			header('Content-transfer-encoding: binary'); 
			header('Content-Disposition: attachment; filename="'.htmlspecialchars($theme_list_array[$id]['history'][0]['filename']).'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: '.strlen($file_content));
		
			echo $file_content;
			exit;
		}
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>

<form name="frm_upload" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="input-form" style="width:95%;">
	<div class="row">
		<h3><?php echo _AT('import_theme'); ?></h3>
	</div>

	<div class="row">
		<input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
		<label for="file"><?php echo _AT('upload_theme_package'); ?></label><br />
		<input type="file" name="themefile" size="40" id="file" />
	</div>

	<div class="row">
		<label for="url"><?php echo _AT('specify_url_to_theme_package'); ?></label><br />
		<input type="text" name="url" value="http://" size="40" id="url" />
	</div>
		
	<div class="row buttons">
		<input type= "submit" name="import" value="<?php echo _AT('import'); ?>" onclick="javascript: return validate_filename(); " />
	</div>
</div>
</form>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php 
?>
<table class="data" summary="" rules="all">
<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('title');?></th>
		<th scope="col"><?php echo _AT('installed').'?';?></th>
		<th scope="col"><?php echo _AT('atutor_version_tested_with');?></th>
		<th scope="col"><?php echo _AT('description');?></th>
		<th scope="col"><?php echo _AT('theme_screenshot');?></th>
	</tr>
</thead>
	
<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="install" value="<?php echo _AT('install'); ?>" />
		<input type="submit" name="download" value="<?php echo _AT('download'); ?>" />
		<input type="submit" name="version_history" value="<?php echo _AT('version_history'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php 
$num_of_themes = count($theme_list_array);

if ($num_of_themes == 0)
{
?>

<tr>
	<td colspan="6"><?php echo _AT('none_found'); ?></td>
</tr>

<?php 
}
else
{
	// display themes
	if(is_array($theme_list_array))
	{
		for ($i=0; $i < $num_of_themes; $i++)
		{
			// check if the theme has been installed
			if (is_dir($local_theme_folder . $theme_list_array[$i]["history"][0]["install_folder"]))
				$installed = true;
			else
				$installed = false;

?>
	<tr onmousedown="document.form['m<?php echo $i; ?>'].checked = true; rowselect(this);"  id="r_<?php echo $i; ?>">
		<td><input type="radio" name="id" value="<?php echo $i; ?>" id="m<?php echo $i; ?>" <?php if ($installed) echo 'disabled="disabled"'; ?> /></td>
		<td><label for="m<?php echo $i; ?>"><?php echo $theme_list_array[$i]["name"]; ?></label></td>
		<td><?php if ($installed) echo _AT("installed"); else echo _AT("not_installed"); ?></td>
		<td><?php echo $theme_list_array[$i]["history"][0]["atutor_version"]; ?></td>
		<td><?php echo $theme_list_array[$i]["description"]; ?></td>
		<td><?php if (file_get_contents($theme_folder.$theme_list_array[$i]["history"][0]["screenshot_file"])) { ?>
			<img src="<?php echo $theme_folder.$theme_list_array[$i]["history"][0]["screenshot_file"]; ?>" border="1" alt="<?php echo _AT('theme_screenshot'); ?>" />
			<?php }?>
		</td>
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
  var file;
  
  if (document.frm_upload.themefile.value != '')
  	file = document.frm_upload.themefile.value;
  else if (document.frm_upload.url.value != 'http://')
  	file = document.frm_upload.url.value;
  	
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
