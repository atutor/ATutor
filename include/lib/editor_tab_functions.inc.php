<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

function in_array_cin($strItem, $arItems)
{
   foreach ($arItems as $key => $strValue)
   {
       if (strtoupper($strItem) == strtoupper($strValue))
       {
		   return $key;
       }
   }
   return false;
} 


function get_tabs() {
	//these are the _AT(x) variable names and their include file
	/* tabs[tab_id] = array(tab_name, file_name,                accesskey) */
	$tabs[0] = array('content',       		'edit.inc.php',          'n');
	$tabs[1] = array('properties',    		'properties.inc.php',    'p');
	$tabs[2] = array('glossary_terms',		'glossary.inc.php',      'g');
	$tabs[3] = array('preview',       		'preview.inc.php',       'r');
	$tabs[4] = array('accessibility', 		'accessibility.inc.php', 'a');	
	//Silvia: Added to declare alternative resources
	$tabs[5] = array('alternative_content', 'alternatives.inc.php',  'l');	
	//Harris: Extended test functionality into content export
	$tabs[6] = array('tests',				'tests.inc.php',		 't');
	
	return $tabs;
}


function output_tabs($current_tab, $changes) {
	global $_base_path;
	$tabs = get_tabs();
	$num_tabs = count($tabs);
?>
	<table class="etabbed-table" border="0" cellpadding="0" cellspacing="0" width="95%">
	<tr>		
		<?php 
		for ($i=0; $i < $num_tabs; $i++): 
			if ($current_tab == $i):?>
				<td class="selected">
					<?php if ($changes[$i]): ?>
						<img src="<?php echo $_base_path; ?>images/changes_bullet.gif" alt="<?php echo _AT('usaved_changes_made'); ?>" height="12" width="15" />
					<?php endif; ?>
					<?php echo _AT($tabs[$i][0]); ?>
				</td>
				<td class="tab-spacer">&nbsp;</td>
			<?php else: ?>
				<td class="tab">
					<?php if ($changes[$i]): ?>
						<img src="<?php echo $_base_path; ?>images/changes_bullet.gif" alt="<?php echo _AT('usaved_changes_made'); ?>" height="12" width="15" />
					<?php endif; ?>

					<?php echo '<input type="submit" name="button_'.$i.'" value="'._AT($tabs[$i][0]).'" title="'._AT($tabs[$i][0]).' - alt '.$tabs[$i][2].'" class="buttontab" accesskey="'.$tabs[$i][2].'" onmouseover="this.style.cursor=\'pointer\';" '.$clickEvent.' />'; ?>
				</td>
				<td class="tab-spacer">&nbsp;</td>
			<?php endif; ?>
		<?php endfor; ?>
		<td >&nbsp;</td>
	</tr>
	</table>
<?php }

