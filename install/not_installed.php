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
// $Id$

define('AT_INCLUDE_PATH', 'include/');
error_reporting(E_ALL ^ E_NOTICE);

require('../include/lib/constants.inc.php');

$new_version = VERSION;

header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$domain = ($_SERVER['HTTP_HOST']);
$rootpath = $dirname = dirname($_SERVER['DOCUMENT_ROOT']);
$AT_SUB_INCLUDE_PATH = "$rootpath/$domain";

if(file_exists($AT_SUB_INCLUDE_PATH."/svn.php")){
//if svn.php exists, this is a base installation
	$AT_SUB_INCLUDE_PATH = "$rootpath/$domain/include";
	require('../include/lib/constants.inc.php');
	require_once('include/common.inc.php');

}else{
//if svn.php does not exist, this is a subsite installation
	$AT_SUB_INCLUDE_PATH = "$rootpath/$domain";
	require_once($AT_SUB_INCLUDE_PATH.'/install/include/common.inc.php');
}



require(AT_INCLUDE_PATH.'header.php');
?>

<?php if(isset($AT_SUBSITE)){ ?>
<p>ATutor does not appear to be installed. <a href="install.php">Continue on to the installation</a>.</p>
<?php }else { ?>
<p>ATutor does not appear to be installed. <a href="index.php">Continue on to the installation</a>.</p>

<?php } ?>

<?php require(AT_INCLUDE_PATH.'footer.php'); ?>