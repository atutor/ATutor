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

require_once(dirname(__FILE__) . '/Applications.class.php');
require_once(dirname(__FILE__) .'/../SecurityToken.php');
require_once(dirname(__FILE__) .'/../BlobCrypter.php');
require_once(dirname(__FILE__) .'/../Crypto.php');
require_once(dirname(__FILE__) .'/../BasicSecurityToken.php');
require_once(dirname(__FILE__) .'/../BasicBlobCrypter.php');

/**
 * Object for Application, (aka Gadgets)
 */
class Application extends Applications{
	var $id;	//application id
	var $url, $title, $height, $screenshot, $thumbnail, $author, $author_email, $description, $settings, $views;
	var $version;

	//constructor
	function Application ($id=0){
		if ($id!=0){
			$this->id = $id;
			$this->getApplicationPrefs();	
		}
	}

	/* 
	 * Add application by URL
	 * @param	object		gadget object retrieved from JSON + cURL
	 */
	function addApplication($gadget_obj){ 
		global $db, $addslashes;

		//TODO: Many more fields to add
//		$id						= $gadget_obj['moduleId'];   //after i change the URL to the key.
		$author					= $addslashes($gadget_obj->author);
		$author_email			= $addslashes($gadget_obj->authorEmail);
		$description			= $addslashes($gadget_obj->description);
		$screenshot				= $addslashes($gadget_obj->screenshot);
		$thumbnail				= $addslashes($gadget_obj->thumbnail);
		$title					= $addslashes($gadget_obj->title);
		$height					= intval($gadget_obj->height);
		$module_id				= intval($gadget_obj->module_id);
		$url					= $addslashes($gadget_obj->url);
		$userPrefs				= $addslashes(serialize($gadget_obj->userPrefs));
		$views					= $addslashes(serialize($gadget_obj->views));

		//determine next id
		$sql = 'SELECT MAX(id) AS max_id FROM '.TABLE_PREFIX.'social_applications';
		$result = mysql_query($sql, $db);
		if ($result){
			$row = mysql_fetch_assoc($result);
			$id = $row['max_id'] + 1;
		} else {
			$id = 1;
		}
		$member_id = $_SESSION['member_id'];

		$sql = 'INSERT INTO '.TABLE_PREFIX."social_applications (id, url, title, height, screenshot, thumbnail, author, author_email, description, settings, views, last_updated) VALUES ($id, '$url', '$title', $height, '$screenshot', '$thumbnail', '$author', '$author_email', '$description', '$userPrefs', '$views', NOW())";
		$result = mysql_query($sql, $db);
		
		//This application is already in the database, get its ID out
		if (!$result){			
			$sql = 'SELECT id, UNIX_TIMESTAMP(last_updated) AS last_updated FROM '.TABLE_PREFIX."social_applications WHERE url='$url'";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			$id = $row['id'];

			//Update the gadget
			//TODO: Needs some sort of comparing instead of blinding updating everytime. Version(can't find any)? Date(need another field in db?)
			//Use TIMESTAMP for now, but i perfer version #.
			//If the gadget is SOCIAL_APPLICATION_UPDATE_SCHEDULE days old, update it
			if (abs($row['last_updated']) < strtotime('-'.SOCIAL_APPLICATION_UPDATE_SCHEDULE.' day')){
				$sql = 'UPDATE '.TABLE_PREFIX."social_applications SET title='$title', height=$height, screenshot='$screenshot', thumbnail='$thumbnail', author='$author', author_email='$author_email', description='$description', settings='$userPrefs', views='$views', last_updated=NOW() WHERE url='$url'";
				$result = mysql_query($sql, $db);
				//echo $sql;
			}
		} 

		//Add a record into application_member table for mapping
		$this->addMemberApplication($member_id, $id);
	}


	/**
	 * Add this application to the member's application list
	 * @param	int		member_id
	 * @param	int		application_id
	 */
	function addMemberApplication($member_id, $app_id){
		global $db;

		$member_id = intval($member_id);
		$app_id = intval($app_id);

		$sql = 'INSERT INTO '.TABLE_PREFIX."social_members_applications (member_id, application_id) VALUES ($member_id, $app_id)";
		$result = mysql_query($sql, $db);

		if ($result){
			$act = new Activity();		
			$act->addActivity($_SESSION['member_id'], '', $app_id);
			unset($act);
		}
	}


