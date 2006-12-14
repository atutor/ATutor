<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: merlot.php 6614 2006-09-27 19:32:29Z greg $

// Check if links is turned on for this course.
$sql ="SELECT home_links, main_links from ".TABLE_PREFIX."courses WHERE course_id=".$_SESSION[course_id];
$result = mysql_query($sql, $db);
while($row = mysql_fetch_assoc($result)){
	if(ereg('links/index.php', $row['home_links']) || ereg('links/index.php', $row['main_links'])){
		$links = true;
	}
}
		If(isset($_REQUEST['query'])){
			$search = stripslashes($_REQUEST['query']);
		}else if(isset($_REQUEST['advsearch'])){
			$search  = '<search>';
			if($_REQUEST['keywords'] != ''){
				$keywords = stripslashes($_REQUEST['keywords']);
				$search .= '<keyword>'.$keywords.'</keyword>';
			}

			if($_REQUEST['title'] != ''){
				$title = stripslashes($_REQUEST['title']);
				$search .= '<title>'.$title.'</title>';
			}

			if($_REQUEST['contributorName'] != ''){
				$author = stripslashes($_REQUEST['contributorName']);
				$search .= '<authorname>'.$author.'</authorname>';
			}

			if($_REQUEST['url'] != ''){
				$url = stripslashes($_REQUEST['url']);
				$search .= '<location>'.$url.'</location>';
			}

			if($_REQUEST['description'] != ''){
				$description = stripslashes($_REQUEST['description']);
				$search .= '<description>'.$description.'</description>';
			}

			if($_REQUEST['category'] != ''){
				$category = intval($_REQUEST['category']);
				$search .= '<category>'.$category.'</category>';
			}

			if($_REQUEST['language'] != ''){
				$language = stripslashes($_REQUEST['language']);
				$search .= '<language>'.$language.'</language>';
			}

			if($_REQUEST['materialType'] != ''){
				$type = stripslashes($_REQUEST['materialType']);
				$search .= '<learningresourcetype>'.$type.'</learningresourcetype>';
				}

			if($_REQUEST['audience'] != ''){
				$context = stripslashes($_REQUEST['audience']);
				$search .= '<context>'.$context.'</context>';
			}

				$search  .= '</search>';
		}

		$search_input = array($merlot_key, $search,'0','25');
		$client = new SoapClient($merlot_location);

		if($search && $search != '<search></search>'  && $search != ''){
			$results = $client->__soapCall('doSearch', $search_input);
			$results = get_object_vars($results);
			
			$result_count = count($results['resultElements']);
			echo '<div style="background-color:#eeeeee;width:95%;"><h3>'. _AT('merlot_results_found') .' - '.$results['totalResultsCount'].' -- '._AT('merlot_results_displayed').' '. $result_count.'</h3></div>';
	
			for($i = 0; $i < $result_count; $i++){
				$this_result =  get_object_vars($results['resultElements'][$i]);
				if(fmod($i,2) ==0){
					$bgstyle = "#ffffff";
				}else{
					$bgstyle = "#eeeeee";
				}
	
			echo '<div style="background-color:'.$bgstyle.';width:90%; margin: 10px; padding:3px;">';
			echo '<a href="'.$this_result['URL'].'" target="merlot">'.$this_result['title'].'</a> [ <a href="'.$this_result['detailURL'].'"  target="merlot">'._AT('merlot_full_record').'</a> ] ';

			// if links db is enabled for this course allow results to be added to it.
			if($this_result['numAssignments']){
				echo '[ <a href="'.$this_result['assignmentsURL'].'"      target="merlot">'._AT('merlot_view_assignments').'</a> ]';
			}
			if($links == true){
				echo '[ <a href="'.$_base_href.'mods/merlot/add_to_links.php?title='.urlencode($this_result['title']).SEP.'desc='.urlencode(substr($this_result['description'], 0,100)).SEP.'url='.urlencode($this_result['URL']).'">'._AT('merlot_add_to_link').'</a> ]';
			}	

			echo '<br/>'._AT('merlot_author').": ".$this_result['authorName'].'<br/>';
			echo _AT('merlot_description').": ".substr($this_result['description'], 0,200).'...<br/>';
			echo _AT('merlot_type').": ".$this_result['itemType'].'<br/>';
			echo _AT('merlot_creation_date').": ".date("F j, Y",strtotime($this_result['creationDate'])).'<br/>';
			echo '</div>';
			unset($this_result);
		}
	}else if($results['totalResultsCount'] < 1){
			echo '<div style="background-color:#eeeeee;width:95%;"><h3>'. _AT('merlot_results_found', $result_count).' 0 </h3></div>';
	}

?>