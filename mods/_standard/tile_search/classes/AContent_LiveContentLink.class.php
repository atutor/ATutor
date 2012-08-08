<?php

	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	ini_set("display_errors", 1);

	define('AT_INCLUDE_PATH', '../../../../include/');
	define('TR_INCLUDE_PATH', '../../../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	require_once("../ims-blti/blti_util.php");
	require_once('ContentDAO.class.php');

	$ret = new AContent_LiveContentLink();

	if($ret->status){
		$msg->addError('IMPORT_FAILED');
	}else
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');

	header('Location: ../index.php');
	exit();

?>

<?php

	/*	
	 * AContent Live Content Link
	 * 
	 * This class establishes an oauth authentication
	 * and submits the LTI form to request the data to the Tool Provider (TP).
	 * Then, it stores the TP data into the ATutor database.
	 * 
	*/


	class AContent_LiveContentLink{

		public static $_singleton	= null;
		public $status = 0;
		
		// LTI version (now, 1p1 or 2p0)
		private $_LTI_version	= '1p1';
		
		private $_AContent_URL	= '';
		private $_consumer_url	= '';

		// LTI settings
		// this settings are all required
		//
		//	VAR				EXAMPLE				DEFINITION
		//
		//	launch_URL		oauth/tool.php		where the page tool.php is located on the Tool Consumer server (AContent/oauth/tool.php)
		//	key				12345				(automatically generated) consumer key required for the OAuth authentication
		//	secret			secret				(automatically generated) consumer secret required for the OAuth authentication
		//
		private $_LTI_Resource	= array('launch_URL'	=> 'oauth/lti/tool.php',
										'key'			=> '12345',
										'secret'		=> 'secret');

		// LTI parameters
		// this settings are all recommended or required (* required fields)
		//
		//	VAR								EXAMPLE						DEFINITION
		//
		//	*resource_link_id				120988f929-274612			unique resource link id to differentiate the content/features
		//	resource_link_title				Weekly Blog				A plain text1 title for the resource (appears in the link)
		//	resource_link_description		A weekly blog.				A plain text description of the link’s destination
		//	user_id							292832126					Uniquely identifies the user (should not contain any identifying info for the user)
		//	roles							Instructor					A comma-separated list of URN (Uniform Resource Name) values for roles
		//	lis_person_name_full			Jane Q. Public				info about the user account that is performing this launch
		//	lis_person_name_family			Public						info about the user account that is performing this launch
		//	lis_person_name_given			Given						info about the user account that is performing this launch
		//	lis_person_contact_email_primary	user@school.edu			info about the user account that is performing this launch
		//	lis_person_sourcedid			school.edu:user				LIS id for the user account that is performing this launch
		//	context_id						456434513					opaque id that uniquely identifies the context that contains the link being launched
		//	context_title					Design of Personal Env.		A plain text title of the context
		//	context_label					SI182						A plain text label for the context
		//	tool_consumer_instance_guid		lmsng.school.edu			This is a unique identifier for the TC.
		//	tool_consumer_instance_desc		Univ of School (LMSng)		This is a plain text user visible field
		//	tool_consumer_info_product_family_code	desire2learn		Info about product family code
		//	tool_consumer_info_version		2.0.3						Consumer info version
		//
		private $_Launch_Data	= array('resource_link_id'					=> '120988f929-274612',
										'resource_link_title'				=> 'Weekly Blog',
										'resource_link_description'			=> 'A weekly blog.',
										//
										'user_id'							=> '292832126',
										'roles'								=> 'Instructor',
										'lis_person_name_full'				=> 'Jane Q. Public',
										'lis_person_name_family'			=> 'Public',
										'lis_person_name_given'				=> 'Given',
										'lis_person_contact_email_primary'	=> 'user@school.edu',
										'lis_person_sourcedid'				=> 'school.edu:user',
										// context = course
										'context_id'						=> '456434513',
										'context_title'						=> 'Design of Personal Environments',
										'context_label'						=> 'SI182',
										//
										'tool_consumer_instance_guid'		=> 'lmsng.school.edu',
										'tool_consumer_instance_desc'		=> 'University of School (LMSng)');

		/**
		 * Singleton Design Pattern
		 * Return the instance of the class DAO.class.php
		 * @access  private
		 * @return  DAO class instance
		 * @author  Mauro Donadio
		 */
		static function getInstance(){

			if (AContent_LiveContentLink::$_singleton == null){ 
				AContent_LiveContentLink::$_singleton = new ContentDAO();
			}
		
			return AContent_LiveContentLink::$_singleton;
		}

		/**
		 * Return content information by given content id
		 * @access  private
		 * @param   parent content id
		 * @return  content row
		 * @author  Mauro Donadio
		 */
		public function __construct(){

			// switch between different versions of LTI
			// now we have 1p1 and 2p0 but we want to manage future versions
			switch($this->_LTI_version){

				// LTI 2.0
				case '2.0':
					/*
					$this->_LTI_Resource['key']		= 'myNewKey';
					$this->_LTI_Resource['secret']	= 'myNewSecret';
					*/

					$rand	= md5(microtime());

					$str	= substr($rand,   0,  8).'-';
					$str	.= substr($rand,  8,  4).'-';
					$str	.= substr($rand, 12,  4).'-';
					$str	.= substr($rand, 16,  4).'-';
					$str	.= substr($rand, 20, 12);
					
					$this->_LTI_Resource['key'] = $str;

					$this->_LTI_Resource['secret']	= substr(md5(uniqid(rand(), TRUE)), 0, 25);
				break;

				// LTI 1.0 and 1.1
				// key and secret provided by the TP
				default:
					break;
			}

			// OAuth 1.0

			if($this->_OAuth())
			{
				
				##
				## GET AND SET PARAMS
				##

				$this->_Launch_Data['resource_link_id']				= substr(uniqid('', true), 0, 16);
				// title
				$this->_Launch_Data['resource_link_title']			= htmlentities($_POST['title']);
				// description
				$this->_Launch_Data['resource_link_description']	= htmlentities($_POST['desc']);
				
				$userDetails	= $this->_getUserDetails();


				// user_id
				$this->_Launch_Data['user_id']					= $userDetails['user_id'];
				// roles
				// AT_course_enrollment
				// role
				$this->_Launch_Data['roles']					= $userDetails['user_role'];
				// fullname
				$this->_Launch_Data['lis_person_name_full']		= $userDetails['user_fullname'];

				$this->_Launch_Data['tool_consumer_instance_guid']	= $this->_consumer_url;
				$this->_Launch_Data['tool_consumer_instance_desc']	= $GLOBALS['_config']['site_name'];

	
				$endpoint		= $this->_LTI_Resource['launch_URL'];

				$key			= $this->_LTI_Resource['key'];
				$secret			= $this->_LTI_Resource['secret'];
				
				$tool_consumer_instance_guid		= $this->_Launch_Data['tool_consumer_instance_guid'];
				$tool_consumer_instance_description	= $this->_Launch_Data['tool_consumer_instance_desc'];

				// send to the TP the course id
				$this->_Launch_Data['tile_course_id']	= htmlentities($_POST['tile_course_id']);
				// set if entire course or lesson
				$this->_Launch_Data['course']			= 1;

				$parms			= signParameters($this->_Launch_Data, $endpoint, "POST", $key, $secret, "Press to Launch", $tool_consumer_instance_guid, $tool_consumer_instance_description);

				//
				// CURL (auto submit form)
				//
				$url = $endpoint;

				$result			= $this->_curlFormAutoSubmit($url, $parms);
				//-- CURL

				$xmlStructure	= strstr($result, 'aContent_LiveContentLink() = ');
				$xmlStructure	= ltrim($xmlStructure, 'aContent_LiveContentLink() = ');
				$xmlStructure	= trim($xmlStructure);

				$xmlStructure	= html_entity_decode($xmlStructure);

				$this->_import($xmlStructure);

			}
			else
			{
				die('Error: Missing OAuth auth!');
			}

			return;
		}

		/**
		 * Return content information by given content id
		 * @access  private
		 * @param   parent content id
		 * @return  content row
		 * @author  Mauro Donadio
		 */
		private function _OAuth(){

			##
		    ## DEFINE MAIN VARIABLES
		    ##

			$this->_AContent_URL		= $GLOBALS['_config']['transformable_uri'];

			if($_SERVER['SERVER_NAME'] == 'localhost'){
				$path = explode(DIRECTORY_SEPARATOR, dirname($_SERVER['PHP_SELF']));
				$this->_consumer_url       = 'http://' . $_SERVER['SERVER_NAME'] . DIRECTORY_SEPARATOR . $path[1] . DIRECTORY_SEPARATOR;
			}else
				$this->_consumer_url       = 'http://' . $_SERVER['SERVER_NAME'] . DIRECTORY_SEPARATOR;

			define("ACONTENT_OAUTH_HOST",			$this->_AContent_URL);
			define("ACONTENT_REQUEST_TOKEN_URL",	ACONTENT_OAUTH_HOST . "oauth/request_token.php");
			define("ACONTENT_AUTHORIZE_URL",		ACONTENT_OAUTH_HOST . "oauth/authorization.php");
			define("ACONTENT_ACCESS_TOKEN_URL",		ACONTENT_OAUTH_HOST . "oauth/access_token.php");


		    ##
			## GET consumer_key and consumer_secret
		    ##

			// Register consumer
			$config						= $this->_register_consumer();

			$config['request_token']	= ACONTENT_REQUEST_TOKEN_URL;


		    ##
			## OAuth 1a authentication
		    ##
		    
		    include_once 'Twitauth.class.php';

			$tw		= new Twitauth($config);
			$res	= $tw->getRequestToken();

			// return
			if($res == null)
				return false;
			else{

				// Sets the class private vars

				//$this->_LTI_Resource['launch_URL']	= $this->_AContent_URL . 'oauth/tool.php';
				$this->_LTI_Resource['launch_URL']	= $this->_AContent_URL . $this->_LTI_Resource['launch_URL'];
				$this->_LTI_Resource['key']			= $config['key'];
				$this->_LTI_Resource['secret']		= $config['secret'];

				return true;
			}
		}

		/**
		 * Return content information by given content id
		 * @access  private
		 * @param   parent content id
		 * @return  content row
		 * @author  Mauro Donadio
		 */
		// gets from TP the key and secret requested by TC for the auth
		private function _register_consumer(){

			$reg_consumer	= '';
			$reg_vars		= '';

			$reg_consumer	= @file_get_contents(ACONTENT_OAUTH_HOST . '/oauth/register_consumer.php?consumer=' . $this->_consumer_url . '&expire=' . $GLOBALS['_config']['transformable_oauth_expire'] . 'lti=' . $this->_LTI_version);
		    $reg_vars		= explode('&',$reg_consumer);


			// for each returned value
			// we get those parms we are interested in (key, secret)
			for($i = 0; $i < count($reg_vars); $i++){

				$tmp	= '';
				$tmp	= explode('=',$reg_vars[$i]);
		
				if($tmp[0] == 'consumer_key')
				{
					$config['key']		= '';
					$config['key']		= $tmp[1];
				}
				elseif($tmp[0] == 'consumer_secret')
				{
					$config['secret']	= '';
					$config['secret']	= $tmp[1];
				}
			}

			return $config;
		}

		/**
		 * Return content information by given content id
		 * @access  private
		 * @param   parent content id
		 * @return  content row
		 * @author  Mauro Donadio
		 */
		private function _getUserDetails(){

			$userDetails	= array();
			
			$userDetails['user_id']			= $GLOBALS['_SESSION']['member_id'];

			if(get_instructor_status())
				$userDetails['user_role']	= 'Instructor';
			else
				$userDetails['user_role']	= 'Student';

			if($GLOBALS['_SESSION']['is_admin'])
				$userDetails['user_role']	= $userDetails['user_role'] . ' / Admin';

			$userDetails['user_fullname']	= get_display_name($GLOBALS['_SESSION']['member_id']);
			$userDetails['user_email']		= '';

			return $userDetails;
		}

		/**
		 * Return the xml data of the selected content to import
		 * @access  private
		 * @param   url of the TP, parameters requested by the TP
		 * @return  xml data of the content to import
		 * @author  Mauro Donadio
		 */
		private function _curlFormAutoSubmit($url, $parms){
			$fields_string	= '';

			//url-ify the data for the POST
			foreach($parms as $key=>$value) {
				$fields_string	.= $key.'=' . $value . '&';
			}
			
			$fields_string	= rtrim($fields_string, '&');

			// CURL

			//open connection
			$ch = curl_init();
		
			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL,			$url);
			curl_setopt($ch, CURLOPT_POST,			TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS,	$fields_string);
			curl_setopt($ch, CURLOPT_HEADER,		0);
		
			//execute post
			ob_start();
				//$result = curl_exec($ch);
				curl_exec($ch); 
		
				//close connection
				curl_close($ch);

			$result	= ob_get_contents();
			ob_end_clean();
			
			return $result;
		}

		/**
		 * Writing of the new data in the ATutor database
		 * @access  private
		 * @param   xml data of the content to import
		 * @return  void
		 * @author  Mauro Donadio
		 */
		private function _import($dataStructure){

			$xml	= simplexml_load_string($dataStructure);

			// import error
			if(!$xml){
				$this->status = 1;
				return;
				die();
			}

			$this->_recursiveFolderScan($xml, (int)htmlentities($_POST['cid']));

			return;
		}


		/**
		 * Writing of the new data in the ATutor database
		 * @access  private
		 * @param   root pointer, parent id
		 * @return  void
		 * @author  Mauro Donadio
		 */
		private function _recursiveFolderScan($current_node, $import_into_id){

			if(!is_int($import_into_id)){
				$import_into_id = 0;
			}

			// course id
			$course_id			= htmlentities($_SESSION['course_id']);

			// for each item to import (child)
			for($i = 0; $i < count($current_node->content_id); $i++){

				$current		= $current_node->content_id[$i];
				$new_parent_id	= $this->_storeData($current, $course_id, $import_into_id);

				$this->_recursiveFolderScan($current, $new_parent_id);
			}

			return;
		}
		

		/**
		 * Writing of the new data in the ATutor database
		 * @access  private
		 * @param   current item pointer, course id, parent id
		 * @return  last inserted row id
		 * @author  Mauro Donadio
		 */
		private function _storeData($current_item, $course_id, $content_parent_id){

			$ContentDAO = self::getInstance();

			if($current_item->content_type == 0){
				$current_item->content_type = 2;
				$current_item->formatting	= 2;
			}

			$ContentDAO->Create($course_id,
								$content_parent_id,
								$current_item->ordering,
								$current_item->revision,
								$current_item->formatting,
								$current_item->keywords,
								$current_item->content_path,
								$current_item->title,
								$current_item->text,
								$current_item->head,
								$current_item->use_customized_head,
								$current_item->test_message,
								$current_item->content_type);

			return mysql_insert_id();

		}

	}
?>