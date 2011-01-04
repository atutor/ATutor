<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id: ATutorOAuthDataStore.php 10055 2010-06-29 20:30:24Z cindy $

/**
 * ATutor's implementation of the OAuthDataStore
 */
class ATutorOAuthDataStore extends OAuthDataStore {
  private $db;

  public function __construct() {
    global $db;
    // this class is used in 2 different contexts, either through atutor where we have a Db class
    // or through Shindig's social API, in which case we have to create our own db handle
    if (isset($db) && $db instanceof DB) {
		// running in atutor's context
		$this->db = $db->get_handle();
    } else {
		// running in shindig's context
		// one of the class paths should point to atutor's document root, abuse that fact to find our config
		if (file_exists('../../../../../include/lib/mysql_connect.inc.php')){
			define('AT_INCLUDE_PATH', '../../../../../include/');
		} else {
			define('AT_INCLUDE_PATH', '../../atutor155/ATutor_164/include/');
		}
		$configFile = AT_INCLUDE_PATH.'lib/mysql_connect.inc.php';
		if (file_exists($configFile)) {
		  include(AT_INCLUDE_PATH.'config.inc.php');
		  include(AT_INCLUDE_PATH . 'lib/constants.inc.php');
		  include(AT_INCLUDE_PATH . 'lib/mysql_connect.inc.php');
		  $this->db = $db;
		}

		if (! isset($configFile)) {
		throw new Exception("Could not locate ATutor's configuration file while scanning extension_class_paths ({$extension_class_paths})");
		}
//      $this->db = mysqli_connect($config['db_host'], $config['db_user'], $config['db_passwd'], $config['db_database']);
//      mysqli_select_db($this->db, $config['db_database']);
    }
  }

  public function lookup_consumer($consumer_key) {
    $consumer_key = mysql_real_escape_string(trim($consumer_key));
	$sql = "SELECT user_id, app_id, consumer_key, consumer_secret FROM ".TABLE_PREFIX."oauth_consumer WHERE consumer_key = '$consumer_key'";
	$res = mysql_query($sql, $this->db);
    if (mysql_num_rows($res)) {
      $ret = mysql_fetch_assoc($res);
      return new OAuthConsumer($ret['consumer_key'], $ret['consumer_secret'], null);
    }
    return null;
  }

  public function lookup_consumer_name($consumer_key){
	$consumer_key = mysql_real_escape_string(trim($consumer_key));
	$sql = "SELECT user_id, app_id, FROM ".TABLE_PREFIX."oauth_consumer WHERE consumer_key = '$consumer_key'";
	$res = mysql_query($sql, $this->db);
	if (mysql_num_rows($res)) {
      $ret = mysql_fetch_assoc($res);
      return $ret;
    }
  }

  public function lookup_token($consumer, $token_type, $token) {	
    $token_type		= mysql_real_escape_string($token_type);
    $consumer_key	= mysql_real_escape_string($consumer->key);
    $token			= mysql_real_escape_string($token);
	$sql = "SELECT * FROM ".TABLE_PREFIX."oauth_token WHERE type = '$token_type' AND consumer_key = '{$consumer_key}' AND token_key = '$token'";
	$res = mysql_query($sql, $this->db);    
    if (mysql_num_rows($res)) {
      $ret = mysql_fetch_assoc($res);
      return new OAuthToken($ret['token_key'], $ret['token_secret']);
    }
    throw new OAuthException("Unexpected token type ($token_type) or unknown token");
  }

  public function lookup_nonce($consumer, $token, $nonce, $timestamp) {
    $timestamp	= mysql_real_escape_string($timestamp);
    $nonce		= mysql_real_escape_string($nonce);
	$sql = "SELECT nonce FROM ".TABLE_PREFIX."oauth_nonce WHERE nonce_timestamp = $timestamp AND nonce = '$nonce'";
    $res = mysql_query($sql, $this->db);
    if (! mysql_num_rows($res)) {
      $nonce = mysql_real_escape_string($nonce);
	  $sql = "INSERT INTO ".TABLE_PREFIX."oauth_nonce (nonce, nonce_timestamp) VALUES ('$nonce', $timestamp)";
	  mysql_query($sql, $this->db);
      return null;
    }
    $ret = mysql_fetch_assoc($res);
    return $ret['nonce'];
  }

