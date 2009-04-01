<?php
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
$_user_location = 'public';
include(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_SOCIAL_INCLUDE.'classes/Application.class.php');

if (empty($_GET['st']) || empty($_GET['name']) || ! isset($_GET['value'])) {
  header("HTTP/1.0 400 Bad Request", true);
  echo "<html><body><h1>400 - Bad Request</h1></body></html>";
} else {
  try {
	$st = urldecode(base64_decode($_GET['st']));
	$key = urldecode($_GET['name']);
	$value = urldecode($_GET['value']);
	$token = BasicSecurityToken::createFromToken($st, 60*60);	//TODO: Change 3600 to a constant
	$app_id = $token->getAppId();
//	$viewer = $token->getViewerId();
	debug($app_id,'appid');
	debug($viewer, 'viewer');
	debug($value, 'value');
	debug($key,'key');
	$app = new Application($app_id);

	$result = $app->setApplicationSettings($_SESSION['member_id'], $key, $value);
	if (!$result){
		echo "<html><body><h1>500 - SQL Error: </h1>" . mysql_error() . "</body></html>";
	}

  } catch (Exception $e) {
	header("HTTP/1.0 400 Bad Request", true);
	echo "<html><body><h1>400 - Bad Request</h1>" . $e->getMessage() . "</body></html>";
  }
}
?>