<?php
/*
Library which implement simple functions for LDAP authentication in Atutor

Maintainer smal (Serhiy Voyt)
smalgroup@gmail.com

Version 0.2
10.11.2008

Distributed under GPL (c)Sehiy Voyt 2005-2009
*/

if (!defined('AT_INCLUDE_PATH')) { exit; }

function get_ldap_config($param){
	/**
        * Get LDAP config param from DB
        * @access  public
        * @param   var $param - LDAP config parametr 
        * @return  LDAP config parametr value
        * @author  smal
        */
	global $db;
		
	$sql    = "SELECT value FROM ".TABLE_PREFIX."config_ldap WHERE name='$param'";
	$result = mysql_query($sql,$db);
	if (!($row = mysql_fetch_assoc($result))) {
		return 'error';
		exit;
		}else{	
		return strtolower($row['value']);
		}
}
	

function ldap_bind_connect($username, $password){
        /**
        * Auth user via LDAP
        * @access  public
        * @param   var $username
        * @param   var $password
        * @return  True if success bind to LDAP with username/password, otherwise return False
        * @author  smal
        */
	
        $ldap_server = ldap_connect(get_ldap_config('ldap_name'),get_ldap_config('ldap_port'));
	if (!$ldap_server) {
		return False;
        }
        #try start TLS
        ldap_set_option($ldap_server, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_start_tls($ldap_server);
        #if (!ldap_start_tls($ldap_server)){
        #    return False;
        #}
	$user_dn = get_ldap_config('ldap_attr_login'). "=" . $username . "," . get_ldap_config('ldap_base_tree');
	$ldap_server_bind = ldap_bind($ldap_server, $user_dn, $password);
	if ($ldap_server_bind == False) {
	       return False;
        }else{
	       return True;
        }
        ldap_close($ldap_server);
}

function get_ldap_config_attr() {
        /**
        * Get LDAP config param's value from DB
        * @access  public
        * @return  array of LDAP config attributes value (which not NULL) from ATutor DB
        * @author  smal
        */
        
	global $db;
	
	$result = array();
	
	$sql = "SELECT value FROM ".TABLE_PREFIX."config_ldap WHERE value != '' AND  name LIKE 'ldap_attr_%'";
	if ($result_sql = mysql_query($sql, $db)) {
		while ($row = mysql_fetch_array($result_sql)){
			array_push($result, $row[0]);
		}
	}else{
		return false;
		exit;
	}
	
	return $result;
	
} 

function get_ldap_entry_info($username, $password, $hash_password){
        /**
        * Get info about user entry from LDAP
        * @access  public
        * @param   var $username
        * @param   var $password
        * @param   var $hash_password
        * @return  array of user attributes value
        * @author  smal
        */
        
	$result = array();	

	$ldap_server = ldap_connect(get_ldap_config('ldap_name'),get_ldap_config('ldap_port'));
	if (!$ldap_server) {
		return false;
	}else{
		$user_dn = get_ldap_config('ldap_attr_login'). "=" . $username . "," . get_ldap_config('ldap_base_tree');
		$ldap_server_bind = ldap_bind($ldap_server, $user_dn, $password);
		if (!$ldap_server_bind) {
			return false;
		}else{
			$filter = get_ldap_config('ldap_attr_login') ."=".$username;
			$attr = get_ldap_config_attr();
			
			if(!$ldap_user= ldap_search($ldap_server, get_ldap_config('ldap_base_tree'),$filter, $attr)){
				return false;
			}else{
				if(!$ldap_user_entry = ldap_first_entry($ldap_server,$ldap_user)){
					return false;
				}else{
					if(!$ldap_user_attr = ldap_get_attributes($ldap_server,$ldap_user_entry)){
						return false;
					}else{
						if(!$ldap_user_info=ldap_get_entries($ldap_server,$ldap_user)){
							return false;
						}else{
				
							for ($i=0;$i<$ldap_user_attr['count'];$i++){
								if (isset($ldap_user_info[0][strtolower($ldap_user_attr[$i])][0])) {
								$result[strtolower($ldap_user_attr[$i])] = $ldap_user_info[0][strtolower($ldap_user_attr[$i])][0]; 
								}
							}
							$result[get_ldap_config('ldap_attr_login')] = $username;
							$result[get_ldap_config('ldap_attr_password')] = $hash_password; 
 							return $result;
						}
					}
				}
			}
		}
	ldap_close($ldap_server);
	}
	
		
}


function add_ldap_log($ldap_source=NULL) {
        /**
        * Function provide logging all user that's authentivated via LDAP
        * @access  public
        * @param   var $ldap_sourse - LDAP server name, optional
        * @return  True if logging success to DB, otherwise False
        * @author  smal
        */
        
	global $db;
	
	$member_id = $_SESSION['member_id'];
	if (!$member_id) {
		$member_id = 0;
	}
	$date = date('Y-m-d H:i:s');
	
	$sql = "INSERT INTO ".TABLE_PREFIX."ldap_log VALUES($member_id,'$date', '$ldap_source')";
	$result = mysql_query($sql,$db);
	if ($result) {
		return true;
	}else{
		return false;
	}	
}

function insert_user_info($user_info) {
	//function provide insert user info from $user_info array into 
	//ATutor MySQL DB
	/**
        * Insert user info from LDAP to ATutor DB
        * @access  public
        * @param   var $user_info - array of user attributes-values
        * @return  member_id of created user or False if error's occured
        * @author  smal
        */
        
	global $db, $_config;
		
	$name = strtolower($user_info[get_ldap_config('ldap_attr_login')]);
	$password = $user_info[get_ldap_config('ldap_attr_password')];
	$email = $user_info[get_ldap_config('ldap_attr_mail')];
	$website = $user_info[get_ldap_config('ldap_attr_website')];
	
	$first_name = $user_info[get_ldap_config('ldap_attr_first_name')];
	$second_name = $user_info[get_ldap_config('ldap_attr_second_name')];
	$last_name = $user_info[get_ldap_config('ldap_attr_last_name')];
		
	$dob = $user_info[get_ldap_config('ldap_attr_dob')];
	//$dob = '0000-00-00';
	$gender = $user_info[get_ldap_config('ldap_attr_gender')];
	$address = $user_info[get_ldap_config('ldap_attr_address')];
	$postal = $user_info[get_ldap_config('ldap_attr_postal')];
	$city = $user_info[get_ldap_config('ldap_attr_city')];
	$province = $user_info[get_ldap_config('ldap_attr_province')];
	$country = $user_info[get_ldap_config('ldap_attr_country')];
	$phone = $user_info[get_ldap_config('ldap_attr_phone')];
	$status = AT_STATUS_STUDENT;
	$now = date('Y-m-d H:i:s');
	
	//check unique login and email
	$sql = "SELECT login FROM ".TABLE_PREFIX."members WHERE login='$name' OR email='$email'";
	if ($result=mysql_query($sql,$db)){
		if(mysql_num_rows($result) > 0){
			return false;
			exit;
		}
	}
	
	$sql = "INSERT INTO ".TABLE_PREFIX."members VALUES (0,'$name','$password','$email','$website','$first_name','$second_name','$last_name', '$dob', '$gender', '$address','$postal','$city','$province','$country', '$phone', $status, '$_config[pref_defaults]', '$now', '$_SESSION[lang]', 1, 1, '0000-00-00 00:00:00')";
	$result = mysql_query($sql, $db);
	
	if (!$result) {
		return false;
	}else{
                if ($row = mysql_fetch_assoc($result)){
                    return $row['member_id'];
                }else{
                    return true;
                }
	}

}


?>
