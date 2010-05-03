<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
$_user_location	= 'public';

define('AT_INCLUDE_PATH', '../../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once('OAuth.php');
//require_once('../Shindig/ATutorOAuthDataStore.php');

// log user in.
if (!isset($_SESSION['member_id'])){
	header('Location: ../../../../../login.php?p='.urlencode($_SERVER['REQUEST_URI']));
	exit;
} 

try {
	$request = OAuthRequest::from_request();
	$token = $request->get_parameter('oauth_token');
	$callback = $request->get_parameter('oauth_callback');
	if (! $token) {
		echo 'Bad Request - missing oauth_token';
		return;
	}
	//oauth customized header template
	$savant->assign('page_title', 'Authentication Page');
	$savant->assign('lang_code', $_SESSION['lang']);
	$savant->assign('lang_charset', $myLang->getCharacterSet());
	$savant->assign('base_path', $_base_path);
	$savant->assign('base_tmpl_path', $_SERVER['HTTP_HOST']);
	$savant->assign('theme', $_SESSION['prefs']['PREF_THEME']);
	$savant->assign('current_date', AT_date(_AT('announcement_date_format')));
	$savant->assign('just_social', $_config['just_social']);
	$savant->display('oauth/header.tmpl.php');

	//authorize template
	$savant->assign('token', $token);
	$savant->assign('callback', $callback);
	$savant->display('oauth/authorize.tmpl.php');

	//oauth customized footer template
	$savant->display('oauth/footer.tmpl.php');
} catch (OAuthException $e) {
  echo $e->getMessage();
  exit;
} catch (Exception $e) {
  echo $e->getMessage();
  exit;
}
?>