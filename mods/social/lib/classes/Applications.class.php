<?php
require_once(AT_SOCIAL_INCLUDE.'classes/Activity.class.php');
require_once(AT_SOCIAL_INCLUDE.'classes/Application.class.php');
require_once(AT_SOCIAL_INCLUDE.'constants.inc.php');

/**
 * Object for Applications, (aka Gadgets)
 */

class Applications {
	//constructor
	function Applications(){}

	/** 
	 * Retrieve a list of applications' titles
	 * @return hash of applications, id=>app obj
	 */
	function listMyApplications(){
		global $db;
		$hash = array();

		$sql = 'SELECT id, title FROM '.TABLE_PREFIX.'applications a, (SELECT application_id FROM '.TABLE_PREFIX.'members_application WHERE member_id='.$_SESSION['member_id'].') AS apps WHERE a.id=apps.application_id';
		$result = mysql_query($sql, $db);

		if ($result){
			while($row = mysql_fetch_assoc($result)){
				$hash[$row['id']] = new Application($row['id']);
			}
		}
		return $hash;
	}

	/**
	 * Retrieve a list of all installed applications
	 */
	function listApplications(){
		global $db;
		$hash = array();

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'applications';
		$result = mysql_query($sql, $db);

		while ($row = mysql_fetch_assoc($result)){
			$hash[$row['id']] = new Application($row['id']);
		}
		return $hash;
	}

	/**
	 * Return the application link for this given id.
	 */
	function getAppLink($title, $id){
		return '<a href="' . AT_SOCIAL_INCLUDE.'../applications.php?app_id='.$id . '"><b>' . $title . '</b></a>';
	}

}
?>