// save all changes to the DB
function save_changes($redir, $current_tab) {
	global $contentManager, $db, $addslashes, $msg;
	
	$_POST['pid']	= intval($_POST['pid']);
	$_POST['cid']	= intval($_POST['cid']);
	
	$_POST['alternatives'] = intval($_POST['alternatives']);
	
	$_POST['title'] = trim($_POST['title']);
	$_POST['head']	= trim($_POST['head']);
	$_POST['use_customized_head']	= isset($_POST['use_customized_head'])?$_POST['use_customized_head']:0;
	$_POST['body_text']	= trim($_POST['body_text']);
	$_POST['formatting'] = intval($_POST['formatting']);
	$_POST['keywords']	= trim($_POST['keywords']);
	$_POST['new_ordering']	= intval($_POST['new_ordering']);
	$_POST['test_message'] = trim($_POST['test_message']);
	$_POST['allow_test_export'] = intval($_POST['allow_test_export']);

	if ($_POST['setvisual']) { $_POST['setvisual'] = 1; }

	if (!($release_date = generate_release_date())) {
		$msg->addError('BAD_DATE');
	}

	if ($_POST['title'] == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	}
		
	if (!$msg->containsErrors()) {

		$_POST['title']			= $addslashes($_POST['title']);
		$_POST['body_text']		= $addslashes($_POST['body_text']);
		$_POST['head']  		= $addslashes($_POST['head']);
		$_POST['keywords']		= $addslashes($_POST['keywords']);
		$_POST['test_message']	= $addslashes($_POST['test_message']);		

		if ($_POST['cid']) {
			/* editing an existing page */
			$err = $contentManager->editContent($_POST['cid'], $_POST['title'], $_POST['body_text'], $_POST['keywords'], $_POST['new_ordering'], $_POST['related'], $_POST['formatting'], $_POST['new_pid'], $release_date, $_POST['head'], $_POST['use_customized_head'], $_POST['test_message'], $_POST['allow_test_export']);

			unset($_POST['move']);
			unset($_POST['new_ordering']);
			$cid = $_POST['cid'];
		} else {
			/* insert new */
			
			$cid = $contentManager->addContent($_SESSION['course_id'],
												  $_POST['new_pid'],
												  $_POST['new_ordering'],
												  $_POST['title'],
												  $_POST['body_text'],
												  $_POST['keywords'],
												  $_POST['related'],
												  $_POST['formatting'],
												  $release_date,
												  $_POST['head'],
												  $_POST['use_customized_head'],
												  $_POST['test_message'],
												  $_POST['allow_test_export']);
			$_POST['cid']    = $cid;
			$_REQUEST['cid'] = $cid;
		}
	}

	//debug($_POST['glossary_defs']);
	/* insert glossary terms */
	if (is_array($_POST['glossary_defs']) && ($num_terms = count($_POST['glossary_defs']))) {
		global $glossary, $glossary_ids, $msg;

		foreach($_POST['glossary_defs'] as $w => $d) {
			$old_w = $w;
			$key = in_array_cin($w, $glossary_ids);
			$w = urldecode($w);
			$d = $addslashes($d);

			if (($key !== false) && (($glossary[$old_w] != $d) || isset($_POST['related_term'][$old_w])) ) {
				$w = addslashes($w);
				$related_id = intval($_POST['related_term'][$old_w]);
				$sql = "UPDATE ".TABLE_PREFIX."glossary SET definition='$d', related_word_id=$related_id WHERE word_id=$key AND course_id=$_SESSION[course_id]";
				$result = mysql_query($sql, $db);
				$glossary[$old_w] = $d;
			} else if ($key === false && ($d != '')) {
				$w = addslashes($w);
				$related_id = intval($_POST['related_term'][$old_w]);
				$sql = "INSERT INTO ".TABLE_PREFIX."glossary VALUES (NULL, $_SESSION[course_id], '$w', '$d', $related_id)";

				//debug($sql);
				$result = mysql_query($sql, $db);
				$glossary[$old_w] = $d;
			}
		}
	}
	if (isset($_GET['tab'])) {
		$current_tab = intval($_GET['tab']);
	}
	if (isset($_POST['current_tab'])) {
		$current_tab = intval($_POST['current_tab']);
	}

	//Add test to this content - @harris
	$sql = 'SELECT * FROM '.TABLE_PREFIX."content_tests_assoc WHERE content_id=$_POST[cid]";
	$result = mysql_query($sql, $db);
	$db_test_array = array();
	while ($row = mysql_fetch_assoc($result)) {
		$db_test_array[] = $row['test_id'];
	}

	if (is_array($_POST['tid']) && sizeof($_POST['tid']) > 0){
		$toBeDeleted = array_diff($db_test_array, $_POST['tid']);
		$toBeAdded = array_diff($_POST['tid'], $db_test_array);
		//Delete entries
		if (!empty($toBeDeleted)){
			$tids = implode(",", $toBeDeleted);
			$sql = 'DELETE FROM '. TABLE_PREFIX . "content_tests_assoc WHERE content_id=$_POST[cid] AND test_id IN ($tids)";
			$result = mysql_query($sql, $db);
		}

		//Add entries
		if (!empty($toBeAdded)){
			foreach ($toBeAdded as $i => $tid){
				$tid = intval($tid);
				$sql = 'INSERT INTO '. TABLE_PREFIX . "content_tests_assoc SET content_id=$_POST[cid], test_id=$tid";
				$result = mysql_query($sql, $db);
				if ($result===false){
					$msg->addError('MYSQL_FAILED');
				}
			}
		}
	} else {
		//All tests has been removed.
		$sql = 'DELETE FROM '. TABLE_PREFIX . "content_tests_assoc WHERE content_id=$_POST[cid]";
		$result = mysql_query($sql, $db);
	}
	//End Add test

	/*Added by Silvia 
	if ($current_tab == 5) {
		echo 'ci sono';
		if(($_POST['alternatives']==1) || ($_GET['alternatives']==1)){
			$sql	= "SELECT primary_resource_id FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid'";
	    	$result = mysql_query($sql, $db);

	    	if (mysql_num_rows($result) > 0) {
	   	 		while ($row = mysql_fetch_assoc($result)) {
	   	 			$sql_type	 = "SELECT * FROM ".TABLE_PREFIX."resource_types";
	    			$result_type = mysql_query($sql_type, $db);
	    			
     	 			if (mysql_num_rows($result_type) > 0) {
	   	 				while ($type = mysql_fetch_assoc($result_type)) {
	   	 					$sql_contr  = "SELECT * FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id='$row[primary_resource_id]' and type_id='$type[type_id]'";
	   	 					$contr		= mysql_query($sql_contr, $db);	   
	   	 					if (mysql_num_rows($contr) > 0) {
	   	 						while ($control = mysql_fetch_assoc($contr)) {
	   	 							if (isset($_POST['checkbox_'.$type[type].'_'.$row[primary_resource_id].'_primary']))
	   	 								continue;
	   	 							else {
	   	 								$sql_del = "DELETE FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id='$control[primary_resource_id]' and type_id='$control[type_id]'";
										$result_del = mysql_query($sql_del, $db);
		 							}
	   	 						}	
	   	 					}
	   	 					else {
	   	 						if (isset($_POST['checkbox_'.$type[type].'_'.$row[primary_resource_id].'_primary'])){
									$sql_ins	= "INSERT INTO ".TABLE_PREFIX."primary_resources_types VALUES ($row[primary_resource_id], $type[type_id])";
									$ins 		= mysql_query($sql_ins, $db);
									}	
	   	 						
	   	 						$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id='$row[primary_resource_id]'";
		    					$result_alt = mysql_query($sql_alt, $db);
	    					
								if (mysql_num_rows($result_alt) > 0) {
     		 						while ($alt = mysql_fetch_assoc($result_alt)) {
										$sql_contr  = "SELECT * FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$alt[secondary_resource_id]' and type_id='$type[type_id]'";
	   	 								$contr	= mysql_query($sql_contr, $db);	   
	   	 								if (mysql_num_rows($contr) > 0) {
	   	 									while ($control = mysql_fetch_assoc($contr)) {
	   	 										if (isset($_POST['checkbox_'.$type[type].'_'.$alt[secondary_resource_id].'_secondary']))
	   	 											continue;
	   	 										else {
	   	 											$sql_del = "DELETE FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$control[secondary_resource_id]' and type_id='$control[type_id]'";
													$result_del = mysql_query($sql_del, $db);
			 										}
		   	 									}		
	   		 								}
	   	 								else {
											if (isset($_POST['checkbox_'.$type[type].'_'.$alt[secondary_resource_id].'_secondary'])){
												$sql_ins	= "INSERT INTO ".TABLE_PREFIX."secondary_resources_types VALUES ($alt[secondary_resource_id], $type[type_id])";
												$ins 		= mysql_query($sql_ins, $db);
	   	 										}
	   	 									$lang=$_POST['lang_'.$alt[secondary_resource_id].'_secondary'];
											$sql_up	= "UPDATE ".TABLE_PREFIX."secondary_resources SET language_code='$lang' WHERE secondary_resource_id=$alt[secondary_resource_id]";
											$up 	= mysql_query($sql_up, $db);
		   	 							}	
									}			
								}
						
								$lang=$_POST['lang_'.$row[primary_resource_id].'_primary'];
								$sql_up	= "UPDATE ".TABLE_PREFIX."primary_resources SET language_code='$lang' WHERE primary_resource_id=$row[primary_resource_id]";
								$up 	= mysql_query($sql_up, $db);
	   	 					}
	   	 				}
					}
				}
	    	}
		}
		else {
			echo 'sono quaqua';
			if ($changes_made)
				$body_ins = $_POST['body_text'];
			else {
				$sql = "SELECT * FROM AT_content WHERE content_id='$cid'";
				$result = mysql_query($sql, $db);
				 //echo $sql;
				while ($row = mysql_fetch_assoc($result)) {
					$body_ins = addslashes($row['text']);
					}
				}
			$sql	= "SELECT primary_resource_id FROM ".TABLE_PREFIX."primary_resources WHERE content_id='$cid' and resource='$body_ins'";
	    	$result = mysql_query($sql, $db);

			if (mysql_num_rows($result) > 0) {
	   			while ($row = mysql_fetch_assoc($result)) {
	   	 			$sql_type	 = "SELECT * FROM ".TABLE_PREFIX."resource_types";
					$result_type = mysql_query($sql_type, $db);
	    				
     	 			if (mysql_num_rows($result_type) > 0) {
	   	 				while ($type = mysql_fetch_assoc($result_type)) {
	   	 					$sql_contr  = "SELECT * FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id='$row[primary_resource_id]' and type_id='$type[type_id]'";
			   	 			$contr		= mysql_query($sql_contr, $db);	   
	   			 			if (mysql_num_rows($contr) > 0) {
	   	 						while ($control = mysql_fetch_assoc($contr)) {
	   	 							if (isset($_POST['checkbox_'.$type[type].'_'.$row[primary_resource_id].'_primary']))
	   	 								continue;
			   	 					else {
	   			 						$sql_del = "DELETE FROM ".TABLE_PREFIX."primary_resources_types WHERE primary_resource_id='$control[primary_resource_id]' and type_id='$control[type_id]'";
										$result_del = mysql_query($sql_del, $db);
		 								}
	   	 							}	
	   	 						}
	   	 					else {
	   	 						if (isset($_POST['checkbox_'.$type[type].'_'.$row[primary_resource_id].'_primary'])){
									$sql_ins	= "INSERT INTO ".TABLE_PREFIX."primary_resources_types VALUES ($row[primary_resource_id], $type[type_id])";
									$ins 		= mysql_query($sql_ins, $db);
									}	
	   	 					
	   	 						$sql_alt	= "SELECT * FROM ".TABLE_PREFIX."secondary_resources WHERE primary_resource_id='$row[primary_resource_id]'";
	    						$result_alt = mysql_query($sql_alt, $db);
	    					
								if (mysql_num_rows($result_alt) > 0) {
     	 							while ($alt = mysql_fetch_assoc($result_alt)) {
										$sql_contr  = "SELECT * FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$alt[secondary_resource_id]' and type_id='$type[type_id]'";
	   	 								$contr	= mysql_query($sql_contr, $db);	   
				   	 					if (mysql_num_rows($contr) > 0) {
	   	 									while ($control = mysql_fetch_assoc($contr)) {
	   	 										if (isset($_POST['checkbox_'.$type[type].'_'.$alt[secondary_resource_id].'_secondary']))
	   	 											continue;
				   	 							else {
	   	 											$sql_del = "DELETE FROM ".TABLE_PREFIX."secondary_resources_types WHERE secondary_resource_id='$control[secondary_resource_id]' and type_id='$control[type_id]'";
													$result_del = mysql_query($sql_del, $db);
		 											}
				   	 							}		
	   	 									}
	   	 								else {
											if (isset($_POST['checkbox_'.$type[type].'_'.$alt[secondary_resource_id].'_secondary'])){
												$sql_ins	= "INSERT INTO ".TABLE_PREFIX."secondary_resources_types VALUES ($alt[secondary_resource_id], $type[type_id])";
												$ins 		= mysql_query($sql_ins, $db);
	   	 										}
					   	 					$lang   = $_POST['lang_'.$alt[secondary_resource_id].'_secondary'];
											$sql_up	= "UPDATE ".TABLE_PREFIX."secondary_resources SET language_code='$lang' WHERE secondary_resource_id=$alt[secondary_resource_id]";
											$up 	= mysql_query($sql_up, $db);
	   	 								}
     	 							$lang=$_POST['lang_'.$row[primary_resource_id].'_primary'];
									$sql_up	= "UPDATE ".TABLE_PREFIX."primary_resources SET language_code='$lang' WHERE primary_resource_id=$row[primary_resource_id]";
									$up 	= mysql_query($sql_up, $db);
	   	 							}
								}
							}
	   	 				}
					}
	   			}
			}
		}
	}

	*/
	//End Added by Silvia 
	
	if (!$msg->containsErrors() && $redir) {
		$_SESSION['save_n_close'] = $_POST['save_n_close'];
		
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.basename($_SERVER['PHP_SELF']).'?cid='.$cid.SEP.'close='.$addslashes($_POST['save_n_close']).SEP.'tab='.$addslashes($_POST['current_tab']).SEP.'setvisual='.$addslashes($_POST['setvisual']).SEP.'displayhead='.$addslashes($_POST['displayhead']).SEP.'alternatives='.$addslashes($_POST['alternatives']));
		exit;
	} else {
		return;
	}
}

