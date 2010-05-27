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
	var $categories; //list of available categories

	function Job(){
		$this->categories = $this->getCategories();
	}

	/**
	 * Add a job posting to the database.
	 * @param	string	job title
	 * @param	string	description
	 * @param	Array	categories (must be converted to JSON before storing into database)
	 * @param   int     1 if public; 0 otherwise.
	 * @param   string  Closing date for this job post, mysql TIMESTAMP format
	 */
	function addJob($title, $description, $categories, $is_public, $closing_date){
		global $addslashes, $db, $msg;
		
		if($_SESSION['jb_employer_id']<1){
		    $msg->addError();   //authentication error
		    exit;
        }
        
		$title = $addslashes($title);
		$description = $addslashes($description);
		if (!empty($categories)){
			foreach($categories as $id => $category){
				$categories[$id] = intval($category);
			}
		}
		$categories = json_encode($categories);
		$is_public = (intval($is_public)==0)?0:1;
		$closing_date = $addslashes($closing_date);

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_postings (employer_id, title, description, categories, is_public, closing_date, created_date, revised_date) VALUES ($_SESSION[jb_employer_id], '$title', '$description', '$categories', $is_public, '$closing_date', NOW(), NOW())";
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


	/** 
	 * Return all the values of a single job
	 * @param	int		The id of the job
	 * @return	Array	row value of the job entry.
	 */
	function getJob($job_id){
		global $addslashes, $db, $msg;
		$job_id = intval($job_id);
		
		$sql = 'SELECT * FROM '.TABLE_PREFIX."jb_postings WHERE id=$job_id";
		$rs = mysql_query($sql, $db);
		if ($rs){
			$row = mysql_fetch_assoc($rs);
			$row['categories'] = $this->convertJsonCategoriesToArray($row['categories']);
		}
		return $row;
	}
	

	/**
	 * Return all jobs
	 * @return	Array	array of rows
	 */
	function getAllJobs(){
		global $addslashes, $db, $msg;

		$sql = 'SELECT * FROM '.TABLE_PREFIX."jb_postings";
		$rs = mysql_query($sql, $db);
		if ($rs){
			while($row = mysql_fetch_assoc($rs)){
				$row['categories'] = $this->convertJsonCategoriesToArray($row['categories']);
				$result[$row['id']] = $row;
			}
		}

		return $result;
	}
	
	/**
	 * Returns a list of jobs that's created by the currented logged in employer
	 */
	function getMyJobs(){
	    global $addslashes, $db, $msg;
	    
	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'jb_postings WHERE employer_id='.$_SESSION['jb_employer_id'];
	    $rs = mysql_query($sql, $db);
	    
	    while($row = mysql_fetch_assoc($rs)){
			$row['categories'] = $this->convertJsonCategoriesToArray($row['categories']);
	        $result[$row['id']] = $row;
        }
        
        return $result;
    }

	function getCategories(){
		global $addslashes, $db, $msg;

		//If this instance already have the categories, don't run the query.
		if(!empty($this->categories)){
			return $this->categories;
		}

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'jb_categories';
		$rs = mysql_query($sql, $db);

		while($row = mysql_fetch_assoc($rs)){
			$result[$row['id']] = $row;
		}
		return $result;
	}

	/**
	 * Match the category id to its name
	 * @param	int		Category ID
	 * @return	string	the name of the category.
	 */
	function getCategoryNameById($id){
		return $this->categories[intval($id)]['name'];
	}

	function search($queries){}

	function approveEmployer($member_id){}

	function disapproveEmployer($member_id){}


	/**
	 * Convert the json formated categories string into an array
	 * @param	string		json format categories
	 * @return	Array		Array of categories integers.  Null if input is an empty string.
	 * @private
	 */
	private function convertJsonCategoriesToArray($categories){
		if ($categories!=''){
			$categories_entry = json_decode($categories);
			return $categories_entry;
		}
		return null;
	}
}
?>
