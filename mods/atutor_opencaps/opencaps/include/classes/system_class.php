<?php
/*
 * OpenCaps
 * http://opencaps.atrc.utoronto.ca
 * 
 * Copyright 2009 Heidi Hazelton
 * Adaptive Technology Resource Centre, University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0. 
 * You may not use this file except in compliance with this License.
 * http://www.opensource.org/licenses/ecl2.php
 * 
 */

class system {
	
	public $system_id;
	public $name;
	private $type; 

	public $base_url;	
	private $get_url;
	private $put_url;
	
	private $username; //optional
	private $password; //optional
	
	
	/* new system connection */
	function __construct($system_id) {
		global $systems;
		
		$_SESSION['rid'] = $system_id;
		
		/* load in system info from config file */
		$this->system_id = $system_id;
		$this->name = $systems[$system_id]['name'];
		$this->base_url = $systems[$system_id]['url'];
		$this->type = $systems[$system_id]['type'];

		$this->username = $systems[$system_id]['username'];
		$this->password = $systems[$system_id]['password'];
						
		$this->get_url = $this->base_url."?action=getMedia"; // url for receiving media info
		$this->put_url = $this->base_url."?action=putCaps"; // url for sending captions back
		$this->returnFormat = 'DFXP';
	}
	
	function openProject($id) {
		global $this_proj, $supported_ext;
		
		$media_info = $this->getMedia();
		
		if (!empty($media_info)) {

			//check if correct format
			$ext = explode(".", $media_info->mediaFile);
			$ext = end($ext);
	
			if (!@in_array($ext, $supported_ext)) {
				$_SESSION['errors'][] = "Media file format not supported.";
				include(INCLUDE_PATH."basic_header.inc.php");
				include(INCLUDE_PATH."footer.inc.php");
				exit;
			}	
					
			//populate project vars					
			$this_proj->name = $media_info->title;
			$this_proj->media_loc = $media_info->mediaFile;
			$this_proj->clip_collection = new clipCollection();	
				
			//import existing captions, if any			
			if (!empty($media_info->captionFile)) 			
				$this->importSystemCaptions($media_info->captionFile);	
										
			//save working file
			$json = json_encode(get_object_vars($this_proj));
			$this_proj->saveJson($json, $this_proj->id);
			
		} else {			
			$_SESSION['errors'][] = "Could not open project - no response from server.";
			include(INCLUDE_PATH."basic_header.inc.php");
			include(INCLUDE_PATH."footer.inc.php");
			exit;
		}	

		// set user as guest
		$_SESSION['valid_user'] = true;
		$_SESSION['mid'] = '99999';
							
		header('Location:editor.php');
		exit;
		
		return;		
	}
	
	
	function getMedia() {
		global $this_proj;
						
		//get json		
		switch ($this->type) {
			case 'matterhorn':
				$media_info = json_decode($this->matterhornAuth('/opencaps/rest/'.$this_proj->id));
				
				//convert naming
				$media_info->mediaFile = $media_info->mediaURL;
				$media_info->captionFile = $media_info->DFXPURL;
				unset($media_info->mediaURL);
				unset($media_info->DFXPURL);
				break;
				
			case 'atutor':				
				$media_info = json_decode(@file_get_contents($this->get_url.'&id='.$this_proj->id));
				break;
				
			default:
				$media_info = json_decode(@file_get_contents($this->get_url.'&id='.$this_proj->id));				
				break;
		}	
		return $media_info;	
	}		
	
	function putCaps($id, $format="SubRipSrt") {
		global $this_proj, $base_url, $systems;
				
		//convert captions to required format
		$convert_url = $base_url.'conversion_service/index.php?cc_url='.urlencode('../projects/'.$_SESSION['pid'].'/opencaps.json').'&cc_result=0&cc_target='.$format.'&cc_name=noname.txt';
		$formatted_captions = trim(@file_get_contents($convert_url));
		
		if (OC_DEBUG_MODE_ON)
		{
			$ocAtDebugMsg = '*************************';
			$ocAtDebugMsg .= '<br/>';
			$ocAtDebugMsg .= '<b>putCaps() - class: system</b>';
			$ocAtDebugMsg .= '<br/>';
			$ocAtDebugMsg .= '<br/>ID: '.$_SESSION['pid'];
			$ocAtDebugMsg .= '<br/>';
			$ocAtDebugMsg .= '<br/>Conversion Service URL:<br/> '.$convert_url;
			$ocAtDebugMsg .= '<br/>';
			$ocAtDebugMsg .= '<br/>Caption Data:<br/>'.$formatted_captions;
			$ocAtDebugMsg .= '<br/>';
			$ocAtDebugMsg .= '<br/>Base Url: '.$base_url;
			$ocAtDebugMsg .= '<br/><br/>';
			$ocAtDebugMsg .= '*************************';
			$_SESSION['feedback'][] = $ocAtDebugMsg;
		}
		
		//send captions
		if (!empty($formatted_captions)) {
			
			switch ($this->type) {
				case 'matterhorn':
					$uri = '/opencaps/rest/'.$this_proj->id.'/TimedText/';			
					$response = matterhornAuth($this->id, $uri, $formatted_captions);					
					break;
					
				default:
					/*
					 * ANTO APPROACH
					 */
					// load and call atutor class
					include_once('system_OcAtutor_class.php');
					OcAtutor::putCaps($systems[ACTIVE_SYSTEM]['url'],'putCaps',$_SESSION['pid'],$formatted_captions);
					break;
			}	
			/*
			 * ANTO: this stuff is too accurate, really tells nothing about the success to the action (e.g. was the file updated?):
			 * 
			*/			 
			if (empty($response)) {
				$_SESSION['feedback'][] = "Successfully updated server.";			
			} else {				
				$_SESSION['errors'][] = "Could not update remote server.";
				
			}
		
			
			
		} else {
			$_SESSION['errors'][] = "Could not convert captions to required format.";
		}
		
		return true;	
		
	}
	