function generate_release_date($now = false) {
	if ($now) {
		$day  = date('d');
		$month= date('m');
		$year = date('Y');
		$hour = date('H');
		$min  = 0;
	} else {
		$day	= intval($_POST['day']);
		$month	= intval($_POST['month']);
		$year	= intval($_POST['year']);
		$hour	= intval($_POST['hour']);
		$min	= intval($_POST['minute']);
	}

	if (!checkdate($month, $day, $year)) {
		return false;
	}

	if (strlen($month) == 1){
		$month = "0$month";
	}
	if (strlen($day) == 1){
		$day = "0$day";
	}
	if (strlen($hour) == 1){
		$hour = "0$hour";
	}
	if (strlen($min) == 1){
		$min = "0$min";
	}
	$release_date = "$year-$month-$day $hour:$min:00";
	
	return $release_date;
}

function check_for_changes($row) {
	global $contentManager, $cid, $glossary, $glossary_ids_related, $addslashes;

	$changes = array();

	if ($row && strcmp(trim($addslashes($_POST['title'])), addslashes($row['title']))) {
		$changes[0] = true;
	} else if (!$row && $_POST['title']) {
		$changes[0] = true;
	}

	if ($row && strcmp($addslashes(trim($_POST['head'])), trim(addslashes($row['head'])))) {
		$changes[0] = true;
	} else if (!$row && $_POST['head']) {
		$changes[0] = true;
	}

	if ($row && strcmp($addslashes(trim($_POST['body_text'])), trim(addslashes($row['text'])))) {
		$changes[0] = true;
	} else if (!$row && $_POST['body_text']) {
		$changes[0] = true;
	}

	/* use customized head: */
	if ($row && isset($_POST['use_customized_head']) && ($_POST['use_customized_head'] != $row['use_customized_head'])) {
		$changes[0] = true;
	}

	/* formatting: */
	if ($row && strcmp(trim($_POST['formatting']), $row['formatting'])) {
		$changes[0] = true;
	} else if (!$row && $_POST['formatting']) {
		$changes[0] = true;
	}

	/* release date: */
	if ($row && strcmp(substr(generate_release_date(), 0, -2), substr($row['release_date'], 0, -2))) {
		/* the substr was added because sometimes the release_date in the db has the seconds field set, which we dont use */
		/* so it would show a difference, even though it should actually be the same, so we ignore the seconds with the -2 */
		/* the seconds gets added if the course was created during the installation process. */
		$changes[1] = true;
	} else if (!$row && strcmp(generate_release_date(), generate_release_date(true))) {
		$changes[1] = true;
	}

	/* related content: */
	$row_related = $contentManager->getRelatedContent($cid);

	if (is_array($_POST['related']) && is_array($row_related)) {
		$sum = array_sum(array_diff($_POST['related'], $row_related));
		$sum += array_sum(array_diff($row_related, $_POST['related']));
		if ($sum > 0) {
			$changes[1] = true;
		}
	} else if (!is_array($_POST['related']) && !empty($row_related)) {
		$changes[1] = true;
	}

	/* ordering */
	if ($cid && isset($_POST['move']) && ($_POST['move'] != -1) && ($_POST['move'] != $row['content_parent_id'])) {
		$changes[1] = true;
	}

	if ($cid && (($_POST['new_ordering'] != $_POST['ordering']) || ($_POST['new_pid'] != $_POST['pid']))) {
		$changes[1] = true;
	}

	/* keywords */
	if ($row && strcmp(trim($_POST['keywords']), $row['keywords'])) {
		$changes[1] = true;
	}  else if (!$row && $_POST['keywords']) {
		$changes[1] = true;
	}


	/* glossary */
	if (is_array($_POST['glossary_defs'])) {
		global $glossary_ids;
		foreach ($_POST['glossary_defs'] as $w => $d) {

			$key = in_array_cin($w, $glossary_ids);
			if ($key === false) {
				/* new term */
				$changes[2] = true;
				break;
			} else if ($cid && ($d &&($d != $glossary[$glossary_ids[$key]]))) {
				/* changed term */
				$changes[2] = true;
				break;
			}
		}

		if (is_array($_POST['related_term'])) {
			foreach($_POST['related_term'] as $term => $r_id) {
				if ($glossary_ids_related[$term] != $r_id) {
					$changes[2] = true;
					break;
				}
			}
		}
	}


	/* test & survey */	
	if ($row && isset($_POST['test_message']) && $_POST['test_message'] != $row['test_message']){
		$changes[6] = true;
	}
	if ($row && isset($_POST['allow_test_export']) && $_POST['allow_test_export'] != $row['allow_test_export']){
		$changes[6] = true;
	}

	return $changes;
}

