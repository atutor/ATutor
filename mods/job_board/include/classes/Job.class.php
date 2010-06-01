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
		$is_public = (isset($is_public))?1:0;
		$closing_date = $addslashes($closing_date);

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_postings (employer_id, title, description, is_public, closing_date, created_date, revised_date) VALUES ($_SESSION[jb_employer_id], '$title', '$description', $is_public, '$closing_date', NOW(), NOW())";
		$result = mysql_query($sql, $db);
		$posting_id = mysql_insert_id();

		//add to posting category table
		if (!empty($categories)){
			foreach($categories as $id => $category){
				$category = intval($category);
				$sql = 'INSERT INTO '.TABLE_PREFIX."jb_posting_categories (posting_id, category_id) VALUES ($posting_id, $category)";
				mysql_query($sql, $db);
			}
		}

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

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_categories (name) VALUES ('$name')";
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
	 * @param	string	password for the login
	 * @param	string	the company that this employer represents
	 * @param	string	a brief description of the company, useful for admin approval.
	 * @param	string	company main website.
	 * @return	null
	 */
	function addEmployerRequest ($name, $email, $password, $company, $description, $website=""){
		global $addslashes, $db, $msg;
		
		$name = $addslashes($name);
		$email = $addslashes($email);
		$password = $addslashes($password);
		$company = $addslashes($company);
		$description = $addslashes($description);
		$website = $addslashes($website);

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_employers (name, email, password, company, description, website, approval_state) VALUES ('$name', '$email', '$password', '$company', '$description', '$website', 0)";
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

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_jobcart (member_id, job_id, created_date) VALUES ($member_id, $job_id, '$created_date')";
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
			$row['categories'] = $this->getPostingCategories($row['id']);
		}
		return $row;
	}
	

	/**
	 * Return all jobs
	 * @param	int		current page number.  The page that the user is viewing
	 * @return	Array	job posts that will be shown on the given page. 
	 */
	function getAllJobs($page=0){
		global $addslashes, $db, $msg;

		//handle pages offset
		$page = intval($page);
		if ($page > 0){
			$offset = ($page - 1) * AT_JB_ROWS_PER_PAGE;
			$extra = " LIMIT $offset, ".AT_JB_ROWS_PER_PAGE;
		} else {
			$extra = '';
		}
		$sql = 'SELECT * FROM '.TABLE_PREFIX."jb_postings $extra";
		$rs = mysql_query($sql, $db);
		if ($rs){
			while($row = mysql_fetch_assoc($rs)){
				$row['categories'] = $this->getPostingCategories($row['id']);
				$result[$row['id']] = $row;
			}
		}

		return $result;
	}
	
	/**
	 * Returns a list of jobs that's created by the currented logged in employer
 	 * @param	int		current page number.  The page that the user is viewing
	 */
	function getMyJobs($page=0){
	    global $addslashes, $db, $msg;
	    
		//handle pages offset
		$page = intval($page);
		if ($page > 0){
			$offset = ($page - 1) * AT_JB_ROWS_PER_PAGE;
			$extra = " LIMIT $offset, ".AT_JB_ROWS_PER_PAGE;
		} else {
			$extra = '';
		}

	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'jb_postings WHERE employer_id='.$_SESSION['jb_employer_id']." $extra";
	    $rs = mysql_query($sql, $db);
	    
	    while($row = mysql_fetch_assoc($rs)){
			$row['categories'] = $this->getPostingCategories($row['id']);
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

	/**
	 * Get the categories by the given posting id
	 * @param	int		posting id
	 * @return	Array	Array of categories integers.  Null if input is an empty string.
	 * @private
	 */
	function getPostingCategories($pid){
		global $addslashes, $db;
		$pid = intval($pid);

		$sql = 'SELECT * FROM '.TABLE_PREFIX."jb_posting_categories WHERE posting_id=$pid";
		$rs = mysql_query($sql, $db);

		while($row = mysql_fetch_assoc($rs)){
			$result[] = $row['category_id'];
		}
		return $result;
	}

	/**
	 * Perform a search with the given filters.
	 * @param	Array	[field]=>[input]
	 * @return	Array	matched entries
	 */
	function search($input){
		global $addslashes, $db; 

		//If input is not an array, quit right away.  
		if (!is_array($input)){
			return;
		}

		//get the search fields
		$title = $addslashes($input['title']);
	}

	function approveEmployer($member_id){}

	function disapproveEmployer($member_id){}



}
?>
