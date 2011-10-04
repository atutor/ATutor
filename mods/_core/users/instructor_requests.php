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

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

/**
 * A simple method to sign the request with the secret using HMAC.  
 * @param   String      Use UTC time, gmdate("Y-m-d\TH:i:s\Z");
 * @param   String      Hashed secret.  Unique per user.   
 */
function at_sign_request($timestamp, $publicKey) {
    global $db;
    if (!isset($_SESSION['login'])) {
        return $url;
    }
    $sql = 'SELECT last_login FROM ' . TABLE_PREFIX . "admins WHERE login='$_SESSION[login]'";
    $result = mysql_query($sql, $db);
    $row = mysql_fetch_assoc($result);
    //This key should be unique often enough yet binds to the user only.
    //easier way is to create a key table
    $privateKey = hash_hmac('sha256', $row['last_login'], $row['password']);

    /* 
     * Our simple way to sign the key
     * include GET header, then sort query, add current timestamp, sign it.
     */
    $canonicalArray['publicKey'] = $publicKey;
    $canonicalArray['timestamp'] = $timestamp;

    $str = "GET http/1.0\n";
    foreach ($canonicalArray as $k => $v) {
        $str .= "$k=" . rawurlencode($v) . "\n";
    }
    $hmacSignature = base64_encode(hash_hmac('sha512', $str, $privateKey, true));
    return rawurlencode($hmacSignature);
}

/**
 * Verify request by the given signedUrl
 * @param   String      querystring without '?', usually the $_SERVER['QUERY_STRING']
 *
 */
function at_verify_request($signature, $timestamp, $publicKey) {
    global $db;
    if ($signature == "" || $timestamp == "" || $publicKey == "") {
        //if parameters are empty, return false.
        return false;
    }
    $sql = 'SELECT last_login FROM ' . TABLE_PREFIX . "admins WHERE login='$_SESSION[login]'";
    $result = mysql_query($sql, $db);
    $row = mysql_fetch_assoc($result);
    $privateKey = hash_hmac('sha256', $row['last_login'], $row['password']);
    
    $canonicalArray = array();
    $canonicalArray['publicKey'] = $publicKey;
    $canonicalArray['timestamp'] = $timestamp;
    //check expirary
    $timeDiff = time() - strtotime($canonicalArray['timestamp']);
    if ($timeDiff > 36000) {
        //more than 10mins, expired.
        //TODO: use constants.
        die('time expired');
        return false;
    }
    //check data integrity
    //generate our own hmac to check
    $str = "GET http/1.0\n";
    foreach ($canonicalArray as $k => $v) {
        $str .= "$k=" . rawurlencode($v) . "\n";
    }
    $hmacSignature = base64_encode(hash_hmac('sha512', $str, $privateKey, true));
    if (rawurldecode($signature) === $hmacSignature) {
        return true;
    } 
    return false;    
}

if (isset($_GET['deny']) && isset($_GET['id'])) {
	header('Location: admin_deny.php?id='.$_GET['id']);
	exit;
	/*
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.intval($_GET['id']);
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', mysql_affected_rows($db), $sql);
	*/

} else if (isset($_GET['approve']) && isset($_GET['id'])) {
    //verify token first.
    if (!at_verify_request($_GET['auth_token'], $_GET['auth_timestamp'], $_GET['auth_publicKey'])) {
        $msg->addError('INVALID_AUTH_REQUEST');
        header('Location: instructor_requests.php');
        exit;
    }
    
	$id = intval($_GET['id']);

	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.$id;
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_DELETE, 'instructor_approvals', mysql_affected_rows($db), $sql);

	$sql = 'UPDATE '.TABLE_PREFIX.'members SET status='.AT_STATUS_INSTRUCTOR.', creation_date=creation_date, last_login=last_login WHERE member_id='.$id;
	$result = mysql_query($sql, $db);

	write_to_log(AT_ADMIN_LOG_UPDATE, 'members', mysql_affected_rows($db), $sql);

	/* notify the users that they have been approved: */
	$sql   = "SELECT email, first_name, last_name FROM ".TABLE_PREFIX."members WHERE member_id=$id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$to_email = $row['email'];

		if ($row['first_name']!="" || $row['last_name']!="") {
			$tmp_message  = $row['first_name'].' '.$row['last_name'].",\n\n";		
		}	
		$tmp_message .= _AT('instructor_request_reply', AT_BASE_HREF);

		if ($to_email != '') {
			require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

			$mail = new ATutorMailer;

			$mail->From     = $_config['contact_email'];
			$mail->AddAddress($to_email);
			$mail->Subject = _AT('instructor_request');
			$mail->Body    = $tmp_message;

			if(!$mail->Send()) {
			   //echo 'There was an error sending the message';
			   $msg->addError('SENDING_ERROR');
			}

			unset($mail);
		}
	}

	$msg->addFeedback('PROFILE_UPDATED_ADMIN');
} else if (!empty($_GET) && !$_GET['submit']) {
	$msg->addError('NO_ITEM_SELECTED');
}

/* Authentication info */
$timestamp = gmdate("Y-m-d\TH:i:s\Z");
$publicKey = hash('sha256', mt_rand());

require(AT_INCLUDE_PATH.'header.inc.php'); 

$sql	= "SELECT M.login, M.first_name, M.last_name, M.email, M.member_id, A.* FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."instructor_approvals A WHERE A.member_id=M.member_id ORDER BY M.login";
$result = mysql_query($sql, $db);
$num_pending = mysql_num_rows($result);

$savant->assign('result', $result);
$savant->assign('num_pending', $num_pending);
$savant->display('admin/users/instructor_requests.tmpl.php');

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>