function paste_from_file() {
	global $msg;
	if ($_FILES['uploadedfile_paste']['name'] == '')	{
		$msg->addError('FILE_NOT_SELECTED');
		return;
	}
	if ($_FILES['uploadedfile_paste']['name']
		&& (($_FILES['uploadedfile_paste']['type'] == 'text/plain')
			|| ($_FILES['uploadedfile_paste']['type'] == 'text/html')) )
		{

		$path_parts = pathinfo($_FILES['uploadedfile_paste']['name']);
		$ext = strtolower($path_parts['extension']);

		if (in_array($ext, array('html', 'htm'))) {
			$_POST['body_text'] = file_get_contents($_FILES['uploadedfile_paste']['tmp_name']);

			/* get the <title></title> of this page				*/

			$start_pos	= strpos(strtolower($_POST['body_text']), '<title>');
			$end_pos	= strpos(strtolower($_POST['body_text']), '</title>');

			if (($start_pos !== false) && ($end_pos !== false)) {
				$start_pos += strlen('<title>');
				$_POST['title'] = trim(substr($_POST['body_text'], $start_pos, $end_pos-$start_pos));
			}
			unset($start_pos);
			unset($end_pos);

			$_POST['head'] = get_html_head_by_tag($_POST['body_text'], array("link", "style", "script")); 
			if (strlen(trim($_POST['head'])) > 0)	
				$_POST['use_customized_head'] = 1;
			else
				$_POST['use_customized_head'] = 0;
			
			$_POST['body_text'] = get_html_body($_POST['body_text']); 

			$msg->addFeedback('FILE_PASTED');
		} else if ($ext == 'txt') {
			$_POST['body_text'] = file_get_contents($_FILES['uploadedfile_paste']['tmp_name']);
			$msg->addFeedback('FILE_PASTED');

		}
	} else {
		$msg->addError('BAD_FILE_TYPE');
	}

	return;
}

