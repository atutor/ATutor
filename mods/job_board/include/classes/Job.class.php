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
	var $cols;		//sortable columns

	function Job(){
		$this->categories = $this->getCategories();

		$cols['title'] = 'title';
		$cols['created_date'] = 'created_date';
		$cols['closing_date'] = 'closing_date';
		$this->cols = $cols;
	}

	/**
	 * Add a job posting to the database.
	 * @param	string	job title
	 * @param	string	description
	 * @param	Array	categories id
	 * @param   int     1 if public; 0 otherwise.
	 * @param   string  Closing date for this job post, mysql TIMESTAMP format
	 * @precondition	ATutor Mailer class imported.
	 */
	function addJob($title, $description, $categories, $is_public, $closing_date){
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		global $addslashes, $db, $msg;
		
		if($_SESSION['jb_employer_id']<1){
		    $msg->addError();   //authentication error
		    exit;
        }

		$title = $addslashes($title);
		$description = $addslashes($description);
		$is_public = (isset($is_public))?1:0;
		$closing_date = $addslashes($closing_date);
		$approval_state = ($_config['jb_posting_approval']==1)?AT_JB_POSTING_STATUS_UNCONFIRMED:AT_JB_POSTING_STATUS_CONFIRMED;	

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_postings (employer_id, title, description, is_public, closing_date, created_date, revised_date, approval_state) VALUES ($_SESSION[jb_employer_id], '$title', '$description', $is_public, '$closing_date', NOW(), NOW(), $approval_state)";
		$result = mysql_query($sql, $db);
		$posting_id = mysql_insert_id();

		//add to posting category table
		if (!empty($categories)){
			foreach($categories as $id => $category){
				$category = intval($category);
				$sql = 'INSERT INTO '.TABLE_PREFIX."jb_posting_categories (posting_id, category_id) VALUES ($posting_id, $category)";
				mysql_query($sql, $db);

				//send out notification if the person is subscribed to the category.
				$sql = 'SELECT m.member_id, m.email FROM '.TABLE_PREFIX.'jb_category_subscribes cs LEFT JOIN '.TABLE_PREFIX."members m ON cs.member_id=m.member_id WHERE category_id=$category";
				$result = mysql_query($sql, $db);
				if($result){
					while($row = mysql_fetch_assoc($result)){
						$mail = new ATutorMailer;
						$mail->AddAddress($row['email'], get_display_name($row['member_id']));
						$body = _AT('jb_subscription_msg');
						$body .= "\n----------------------------------------------\n";
						$body .= _AT('posted_by').": ".get_display_name($row['member_id'])."\n";
						$body .= $_POST['body']."\n";
						$mail->FromName = $_config['site_name'];
						$mail->From     = $_config['contact_email'];
						$mail->Subject = _AT('jb_subscription_mail_subject');
						$mail->Body    = $body;
/* 
 * TODO: 
 * Take out these comments, it's here cause my email isn't set up and it slows down my browser.
						if(!$mail->Send()) {
							$msg->addError('SENDING_ERROR');
						}
*/
						unset($mail);
					}
				}
				
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

		$name = $addslashes(trim($name));

		//don't update if it's empty.
		if ($name==''){
			$msg->addError('JB_CATEGORY_NAME_CANNOT_BE_EMPTY');
			return;
		}

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_categories (name) VALUES ('$name')";
		$result = mysql_query($sql, $db);

		if (!$result){
			//TODO: db error message
			$msg->addError();
		}

		//add this category to the category list.
		$row['id'] = mysql_insert_id();;
		$row['name'] = $name;
		$this->categories[] = $row;
		$msg->addFeedback('JB_CATEGORY_ADDED_SUCCESSFULLY');
	}

	/** 
	 * Add an employer from the registration page.	 
	 * @param	string	username 
	 * @param	string	password for the login
	 * @param	string	employer name
	 * @param	string  employer's email
	 * @param	string	the company that this employer represents
	 * @param	string	a brief description of the company, useful for admin approval.
	 * @param	string	Requested date in the format of mysql TIMESTAMP, (yyyy-mm-dd hh:mm:ss)
	 * @param	string	company main website.
	 * @return	the ID of this employer.
	 */
	function addEmployerRequest ($username, $password, $employer_name, $email, $company, $description, $requested_date, $website=""){
		global $addslashes, $db, $msg;
		
		$username = $addslashes($username);
		$password = $addslashes($password);
		$employer_name = $addslashes($employer_name);
		$email = $addslashes($email);
		$company = $addslashes($company);
		$description = $addslashes($description);
		$requested_date = $addslashes($requested_date);
		$website = $addslashes($website);
		$approval_status = AT_JB_STATUS_UNCONFIRMED;

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_employers (username, password, employer_name, email, company, description, website, requested_date, approval_state) VALUES ('$username', '$password', '$employer_name', '$email', '$company', '$description', '$website', '$requested_date', $approval_status)";
		$result = mysql_query($sql, $db);
		if (!$result){
			//TODO: db error message
			$msg->addError();
		}
		return mysql_insert_id();
	}


	/**
	 * Bookmark this job.
	 * @param	int		ATutor's member_id
	 * @param	int		Job id
	 * @return	null
	 */
	function addToJobCart($member_id, $job_id){
		global $db, $msg;

		$member_id = intval($member_id);
		$job_id = intval($job_id);

		$sql = 'INSERT INTO '.TABLE_PREFIX."jb_jobcart (member_id, job_id, created_date) VALUES ($member_id, $job_id, NOW())";
		$result = mysql_query($sql, $db);

		if (!$result){
			//TODO: db error message 
			$msg->addError();
		}
	}

	/**
	 * Update the job posting 
	 * @param	int		Job's id
	 * @param	string	job title
	 * @param	string	description
	 * @param	Array	categories id
	 * @param   int     1 if public; 0 otherwise.
	 * @param   string  Closing date for this job post, mysql TIMESTAMP format
	 * @param	int		Check job_board/include/constants.inc.php
	 */
	function updateJob($id, $title, $description, $categories, $is_public, $closing_date, $approval_state){
		global $addslashes, $db, $msg;
		
		$id = intval($id);
		$title = $addslashes($title);
		$description = $addslashes($description);
		$is_public = (isset($is_public))?1:0;
		$closing_date = $addslashes($closing_date);
		$approval_state = intval($approval_state);

		$sql = 'UPDATE '.TABLE_PREFIX."jb_postings SET title='$title', description='$description', is_public=$is_public, closing_date='$closing_date', approval_state=$approval_state WHERE id=$id";
		mysql_query($sql, $db);

		//update to posting category table
		if (!empty($categories)){
			//remove all
			$sql = 'DELETE FROM '.TABLE_PREFIX."jb_posting_categories WHERE posting_id=$id";
			mysql_query($sql, $db);

			foreach($categories as $category){
				$category = intval($category);				
				//add all the categories back.
				$sql = 'INSERT INTO '.TABLE_PREFIX."jb_posting_categories (posting_id, category_id) VALUES ($id, $category)";
				mysql_query($sql, $db);
			}
		}
	}

	/**
	 * Update category name, used by admin only.
	 * @param	int		category id
	 * @param	string	category name
	 * @return	null
	 */
	function updateCategory($id, $name){
		global $addslashes, $db, $msg;

		$id = intval($id);
		$name = $addslashes(trim($name));

		//don't update if it's empty.
		if ($name==''){
			$msg->addError('JB_CATEGORY_NAME_CANNOT_BE_EMPTY');
			return;
		}

		$sql = 'UPDATE '.TABLE_PREFIX."jb_categories SET name='$name' WHERE id=$id";
		$result = mysql_query($sql, $db);

		if (!$result){
			$msg->addError();
		}
	}

	function updateEmployer($employer_id, $company, $note){}

	/**
	 * Remove this job posting entry from the database
	 * @param	int		job posting id
	 */
	function removeJob($job_id){
		global $db;
		$job_id = intval($job_id);

		//Delete all associated posting_categories
//		$sql = 'DELETE FROM '.TABLE_PREFIX."jb_posting_categories WHERE posting_id=$job_id";
//		mysql_query($sql, $db);

		//Delete job cart posting entries


		//Delete job post
	}

	/**
	 * Remove the category 
	 * @param	int		category id.
	 */
	function removeCategory($cat_id){
		global $db;

		$cat_id = intval($cat_id);
		if($cat_id < 1){
			return;
		}

		//Remove all categories entries with this category id
		$sql = 'DELETE FROM '.TABLE_PREFIX."jb_posting_categories WHERE category_id=$cat_id";
		mysql_query($sql, $db);

		$sql = 'DELETE FROM '.TABLE_PREFIX."jb_categories WHERE id=$cat_id";
		mysql_query($sql, $db);
	}

	function removeEmployer($member_id){}

	/**
	 * Remove the job bookmark 
	 * @param	int		member id
	 * @param	int		job posting id
	 * @return	null
	 */
	function removeFromJobCart($member_id, $job_id){
		global $db;
		$member_id = intval($member_id);
		$job_id = intval($job_id);

		$sql = 'DELETE FROM '.TABLE_PREFIX."jb_jobcart WHERE member_id=$member_id AND job_id=$job_id";
		mysql_query($sql, $db);
	}


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
	 * @param	string		sortable columns: title, created_date, closing_date
	 * @param	string		asc for ascending, else descending
	 * @param	boolean		true if this is an admin.  If set to true. will return all 
	 *						entries even if it's not approved.  Default is false
	 * @return	Array		job posts that will be shown on the given page. 
	 *                      Return empty array if no entries.
	 */
	function getAllJobs($col, $order, $is_admin=false){
		global $addslashes, $db, $msg;
		$result = array();

		//if not admin, filter only the ones that's approved.
		if(!$is_admin){
			$now = date('Y-m-d H:i:s');
			$filter_sql = "WHERE closing_date >= '$now' AND approval_state=".AT_JB_POSTING_STATUS_CONFIRMED;
		} else {
			$filter_sql = '';
		}

		//order
		$col = isset($this->cols[$col])?$this->cols[$col]:$this->cols['created_date'];
		$order = ($order=='ASC')?'ASC':'DESC';

		$sql = 'SELECT * FROM '.TABLE_PREFIX."jb_postings $filter_sql ORDER BY $col $order";
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
	 * @param	string		sortable columns: title, created_date, closing_date
	 * @param	string		asc for ascending, else descending
	 * @return	Array	job posts that will be shown on the given page. 
	 */
	function getMyJobs($col, $order){
	    global $addslashes, $db, $msg;
	    $result = array();

		//order
		$col = isset($this->cols[$col])?$this->cols[$col]:$this->cols['created_date'];
		$order = ($order=='ASC')?'ASC':'DESC';
	    
	    $sql = 'SELECT * FROM '.TABLE_PREFIX.'jb_postings WHERE employer_id='.$_SESSION['jb_employer_id']." ORDER BY $col $order";
	    $rs = mysql_query($sql, $db);
	    
	    while($row = mysql_fetch_assoc($rs)){
			$row['categories'] = $this->getPostingCategories($row['id']);
	        $result[$row['id']] = $row;
        }
        
        return $result;
    }

	/**
	 * Returns a list of jobs that are bookmarked.
	 * @return	Array	job posts that are bookmarked by the ATutor user
	 */
	 function getBookmarkJobs(){
		 global $db;
		 $member_id = $_SESSION['member_id'];
		 $result = array();

		 $sql = 'SELECT * FROM '.TABLE_PREFIX."jb_jobcart WHERE member_id=$member_id";
		 $rs = mysql_query($sql, $db);
		 if($rs){
			 while($row=mysql_fetch_assoc($rs)){
				$result[] = $row['job_id'];
			 }
		 }
		 return $result;
	 }

	//returns the list of categories.
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
		foreach($this->categories as $category){
			if ($category['id']==$id){
				return $category['name'];
			}
		}
		//if it can't find any category, then return 'no category'
		return _AT('jb_no_category');
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

		if($rs){
		    while($row = mysql_fetch_assoc($rs)){
			    $result[] = $row['category_id'];
		    }
		}
		return $result;
	}

	/**
	 * Get the list of categories that this member is subscribed to.
	 * @param	int		member id
	 * @return	Array	list of categories
	 */
	function getSubscribedCategories($member_id){
		global $db;

		$member_id = intval($member_id);
		$result = array();
		
		$sql = 'SELECT category_id FROM '.TABLE_PREFIX."jb_category_subscribes WHERE member_id=$member_id";
		$rs = mysql_query($sql, $db);
		
		if ($rs){
			while($row = mysql_fetch_array($rs)){
				$result[] = $row[0];
			}
		}
		return $result;
	}

	/**
	 * Perform a search with the given filters.
	 * @param	Array	[field]=>[input].  Format must be the following:
	 *						[title]		 =>[string] *no longer in use
	 *						[categories] =>Array(integer)
	 *						[email]		 =>[string] *no longer in use
	 *						[description]=>[string] *no longer in use
	 *						[bookmark]	 =>[string] (on/off)
	 *						[archive]	 =>[string] (on/off)
	 * @param	string		sortable columns: title, created_date, closing_date
	 * @param	string		asc for ascending, else descending
	 * @return	Array	matched entries
	 */
	function search($input, $col, $order){
		global $addslashes, $db; 
        $result = array();
		//If input is not an array, quit right away.  
		if (!is_array($input)){
			return;
		}

		//get the search fields
		$general = $addslashes($input['general']);
//		$title = $addslashes($input['title']);
//		$email = $addslashes($input['email']);
//		$description = $addslashes($input['description']);
		$categories = $input['categories'];
		$bookmark = $input['bookmark'];
		$archive = $input['archive'];

		//create sub sql for general search
		if ($general!=''){
			$general_sql = "`title` LIKE '%$general%' OR `description` LIKE '%$general%' OR ";
		}

		//create sub sql for the search fields.
		//*merged with general search 
/*		if ($title!=''){
			$title_bits = explode(' ', $input['title']);
			$title_sql = '';
			//concat all the title search fields together.
			foreach($title_bits as $v){
				$title_sql .= "`title` LIKE '%$v%' OR ";
			}
		}
*/		
/*
 * Not sure if this is actually useful.
		if ($email!=''){
			$email_bits = explode(' ', $input['email']);
			$email_sql = '';
			//concat all the email search fields together.
			foreach($email_bits as $v){
				$email_sql .= "`email` LIKE '%$v%' OR ";
			}
		}
*/
/*
		if ($description!=''){
			$description_bits = explode(' ', $input['description']);
			$description_sql = '';
			//concat all the description search fields together.
			foreach($description_bits as $v){
				$description_sql .= "`description` LIKE '%$v%' OR ";
			}			
		}
*/		
		if (is_array($categories) && !empty($categories)){
			foreach($categories as $k=>$category_id){
				//if 'any' is selected, use all category
				if ($category_id==0){
					$categories = $this->getCategories();
					foreach ($categories as $k2=>$v2){
						$categories[$k2] = intval($v2['id']);
					}
					break;
				}
				$categories[$k] = intval($category_id);				
			}
			$categories = '('. implode(',', $categories) . ')';
			$categories_sql = 'RIGHT JOIN (SELECT DISTINCT posting_id FROM '.TABLE_PREFIX."jb_posting_categories WHERE category_id IN $categories) AS pc ON p.id=pc.posting_id ";
		}

		if($bookmark!=''){
			$bookmark_jobs = $this->getBookmarkJobs();
			$bookmarks = '('. implode(',', $bookmark_jobs) . ')';
			$bookmark_sql = "`id` IN $bookmarks OR ";
		}

		//load entries with expired closing date
		if ($archive==''){
			$now = date('Y-m-d H:i:s');
			$closing_sql = "closing_date >= '$now' AND ";
		}

		//only closed time and approved state
		//this sql must go first
		$approval_closing_sql = "($closing_sql approval_state=".AT_JB_POSTING_STATUS_CONFIRMED.')';
		
		$sql_wc = $general_sql . $title_sql . $email_sql . $description_sql . $bookmark_sql; //where clause
		if ($sql_wc!=''){
			$sql_wc = substr($sql_wc, 0, -3);
			$sql_wc = ' AND ('. $sql_wc . ')';
		}
		
		//order
		$col = isset($this->cols[$col])?$this->cols[$col]:$this->cols['created_date'];
		$order = ($order=='ASC')?'ASC':'DESC';

		//compose the search query
		$sql = 'SELECT p.* FROM '.TABLE_PREFIX."jb_postings AS p $categories_sql WHERE $approval_closing_sql $sql_wc ORDER BY $col $order";
		$rs = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($rs)){
			$row['categories'] = $this->getPostingCategories($row['id']);
			$result[] = $row;
		}
		return $result;
	}

	function approveEmployer($member_id){}

	function disapproveEmployer($member_id){}


	/** 
	 * Update subscription for the categories.  Remove existing entries first, then re-insert new ones.
	 * @param	int		Member id
	 * @param	Array	Categories IDs.  [index]=>[category_id]
	 */
	function subscribeCategories ($member_id, $categories){
		global $db;

		$member_id = intval($member_id);

		//remove old subscriptions
		$sql = 'DELETE FROM '.TABLE_PREFIX."jb_category_subscribes WHERE member_id=$member_id";
		mysql_query($sql, $db);

		if (!empty($categories)){
			foreach($categories as $category){
				$category = intval($category);
				if($category < 1){
					continue;
				}

				//add new subscription
				$sql = 'INSERT INTO '.TABLE_PREFIX."jb_category_subscribes (member_id, category_id) VALUES ($member_id, $category)";
				mysql_query($sql, $db);
			}
		}
	}
}
?>
