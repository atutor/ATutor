<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: index_instructor.php 7208 2008-02-20 16:07:24Z cindy $

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');
require("common.inc.php");

if ($_REQUEST["certificate_id"] == 0)
{
	$msg->printErrors('NO_ITEM_SELECTED');
	exit;
}

if ($_REQUEST["result_id"] > 0) $tokens = initialize_tokens($_REQUEST['result_id']);

$sql = "select * from ".TABLE_PREFIX."certificate_text where certificate_id=".$_REQUEST["certificate_id"];
$result	= mysql_query($sql, $db) or die(mysql_error());

$url="http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']). "/default_certificate.pdf";

$fdf = "%FDF-1.2\n%????\n";
$fdf .= "1 0 obj \n<< /FDF << /Fields [\n";

while ($row = mysql_fetch_assoc($result))
{
  $key = addcslashes($row["field_name"], "\n\r\t\\()");
  if ($_REQUEST["result_id"] > 0)
  	$value = $addslashes(replace_tokens($row["field_value"], $tokens), "\n\r\t\\()");
  else
  	$value = $addslashes($row["field_value"], "\n\r\t\\()");

  $fdf .= "<< /T ($key) /V ($value) >> \n";
}

$fdf .= "]\n/F ($url) >>";
$fdf .= ">>\nendobj\ntrailer\n<<\n";
$fdf .= "/Root 1 0 R \n\n>>\n";
$fdf .= "%%EOF";

header('Content-type: application/vnd.fdf');
echo $fdf;

?>