<?php
$_user_location	= 'public';
define('AT_INCLUDE_PATH', '../');
require (AT_INCLUDE_PATH.'vitals.inc.php');
session_start();

include 'securimage.php';

$img = new securimage();

$img->show(); // alternate use:  $img->show('/path/to/background.jpg');
?>
