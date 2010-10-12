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

class ATOCProjectManager
{
	/*
	private $id;
	private $login;
	private $courseId;
	private $name;
	private $mediaFile;
	private $captionFile;
	private $date;
	*/
	
	/**
	 * @desc Load Projects
	 * @param String $theLogin
	 * @param int $theCourseId
	 * @param int $theId
	 * @return Array $myProjects
	 */
	public function _loadProjects($theLogin, $theCourseId, $theId)
	{
		//echo '<h3>START method: _loadProjects()</h3>';

		global $db;
		
		$myProjects = array();
		$myGetMediaObj = new OcJsonFileProject();
		
		$sql = "SELECT * FROM ".TABLE_PREFIX."atopencaps_mod WHERE login = '".$theLogin."' AND courseId = ".$theCourseId;
		$sqlWhere = '';

		if($theId!=0)
		{
			$sqlWhere .= ' AND id = '.$theId;
		} 
		
		$sqlOrder = ' ORDER BY id DESC';
		$sql .= $sqlWhere.$sqlOrder;
			//echo "<p>".$sql.'</p>';

		$result = mysql_query($sql, $db);
		$num_affected = mysql_affected_rows($db);
		
		if ($ocAtSettings['debugMode'] && $num_affected==0)
		{
			$ocAtSettings['messages'] = '<p>_loadProjects(): no data to load </p>';
		}
		
		// get all project data into an array. 
		$myCounter = 0;
		while ($row = @mysql_fetch_assoc($result)) 
		{
			//$existing_accounts[$row['public_field']];
			$myProjects[$myCounter]['id'] = $row['id'];
			$myProjects[$myCounter]['login'] = $row['login'];
			$myProjects[$myCounter]['courseId'] = $row['courseId'];
			$myProjects[$myCounter]['name'] = $row['name'];
			$myProjects[$myCounter]['mediaFile'] = $row['mediaFile'];
			$myProjects[$myCounter]['captionFile'] = $row['captionFile'];
			$myProjects[$myCounter]['timeStamp'] = $row['timeStamp'];
			$myProjects[$myCounter]['width'] = $row['width'];
			$myProjects[$myCounter]['height'] = $row['height'];
			
			$myCounter++;
			//echo "<BR/>ID: ".$row['id'];
		} // end while

		return $myProjects;

	} // _loadProjects()


	/**
	 * @desc _createCcFile
	 * @param String $theMediaFile
	 * @return String $theCaptionFile
	 */
	public function _createCcFile($theMediaFile)
	{
		global $ocAtSettings; //

		$theNewFileName = '';
		$bol=false;
		$theCaptionFile = '';
		$theCaptionFilePath = '';
		$theCaptionFilePathFull = '';
		$i = 0;
		
		while (!$bol)
		{
			$ccNameTemp = explode('.',$theMediaFile);
			//print_r($ccNameTemp);
			if ($i==0)
			{
				$theCaptionFile = $ccNameTemp[0].'.'.$ocAtSettings['defaultCcExt'];
			} else {
				$theCaptionFile = $ccNameTemp[0].'_'.$i.'.'.$ocAtSettings['defaultCcExt'];
			}

			// fix path
			$theCaptionFilePath = str_replace('/',$ocAtSettings['dirSep'],$theCaptionFile);
	
			$theCaptionFilePathFull = AT_CONTENT_DIR.$_SESSION['course_id'].$ocAtSettings['dirSep'].$theCaptionFilePath;
			
			if (!file_exists($theCaptionFilePathFull))
			{
				$bol=true;
			}
			$i++;
			
		} // end while
	
		file_put_contents($theCaptionFilePathFull, '');
		return $theCaptionFile;

	} // end createNewCcFile
	

	/**
	 * @desc Set Active Project
	 * @param int $theId
	 * @param string $theLogin
	 * @param int $theCourseId
	 * @param string $theSessionId
	 */
	public function _setActiveProject($theId, $theLogin, $theCourseId, $theSessionId)
	{
		global $db, $ocAtSettings; // load AT db object
		
		// split id 
		$temId = explode('-',$theId);
		
		if($theId!=0 && $theLogin!='' && $theCourseId!=0 && $theSessionId!='')
		{
			$sql = "UPDATE ".TABLE_PREFIX."atopencaps_mod SET sessionId='$theSessionId'";
			$sql .= " WHERE id = $theId AND login='$theLogin' AND courseId=$theCourseId";
			$result = mysql_query($sql, $db);
			$num_affected = mysql_affected_rows($db);
				//echo '<br/>'.$sql;
			$result = null;
		}
		
	} // end _setActiveProject
	
	
	/**
	 * @desc Add/Edit/Delete captioning Project.
	 * @param int $theId
	 * @param string $theLogin
	 * @param int $theCourseId
	 * @param string $theName
	 * @param string $theMediaFile
	 * @param string $theCaptionFile
	 * @param int $theWidth
	 * @param int $theHeight
	 * @param int $theAction
	 */
	public function _addEditProject($theId, $theLogin, $theCourseId, $theName, $theMediaFile, $theCaptionFile, $theWidth, $theHeight, $theAction='')
	{

		global $db, $ocAtSettings; // load AT db object
		
		if ($theAction=="deleteProject")
		{
			$sql = "DELETE FROM ".TABLE_PREFIX."atopencaps_mod";
			$sql .= " WHERE id = $theId AND login='$theLogin'";
		
		} else if ($theId != 0)
		{
			// UPDATE
			$sql = "UPDATE ".TABLE_PREFIX."atopencaps_mod SET name='$theName', mediaFile='$theMediaFile', captionFile='$theCaptionFile', width='".$theWidth."', height='".$theHeight."'";
			$sql .= " WHERE id = $theId AND login='$theLogin'";

		} else {
			
			// create caption file if does not exist
			if($theCaptionFile=='')
			{
				$theCaptionFile = $this->_createCcFile($theMediaFile);
			}

			// INSERT
			$sql = "INSERT INTO ".TABLE_PREFIX."atopencaps_mod (`login`, `courseId`, `name`, `mediaFile`, `captionFile`, `width`, `height`) VALUES";
			$sql .= "('".$theLogin."', ".$theCourseId.", '".$theName."', '".$theMediaFile."', '".$theCaptionFile."', '".$theWidth."', '".$theHeight."')";
				
		}
		
			//echo $sql;
		// run SQL 
		$result = mysql_query($sql, $db);
		$num_affected = mysql_affected_rows($db);
		$result = null;
		
	} // end addProject()
	
