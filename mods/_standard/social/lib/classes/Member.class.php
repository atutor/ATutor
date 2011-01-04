<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: Member.class.php 10055 2010-06-29 20:30:24Z cindy $

/**
 * Members class for Social Networking
 * TODO: Extend it for the entire ATutor.
 */
class Member {
	var $id;		//member id
	var $profile;	//profile details

	function Member($id){
		$this->id = intval($id);
	}


	/**
	 * Add a new job position
	 * @param	string		Name of the company, in full.
	 * @param	string		Title of this position
	 * @param	int			Started date for this position, in the format of yyyymm
	 * @param	int			Position ended on this date, in the format of yyyymm, or 'NOW'. 
	 *						'NOW' means it is still on going.
	 * @param	string		Description of what the position was about
	 */
	function addPosition($company, $title, $from, $to, $description){
		global $addslashes, $db;
		$member_id	= $this->id;
		$company	= $addslashes($company);
		$title		= $addslashes($title);
		$from		= $addslashes($from);
		$to			= $addslashes($to);
		$description = $addslashes($description);

		$sql = 'INSERT INTO '.TABLE_PREFIX."social_member_position (member_id, company, title, `from`, `to`, description) VALUES ($member_id, '$company', '$title', '$from', '$to', '$description')";
		mysql_query($sql, $db);
	}

	
	/**
	 * Add a new education
	 * TODO: University names can be generated from another table.
	 * 
	 * @param	string		Name of the University, in full. Might need to pull from another table.
	 * @param	int			This education begins on this date, yyyymm
	 * @param	int			This education ends on this date, yyyymm, or can be 'NOW'
	 * @param	string		The full name of the country this University is in, ie. Canada
	 * @param	string		The full name of the province this University is in, ie. Ontario
	 * @param	string		The name of the degree, ie. B.Sc.
	 * @param	string		The field of study, ie. Computer Science
	 * @param	string		The description of this education.
	 */
	function addEducation($university, $from, $to, $country, 
						  $province, $degree, $field, $description){ 
		global $addslashes, $db;
		$member_id			= $this->id;
		$university			= $addslashes($university);
		$from				= $addslashes($from);
		$to					= $addslashes($to);
		$country			= $addslashes($country);
		$province			= $addslashes($province);
		$degree				= $addslashes($degree);
		$field				= $addslashes($field);
		$description		= $addslashes($description);
		
		$sql = 'INSERT INTO '.TABLE_PREFIX."social_member_education (member_id, university, `from`, `to`, country, province, degree, field, description) VALUES ($member_id, '$university', '$from', '$to', '$country', '$province', '$degree', '$field', '$description')";
		mysql_query($sql, $db);
	}


	/**
	 * Add a new website associated with this member, can be blog, work, portfolio, etc.
	 * @param	string		Unique URL of the website
	 * @param	string		A name for the website.
	 */
	function addWebsite($url, $site_name){ 
		global $addslashes, $db;
		$member_id	= $this->id;
		$url		= $addslashes($url);
		$site_name	= $addslashes($site_name);

		$sql = 'INSERT INTO '. TABLE_PREFIX ."social_member_websites (member_id, url, site_name) VALUES ($member_id, '$url', '$site_name')";
		mysql_query($sql, $db);
	}


	/** 
	 * Add new interest for this member, in CSV format
	 * @param	string		interest
	 */
	function addInterests($interests){
		$this->updateAdditionalInformation($interests);
	}


	/** 
	 * Add new interest for this member, in CSV format
	 * @param	string		interest
	 */
	function addAssociations($associations){
		$this->updateAdditionalInformation('', $associations);
	}