	function importSystemCaptions($capfile) {		
		global $base_url, $this_proj;		
		
		$ccollection = new clipCollection() ;
		
		//get captions		
		switch ($this->type) {
			case 'matterhorn':
				$uri = substr($capfile, strlen($remote_systems[$_SESSION['rid']]['url']));
				$caps = matterhornAuth($_SESSION['rid'], $uri);
				$caption_file = INCLUDE_PATH.'../projects/'.$this->id.'/'.'captions.xml';
				@file_put_contents($caption_file, $caps);	
				break;
				
			case 'atutor':
				$caption_file = $capfile;
				break;
				
			default:
				break;
		}	
					
		$convert_url = $base_url.'/conversion_service/?cc_url='.$caption_file.'&cc_result=0&cc_target=JSONcc&cc_name=noname.___';					
		$json_captions = json_decode(@file_get_contents($convert_url));

		if (!empty($json_captions) && $json_captions != "The format of source Caption was not recognized.") {			
			
			foreach ($json_captions->captionCollection as $clip) {				
				$this_clip = new clip($clip->inTime, $clip->outTime, trim($clip->caption));
				$ccollection->addClip($this_clip);
			}	

			$_SESSION['feedback'][] = "Captions imported successfully.";
										
		} else  {
			$_SESSION['errors'][] = "Problem with caption file - the format is incorrect, or unsupported.";
		}		
		
		$this_proj->clip_collection = $ccollection;
			
		//save reference file
		$json = json_encode(get_object_vars($this_proj));		
		$this_proj->saveJson($json, $this->id);		
		return;

	}			
	
	
	/* digest auth for matterhorn systems */
	function matterhornAuth($uri, $content='') {
		global $this_proj;
			
	    $username = $this->username;
	    $password = $this->password;
			
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    
	    curl_setopt($ch, CURLOPT_URL, $systems[$rid]['url'].$uri);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
	    curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);   
	   	curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-Auth: Digest")); 
		
	    if ($content == "media") {
	    	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	    	
	    	$temp_file = 'projects/'.$this_proj->id.'/media'; //use tempname();
	    	$fh = fopen($temp_file, 'w');
			curl_setopt($ch, CURLOPT_FILE, $fh);
			curl_exec($ch);
			curl_close($ch);
			fclose($fh); 
			return $temp_file;
			
	    } else if (!empty($content)) {
		    curl_setopt($ch, CURLOPT_POST, 1);
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, $content);   
	    }
	        
	    $response = curl_exec($ch);
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	
	    curl_close($ch);
	
		return $response;	
	}

	
	/* list a system's available projects */
	function printProjects($pageNum=1) {
				
		//check if connected
		if (!@file_get_contents($this->url)) {
			echo "Can't connect to remote server.";
			return;
		}
		
		$projsPerPage = 20;

		$uri = "/opencaps/rest/list/processed.json";	
		$remote_media = json_decode(matterhornAuth($rid, $uri));	
				
		$numrows = $remote_media->total;		
		$maxPage = ceil($numrows/$projsPerPage);
				
		$nav  = 'Page: ';		
		for($page = 1; $page <= $maxPage; $page++) {
		   if ($page == $pageNum) {
		      $nav .= " $page "; 
		   } else {
		      $nav .= ' <a href="start_remote.php?r='.$rid.'&page='.$page.'">'.$page.'</a> ';
		   }
		}		
		
		if ($maxPage > 1)
			echo $nav."<br /><br />";
		
		$offset = ($pageNum - 1) * $projsPerPage;
		
		$uri = "/opencaps/rest/list/processed.json?count=90&startPage=".($pageNum-1);		
				
		//$remote_media = $remote_systems[$rid]['url']."/opencaps/rest/list/processed.json?count=$projsPerPage&startPage=".($pageNum-1);
		
		$_SESSION['rid'] = $rid;		
		$remote_media = json_decode(matterhornAuth($rid, $uri));		
				
		if (!empty($remote_media->results)) {
			echo '<ul class="proj-list">';
			foreach($remote_media->results as $rm) {			
				echo '<li><label><input type="radio" name="proj" value='.$rm->id.' /> '.$rm->title.'</label></li>';
			}
			echo '</ul>';
			echo "<div style='text-align:right;'><input type='submit' class='button' style='width:6em;margin-top:5px;' name='startopen' value='Submit' /></div>";
		} else {
			echo "No projects yet.";
		}
	}	
	
}	

?>