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

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if ($_GET['d']){
	/* We must ensure that any previous feedback is flushed, since AT_FEEDBACK_CANCELLED might be present
		 * if Yes/Delete was chosen above
		 */
	$msg->deleteFeedback('CANCELLED');
	
	$sql	= "DELETE FROM ".TABLE_PREFIX."resource_categories WHERE CatID=$_GET[CatID] AND course_id=$_SESSION[course_id]";

	$result	= mysql_query($sql, $db);

	$num_deleted = mysql_affected_rows($db);

	if ($num_deleted > 0) {
		$sql	= "DELETE FROM ".TABLE_PREFIX."resource_links WHERE CatID=$_GET[CatID]";
		$result	= mysql_query($sql, $db);
	}
	
	$msg->addFeedback('LINK_CAT_DELETED');
	header('Location: index.php');
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
		$msg->printErrors('LINK_CAT_NOT_EMPTY');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	if (!$_GET['d']) {
		$msg->printWarnings('DELETE_CATEGORY');
		
		/* Since we do not know which choice will be taken, assume it No/Cancel, addFeedback('CANCELLED)
	 	* If sent to resources/links/index.php then OK, else if sent back here & if $_GET['d']=1 then assumed choice was not taken
	 	* ensure that addFeeback('CANCELLED') is properly cleaned up, see above
	 	*/
		$msg->addFeedback('CANCELLED');
?>
		
		<p align="center"><a href="resources/links/delete_cat.php?CatID=<?php echo $_GET['CatID'].SEP.'d=1'; ?>"><?php echo _AT('yes_delete'); ?></a>, <a href="resources/links/index.php"><?php echo _AT('no_cancel'); ?></a></p>
<?php }

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>