	/** 
	 * Add new interest for this member, in CSV format
	 * @param	string		interest
	 */
	function addAwards($awards){
		$this->updateAdditionalInformation('', '', $awards);
	}
	/**
	 * Add a new representation
	 * Special field for LCA to add a represetnative or agent.
	 * 
	 * @param	string		The represetnative full name
	 * @param	string		The title of the representative
	 * @param	string		The represetnative's phone number
	 * @param	string		The representativ's email address
	 * @param	string		The representative's mailing address
	 */
	function addRepresentation($rep_name, $rep_title, $rep_phone, $rep_email, $rep_address){ 
		global $addslashes, $db;
		$member_id			= $this->id;
		$rep_name			= $addslashes($rep_name);
		$rep_title			= $addslashes($rep_title);
		$rep_phone			= $addslashes($rep_phone);
		$rep_email			= $addslashes($rep_email);
		$rep_address			= $addslashes($rep_address);
		
		$sql = 'INSERT INTO '.TABLE_PREFIX."social_member_representation (member_id, rep_name, rep_title, rep_phone, rep_email, rep_address) VALUES ($member_id, '$rep_name', '$rep_title', '$rep_phone', '$rep_email', '$rep_address')";
		mysql_query($sql, $db);
		header('Location:edit_profile.php');
		exit;
	}

	/**
	 * Add a new contact person
	 * Special field for LCA to add a contact such as a parent or gaurdian.
	 * 
	 * @param	string		The contact full name
	 * @param	string		The contact's phone number
	 * @param	string		The contact's email address
	 * @param	string		The contact's mailing address
	 */

	function addContact($con_name, $con_phone, $con_email, $con_address){ 
		global $addslashes, $db;
		$member_id			= $this->id;
		$con_name			= $addslashes($con_name);
		$con_phone			= $addslashes($con_phone);
		$con_email			= $addslashes($con_email);
		$con_address			= $addslashes($con_address);
		
		$sql = 'INSERT INTO '.TABLE_PREFIX."social_member_contact (member_id, con_name, con_phone, con_email, con_address) VALUES ($member_id, '$con_name', '$con_phone', '$con_email', '$con_address')";
		mysql_query($sql, $db);
		header('Location:edit_profile.php');
		exit;
	}
	/**
	 * Add personal characteristics
	 * Special field for LCA to add a contact such as a parent or gaurdian.
	 * 
	 * @param	string		person's weight
	 * @param	string		person's height
	 * @param	string		person's hair colour
	 * @param	string		person's eye colour
	 * @param	string		person's ethnicity
	 * @param	string		person's languages spoken
	 * @param	string		person's disbailities
	 */

	function addPersonal($per_weight, $per_height, $per_hair, $per_eyes, $per_ethnicity, $per_languages, $per_disabilities){ 
		global $addslashes, $db;
		$member_id			= $this->id;
		$per_weight			= $addslashes($per_weight);
		$per_height			= $addslashes($per_height);
		$per_hair			= $addslashes($per_hair);
		$per_eyes			= $addslashes($per_eyes);
		$per_ethnicity			= $addslashes($per_ethnicity);
		$per_languages			= $addslashes($per_languages);
		$per_disabilities		= $addslashes($per_disabilities);

		$sql = 'INSERT INTO '.TABLE_PREFIX."social_member_personal (member_id, per_weight, per_height, per_hair, per_eyes, per_ethnicity, per_languages, per_disabilities) VALUES ($member_id, '$per_weight', '$per_height', '$per_hair', '$per_eyes','$per_ethnicity','$per_languages','$per_disabilities')";
		mysql_query($sql, $db);
		header('Location:edit_profile.php');
		exit;
	}



