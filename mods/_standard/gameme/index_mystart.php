<?php
namespace gameme;
use gameme\PHPGamification\DAO;

global $_base_path;
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet
require (AT_INCLUDE_PATH.'header.inc.php');
global $_base_path;
$this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$_base_path);
require_once($this_path.'mods/_standard/gameme/gamify.lib.php');
require_once($this_path.'mods/_standard/gameme/PHPGamification/PHPGamification.class.php');
$gamification = new PHPGamification();
$gamification->setDAO(new DAO(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD));
$gamification->setUserId($_SESSION['member_id']);

showUserLog($gamification);
?>

<div id="helloworld">
	This is a page of the Hello World module that requires a login session, but might contain a tool that is not a course tool :)
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>