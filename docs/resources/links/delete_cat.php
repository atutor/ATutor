<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
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
$_section[2][0] = _AT('delete_category');

authenticate(AT_PRIV_LINKS);

if ($_GET['d']){
	$sql	= "DELETE FROM ".TABLE_PREFIX."resource_categories WHERE CatID=$_GET[CatID] AND course_id=$_SESSION[course_id]";

	$result	= mysql_query($sql, $db);

	$num_deleted = mysql_affected_rows($db);

	if ($num_deleted > 0) {
		$sql	= "DELETE FROM ".TABLE_PREFIX."resource_links WHERE CatID=$_GET[CatID]";
		$result	= mysql_query($sql, $db);
	}
	header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_LINK_CAT_DELETED));
	exit;
}
require (AT_INCLUDE_PATH.'header.inc.php');

$_GET['CatID'] = intval($_GET['CatID']);

?>
<h2><a href="resources/index.php?g=11"><?php echo _AT('resources'); ?></a></h2>
<h3><a href="resources/links/index.php?g=11"><?php echo _AT('links_database'); ?></a></h3>
<h4><?php echo _AT('delete_category'); ?></h4>

<?php 
	$sql	= "SELECT CatID FROM ".TABLE_PREFIX."resource_categories WHERE CatParent=$_GET[CatID] LIMIT 1";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$error[] = AT_ERROR_LINK_CAT_NOT_EMPTY;
		print_errors($error);
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	if (!$_GET['d']) {
		$warning[] =  AT_WARNING_DELETE_CATEGORY;
		print_warnings($warning)
?>
		<p align="center"><a href="resources/links/delete_cat.php?CatID=<?php echo $_GET['CatID'].SEP.'d=1'; ?>"><?php echo _AT('yes_delete'); ?></a>, <a href="resources/links/index.php?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>"><?php echo _AT('no_cancel'); ?></a></p>
<?php }

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>