<?php
/**
 * Simple Privacy Object
 */
class PrivacyObject{
	var $profile_prefs;
	var $search_prefs;
	var $activities_prefs;

	//constructor
	function __constructor(){}


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
	 * @param	mixed	array of profile preferences
	 */
	function setProfile($prefs){
		$this->profile_prefs = $prefs;
	}

	/*
	 * @param	mixed	array of prefs 
	 */
	function setSearch($prefs){
		$this->search_prefs = $prefs;
	}

	function setActivity($prefs){
		$this->activities_prefs = $prefs;
	}

}
?>