	/** 
	 * Add additional information, including interest, awards, associations.
	 * @param	string		CSV format of interests, ie. camping, biking, etc
	 * @param	string		CSV format of associations, clubs, groups, ie. IEEE
	 * @param	string		CSV format of awards, honors
	 * @param	string		expterise, occupation
 	 * @param	string		any extra information
	 */
	function addAdditionalInformation($interests, $associations, $awards, $expertise, $others){ 
		global $addslashes, $db;
		$member_id		= $this->id;
		$interests		= $addslashes($interests);
		$associations	= $addslashes($associations);
		$awards			= $addslashes($awards);
		$expertise		= $addslashes($expertise);
		$others			= $addslashes($others);
		$sql = 'INSERT INTO ' . TABLE_PREFIX . "social_member_additional_information (member_id, interests,  associations, awards, expertise, others) VALUES ($member_id, '$interests', '$associations', '$awards', '$expertise', '$others')";
		mysql_query($sql, $db);
	}


	/** 
	 * Add visitor
	 * @param	int		visitor id
	 */
	function addVisitor($visitor_id){
		global $db;
		$visitor_id = intval($visitor_id);
		$sql = 'INSERT INTO '.TABLE_PREFIX."social_member_track (`member_id`, `visitor_id`, `timestamp`) VALUES (".$this->getID().", $visitor_id, NOW())";
		mysql_query($sql, $db);
	}


	/**
	 * Update a new job position
	 * @param	int			The id of this entry
	 * @param	string		Name of the company, in full.
	 * @param	string		Tht title of this position
	 * @param	int			Started date for this position, in the format of yyyymm
	 * @param	int			Position ended on this date, in the format of yyyymm, or 'NOW'. 
	 *						'NOW' means it is still on going.
	 * @param	string		Description of the position
	 */
	function updatePosition($id, $company, $title, $from, $to, $description){ 
		global $addslashes, $db;
		$id			 = intval($id);
		$company	 = $addslashes($company);
		$title		 = $addslashes($title);
		$form		 = $addslashes($form);
		$to			 = $addslashes($to);
		$description = $addslashes($description);

		$sql = 'UPDATE '.TABLE_PREFIX."social_member_position SET company='$company', title='$title', `from`='$from', `to`='$to', description='$description' WHERE id=$id";
		mysql_query($sql, $db);
	}


	/**
	 * Update a new education
	 * TODO: University names can be generated from another table.
	 * 
	 * @param	int			ID of this entry
	 * @param	string		Name of the University, in full. Might need to pull from another table.
	 * @param	int			This education begins on this date, yyyymm
	 * @param	int			This education ends on this date, yyyymm, or can be 'NOW'
	 * @param	string		The full name of the country this University is in, ie. Canada
	 * @param	string		The full name of the province this University is in, ie. Ontario
	 * @param	string		The name of the degree, ie. B.Sc.
	 * @param	string		The field of study, ie. Computer Science
	 * @param	string		The description of this education.
	 */
	function updateEducation($id, $university, $from, $to, $country, $province, $degree, $field, $description){ 
		global $addslashes, $db;
		$id					= intval($id);
		$university			= $addslashes($university);
		$from				= $addslashes($from);
		$to					= $addslashes($to);
		$country			= $addslashes($country);
		$province			= $addslashes($province);
		$degree				= $addslashes($degree);
		$field				= $addslashes($field);
		$description		= $addslashes($description);

		$sql = 'UPDATE '.TABLE_PREFIX."social_member_education SET university='$university', `from`='$from', `to`='$to', country='$country', province='$province', degree='$degree', field='$field', description='$description' WHERE id=$id";
		mysql_query($sql, $db);		
	}


	/**
	 * Updates a new website associated with this member, can be blog, work, portfolio, etc.
	 * @param	int			ID of this entry
	 * @param	string		Unique URL of the website
	 * @param	string		A name for the website.
	 */
	function updateWebsite($id, $url, $site_name){ 
		global $addslashes, $db;
		$id			= intval($id);
		$url		= $addslashes($url);
		$site_name	= $addslashes($site_name);

		$sql = 'UPDATE '.TABLE_PREFIX."social_member_websites SET url='$url', site_name='$site_name' WHERE id=$id";
		mysql_query($sql, $db);
	}


