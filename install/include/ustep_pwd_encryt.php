<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: ustep_pwd_encryt.php 10055 2010-06-29 20:30:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

print "Checking database<br><br>";

$sql = "SELECT member_id, first_name, last_name, password FROM ".TABLE_PREFIX."members";
$result = mysql_query($sql, $db);

while ($row = mysql_fetch_assoc($result))
{
	if (strlen($row["password"]) < 40)
	{
		print "updating member ".$row["first_name"]." ".$row["last_name"].": from ".$row["password"]." to " .sha1($row["password"])."<br>";
		$sql_update = "UPDATE ".TABLE_PREFIX."members set password = '".sha1($row["password"])."', creation_date = creation_date WHERE member_id=".$row["member_id"];
		$result_update = mysql_query($sql_update, $db);
	}
}

print "<br>Done!";
?>
