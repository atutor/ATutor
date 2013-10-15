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
// $Id$

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
		$member_id	= $this->id;
		$sql = "INSERT INTO %ssocial_member_position (member_id, company, title, `from`, `to`, description) VALUES (%d, '%s', '%s', '%s', '%s', '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $member_id, $company, $title, $from, $to, $description));
		editSocialFeedback($result);
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
	function addEducation($university, $from, $to, $country, $province, $degree, $field, $description){ 
		$member_id	= $this->id;
		$sql = "INSERT INTO %ssocial_member_education (member_id, university, `from`, `to`, country, province, degree, field, description) 
		VALUES (%s, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $member_id, $university, $from, $to, $country, $province, $degree, $field, $description));
		editSocialFeedback($result);
	}


	/**
	 * Add a new website associated with this member, can be blog, work, portfolio, etc.
	 * @param	string		Unique URL of the website
	 * @param	string		A name for the website.
	 */
	function addWebsite($url, $site_name){ 
		$member_id	= $this->id;
		$sql = "INSERT INTO %ssocial_member_websites (member_id, url, site_name) VALUES (%d, '%s', '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $member_id, $url, $site_name));
		editSocialFeedback($result);
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
		$member_id	= $this->id;
		$sql = "INSERT INTO %ssocial_member_representation (member_id, rep_name, rep_title, rep_phone, rep_email, rep_address) VALUES (%d, '%s', '%s', '%s', '%s', '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $member_id, $rep_name, $rep_title, $rep_phone, $rep_email, $rep_address));
		editSocialFeedback($result);
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
		$member_id = $this->id;
		$sql = "INSERT INTO %ssocial_member_contact (member_id, con_name, con_phone, con_email, con_address) VALUES (%d, '%s', '%s', '%s', '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $member_id, $con_name, $con_phone, $con_email, $con_address));
		editSocialFeedback($result);
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
		$member_id	= $this->id;
		$sql = "INSERT INTO %ssocial_member_personal (member_id, per_weight, per_height, per_hair, per_eyes, per_ethnicity, per_languages, per_disabilities) VALUES (%d, '%s', '%s', '%s', '%s','%s','%s','%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $member_id, $per_weight, $per_height, $per_hair, $per_eyes, $per_ethnicity, $per_languages, $per_disabilities));
		editSocialFeedback($result);
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
		$member_id = $this->id;
		$sql = "INSERT INTO %ssocial_member_additional_information (member_id, interests,  associations, awards, expertise, others) VALUES (%d, '%s', '%s', '%s', '%s', '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $member_id, $interests, $associations, $awards, $expertise, $others));
		editSocialFeedback($result);
	}


	/** 
	 * Add visitor
	 * @param	int		visitor id
	 */
	function addVisitor($visitor_id){
		$sql = "INSERT INTO %ssocial_member_track (`member_id`, `visitor_id`, `timestamp`) VALUES (%d, %d, NOW())";
		queryDB($sql, array(TABLE_PREFIX, $this->getID(), $visitor_id));
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
		$sql = "UPDATE %ssocial_member_position SET company='%s', title='%s', `from`='%s', `to`='%s', description='%s' WHERE id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $company, $title, $from, $to, $description, $id));
		editSocialFeedback($result);
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
		$sql = "UPDATE %ssocial_member_education 
		                SET 
		                university='%s', 
		                `from`='%s', 
		                `to`='%s', 
		                country='%s', 
		                province='%s', 
		                degree='%s', 
		                field='%s', 
		                description='%s' 
		                WHERE id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $university, $from, $to, $country, $province, $degree, $field, $description, $id));	
		editSocialFeedback($result);	
	}


	/**
	 * Updates a new website associated with this member, can be blog, work, portfolio, etc.
	 * @param	int			ID of this entry
	 * @param	string		Unique URL of the website
	 * @param	string		A name for the website.
	 */
	function updateWebsite($id, $url, $site_name){ 
		$sql = "UPDATE %ssocial_member_websites SET url='%s', site_name='%s' WHERE id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $url, $site_name, $id));
		editSocialFeedback($result);
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

		$sql2 = "INSERT INTO %ssocial_member_additional_information SET ".$sql.", member_id=%d ON DUPLICATE KEY UPDATE ".$sql;
		$result = queryDB($sql2, array(TABLE_PREFIX, $_SESSION['member_id']));
		editSocialFeedback($result);	
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
		$member_id	= $this->id;
		$sql = "UPDATE %ssocial_member_representation SET rep_name='%s', rep_title='%s', rep_phone='%s', rep_email='%s', rep_address='%s' WHERE member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $rep_name, $rep_title, $rep_phone, $rep_email, $rep_address, $member_id));
		editSocialFeedback($result);
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
		$member_id			= $this->id;
		$sql = "UPDATE %ssocial_member_contact SET con_name='%s', con_phone='%s', con_email='%s', con_address='%s' WHERE member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $con_name, $con_phone, $con_email, $con_address, $member_id));
		editSocialFeedback($result);
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
		$member_id	= $this->id;
		$sql = "UPDATE %ssocial_member_personal SET per_weight = '%s', per_height = '%s', per_hair = '%s', per_eyes = '%s', per_ethnicity = '%s', per_languages = '%s', per_disabilities = '%s' WHERE member_id = %d";
		$result = queryDB($sql, array(TABLE_PREFIX, $per_weight, $per_height, $per_hair, $per_eyes, $per_ethnicity, $per_languages, $per_disabilities, $member_id));
	    editSocialFeedback($result);
		header('Location:edit_profile.php');
		exit;
	}


	/**
	 * Get member info
	 * This method tends to be have a negative impact on system run time.  
	 */
	function getDetails(){
		$sql =	'SELECT core.*, T.interests, T.associations, T.awards, T.expertise, T.others FROM '.
				'(SELECT * FROM %smembers WHERE member_id=%d) AS core '.
				'LEFT JOIN %ssocial_member_additional_information T ON core.member_id=T.member_id';
		$row = queryDB($sql, array(TABLE_PREFIX, $this->id, TABLE_PREFIX ), TRUE);
		$this->profile = $row;

		return $this->profile;
	}

	/**
	 * Get member address
	 */
	function getAddress(){
		$sql = 'SELECT address, postal, city, province, country FROM %smembers WHERE member_id=%d';
		$row = queryDB($sql, array(TABLE_PREFIX, $this->id), TRUE);

		return $row;
	}
	
	/**
	 * Get position info
	 * @return	the array of job/position
	 */
	function getPosition(){
		$position = array();
		$sql = 'SELECT * FROM %ssocial_member_position WHERE member_id=%d';
		$rows_positions = queryDB($sql, array(TABLE_PREFIX, $this->id));
		if(count($rows_positions ) > 0){
		    foreach($rows_positions as $row){
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
		$education = array();
		$sql = 'SELECT * FROM %ssocial_member_education WHERE member_id=%d';
		$rows_education = queryDB($sql, array(TABLE_PREFIX, $this->id));
		if(count($rows_education) > 0){
		    foreach($rows_education as $row){
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
		$websites = array();
		$sql = 'SELECT * FROM %ssocial_member_websites WHERE member_id=%d';
		$rows_websites = queryDB($sql, array(TABLE_PREFIX, $this->id));
		if(count($rows_websites) > 0){
		    foreach($rows_websites as $row){
				//escape XSS
				$row['url'] = htmlentities_utf8($row['url']);			
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
		$representation = array();
		$sql = 'SELECT * FROM %ssocial_member_representation WHERE member_id=%d';
		$rows_reps = queryDB($sql, array(TABLE_PREFIX, $this->id));
		if (count($rows_reps) > 0){
			foreach($rows_reps as $row){
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
		$contact = array();
		$sql = 'SELECT * FROM %ssocial_member_contact WHERE member_id=%d';
		$rows_contacts = queryDB($sql, array(TABLE_PREFIX, $this->id));
		if (count($rows_contacts) > 0){
		    foreach($rows_contacts as $row){
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
		$personal = array();
		$sql = 'SELECT * FROM %ssocial_member_personal WHERE member_id=%d';
		$personal = queryDB($sql, array(TABLE_PREFIX, $this->id), TRUE);
		return $personal;
	}

	/**
	 * Get visitor counts within a month, the resultant array contains a daily, weekly, monthly, and a total count.
	 * @return	the count of all visitors on this page, within a month. 
	 */
	function getVisitors(){
		$count = array('month'=>0, 'week'=>0, 'day'=>0, 'total'=>0);
		//Time offsets
		$month	= time() - 60*60*24*30;	//month, within 30days.
		$week	= time() - 60*60*24*7;		//week, within 7 days.
		$day	= time() - 60*60*24;		//day, within 24 hours.

		$sql = 'SELECT visitor_id, UNIX_TIMESTAMP(timestamp) AS `current_time` FROM %ssocial_member_track WHERE member_id=%d';
		$rows_visitors = queryDB($sql, array(TABLE_PREFIX, $this->id));
		
		if (count($rows_visitors) > 0){
			foreach($rows_visitors as $row){
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
			$sql = "DELETE FROM %ssocial_member_track WHERE UNIX_TIMESTAMP(`timestamp`) < %d";
			$result = queryDB($sql, array(TABLE_PREFIX, $month));
		}
		return $result;
	}

	/**
	 * Delete position
	 * @param	int		position id
	 */
	function deletePosition($id){
		$sql = 'DELETE FROM %ssocial_member_position WHERE id=%d';
		$result = queryDB($sql, array(TABLE_PREFIX, $id));
		 editSocialFeedback($result);
	 }

	/**
	 * Delete education
	 * @param	int		education id
	 */
	function deleteEducation($id){
		$sql = 'DELETE FROM %ssocial_member_education WHERE id=%d';
		$result = queryDB($sql, array(TABLE_PREFIX, $id));
		 editSocialFeedback($result);
	}

	/**
	 * Delete websites
	 * @param	int		websites id
	 */
	function deleteWebsite($id){
		$sql = 'DELETE FROM %ssocial_member_websites WHERE id=%d';
		$result = queryDB($sql, array(TABLE_PREFIX, $id));
		 editSocialFeedback($result);
	}
	
	/**
	 * Delete interest
	 */
	function deleteInterests(){
		$sql = "UPDATE %ssocial_member_additional_information SET interests='' WHERE member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $this->getID()));
		 editSocialFeedback($result);
	}

	/**
	 * Delete associations
	 */
	function deleteAssociations(){
		$sql = "UPDATE %ssocial_member_additional_information SET associations='' WHERE member_id=%d";
		$result = queryDB($sql, array(TABLE_PREFIX, $this->getID()));
		 editSocialFeedback($result);
	}

	/**
	 * Delete awards
	 */
	function deleteAwards(){
		$sql = "UPDATE %ssocial_member_additional_information SET awards='' WHERE member_id=%d";		
		$result = queryDB($sql, array(TABLE_PREFIX, $this->getID()));
		 editSocialFeedback($result);
	}

	/**
	 * Delete representation
	 */
	function deleteRepresentation($member_id){
		$sql = 'DELETE FROM %ssocial_member_representation WHERE member_id=%d';
		$result = queryDB($sql, array(TABLE_PREFIX, $this->getID()));
		 editSocialFeedback($result);

	}
	
	/**
	 * Delete contact
	 */
	function deleteContact(){
		$sql = 'DELETE FROM %ssocial_member_contact WHERE member_id=%d';
		$result = queryDB($sql, array(TABLE_PREFIX, $this->getID()));
		 editSocialFeedback($result);
	}

	/**
	 * Delete personal information
	 */
	function deletePersonal(){
		$sql = 'DELETE FROM %ssocial_member_personal WHERE member_id=%d';		
		$result = queryDB($sql, array(TABLE_PREFIX, $this->getID()));
		 editSocialFeedback($result);
	}

	/**
	 * Get the ID of this member
	 */
	function getID(){
		return $this->id;
	}
}
?>
