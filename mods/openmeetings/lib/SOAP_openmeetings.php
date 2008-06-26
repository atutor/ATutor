<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Cindy Qi Li, Harris Wong		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: SOAP_openmeetings.php 7575 2008-06-02 18:17:14Z hwong $
if (!defined('AT_INCLUDE_PATH')) { exit; }

//require(AT_INCLUDE_PATH . 'classes/nusoap.php');
require('nusoap.php');  //newer version, come with the module

/**
* SOAP_openmeetings
* Class for using the SOAP service for openmeetings
* Please refer to the following API: 
*	http://code.google.com/p/openmeetings/wiki/SoapMethods
*
* @access	public
* @author	Harris Wong
*/
class SOAP_openmeetings {
	var $_sid			= "";	//session id
	var $_soapClient	= NULL;	//soap connector
	var $_wsdl			= "";	//soap service link

	function SOAP_openmeetings($wsdl) {
		$this->_wsdl			= $wsdl;
		$this->_soapClient		= new nusoap_client($this->_wsdl, true);
		$getSession_obj			= $this->_performAPICall('getSession', '');	
		//check session id
		if (!$getSession_obj){
			$this->_sid = session_id();  //openmeeting will return error code on this
		} else {
			$this->_sid = $getSession_obj['return']['session_id'];
		}
	}

    /**
    * Login as an user and sets a session
    *
    * @param  array
    * @return mixed
    * @access public
    */
    function login($parameters = array()) {
        if (!isset($parameters["username"])) {
            return false;
        }
        return $this->_performAPICall(
          "loginUser",

          array(
            "SID"         => $this->_sid,
            "username"    => $parameters["username"],
            "userpass"    => $parameters["userpass"]
          )
        );
    }


	/**
	 * Sets user object
     * @param  array
     * @return mixed
     * @access public
     */
    function saveUserInstance($parameters = array()) {
        return $this->_performAPICall(
          "setUserObject",

          array(
            "SID"					=> $this->_sid,
            "username"				=> $parameters["username"],
            "firstname"				=> $parameters["firstname"],
		    "lastname"				=> $parameters["lastname"],
		    "profilePictureUrl"		=> $parameters[""],
		    "email"					=> $parameters["email"]
          )
        );
    }

	/**
	 * Get error message
	 */
	function getError($code){
		return $this->_performAPICall(
			"getErrorByCode",
			array(
				"SID"				=> $this->_sid,
				"errorid"			=> $code,
				"language_id"		=> 1
				)
		);
	}

	/**
	 * Creating a room
	 */
	function addRoom($parameters = array()){
        return $this->_performAPICall(
          "addRoom",

          array(
            "SID"						=> $parameters["SID"],
			'name'						=> $parameters["name"],
			'roomtypes_id'				=> $parameters["roomtypes_id"],
			'comment'					=> 'Room created by ATutor',
			'numberOfPartizipants'		=> $parameters["numberOfPartizipants"],
			'ispublic'					=> $parameters["ispublic"],
			'videoPodWidth'				=> $parameters["videoPodWidth"],
			'videoPodHeight'			=> $parameters["videoPodHeight"],
			'videoPodXPosition'			=> 2, 
			'videoPodYPosition'			=> 2, 
			'moderationPanelXPosition'	=> 400, 
			'showWhiteBoard'			=> $parameters["showWhiteBoard"],
			'whiteBoardPanelXPosition'	=> 276, 
			'whiteBoardPanelYPosition'	=> 2, 
			'whiteBoardPanelHeight'		=> $parameters["whiteBoardPanelHeight"],
			'whiteBoardPanelWidth'		=> $parameters["whiteBoardPanelWidth"],
			'showFilesPanel'			=> $parameters["showFilesPanel"], 
			'filesPanelXPosition'		=> 2, 
			'filesPanelYPosition'		=> 284, 
			'filesPanelHeight'			=> $parameters["filesPanelHeight"], 
			'filesPanelWidth'			=> $parameters["filesPanelWidth"]
          )
        );
	}


	/**
	 * Delete room
	 */
	function deleteRoom($parameters = array()){
		return $this->_performAPICall(
			"deleteRoom",
			array(
				"SID"		=> $parameters["SID"],
				"rooms_id"	=> $parameters["rooms_id"]
			)
		);
	}


	/**
	 * return the session id.
	 */
	function getSid(){
		return $this->_sid;
	}
	 


   /**
    * @param  string
    * @param  array
    * @return mixed
    * @access private
    */
    function _performAPICall($apiCall, $parameters) {
			$result = $this->_soapClient->call(
			  $apiCall,
			  $parameters
			);
		if ($this->_soapClient->fault){
			return false;
		} elseif ($this->_soapClient->getError()){
			return false;
		}

		// if (!PEAR::isError($result)) {
		if (is_array($result)) {
            return $result;
        } else {
			return false;
        }
    }

	function myErrors(){
		return $this->_soapClient->getError();
	}
}
?>