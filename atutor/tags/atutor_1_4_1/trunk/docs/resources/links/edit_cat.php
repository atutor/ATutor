<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('resources');
$_section[0][1] = 'resources/';
$_section[1][0] = _AT('links_database');
$_section[1][1] = 'resources/links/';
$_section[2][0] = _AT('edit_category');

authenticate(AT_PRIV_LINKS);

if (isset($_POST['submit'])) {
	$_POST['CatID'] = intval($_POST['CatID']);

	$sql	= "UPDATE ".TABLE_PREFIX."resource_categories SET CatName='$_POST[cat_name]' WHERE CatID=$_POST[CatID] AND course_id=$_SESSION[course_id]";

	$result	= mysql_query($sql, $db);

	header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_LINK_CAT_EDITED));
	exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');

$_GET['CatID'] = intval($_GET['CatID']);

?>
<h2><a href="resources/index.php?g=11"><?php echo _AT('resources'); ?></a></h2>
<h3><a href="resources/links/index.php?g=11"><?php echo _AT('links_database'); ?></a></h3>
<h4><?php echo _AT('edit_category'); ?></h4>

<?php
	
	require('mysql.php'); /* Access to all the database functions */
	$db2 = new MySQL;
	if(!$db2->init()) {
		$errors[]=AT_ERROR_NO_DB_CONNECT;
		print_errors($errors);
		exit;
	}


	$catName = $db2->get_CatNames($_GET['CatID']);

?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="CatID" value="<?php echo $_GET['CatID']; ?>" />
	<p>
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" align="center" summary="">
	<tr>
		<th class="cat" colspan="2"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php echo _AT('edit_category'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right"><label for="cat"><b><?php echo _AT('category_name'); ?>:</b></label></td>
		<td class="row1"><input name="cat_name" class="formfield" size="40" value="<?php echo stripslashes(htmlspecialchars($catName)); ?>" id="cat" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" name="submit" value="<?php echo _AT('edit_category'); ?>" class="button" accesskey="s" /> <input type="reset" value=" <?php echo _AT('reset'); ?> " class="button" /></td>
	</tr>
	</table>
	</p>
	</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>