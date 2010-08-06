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

class Employer{
	var $id;			//employer id
	var $username;      //employer's username
	var $name;			//employer name
	var $company;		//company name
	var $description;   //description of this employer
	var $email;			//employer's email
	var $website;		//company's website
	var $last_login;    //last login date
	var $approval_state;//approval state

	//constructor
	function Employer($id){
		global $db;

		$id = intval($id);
		$this->id = $id;
		
		$sql = 'SELECT username, employer_name, email, company, description, website, last_login, approval_state FROM '.TABLE_PREFIX."jb_employers WHERE id=$id";
		$result = mysql_query($sql, $db);
		if ($result){
	        $row = mysql_fetch_assoc($result);

	        $this->name		= $row['employer_name'];
	        $this->username = $row['username'];
	        $this->company	= $row['company'];
	        $this->description	= $row['description'];
	        $this->email	= $row['email'];
	        $this->website	= $row['website'];
	        $this->last_login = $row['last_login'];
	        $this->approval_state = $row['approval_state'];
		}
	}
	
	function getId(){
		return $this->id;
	}

	function getUsername(){
	    return $this->username;
    }

	function getName(){
		return $this->name;
	}
	
	function getEmail(){
		return $this->email;
	}

	function getCompany(){
		return $this->company;
	}
	
	function getDescription(){
	    return $this->description;
    }

	function getWebsite(){
		return $this->website;
	}
	
	function getLastLogin(){
	    return $this->last_login;
    }
    
    function getApprovalState(){
        return $this->approval_state;
    }

	/**
	 * Set the approval state value
	 * @param	int		Approval state, check constants.inc.php
	 * @return	null
	 */
	function setApprovalState($state){
		global $addslashes, $db;
		
		//change this if the approval_state has more than 3 values in the constant file
		$state = (intval($state) > 2)?AT_JB_STATUS_UNCONFIRMED:intval($state);

		$sql = 'UPDATE '.TABLE_PREFIX."jb_employers SET approval_state='$state' WHERE id=".$this->id;
		mysql_query($sql, $db);
	}


	/**
	 * Update the employer profile.  
	 *
	 * @param	string		employer's name
	 * @param	string		company's name
	 * @param	string		employer's email
	 * @param	string		employer's website.
     * @param   string      employer's description
	 * @return	null
	 */
	 function updateProfile($name, $company, $email, $website, $description){
		global $addslashes, $db;
		$name = $addslashes($name);
		$company = $addslashes($company);
		$email = $addslashes($email);
		$website = $addslashes($website);
        $description = $addslashes($description);
		
		$sql = 'UPDATE '.TABLE_PREFIX."jb_employers SET employer_name='$name', company='$company', email='$email', website='$website', description='$description' WHERE id=".$this->id;
		$result = mysql_query($sql, $db);
		if ($result){
			return true;
		} 
		return false;
	 }
	 
	 /**
	  * Update password 
	  * @pass   string      SHA1 encrpted password, length=40
	  */
	 function updatePassword($pass){
	    global $addslashes, $db;
	    $pass = $addslashes($pass);
	  
	    //if password is empty, or not encrypted, quit
	    if ($pass=='' || strlen($pass)!=40){
	        return;
        }
        
	    $sql = 'UPDATE '.TABLE_PREFIX."jb_employers SET password='$pass' WHERE id=".$this->id;
	    mysql_query($sql, $db);
	 }

	/**
	 * Simple authentication.  
	 *
	 * @return	Boolean		True if is an employer with the valid id.  False otherwise.
 	 * @precondition		User has been authenticated via the login page.
	 * @access public
	 */
	function authenticate(){
		if(isset($_SESSION['jb_employer_id']) && $_SESSION['jb_employer_id'] > 0){
			return true;
		}
		return false;
	}
}
?>
