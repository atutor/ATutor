<?php
/*
Library which implements basic work with RSA encryption + decryption
also using authoring hash-cookie

maintainer smal (Serhiy Voyt)
smalgroup@gmail.com

Before use read carefully readme.txt and define varaibles below

This script is a part of ATutor LDAP-authoring module and it's required to right work all LDAP-authoring in ATutor. RSA

Before use this script you must check your system to folowing:
1.In your system must be installed OpenSSL package - required to generate private key.
2.In PHP must be enabled OpenSSL Functions - required to rsa.inc.php (see phpinfo() to check this)


*/

define('EXP', 10001); 
define('PUBLIC_KEY', 'B1CBE3B5456CDF6D5A85F32715415A0F85ADAB289B7AD21CA2B925BD28231994B72856093C46D2A67CF8136CBDCF430C0EF7990403DAF4830CE4633D98A16703');
define('PRIVATE_KEY', AT_INCLUDE_PATH.'/lib/pk.pem');
define('TTL', 120);

function auth_cookie() {
	
	global $db;
	
	$hash = md5(mt_rand());
	$time = time();

	$sql    = "INSERT INTO ".TABLE_PREFIX."auth_cookie VALUES(0, '$hash', $time)";
	$result = mysql_query($sql, $db);
	
	$id = mysql_insert_id();
	
	$auth_cookie = "|".$hash;
	return $auth_cookie;
}


function rsa_decode($key,$enc_str){
	
	if ($fp = fopen($key, 'r')){
    		$priv_key = fread($fp, 8192);
    		fclose($fp);
	}else{
		 return false;
		 exit;
	}

	if (!$keyh = openssl_get_privatekey($priv_key)) {
		return false;
		exit;
	}
	
	$pub_key = openssl_pkey_get_public($key);
	echo ($pub_key);

	if (openssl_private_decrypt(base64_decode($enc_str), $decoded_string, $keyh)){
    		return $decoded_string;	
    	}else{
    		 return false;
    		 exit;
    	}
	
	
}

function clear_auth_cookie(){
	
	global $db;
	
	$cur_time = time() - TTL;
	$sql = "DELETE FROM ".TABLE_PREFIX."auth_cookie WHERE ttl < ".$cur_time;
	mysql_query($sql,$db);
	
}


function check_valid_login($decoded_auth){

	global $db;
	

	list($password, $hash) = explode("|", $decoded_auth);
	
	$sql = "SELECT ttl FROM ".TABLE_PREFIX."auth_cookie WHERE hash ='$hash'";
	
	if ($result = mysql_query($sql, $db)){
		if ($row = mysql_fetch_array($result)){
			$ttl_valid = time() - $row['ttl'];
			if ($ttl_valid < TTL) {
				return $password;
			}else{
				return false;
			}
			
		}	
	}
	
			
}

?>