<?php
/**
 * Privacy Object
 */
class PrivacyObject{
	var $profile_prefs;
	var $search_prefs;
	var $activities_prefs;

	//constructor
	function __constructor(){}


	//Return bitwise representation of the profile privacy settings
	function getProfile(){
		return $profile_prefs;
	}

	//Return bitwise representation of the search privacy settings
	function getSearch(){
		return $search_prefs;
	}

	//Return bitwise representation of the activity privacy settings
	function getActivity(){
		return $activity_prefs;
	}

	//Set 
	function setProfile($prefs){
		$prefs = abs($prefs);
		$this->profile_prefs = $prefs;
	}

	function setSearch($prefs){
		$prefs = abs($prefs);
		$this->search_prefs = $prefs;
	}

	function setActivity($prefs){
		$prefs = abs($prefs);
		$this->activities_prefs = $prefs;
	}

}
?>