	/** 
	 * Update additional information, including interest, awards, associations.
	 * @param	string		CSV format of interests, ie. camping, biking, etc
	 * @param	string		CSV format of associations, clubs, groups, ie. IEEE
	 * @param	string		CSV format of awards, honors
	 * @param	string		expterise, occupation
 	 * @param	string		any extra information
	 */
	function updateAdditionalInformation($interests='', $associations='', $awards='', $expertise='', $others=''){ 
		global $addslashes, $db;
		$interests = $addslashes($interests);
		$associations = $addslashes($associations);
		$awards = $addslashes($awards);
		$expertise = $addslashes($expertise);
		$others = $addslashes($others);

		$sql = '';
		//tricky, not all fields get updated at once.  Update only the ones that has entries.
		if ($interests!=''){
			$sql .= "interests='$interests', ";
		}
		if ($associations!=''){
			$sql .= " associations='$associations', ";
		}
		if ($awards!=''){
			$sql .= "awards='$awards', ";
		}
		if ($expertise!=''){
			$sql .= "expertise='$expertise', ";
		}
		if ($others!=''){
			$sql .= "others='$others', ";		
		}
		if ($sql!=''){
			$sql = substr($sql, 0, -2);
		}

		$sql2 = 'INSERT INTO '.TABLE_PREFIX."social_member_additional_information SET ".$sql.", member_id=".$_SESSION['member_id'] . " ON DUPLICATE KEY UPDATE ".$sql;
		mysql_query($sql2, $db);
	}
	/**
	 * Edit representation
	 * Special field for LCA to add a represetnative or agent.
	 * 
	 * @param	string		The represetnative full name
	 * @param	string		The title of the representative
	 * @param	string		The represetnative's phone number
	 * @param	string		The representativ's email address
	 * @param	string		The representative's mailing address
	 */
	function updateRepresentation($id, $rep_name, $rep_title, $rep_phone, $rep_email, $rep_address){ 
		global $addslashes, $db;
		$member_id			= $this->id;
		$rep_name			= $addslashes($rep_name);
		$rep_title			= $addslashes($rep_title);
		$rep_phone			= $addslashes($rep_phone);
		$rep_email			= $addslashes($rep_email);
		$rep_address			= $addslashes($rep_address);
		
		$sql = "UPDATE ".TABLE_PREFIX."social_member_representation SET rep_name='$rep_name', rep_title='$rep_title', rep_phone='$rep_phone', rep_email='$rep_email', rep_address='$rep_address' WHERE id='$id'";
		mysql_query($sql, $db);
	}

	/**
	 * Edit contact person
	 * Special field for LCA to add a contact such as a parent or gaurdian.
	 * 
	 * @param	string		The contact full name
	 * @param	string		The contact's phone number
	 * @param	string		The contact's email address
	 * @param	string		The contact's mailing address
	 */

	function updateContact($con_name, $con_phone, $con_email, $con_address){ 
		global $addslashes, $db;
		$member_id			= $this->id;
		$con_name			= $addslashes($con_name);
		$con_phone			= $addslashes($con_phone);
		$con_email			= $addslashes($con_email);
		$con_address			= $addslashes($con_address);
		
		$sql = "UPDATE ".TABLE_PREFIX."social_member_contact SET con_name='$con_name', con_phone='$con_phone', con_email='$con_email', con_address='con_address' WHERE id='$id'";
		mysql_query($sql, $db);
	}

	/**
	 * Update personal characteristics
	 * Special field for LCA to add a contact such as a parent or gaurdian.
	 * 
	 * @param	string		person's weight
	 * @param	string		person's height
	 * @param	string		person's hair colour
	 * @param	string		person's eye colour
	 * @param	string		person's ethnicity
	 * @param	string		person's languages spoken
	 * @param	string		person's disbailities
	 */

