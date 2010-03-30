<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 8901 2009-11-11 19:10:19Z cindy $

/**
 * This script is called by mods/_core/imscp/ims_export.php
 * to export course content out of ATutor and import into
 * transformable.
 * This script also used as a callback endpoint for Tansformable
 * OAuth authentication.
 * @access public
 * @input  1. $client_callback_url is required
 *         2. when called back by Transformable OAuth authentication, 
 *         a request token var 'oauth_token' is required.
 * @output $access_token_key, to import into transformable
 */

if (!defined('AT_INCLUDE_PATH')) 
{ // when the script is called back by oauth server, 
	define('AT_INCLUDE_PATH', '../../../../include/');
	require_once(AT_INCLUDE_PATH.'vitals.inc.php');
}

require_once('OAuthUtility.class.php');
require_once("OAuth.php");
global $msg;

// check whether the transformable url is accessible
if (!OAuthUtility::isAccessible(AT_TILE_OAUTH_REGISTER_CONSUMER_URL))
{
	$msg->addError(array('TILE_AUTHENTICATION_FAIL', _AT('tile_not_accessible')));
	header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
	exit;
}

// check whether the last access token has expired. If not, return it, otherwise, get a new access token.
// skip this step when this script is called by oauth server callback
if (isset($_SESSION['member_id']))
	$access_token_key = OAuthUtility::getUnexpiredAccessToken();