  public function new_request_token($consumer, $token_secret = null) {
    $consumer_key		= mysql_real_escape_string($consumer->key);
    $consumer_secret	= mysql_real_escape_string($consumer->secret);
	$sql = "SELECT user_id FROM ".TABLE_PREFIX."oauth_consumer WHERE consumer_key = '$consumer_key' AND consumer_secret = '$consumer_secret'";
//echo $sql;exit;
    $res = mysql_query($sql, $this->db);
    if (mysql_num_rows($res)) {
      $ret = mysql_fetch_assoc($res);
      $user_id = intval($ret['user_id']);
      if ($token_secret === null) {
        $token_secret = md5(uniqid(rand(), true));
      }
      $token = new OAuthToken($this->genGUID(), $token_secret);
      $token_key = mysql_real_escape_string($token->key);
      $token_secret = mysql_real_escape_string($token->secret);
	  $sql = "INSERT INTO ".TABLE_PREFIX."oauth_token (consumer_key, type, token_key, token_secret, user_id) VALUES ('$consumer_key', 'request', '$token_key', '$token_secret', $user_id)";
      mysql_query($sql, $this->db);
      return $token;
    } else {
      throw new OAuthException("Invalid consumer key ($consumer_key)");
    }
  }

  public function new_access_token($oauthToken, $consumer) {
    $org_token_key = $token_key = mysql_real_escape_string($oauthToken->key);
	$sql = "SELECT * FROM ".TABLE_PREFIX."oauth_token WHERE type = 'request' AND token_key = '$token_key'";
	$res = mysql_query($sql, $this->db);
    if (mysql_num_rows($res)) {
      $ret = mysql_fetch_assoc($res);
      if ($ret['authorized']) {
        $token = new OAuthToken($this->genGUID(), md5(uniqid(rand(), true)));
        $token_key		= mysql_real_escape_string($token->key);
        $token_secret	= mysql_real_escape_string($token->secret);
        $consumer_key	= mysql_real_escape_string($ret['consumer_key']);
        $user_id		= intval($ret['user_id']);
		$sql = "INSERT INTO ".TABLE_PREFIX."oauth_token (consumer_key, type, token_key, token_secret, user_id) VALUES ('$consumer_key', 'access', '$token_key', '$token_secret', $user_id)";
        @mysql_query($sql, $this->db);
		$sql = "DELETE FROM ".TABLE_PREFIX."oauth_token WHERE type = 'request' AND token_key = '$org_token_key'";
		mysql_query($sql, $this->db);
        return $token;
      }
    }
    return null;
  }

  public function authorize_request_token($token) {
    $token		= mysql_real_escape_string($token);
    $user_id	= intval($_SESSION['member_id']);
	$sql = "UPDATE ".TABLE_PREFIX."oauth_token SET authorized = 1, user_id = $user_id WHERE token_key = '$token'";
	mysql_query($sql, $this->db);
  }

  public function get_user_id($token) {
    $token_key = mysql_real_escape_string($token->key);
	$sql = "SELECT user_id FROM ".TABLE_PREFIX."oauth_token WHERE token_key = '$token_key'";
	$res = mysql_query($sql, $this->db);
    if (mysql_num_rows($res)) {
      list($user_id) = mysql_fetch_row($res);
      return $user_id;
    }
    return null;
  }

  public function get_app_id($token) {
    $token_key = mysql_real_escape_string($token->key);
	$sql = "SELECT app_id FROM ".TABLE_PREFIX."oauth_consumer WHERE consumer_key = '$token_key'";
	$res = mysql_query($sql, $this->db);
    $ret = 0;
    if (mysql_num_rows($res)) {
      list($ret) = mysql_fetch_row($res);
    }
    return $ret;
  }

  /**
   * @see http://jasonfarrell.com/misc/guid.phps Taken from here
   * e.g. output: 372472a2-d557-4630-bc7d-bae54c934da1
   * word*2-, word-, (w)ord-, (w)ord-, word*3
   */
  private function genGUID() {
    $guidstr = '';
    for ($i = 1; $i <= 16; $i ++) {
      $b = (int)rand(0, 0xff);
      // version 4 (random)
      if ($i == 7) {
        $b &= 0x0f;
      }
      $b |= 0x40;
      // variant
      if ($i == 9) {
        $b &= 0x3f;
      }
      $b |= 0x80;
      $guidstr .= sprintf("%02s", base_convert($b, 10, 16));
      if ($i == 4 || $i == 6 || $i == 8 || $i == 10) {
        $guidstr .= '-';
      }
    }
    return $guidstr;
  }
}