	/**
	 * @desc _getProjecDataJson
	 * @param unknown_type $theId
	 * @param unknown_type $theBaseUrl
	 * @return unknown
	 */
	public function _getProjecDataJson($theId, $theBaseUrl)
	
	{
		global $db, $ocAtSettings;
		
		// split id 
		$temId = explode('-',$theId);
	
		$myProjects = array();
		$myGetMediaObj = new OcJsonFileProject();
		
		$sql = "SELECT * FROM ".TABLE_PREFIX."atopencaps_mod WHERE id = $temId[0] AND sessionId ='$temId[1]'";
	
		$result = mysql_query($sql, $db);
		$num_affected = mysql_affected_rows($db);
		
		// debug
		if ($ocAtSettings['debugMode'] && $num_affected==0)
		{
			echo "<p>_getProjecDataJson() function reported that: there are no Captioning Projects matching the criteria 
			<br/>SQL: ".$sql.'</p>';
		}
		
		// get all project data into an array. 
		$myCounter = 0;
		while ($row = mysql_fetch_assoc($result)) 
		{
				// build object to JSON
				$myGetMediaObj->setVars('id',$theId);
				$myGetMediaObj->setVars('login',$row['login']);
				$myGetMediaObj->setVars('title',$row['name']);
				
				$rex1 = '/(http:|https:)/i';
				
				if (preg_match($rex1, $row['mediaFile']))
				{
					$myGetMediaObj->setVars('mediaFile',''.$row['mediaFile']);
					$myGetMediaObj->setVars('captionFile',$theBaseUrl.'content/'.$row['courseId'].'/'.$row['captionFile']);
					
				} else if ($ocAtSettings['contentUrlType']== 0) {
					$myGetMediaObj->setVars('mediaFile',$theBaseUrl.'get.php/'.''.$row['mediaFile']);
					$myGetMediaObj->setVars('captionFile',$theBaseUrl.'get.php/'.''.$row['captionFile']);
					
				} else if ($ocAtSettings['contentUrlType']== 1) {
					$myGetMediaObj->setVars('mediaFile',$theBaseUrl.'content/'.$row['courseId'].'/'.$row['mediaFile']);
					$myGetMediaObj->setVars('captionFile',$theBaseUrl.'content/'.$row['courseId'].'/'.$row['captionFile']);
					
				}
				
				$myGetMediaObj->setVars('timeStamp',$row['timeStamp']);
				$myGetMediaObj->setVars('returnFormat',$ocAtSettings['ccReturnFormat']);
				
		} // end while
		
		$jsonFile = json_encode($myGetMediaObj);
		
		return $jsonFile;


	} // _getProjecDataJason()
	
	
	/**
	 * @desc _saveCaptionData
	 * @param String $theId
	 * @param String $theCcData
	 * @param int $theWidth
	 * @param int $theHeight
	 */
	public function _saveCaptionData($theId,$theCcData,$theWidth,$theHeight)
	{
		global $db, $ocAtSettings;
		
		$temId = explode('-',$theId);
		
		$sql = "SELECT * FROM ".TABLE_PREFIX."atopencaps_mod WHERE id = $temId[0] AND sessionId ='$temId[1]'";
		
		$result = mysql_query($sql, $db);
		$num_affected = mysql_affected_rows($db);
		
		$row = mysql_fetch_assoc($result);

		if ($row['captionFile']!='')
		{
			// fix path
			$theCaptionFilePath = str_replace('/',$ocAtSettings['dirSep'],$row['captionFile']);
			
			$theCaptionFilePathFull = AT_CONTENT_DIR.$row['courseId'].$ocAtSettings['dirSep'].$theCaptionFilePath;
			
			file_put_contents($theCaptionFilePathFull, $theCcData);

			// update metadata
			if ($theWidth!='' && $theHeight!='')
			{
				$sqlUpdateMetadata = "UPDATE ".TABLE_PREFIX."atopencaps_mod SET width='".$theWidth."', height='".$theHeight."'";
				$sqlUpdateMetadata .=  " WHERE id = $temId[0] AND sessionId ='$temId[1]'";
				//$sqlUpdateMetadata .=  " AND login='$theLogin'";
				//echo '<br/>'.$sqlUpdateMetadata;
				$result1 = mysql_query($sqlUpdateMetadata, $db);
				$result1 = null;
			}
			$result = null;
		
			//return  '<h3>Data Saved in : '.$row['captionFile'].'</h3>';
			return '<p>Data Saved in : '.$theCaptionFilePathFull.'</p>';
		}

	} // _saveCaptionData()
	
	
} // end class
?>