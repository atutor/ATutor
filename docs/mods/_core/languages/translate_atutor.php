<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca						*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.		*/
/****************************************************************/

$page = 'translate';
$_user_location = 'public';
$page_title = 'ATutor: LCMS: Translation';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_REQUEST['f']) {
	$_REQUEST['f']	= 'en';
}

if (!defined('AT_DEVEL_TRANSLATE') || !AT_DEVEL_TRANSLATE) { exit; }

$_INCLUDE_PATH = AT_INCLUDE_PATH;
$_TABLE_PREFIX = TABLE_PREFIX;
$_TABLE_SUFFIX = '';

$_SESSION['language'] = $_SESSION['lang'];

global $db;
global $_user_location;
global $_base_path;
global $addslashes;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title>ATutor Translator Site</title>
	<link rel="stylesheet" href="<?php echo AT_BASE_HREF; ?>include/style_popup.css" type="text/css" />
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<base href="<?php echo AT_BASE_HREF; ?>" />
</head>

<body>

<?php

echo '<div align="right"><a href="javascript:window.close()">' . _AT('close_window') . '</a></div>';
echo '<h3>ATutor Translator Site</h3>';

$variables = array('_template','_msgs','_module');

$atutor_test = '<a href="'.$_base_path.'" title="Open ATutor in a new window" target="new">';

$_SESSION['status'] = 2;
$_USER_ADMIN = $_SESSION['status'];

$sql = "SELECT english_name, char_set FROM ".TABLE_PREFIX."languages WHERE language_code = '$_SESSION[language]'";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);

?>
<ol>
	<li><br />
	<table border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #cccccc;" >
	<tr>
		<td  bgcolor="#eeeeee" nowrap="nowrap"><h5 class="heading2">Translate</h5></td>
		<td style="font-size:small; border-left: 1px solid #cccccc;">
			From <strong>English - iso-8859-1</strong> to <strong> <?php echo $row['english_name'] . ' - ' . $row['char_set']; ?> </strong>
		</td>
	</tr>
	</table>
	</li>
	<br />

	<?php require_once('translator.php'); ?>

</body>
</html>