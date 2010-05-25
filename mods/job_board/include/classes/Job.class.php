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

class Job{
	function Job(){}

	/**
	 * Add a job posting to the database.
	 * @param	string	job title
	 * @param	string	description
	 * @param	Array	categories
	 */
	function addJob($title, $description, $categories, $is_public, $closing_date){
		global $addslashes, $db, $msg;

		$title = $addslashes($title);
		$description = $addslashes($description);
		if (!empty($categories)){
			foreach($categories as $id => $category){
				$categories[$id] = intval($category);
			}
		}
		$is_public = (intval($is_public)==0)?0:1;
		$closing_date = $addslashes($closing_date);

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_postings SET (employer_id, titlie, descriptions, categories, is_public, closing_date, create_date, revised_date) VALUES ($employer_id, '$title', '$descriptions', '$categories', $is_public, '$closing_date', NOW(), NOW())";
		$result = mysql_query($sql, $db);

		if (!$result){
			//TODO: db error message
			$msg->addError();
		}
	}

	/**
	 * Add a category, used by Admin only. 
	 * @param	string	name of the category 
	 */
	function addCategory($name){
		global $addslashes, $db, $msg;

		$name = $addslashes($name);

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_categories SET (name) VALUES ('$name')";
		$result = mysql_query($sql, $db);

		if (!$result){
			//TODO: db error message
			$msg->addError();
		}
	}

	/** 
	 * Add an employer from the registration page.
	 * @param	string	employer name
	 * @param	string	email of the employer
	 * @param	string	the company that this employer represents
	 * @param	string	a brief description of the company, useful for admin approval.
	 * @return	null
	 */
	function addEmployerRequest ($name, $email, $company, $description){
		global $addslashes, $db, $msg;
		
		$name = $addslashes($name);
		$email = $addslashes($email);
		$company = $addslashes($company);
		$description = $addslashes($description);

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_employers SET (name, email, company, description, approval_state) VALUES ('$name', '$email', '$company', '$description', 0)";
		$result = mysql_query($sql, $db);

		if (!$result){
			//TODO: db error message
			$msg->addError();
		}
	}


	/**
	 * Map a ATutor member_id to a job post.  
	 * @param	int		ATutor's member_id
	 * @param	int		Job id
	 * @return	null
	 */
	function addToJobCart($member_id, $job_id){
		global $db, $msg;

		$member_id = intval($member_id);
		$job_id = intval($job_id);

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_jobcart SET (member_id, job_id, created_date) VALUES ($member_id, $job_id, '$created_date')";
		$result = mysql_sql($sql, $db);

		if (!$result){
			//TODO: db error message
			$msg->addError();
		}
	}

	function updateJob($job_id, $description, $categories, $is_public){}

	function updateEmploer($job_id, $company, $note){}

	function removeJob($job_id){}

	function removeCategory($cat_id){}

	function removeEmployer($member_id){}

	function removeFromJobCart($member_id, $job_id){}

	function getJob($job_id){}
	
	function getAllJobs(){
		global $addslashes, $db, $msg;

		$sql = 'SELECT * FROM '.TABLE_PREFIX."jb_postings";
		$rs = mysql_query($sql, $db);
		
		while($row = mysql_fetch_assoc($rs)){
			$result[$row['id']] = $row;
		}

		return $result;
	}

	function getCategories(){
		global $addslashes, $db, $msg;

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'jb_categories';
		$rs = mysql_query($sql, $db);

		while($row = mysql_fetch_assoc($rs)){
			$result[$row['id']] = $row;
		}
		return $result;
	}

	function search($queries){}

	function approveEmployer($member_id){}

	function disapproveEmployer($member_id){}


}
?>