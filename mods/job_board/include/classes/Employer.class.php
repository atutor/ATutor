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
	var $name;			//employer name
	var $company;		//company name
	var $email;			//employer's email
	var $website;		//company's website

	//constructor
	function Employer($id){
		global $db;

		$id = intval($id);
		$this->id = $id;
		
		$sql = 'SELECT employer_name, email, company, website FROM '.TABLE_PREFIX."jb_employers WHERE id=$id";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		
		$this->name		= $row['employer_name'];
		$this->company	= $row['company'];
		$this->email	= $row['email'];
		$this->website	= $row['website'];
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

	function getWebsite(){
		return $this->website;
	}


	/**
	 * Update the employer profile.  
	 *
	 * @param	string		employer's name
	 * @param	string		company's name
	 * @param	string		employer's email
	 * @param	string		employer's website.
	 * @return	null
	 */
	 function updateProfile($name, $company, $email, $website){
		global $addslashes, $db;
		$name = $addslashes($name);
		$company = $addslashes($company);
		$email = $addslashes($email);
		$website = $addslashes($website);
		
		$sql = 'UPDATE '.TABLE_PREFIX."jb_employers SET name='$name', company='$company', email='$email', '$website' WHERE id=".$this->id;
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