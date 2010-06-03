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
	function Employer(){}

	/**
	 * Simple authentication.  
	 * @return	Boolean		True if is an employer with the valid id.  False otherwise.
 	 * @precondition		User has been authenticated via the login page.
	 */
	function authenticate(){
		if(isset($_SESSION['jb_employer_id']) && $_SESSION['jb_employer_id'] > 0){
			return true;
		}
		return false;
	}
}
?>