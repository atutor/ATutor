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

class database { 
	public $db;
	
	public function __construct() {
		$this->connect();					
	}

	private function connect() {
						
		require(INCLUDE_PATH.'config.inc.php');
		$this->db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
		if (!$this->db) {
			$_SESSION['errors'][] = 'Unable to connect to database. <br />';
		}
		if (!@mysql_select_db(DB_NAME, $this->db)) {
			$_SESSION['errors'][] = 'Connection established, but database "'.DB_NAME.'" cannot be selected. <br />';
		}		
		
		if (isset($_SESSION['errors'])) {
			include(INCLUDE_PATH."basic_header.inc.php");
			include(INCLUDE_PATH."footer.inc.php");			
			exit;
		}
		
		return true;
	}
	
	/* adds a new proj to the db, returns proj id */
	public function addProj($proj) {
				
		$sql = "INSERT INTO projects VALUES (0, $_SESSION[mid], '$proj->name', '$proj->media_loc', 0, NOW())";		
		if (!$result = mysql_query($sql, $this->db)) {
			$_SESSION['errors'][] = 'Database error - could not add project.';
			return false;
		}
		return mysql_insert_id();
	}	
	
	public function updateProj($proj) {
		$sql = "UPDATE projects SET name='".$proj->name."', video_file='".$proj->media_loc."' WHERE project_id=".$proj->id;		
		if (!$result = mysql_query($sql, $this->db)) {
			$err = 'Database error <br />';
			exit;	
		}
		return true;		
	}
	

	
	/*public function getProjInfo($id) {
		global $db;
		
		$sql = "SELECT name, video_file, layout_preset, last_accessed FROM projects WHERE project_id=".intval($id);
		$result = mysql_query($sql, $db);
	
		if ($row = mysql_fetch_assoc($result)) {
			return $row;
		}	
	}
	
	public function getMovie() {
		global $db;
		
		$sql = "SELECT video_file FROM projects WHERE project_id=".intval($_SESSION['pid']);
		$result = mysql_query($sql, $db);
	
		if ($row = mysql_fetch_assoc($result)) {
			return 'projects/'.$_SESSION['pid'].'/movies/'.$row['video_file'];
		}	
	
	}*/	
}



?>