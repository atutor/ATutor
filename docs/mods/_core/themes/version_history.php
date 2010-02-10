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

if (isset($_POST["cancel"]))
{
	header('Location: '.AT_BASE_HREF.'mods/_core/themes/install_themes.php');
	exit;
}

// check the connection to server update.atutor.ca
$update_server = "update.atutor.ca"; 
$connection_test_file = "http://" . $update_server . '/index.php';
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

if (isset($_GET["id"])) $id = intval($_GET["id"]);
else if (isset($_POST["id"])) $id = intval($_POST["id"]);

// get theme list
$theme_folder = "http://" . $update_server . '/themes/';

$theme_list_xml = @file_get_contents($theme_folder . 'theme_list.xml');

if ($theme_list_xml) 
{
	$themeListParser = new ThemeListParser();
	$themeListParser->parse($theme_list_xml);
	$theme_list_array = $themeListParser->getParsedArray();
}
// end of get theme list

$theme_content_folder = AT_CONTENT_DIR . "theme";

//debug($_POST["vid"]);
//exit;
// download process
if (isset($_POST["download"]) && !isset($_POST["vid"]))
{
	$msg->addError('NO_ITEM_SELECTED');
}
else if ($_POST['download'])
{
	$vid = intval($_POST['vid']);
	$file_content = @file_get_contents($theme_folder . $theme_list_array[$id]['history'][$vid]['location'].$theme_list_array[$id]['history'][$vid]['filename']);

	if (!$file_content)
	{
		$msg->addError('FILE_NOT_EXIST');
	}
	else
	{
		header('Content-Type: application/x-zip');
		header('Content-transfer-encoding: binary'); 
		header('Content-Disposition: attachment; filename="'.htmlspecialchars($theme_list_array[$id]['history'][$vid]['filename']).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.strlen($file_content));
	
		echo $file_content;
		exit;
	}
}

require (AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<div class="input-form">

<?php 
?>
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<table class="data" summary="" style="width: 100%" rules="cols">
<thead>
	<tr>
		<th scope="col">&nbsp;</th>
		<th scope="col"><?php echo _AT('version');?></th>
		<th scope="col"><?php echo _AT('atutor_version_tested_with');?></th>
		<th scope="col"><?php echo _AT('publish_date');?></th>
		<th scope="col"><?php echo _AT('state');?></th>
		<th scope="col"><?php echo _AT('maintainers');?></th>
		<th scope="col"><?php echo _AT('notes');?></th>
	</tr>
</thead>

<tfoot>
<tr>
	<td colspan="7">
		<input type="submit" name="download" value="<?php echo _AT('download'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php 
$num_of_versions = count($theme_list_array[$id]['history']);

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
	if(is_array($theme_list_array[$id]['history']))
	{
		for ($i=0; $i < $num_of_versions; $i++)
		{
?>
	<tr onmousedown="document.form['m<?php echo $i; ?>'].checked = true; rowselect(this);"  id="r_<?php echo $i; ?>">
		<td><input type="radio" name="vid" value="<?php echo $i; ?>" id="m<?php echo $i; ?>" /></td>
		<td><label for="m<?php echo $i; ?>"><?php echo $theme_list_array[$id]["name"] . ' ' .$theme_list_array[$id]['history'][$i]["version"]; ?></label></td>
		<td><?php echo $theme_list_array[$id]['history'][$i]["atutor_version"]; ?></td>
		<td><?php echo $theme_list_array[$id]['history'][$i]["date"]; ?></td>
		<td><?php echo $theme_list_array[$id]['history'][$i]["state"]; ?></td>
		<td><?php echo $theme_list_array[$id]['history'][$i]["maintainer"]; ?></td>
		<td><?php echo $theme_list_array[$id]['history'][$i]["notes"]; ?></td>
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

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
