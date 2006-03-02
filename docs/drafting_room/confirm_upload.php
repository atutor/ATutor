<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

$id = abs($_GET['id']);

$sql = "SELECT owner_type, owner_id, member_id, file_name, folder_id FROM ".TABLE_PREFIX."files WHERE file_id=$id";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);

// authenticate this workspace and owner.... //

$files = array();
$sql = "SELECT * FROM ".TABLE_PREFIX."files WHERE file_name='$row[file_name]' AND owner_type=$row[owner_type] AND owner_id=$row[owner_id] AND folder_id=$row[folder_id] AND file_id<>$id";
$result = mysql_query($sql, $db);
while ($file_row = mysql_fetch_assoc($result)) {
	$files[] = $file_row;
}

debug($files);
?>

<form>
<div class="input-form">
	<div class="row">
		Files with the name <?php echo $row['file_name']; ?> already exists in this folder. Would you like to add it an updated version of an existing file or replace the existing file.
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>