<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

/**
 * Simple Privacy Object
 */
class PrivacyObject{
	var $profile_prefs;
	var $search_prefs;
	var $activities_prefs;

	//constructor
	function PrivacyObject(){
		//For now, default PrivacyObject allows all First degree connection to see everything.
		$demo = array(	AT_SOCIAL_EVERYONE_VISIBILITY=>0, 
						AT_SOCIAL_FRIENDS_VISIBILITY=>1, 
						AT_SOCIAL_FRIENDS_OF_FRIENDS_VISIBILITY=>1,
						AT_SOCIAL_NETWORK_VISIBILITY=>1,
						AT_SOCIAL_GROUPS_VISIBILITY=>1
						);

		$profile_prefs = array(
								AT_SOCIAL_PROFILE_BASIC			=> $demo,
								AT_SOCIAL_PROFILE_PROFILE		=> $demo,
								AT_SOCIAL_PROFILE_STATUS_UPDATE	=> $demo,
								AT_SOCIAL_PROFILE_MEDIA			=> $demo,
								AT_SOCIAL_PROFILE_CONNECTION	=> $demo,
								AT_SOCIAL_PROFILE_EDUCATION		=> $demo,
								AT_SOCIAL_PROFILE_POSITION		=> $demo
							  );

		$search_prefs = array(	
								AT_SOCIAL_SEARCH_VISIBILITY	=> array(AT_SOCIAL_EVERYONE_VISIBILITY=>0, AT_SOCIAL_FRIENDS_VISIBILITY=>1, 
																	 AT_SOCIAL_FRIENDS_OF_FRIENDS_VISIBILITY=>1, AT_SOCIAL_NETWORK_VISIBILITY=>1,
																	 AT_SOCIAL_GROUPS_VISIBILITY=>1),
								AT_SOCIAL_SEARCH_PROFILE	=> array(AT_SOCIAL_EVERYONE_VISIBILITY=>1, AT_SOCIAL_FRIENDS_VISIBILITY=>1),
								AT_SOCIAL_SEARCH_CONNECTION	=> array(AT_SOCIAL_EVERYONE_VISIBILITY=>1, AT_SOCIAL_FRIENDS_VISIBILITY=>1),
								AT_SOCIAL_SEARCH_EDUCATION	=> array(AT_SOCIAL_EVERYONE_VISIBILITY=>1, AT_SOCIAL_FRIENDS_VISIBILITY=>1),
								AT_SOCIAL_SEARCH_POSITION	=> array(AT_SOCIAL_EVERYONE_VISIBILITY=>1, AT_SOCIAL_FRIENDS_VISIBILITY=>1)
							   );

		//set them
		$this->setProfile($profile_prefs);
		$this->setSearch($search_prefs);
	}


	//Return bitwise representation of the profile privacy settings
	function getProfile(){
		return $this->profile_prefs;
	}

	//Return bitwise representation of the search privacy settings
	function getSearch(){
		return $this->search_prefs;
	}

	//Return bitwise representation of the activity privacy settings
	function getActivity(){
		return $this->activity_prefs;
	}

	/* Set 
	 * @param	mixed	array of profile preferences array
	 */
	function setProfile($prefs){
		$this->profile_prefs = $prefs;
	}

	/*
	 * @param	mixed	array of prefs arrays
	 */
	function setSearch($prefs){
		$this->search_prefs = $prefs;
	}

	function setActivity($prefs){
		$this->activities_prefs = $prefs;
	}
}
?>