//for accessibility checker
function write_temp_file() {
	global $_POST, $msg;

	if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
		$content_base = 'get.php/';
	} else {
		$content_base = 'content/' . $_SESSION['course_id'] . '/';
	}

	if ($_POST['content_path']) {
		$content_base .= $_POST['content_path'] . '/';
	}

	$file_name = $_POST['cid'].'.html';

	if ($handle = fopen(AT_CONTENT_DIR . $file_name, 'wb+')) {
		$temp_content = '<h2>'.AT_print(stripslashes($_POST['title']), 'content.title').'</h2>';

		if ($_POST['body_text'] != '') {
			$temp_content .= format_content(stripslashes($_POST['body_text']), $_POST['formatting'], $_POST['glossary_defs']);
		}
		$temp_title = $_POST['title'];

		$html_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
			"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<head>
			<base href="{BASE_HREF}" />
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
			<title>{TITLE}</title>
			<meta name="Generator" content="ATutor accessibility checker file - can be deleted">
		</head>
		<body>{CONTENT}</body>
		</html>';

		$page_html = str_replace(	array('{BASE_HREF}', '{TITLE}', '{CONTENT}'),
									array($content_base, $temp_title, $temp_content),
									$html_template);
		
		if (!@fwrite($handle, $page_html)) {
			$msg->addError('FILE_NOT_SAVED');       
	   }
	} else {
		$msg->addError('FILE_NOT_SAVED');
	}
	$msg->printErrors();
}
?>
