<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

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
global $msg, $_config;

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
		$sql = "SELECT * FROM %soauth_client_servers WHERE oauth_server='%s'";
		$row = queryDB($sql, array(TABLE_PREFIX, $_config['transformable_uri']), TRUE);	
		
		if(count($row) == 0){
			$register_consumer_url = AT_TILE_OAUTH_REGISTER_CONSUMER_URL.'?consumer='.urlencode(AT_BASE_HREF).'&expire='.$_config['transformable_oauth_expire'];
			$oauth_server_response = file_get_contents($register_consumer_url);
		
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

				$sql = "INSERT INTO %soauth_client_servers
					    (oauth_server, consumer_key, consumer_secret, expire_threshold, create_date)
					    VALUES ('%s', '%s',
					    '%s', %d, now())";
				$result = queryDB($sql, array(TABLE_PREFIX, $_config['transformable_uri'], $consumer_key, $consumer_secret, $expire_threshold));
				$oauth_server_id = at_insert_id();
			}
		}
		else
		{
			$oauth_server_id = $row['oauth_server_id'];
			$consumer_key = $row['consumer_key'];
			$consumer_secret = $row['consumer_secret'];
			$expire_threshold = $row['expire_threshold'];
		}
		$consumer = new OAuthConsumer($consumer_key, $consumer_secret, $client_callback_url);
		
		// 2. get request token
		$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", AT_TILE_OAUTH_REQUEST_TOKEN_URL);
		$req_req->sign_request($sig_method, $consumer, NULL);

		$oauth_server_response = file_get_contents($req_req);

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

			$sql = "INSERT INTO %soauth_client_tokens
					(oauth_server_id, token, token_type, token_secret, member_id, assign_date)
					VALUES (%d, '%s', 'request', '%s', %d, now())";
			$result = queryDB($sql, array(TABLE_PREFIX, $oauth_server_id, $request_token_key, $request_token_secret, $_SESSION['member_id']));
		}
		else
		{
			$msg->addError(array('TILE_AUTHENTICATION_FAIL', $error));
			header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
			exit;
		}
		
		$request_token = new OAuthToken($request_token_key, $request_token_secret);

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
		          FROM %soauth_client_servers ocs, %soauth_client_tokens oct  
		         WHERE ocs.oauth_server_id = oct.oauth_server_id
		           AND oct.token = '%s'
		           AND token_type='request'";		
		$row = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_GET['oauth_token']), TRUE);
		
		if(count($row) == 0){
			$msg->addError(array('TILE_AUTHENTICATION_FAIL', _AT('wrong_request_token')));
			header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
			exit;
		}
		

		$consumer = new OAuthConsumer($row['consumer_key'], $row['consumer_secret'], $client_callback_url);
		$request_token = new OAuthToken($_GET['oauth_token'], $row['token_secret']);
		
		// 4. get access token
		$access_req = OAuthRequest::from_consumer_and_token($consumer, $request_token, "GET", AT_TILE_OAUTH_ACCESS_TOKEN_URL);
		$access_req->sign_request($sig_method, $consumer, NULL);
		
		$oauth_server_response = file_get_contents($access_req);
			
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
			$sql = "INSERT INTO %soauth_client_tokens
					(oauth_server_id, token, token_type, token_secret, member_id, assign_date)
					VALUES (%d, '%s', 'access', '%s', %d, now())";
			$result = queryDB($sql, array(TABLE_PREFIX, $row['oauth_server_id'], $access_token_key, $access_token_secret, $row['member_id']));
						
			// delete request_token
			$sql = "DELETE FROM %soauth_client_tokens WHERE token = '%s' AND token_type='request'";
			$result = queryDB($sql, array(TABLE_PREFIX, $_GET['oauth_token']));
		}
		else
		{
			$msg->addError(array('TILE_AUTHENTICATION_FAIL', $error));
			header('Location: '.AT_BASE_HREF.'mods/_core/imscp/index.php');
			exit;
		}
	}
}

?>