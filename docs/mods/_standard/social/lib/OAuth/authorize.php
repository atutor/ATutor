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

require_once('OAuth.php');
//require_once('../Shindig/ATutorOAuthDataStore.php');
session_name('ATutorID');
session_start();

// log user in.
if (!isset($_SESSION['member_id'])){
	header('Location: ../../../../../login.php?p='.urlencode($_SERVER['REQUEST_URI']));
	exit;
} 

try {
	$request = OAuthRequest::from_request();
	debug($request);
	$token = $request->get_parameter('oauth_token');
	$callback = $request->get_parameter('oauth_callback');
	if (! $token) {
		echo 'Bad Request - missing oauth_token';
		return;
	}

	//bounce to authorize page
	//$this->template('oauth/authorize.php', array('oauth_token' => $token, 'oauth_callback' => $callback));	
	echo 'forwarding...';
	
} catch (OAuthException $e) {
  echo $e->getMessage();
  exit;
} catch (Exception $e) {
  echo $e->getMessage();
  exit;
}
?>

<h1>Grant access to your private information?</h1>

<p>An application is requesting access to your information. You should
only approve this request if you trust the application.</p>

<form action="approve_authorization.php" method="post">
	<input type="hidden" name="oauth_token"	value="<?php echo htmlspecialchars($token); ?>" /> 
	<input type="hidden" name="oauth_callback" value="<?php echo htmlspecialchars($callback); ?>" />
	<input type="submit" value="Approve" />
	<input type="button" value="Decline" onclick="location.href='/'" />
</form>
<div style="clear: both"></div>
