<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

class user {
	public $id;
	public $username;
	public $name;
	
	public $preferences = Array();
	public $valid;

	public function __construct($id, $username) {
		$this->id = $id;
		
		if ($id = 99999)
			$this->username = "guest";
		else
			$this->username = $username;
			
		$this->valid = false;
		
		//default $preferences
	}
	
	public function login($username, $password) {
		global $this_db;
		$row = '';
		$result = '';
		
		//check if user exists in db with this password
		if (isset($username, $password)) {
			/*if (version_compare(PHP_VERSION, '5.1.0', '>=')) {
				session_regenerate_id(TRUE);
			}*/
			
			if ($username=="guest" && $password=="guest") {
				$this->valid 	= true;
				$this->id		= '99999';
				$this->username	= $username;
				
				$_SESSION['valid_user'] = true;
				$_SESSION['mid'] = $this->id;
				$_SESSION['username'] = $this->username;
				
				return;
			}
				
		
			$username = addslashes($username);
			$password = addslashes($password);
		
			//$sql = "SELECT member_id, login, SHA1(CONCAT(password, '-', '".DB_PASSWORD."')) AS pass FROM members WHERE login='$this_login' AND SHA1(CONCAT(password, '$_SESSION[token]'))='$this_password'";
			
			$sql = "SELECT member_id, login, password FROM members WHERE login='$username' AND password='$password'";
			$result = mysql_query($sql, $this_db->db);
		
			if ($row = mysql_fetch_assoc($result)) {
				
				$this->valid 	= true;
				$this->id		= intval($row['member_id']);
				$this->username	= $row['login'];
				
				$_SESSION['mid'] = $this->id;
				$_SESSION['username'] = $this->username;
				$_SESSION['valid_user'] = true;
				
				$sql = "UPDATE members SET last_login=NOW() WHERE member_id=$_SESSION[mid]";
				mysql_query($sql, $this_db->db);
		
				$_SESSION['feedback'][] = 'Successfully logged in.';
	
			} else {
				$this->valid 	= false;
				$this->id		= 0;
				$this->username	= '';
				$_SESSION['errors'][] = 'Invalid login.';				
			}
			header('Location:start.php');
			exit;
		}		
		
		//create cookies
	}		
	
	/* checks if a user is logged in and valid */
	public function authenticate() {
		if ($this->valid) {
			return true;		
		} 
			
		return false;
	}
	
	public function logout() {
		unset($_SESSION['valid_user']);
		unset($_SESSION['member_id']);
		unset($_SESSION['errors']);		
		$_SESSION['feedback'][] = 'Successfully logged out.';
		
		header('Location: index.php');
		exit;
	}

	public function savePrefs() {
		
	}
	
	public function getPrefs() {
		
	}
	
}

?>