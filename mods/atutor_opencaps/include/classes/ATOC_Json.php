<?php
/****************************************************************/
/* Atutor-OpenCaps Module						
/****************************************************************/
/* Copyright (c) 2010                           
/* Written by Antonio Gamba						
/* Adaptive Technology Resource Centre / University of Toronto
/*
/* This program is free software. You can redistribute it and/or
/* modify it under the terms of the GNU General Public License
/* as published by the Free Software Foundation.
/****************************************************************/

class OcJsonFileProject
{
	// set here all the global vars
	public $id;
	public $title;
	public $login;
	public $mediaFile;
	public $captionFile;
	public $timeStamp;
	public $returnFormat;
	
	/**
	 * @desc setVars
	 * @param String $varName
	 * @param String $varValue
	 */
	public function setVars($varName, $varValue)
	{
		$this->$varName = $varValue;
	}
	
	/**
	 * @desc addProjectData
	 * @param String $theProjectData
	 */
	public function addProjectData($theProjectData)
	{
		
		$this->results[]=$theProjectData;
	}

} // end class
?>