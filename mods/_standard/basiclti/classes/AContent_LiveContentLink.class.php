<?php
/************************************************************************/
/* AContent                                                             */
/************************************************************************/
/* Copyright (c) 2012                                                   */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

	require_once("../launch/ims-blti/blti_util.php");

	class AContent_LiveContentLink{

		// content id
		private $_content_id	= null;
		// content type: course 1, content 0
		private $_course		= 1;
		
		// array of contentes
		private $_tree			= array();

		// LTI version (now, 1p1 or 2p0)
		private $_LTI_version	= '1p1';

		// OAuth 1a variables
		private $_consumer_url					= '';
		private $_AContent_URL					= '';
		private $_ACONTENT_OAUTH_HOST			= '';
		private $_ACONTENT_REQUEST_TOKEN_URL	= '';
		private $_ACONTENT_AUTHORIZE_URL		= '';
		private $_ACONTENT_ACCESS_TOKEN_URL		= '';
		private $_ACONTENT_REGISTER_CONSUMER	= '';

		// LTI settings
		private $_LTI_Resource	= array('launch_URL'	=> 'oauth/tool.php',
										'key'			=> '12345',
										'secret'		=> 'secret');

		// parameters
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
										'tool_consumer_instance_desc'		=> 'University of School (LMSng)',
										'tool_consumer_info_product_family_code'=> 'desire2learn',
										'tool_consumer_info_version'		=> '2.0.3');

		/**
		 * Sets the main variables
		 * @access  public
		 * @param   content id, content type: course 1, content 0
		 * @return  void
		 * @author  Mauro Donadio
		 */
		public function __construct($content_id = null, $course = null){

			if($content_id === null OR $course === null){
				return FALSE;
			}else{

				//
				// setting variables
				//
				
				// general
				$this->_content_id	= $content_id;
				$this->_course		= $course;

				// OAuth 1a

				if($_SERVER['SERVER_NAME'] == 'localhost'){
					$path = explode('/', dirname($_SERVER['PHP_SELF']));
					$this->_consumer_url	= 'http://' . $_SERVER['SERVER_NAME'] . '/' . $path[1] . '/';
				}else
					$this->_consumer_url	= 'http://' . $_SERVER['SERVER_NAME'] . '/';

				$this->_ACONTENT_OAUTH_HOST			= $GLOBALS['_config']['transformable_uri'];
				$this->_ACONTENT_REQUEST_TOKEN_URL	= $this->_ACONTENT_OAUTH_HOST . "oauth/request_token.php";
				$this->_ACONTENT_AUTHORIZE_URL		= $this->_ACONTENT_OAUTH_HOST . "oauth/authorization.php";
				$this->_ACONTENT_ACCESS_TOKEN_URL	= $this->_ACONTENT_OAUTH_HOST . "oauth/access_token.php";
				$this->_ACONTENT_REGISTER_CONSUMER	= $this->_ACONTENT_OAUTH_HOST . "oauth/register_consumer.php";
			}
			
			return;
		}

		/**
		 * Establishes an LTI connection and returns the the XML resulting data
		 * @access  public
		 * @return  XML data structure
		 * @author  Mauro Donadio
		 */
		public function getXMLdata(){

			if($this->_content_id === null OR $this->_course === null)
				return FALSE;

			// switch between different versions of LTI
			// now we have 1p1 and 2p0 but we want to manage future versions
			switch($this->_LTI_version){

				// LTI 2.0
				case '2.0':

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

			} //-- switch

			//
			// OAuth 1.0
			//

			if($this->_OAuth()){
				
				##
				## GET AND SET PARAMS
				##

				// id
				$this->_Launch_Data['resource_link_id']				= substr(uniqid('', true), 0, 16);
				// title
				$this->_Launch_Data['resource_link_title']			= htmlentities($_POST['title']);
				// description
				$this->_Launch_Data['resource_link_description']	= htmlentities($_POST['desc']);


				$userDetails	= $this->_getUserDetails();

				// user_id
				$this->_Launch_Data['user_id']						= $userDetails['user_id'];
				// roles
				// AT_course_enrollment
				// role
				$this->_Launch_Data['roles']						= $userDetails['user_role'];
				// fullname
				$this->_Launch_Data['lis_person_name_full']			= $userDetails['user_fullname'];

				$this->_Launch_Data['tool_consumer_instance_guid']	= $this->_consumer_url;
				$this->_Launch_Data['tool_consumer_instance_desc']	= $GLOBALS['_config']['site_name'];
	
				$endpoint		= $this->_LTI_Resource['launch_URL'];

				$key			= $this->_LTI_Resource['key'];
				$secret			= $this->_LTI_Resource['secret'];
				
				$tool_consumer_instance_guid		= $this->_Launch_Data['tool_consumer_instance_guid'];
				$tool_consumer_instance_description	= $this->_Launch_Data['tool_consumer_instance_desc'];

				// send to the TP the course id
				$this->_Launch_Data['tile_course_id']	= $this->_content_id;
				// set if entire course or lesson
				$this->_Launch_Data['course']			= $this->_course;

				//
				//	LTI connection
				//

				$parms			= signParameters($this->_Launch_Data, $endpoint, "POST", $key, $secret, "Press to Launch", $tool_consumer_instance_guid, $tool_consumer_instance_description);

				//
				// CURL (auto submit form)
				//

				$result			= $this->_curlFormAutoSubmit($endpoint, $parms);

				$xmlStructure	= strstr($result,		'aContent_LiveContentLink() = ');
				$xmlStructure	= ltrim($xmlStructure,	'aContent_LiveContentLink() = ');
				$xmlStructure	= trim($xmlStructure);

				$xmlStructure	= html_entity_decode($xmlStructure);

				return $xmlStructure;

			}
			else
			{
				die('Error: Missing OAuth auth!');
			}

			return;
		}

		/**
		 * Establishes an oauth authentication
		 * @access  private
		 * @return  bool
		 * @author  Mauro Donadio
		 */
		private function _OAuth(){

		    ##
			## GET consumer_key and consumer_secret
		    ##

			// Register consumer
			$config						= $this->_register_consumer();
			$config['request_token']	= $this->_ACONTENT_REQUEST_TOKEN_URL;

		    ##
			## OAuth 1a authentication
		    ##

		    include_once 'Twitauth.class.php';

			$tw		= new Twitauth($config);
			$res	= $tw->getRequestToken();

			if($res == null)
				return false;
			else{

				// Sets the class private vars

				$this->_LTI_Resource['launch_URL']	= $this->_ACONTENT_OAUTH_HOST . 'oauth/lti/tool.php';
				$this->_LTI_Resource['key']			= $config['key'];
				$this->_LTI_Resource['secret']		= $config['secret'];

				return true;
			}
		}

		/**
		 * Gets some variables for the LTI connection
		 * @access  private
		 * @return  user details
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
		 * Gets the key and secret from AContent through the register_consumer.php endpoint
		 * @access  private
		 * @return  array of configuration parameters
		 * @author  Mauro Donadio
		 */
		private function _register_consumer(){

			$reg_consumer	= '';
			$reg_vars		= '';

			// send a request to the TP
			// the TP will return the consumer_key and consumer_secret
			$reg_consumer	= file_get_contents($this->_ACONTENT_REGISTER_CONSUMER. '?consumer=' . $this->_consumer_url . '&expire=' . $GLOBALS['_config']['transformable_oauth_expire'] . 'lti=' . $this->_LTI_version);

			// for each returned value
			// we get those parms we are interested in (key, secret)
			
			$params			= $this->_getURLparams($reg_consumer);
			
			$config['key']		= $params['consumer_key'];
			$config['secret']	= $params['consumer_secret'];

			return $config;
		}

		/**
		 * Gets the all the parameters by the given url
		 * @access  private
		 * @param	url from which to extract the parameters
		 * @return  array of parameters
		 * @author  Mauro Donadio
		 */
		private function _getURLparams($url = null){
			
			$params		= null;

			if($url == NULL){
				foreach ($_GET as $key => $value) {
					$params[$key]	= urldecode($value);
				}
			}else{
				$p	= parse_url($url);
				$p	= explode('&', $p['path']);

				foreach ($p as $key => $value) {
					$p	= explode('=', $value);
					$params[$p[0]]	= urldecode($p[1]);
				}
			}
			
			return $params;
		}

		/**
		 * Return the LTI data
		 * @access  private
		 * @param   url of the TP, parameters requested by the TP
		 * @return  LTI data
		 * @author  Mauro Donadio
		 */
		private function _curlFormAutoSubmit($url, $parms){

			$fields_string	= '';

			// url-ify the data for the POST
			foreach($parms as $key=>$value) {
				$fields_string	.= $key.'=' . urlencode($value) . '&';
			}
			
			$fields_string	= rtrim($fields_string, '&');

			// CURL

			// open connection
			$ch = curl_init();
		
			//set the url, number of POST vars, POST data
			curl_setopt($ch, CURLOPT_URL,			$url);
			curl_setopt($ch, CURLOPT_POST,			TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS,	$fields_string);
			curl_setopt($ch, CURLOPT_HEADER,		0);
		
			// output buffer start
			ob_start();

				curl_exec($ch); 
		
				// close connection
				curl_close($ch);

			// output buffer get contents
			$result	= ob_get_contents();

			// output buffer close 
			ob_end_clean();
			
			return $result;
		}

	}
?>