if ($access_token_key == '')
{ 
	// initialize basic variables
	$sig_method = new OAuthSignatureMethod_HMAC_SHA1(); // use HMAC signature method as default
	
	if (!isset($_GET['oauth_token'])) // before oauth server authentication, get request token from oauth server
	{
		// 1. register consumer
		$sql = "SELECT * FROM ".TABLE_PREFIX."oauth_client_servers 
		         WHERE oauth_server='".mysql_real_escape_string(AT_TILE_BASE_URL)."'";
		$result = mysql_query($sql, $db);
	
		if (mysql_num_rows($result) == 0)
		{
			$register_consumer_url = AT_TILE_OAUTH_REGISTER_CONSUMER_URL.'?consumer='.urlencode(AT_BASE_HREF).'&expire='.AT_TILE_OAUTH_TOKEN_EXPIRE_THRESHOLD;
			$oauth_server_response = file_get_contents($register_consumer_url);
		
//			debug('register consumer - request: '.$register_consumer_url);
//			debug('register consumer - OAUTH response: '.$oauth_server_response);
			
			// handle OAUTH response on register consumer
			foreach (explode('&', $oauth_server_response) as $rtn)
			{
				$rtn_pair = explode('=', $rtn);
				
				if ($rtn_pair[0] == 'consumer_key') $consumer_key = $rtn_pair[1];
				if ($rtn_pair[0] == 'consumer_secret') $consumer_secret = $rtn_pair[1];
				if ($rtn_pair[0] == 'expire') $expire_threshold = $rtn_pair[1];
				if ($rtn_pair[0] == 'error') $error = urldecode($rtn_pair[1]);
			}
			
			if ($error <> '')
			{
				$msg->addError(array('TILE_AUTHENTICATION_FAIL', $error));
				header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
				exit;
			}
			else
			{
				$sql = "INSERT INTO ".TABLE_PREFIX."oauth_client_servers
					    (oauth_server, consumer_key, consumer_secret, expire_threshold, create_date)
					    VALUES ('".mysql_real_escape_string(AT_TILE_BASE_URL)."', '".$consumer_key."',
					    '".$consumer_secret."', ".$expire_threshold.", now())";
				$result = mysql_query($sql, $db);
				$oauth_server_id = mysql_insert_id();
			}
		}
		else
		{
			$row = mysql_fetch_assoc($result);
			$oauth_server_id = $row['oauth_server_id'];
			$consumer_key = $row['consumer_key'];
			$consumer_secret = $row['consumer_secret'];
			$expire_threshold = $row['expire_threshold'];
		}
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret, $client_callback_url);
		
	//	debug('consumer: '.$consumer);
	//	debug('--- END OF REGISTERING CONSUMER ---');
	
		// 2. get request token
		$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", AT_TILE_OAUTH_REQUEST_TOKEN_URL);
		$req_req->sign_request($sig_method, $consumer, NULL);

		$oauth_server_response = file_get_contents($req_req);
		
	//	debug('request token - request: '."\n".$req_req);
	//	debug('request token - response: '."\n".$oauth_server_response);
		
		// handle OAUTH request token response
		foreach (explode('&', $oauth_server_response) as $rtn)
		{
			$rtn_pair = explode('=', $rtn);
			
			if ($rtn_pair[0] == 'oauth_token') $request_token_key = $rtn_pair[1];
			if ($rtn_pair[0] == 'oauth_token_secret') $request_token_secret = $rtn_pair[1];
			if ($rtn_pair[0] == 'error') $error = urldecode($rtn_pair[1]);
		}
		
		if ($error == '' && strlen($request_token_key) > 0 && strlen($request_token_secret) > 0)
		{
			$sql = "INSERT INTO ".TABLE_PREFIX."oauth_client_tokens
					(oauth_server_id, token, token_type, token_secret, member_id, assign_date)
					VALUES (".$oauth_server_id.", '".$request_token_key."', 'request',
					'".$request_token_secret."', ".$_SESSION['member_id'].", now())";
			$result = mysql_query($sql, $db);
		}
		else
		{
			$msg->addError(array('TILE_AUTHENTICATION_FAIL', $error));
			header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
			exit;
		}
		
		$request_token = new OAuthToken($request_token_key, $request_token_secret);
		
	//	debug('--- END OF REQESTING REQUEST TOKEN ---');
		
		// 3. authorization
		$auth_req = AT_TILE_OAUTH_AUTHORIZATION_URL.'?oauth_token='.$request_token_key.'&oauth_callback='.urlencode($client_callback_url);
		
		header('Location: '.$auth_req);
		exit;
	}
	else // authenticated
	{
		// get consumer id by request token
		$sql = "SELECT ocs.oauth_server_id, ocs.consumer_key, ocs.consumer_secret, 
		               ocs.expire_threshold, oct.member_id, oct.token_secret
		          FROM ".TABLE_PREFIX."oauth_client_servers ocs, ".TABLE_PREFIX."oauth_client_tokens oct  
		         WHERE ocs.oauth_server_id = oct.oauth_server_id
		           AND oct.token = '".$_GET['oauth_token']."'
		           AND token_type='request'";
		
		$result = mysql_query($sql, $db);
		if (mysql_num_rows($result)==0)
		{
			$msg->addError(array('TILE_AUTHENTICATION_FAIL', _AT('wrong_request_token')));
			header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
			exit;
		}
		
		$row = mysql_fetch_assoc($result); 
		
		$consumer = new OAuthConsumer($row['consumer_key'], $row['consumer_secret'], $client_callback_url);
		$request_token = new OAuthToken($_GET['oauth_token'], $row['token_secret']);
		
		// 4. get access token
		$access_req = OAuthRequest::from_consumer_and_token($consumer, $request_token, "GET", AT_TILE_OAUTH_ACCESS_TOKEN_URL);
		$access_req->sign_request($sig_method, $consumer, NULL);
		
		$oauth_server_response = file_get_contents($access_req);
		
	//	debug('access token - request: '."\n".$access_req);
	//	debug('access token - response: '."\n".$oauth_server_response);
		
		// handle OAUTH response on access token
		foreach (explode('&', $oauth_server_response) as $rtn)
		{
			$rtn_pair = explode('=', $rtn);
			
			if ($rtn_pair[0] == 'oauth_token') $access_token_key = $rtn_pair[1];
			if ($rtn_pair[0] == 'oauth_token_secret') $access_token_secret = $rtn_pair[1];
			if ($rtn_pair[0] == 'error') $error = urldecode($rtn_pair[1]);
		}
		
		if ($error == '' && strlen($access_token_key) > 0 && strlen($access_token_secret) > 0)
		{
			// insert access token
			$sql = "INSERT INTO ".TABLE_PREFIX."oauth_client_tokens
					(oauth_server_id, token, token_type, token_secret, member_id, assign_date)
					VALUES (".$row['oauth_server_id'].", '".$access_token_key."', 'access',
					'".$access_token_secret."', ".$row['member_id'].", now())";
			$result = mysql_query($sql, $db);
			
			// delete request_token
			$sql = "DELETE FROM ".TABLE_PREFIX."oauth_client_tokens
					 WHERE token = '".$_GET['oauth_token']."'
					   AND token_type='request'";
			$result = mysql_query($sql, $db);
		}
		else
		{
			$msg->addError(array('TILE_AUTHENTICATION_FAIL', $error));
			header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
			exit;
		}
	}
}
//debug('access token key: '.$access_token_key);
//	debug('--- END OF REQESTING ACCESS TOKEN ---');
//	exit;
?>