	/**
	 * Parse application details
	 * @return	array of the attributes
	 *
	 */
	function parseModulePrefs($app_url){
		//parse all the attributes of the <ModulePrefs> tag
		//and save everything in the object.
		 $gadget = $this->fetch_gadget_metadata($app_url);
		 return $gadget->gadgets;
	}


	// Restful - JSON CURL data transfer
	private function fetch_gadget_metadata($app_url) {
		$request = json_encode(array(
			'context' => array('country' => 'US', 'language' => 'en', 'view' => 'default', 
				'container' => 'atutor'), 
			'gadgets' => array(array('url' => $app_url, 'moduleId' => '1'))));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, AT_SHINDIG_URL.'/gadgets/metadata');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'request=' . urlencode($request));
		$content = @curl_exec($ch);
		return json_decode($content);
	}


	/**
	 * Add application perferences into the table.
	 * @param	int		member id
	 * @param	string	hash's key, usually the name of the application
	 * @param	string	hash's value, contains key, value, and st.
	 * @return	true(1) if the perference has been updated, false(0) otherwise.
	 */
	function setApplicationSettings($member_id, $key, $value){
		global $addslashes, $db;

		$app_id		= $this->id;
		$member_id	= intval($member_id);		
		$key		= $addslashes($key);
		$value		= $addslashes($value);

		$sql = 'INSERT INTO '.TABLE_PREFIX."social_application_settings (application_id, member_id, name, value) VALUES ($app_id, $member_id, '$key', '$value') ON DUPLICATE KEY UPDATE value='$value'";
		$result = mysql_query($sql, $db);

		//TODO: Might want to add something here to throw appropriate exceptions
		return $result;
	}


	/**
	 * Get member's applications
	 * @param	int		the member id
	 */
	function getMemberApplications($member_id){
		global $db;
		$result = array();

		$member_id = intval($member_id);
		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_members_applications WHERE member_id='.$member_id;
		$rs = mysql_query($sql, $db);
		if ($rs){
			while($row = mysql_fetch_assoc($rs)){
				$result[] = $row['app_id'];
			}
		}
		return $result;
	}


	/**
	 * Get user perferences for this application
	 * @return	array
	 */
	function getApplicationSettings($member_id){
		global $db;
		$result = array();
		$member_id = intval($member_id);

		$sql = 'SELECT * FROM '.TABLE_PREFIX."social_application_settings WHERE member_id=$member_id AND application_id=".$this->id;
		$rs = mysql_query($sql);
		if ($rs){
			//loop cause an application can have multiple pairs of key=>value
			while ($row = mysql_fetch_assoc($rs)){
				$result[$row['name']] = $row['value'];
			}
		}
		return $result;
	}


	function getId(){
		return $this->id;
	}

	function getUrl(){
		return $this->url;
	}

	function getTitle(){
		return $this->title;
	}

	function getModuleId(){
		return $this->module_id;
	}

	function getHeight(){
		if ($this->height==0){
			return 600;
		}
		return $this->height;
	}

	function getScreenshot(){
		return $this->screenshot;
	}

	function getThumbnail(){
		//check if this thumbnail link is a relative link
		$url = parse_url($this->thumbnail);
		if (!isset($url['scheme']) && !isset($url['host'])){
			$orig_url = parse_url($this->getUrl());
			return $orig_url['scheme'].'://'.$orig_url['host'].$this->thumbnail;
		}
		return $this->thumbnail;
	}

	function getAuthor(){
		return $this->author;
	}

	function getAuthorEmail(){
		return $this->author_email;
	}

	function getDescription(){
		return $this->description;
	}

	function getSettings(){
		return unserialize($this->settings);
	}

	function getViews(){
		return unserialize($this->views);
	}

	/** 
	 * Return iframe URL based on the given parameters
	 * @param	int			owner id
	 * @param	string		avaiable options are 'profile', 'canvas'
	 *						http://code.google.com/apis/orkut/docs/orkutdevguide/orkutdevguide-0.8.html#ops_mode
	 * @param	string		extra application parameters
	 * @return	iframe url
	 */
	function getIframeUrl($oid, $view='profile', $appParams=''){
		//let view=profile as default option
		if ($view!='profile' && $view!='canvas'){
			$view = 'profile';
		}

		$app_settings = $this->getSettings();
		$user_settings = $this->getApplicationSettings($_SESSION['member_id']);

		//retrieve user preferences
		foreach ($app_settings as $key => $setting) {
			if (! empty($key)) {
			  $value = isset($user_settings[$key]) ? $user_settings[$key] : (isset($setting->default) ? $setting->default : null);
			  if (isset($user_settings[$key])) {
				unset($user_settings[$key]);
			  }
			  //shindig doesn't like ';', it only takes '&' as of Apr 6th, 2009
			  //$prefs .= SEP.'up_' . urlencode($key) . '=' . urlencode($value);
			  $prefs .= '&up_' . urlencode($key) . '=' . urlencode($value);
			}
		}
		foreach ($user_settings as $name => $value) {
			// if some keys _are_ set in the db, but not in the gadget metadata, we still parse them on the url
			// (the above loop unsets the entries that matched  
			if (! empty($value) && ! isset($appParams[$name])) {
			//shindig doesn't like ';', it only takes '&' as of Apr 6th, 2009
			//$prefs .= SEP.'up_' . urlencode($name) . '=' . urlencode($value);
			$prefs .= '&up_' . urlencode($name) . '=' . urlencode($value);
			}
		}

		//generate security token
		$securityToken = BasicSecurityToken::createFromValues((isset($_REQUEST['id'])?$_REQUEST['id']:$_SESSION['member_id']), // owner
						$_SESSION['member_id'], // viewer
						$this->getId(), // app id
						'default', // domain key, shindig will check for php/config/<domain>.php for container specific configuration
						urlencode($this->getUrl()), // app url
						$this->getModuleId());// mod id
//debug($securityToken);
		$url = AT_SHINDIG_URL.'/gadgets/ifr?' 
			. "synd=default" 
			. "&container=default" 
			. "&viewer=". $_SESSION['member_id']
			. "&owner=" . $oid
			. "&aid=" . $this->getId()			//application id
			. "&mid=" . $this->getModuleId()	//not sure what mod_id is
			. "&country=US" 
			. "&lang=en" 
			. "&view=" . $view	//canvas for this big thing, should be a variable
			. "&parent=" . urlencode("http://" . $_SERVER['HTTP_HOST']) . $prefs . (isset($appParams) ? '&view-params=' . urlencode($appParams) : '') 
			. "&st=" . urlencode(base64_encode($securityToken->toSerialForm())) 
			. "&v=" . $this->getVersion()
			. "&url=" . urlencode($this->getUrl()) . "#rpctoken=" . rand(0, getrandmax());
		return $url;
	}

	//TO BE IMPLEMENTED
	function getVersion(){
		return '0.1';
	}


	/**
	 * Retrieve all information about this gadget and save it in the object
	 * @private
	 */
	function getApplicationPrefs(){
		global $db;

		$sql = 'SELECT * FROM '.TABLE_PREFIX.'social_applications WHERE id='.$this->id;
		$rs = mysql_query($sql);

		if ($rs){
			$row = mysql_fetch_assoc($rs);
			//assign values
			$this->url			= $row['url'];
			$this->title		= $row['title'];
			$this->height		= $row['height'];
			$this->screenshot	= $row['screenshot'];
			$this->thumbnail	= $row['thumbnail'];
			$this->author		= $row['author'];
			$this->author_email	= $row['author_email'];
			$this->description	= $row['description'];
			$this->settings		= $row['settings'];
			$this->views		= $row['views'];
			$this->last_updated	= $row['last_updated'];
		}
	}

	/** 
	 * Delete an application
	 */
	function deleteApplication(){
		global $db;

		//delete application mapping
		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_members_applications WHERE application_id='.$this->id.' AND member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql);

		//delete application data?
		$sql = 'DELETE FROM '.TABLE_PREFIX.'social_application_settings WHERE application_id='.$this->id.' AND member_id='.$_SESSION['member_id'];
		$rs = mysql_query($sql);

		return $rs;
	}
}
?>