	function updatePersonal($per_weight, $per_height, $per_hair, $per_eyes, $per_ethnicity, $per_languages, $per_disabilities){ 
		global $addslashes, $db;
		$member_id			= $this->id;
		$per_weight			= $addslashes($per_weight);
		$per_height			= $addslashes($per_height);
		$per_hair			= $addslashes($per_hair);
		$per_eyes			= $addslashes($per_eyes);
		$per_ethnicity			= $addslashes($per_ethnicity);
		$per_languages			= $addslashes($per_languages);
		$per_disabilities		= $addslashes($per_disabilities);

		$sql = "UPDATE ".TABLE_PREFIX."social_member_personal SET per_weight = '$per_weight', per_height = '$per_height', per_hair = '$per_hair', per_eyes = '$per_eyes', per_ethnicity = '$per_ethnicity', per_languages = '$per_languages', per_disabilities = '$per_disabilities' WHERE member_id = '$member_id'";
		mysql_query($sql, $db);
		header('Location:edit_profile.php');
		exit;
	}


	/**
	 * Get member info
	 * This method tends to be have a negative impact on system run time.  
	 */
	function getDetails(){
		global $db;
		$sql =	'SELECT core.*, T.interests, T.associations, T.awards, T.expertise, T.others FROM '.
				'(SELECT * FROM '.TABLE_PREFIX.'members WHERE member_id='.$this->id.') AS core '.
				'LEFT JOIN '.
				TABLE_PREFIX.'social_member_additional_information T ON core.member_id=T.member_id';
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
			$this->profile = $row;
		}
		return $this->profile;
	}


	/**
	 * Get member address
	 */
	function getAddress(){
		global $db;
		$sql = 'SELECT address, postal, city, province, country FROM '.TABLE_PREFIX.'members WHERE member_id='.$this->id;
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
		}
		return $row;
	}
	

	/**
	 * Get position info
	 * @return	the array of job/position
	 */
	function getPosition(){
		global $db;
		$position = array();

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_position WHERE member_id='.$this->id;
		$result = mysql_query($sql, $db);
		if ($result){
			while($row = mysql_fetch_assoc($result)){
				$position[] = $row;
			}
		}
		return $position;
	}


	/**
	 * Get education info
	 * can be 1+ 
	 * @return	the array of education details
	 */
	function getEducation(){
		global $db;
		$education = array();

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_education WHERE member_id='.$this->id;
		$result = mysql_query($sql, $db);
		if ($result){
			while($row = mysql_fetch_assoc($result)){
				$education[] = $row;
			}
		}
		return $education;
	}


	/** 
	 * Get websites. can be 1+
	 * @return	the array of website details.
	 */
	function getWebsites(){
		global $db;
		$websites = array();

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_websites WHERE member_id='.$this->id;
		$result = mysql_query($sql, $db);
		if ($result){
			while($row = mysql_fetch_assoc($result)){
				//escape XSS
				$row['url'] = htmlentities($row['url']);
				
				//index row entry
				$websites[] = $row;
			}
		}
		return $websites;
	}


	/**
	 * Get member's representative info 
	 * @return	the array of representative's details
	 */
	function getRepresentation(){
		global $db;
		$representation = array();

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_representation WHERE member_id='.$this->id;
		$result = mysql_query($sql, $db);
		if ($result){
			while($row = mysql_fetch_assoc($result)){
				$representation[] = $row;
			}
		}
		return $representation;
	}
	/**
	 * Get member's alternate contact info 
	 * @return	the array of contact's details
	 */
	function getContact(){
		global $db;
		$contact = array();

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_contact WHERE member_id='.$this->id;
		$result = mysql_query($sql, $db);
		if ($result){
			while($row = mysql_fetch_assoc($result)){
				$contact[] = $row;
			}
		}
		return $contact;
	}

	/**
	 * Get member's personal info 
	 * @return	the array of personal characteristics
	 */
	function getPersonal(){
		global $db;
		$personal = array();

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_member_personal WHERE member_id='.$this->id;


		$result = mysql_query($sql, $db);
		if ($result){
			$personal = mysql_fetch_assoc($result);
		}
		return $personal;

	}

	/**
	 * Get visitor counts within a month, the resultant array contains a daily, weekly, monthly, and a total count.
	 * @return	the count of all visitors on this page, within a month. 
	 */
	function getVisitors(){
		global $db;
		$count = array('month'=>0, 'week'=>0, 'day'=>0, 'total'=>0);
		//Time offsets
		$month	= time() - 60*60*24*30;	//month, within 30days.
		$week	= time() - 60*60*24*7;		//week, within 7 days.
		$day	= time() - 60*60*24;		//day, within 24 hours.

		$sql = 'SELECT visitor_id, UNIX_TIMESTAMP(timestamp) AS `current_time` FROM '.TABLE_PREFIX.'social_member_track WHERE member_id='.$this->id;
		$result = mysql_query($sql, $db);
		if ($result){
			while ($row = mysql_fetch_assoc($result)){
				if($row['current_time'] >= $month && $row['current_time'] <= $week){
					$count['month']++;
				} elseif ($row['current_time'] > $week && $row['current_time'] <= $day){
					$count['week']++;
				} elseif ($row['current_time'] > $day){
					$count['day']++;
				} else {
					continue;
				}
				$count['total']++;
			}
		}

		//clean up table randomly, 1%
		if (rand(1,100) == 1){
			$sql = 'DELETE FROM '.TABLE_PREFIX."social_member_track WHERE UNIX_TIMESTAMP(`timestamp`) < $month";
			mysql_query($sql, $db);
		}
		
		return $count;
	}

	
	/**
	 * Delete position
	 * @param	int		position id
	 */
	function deletePosition($id){
		global $db;

		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_member_position WHERE id='.$id;
		$result = mysql_query($sql, $db);
	 }

	
	/**
	 * Delete education
	 * @param	int		education id
	 */
	function deleteEducation($id){
		global $db;

		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_member_education WHERE id='.$id;
		$result = mysql_query($sql, $db);
	}

	
	/**
	 * Delete websites
	 * @param	int		websites id
	 */
	function deleteWebsite($id){
		global $db;

		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_member_websites WHERE id='.$id;
		$result = mysql_query($sql, $db);
	}
	

	/**
	 * Delete interest
	 */
	function deleteInterests(){
		global $db;

		$sql = 'UPDATE '.TABLE_PREFIX."social_member_additional_information SET interests='' WHERE member_id=".$this->getID();
		$result = mysql_query($sql, $db);
	}


	/**
	 * Delete associations
	 */
	function deleteAssociations(){
		global $db;

		$sql = 'UPDATE '.TABLE_PREFIX."social_member_additional_information SET associations='' WHERE member_id=".$this->getID();
		$result = mysql_query($sql, $db);
	}

	
	/**
	 * Delete awards
	 */
	function deleteAwards(){
		global $db;

		$sql = 'UPDATE '.TABLE_PREFIX."social_member_additional_information SET awards='' WHERE member_id=".$this->getID();
		$result = mysql_query($sql, $db);
	}

	/**
	 * Delete representation
	 */
	function deleteRepresentation($member_id){
		global $db;

		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_member_representation WHERE member_id='.$this->getID();
		$result = mysql_query($sql, $db);

	}
	/**
	 * Delete contact
	 */
	function deleteContact(){
		global $db;
		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_member_contact WHERE member_id='.$this->getID();
		$result = mysql_query($sql, $db);
	}

	/**
	 * Delete personal information
	 */
	function deletePersonal(){
		global $db;
		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_member_personal WHERE member_id='.$this->getID();
		$result = mysql_query($sql, $db);
	}


	/**
	 * Get the ID of this member
	 */
	function getID(){
		return $this->id;
	}
}
?>
