<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
exit('this file is no longer used');

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('tools');

//get the login name or real name for member_id translation
$sql14 = "select member_id, login, first_name, last_name from ".TABLE_PREFIX."members";
$result14=mysql_query($sql14, $db);
while($row=mysql_fetch_array($result14)){
	if($row['first_name'] && $row['last_name']){
		$this_user[$row['member_id']]= $row['first_name'].' '. $row['last_name'];
	}else{
		$this_user[$row['member_id']]= $row['login'];
	}
}

///////////
// Create a CSV dump of the tracking data for this course
if($_GET['csv']=='1'){
	$sql5 = "select * from ".TABLE_PREFIX."g_refs";
	$result = mysql_query($sql5,$db);
	$refs = array();
	while ($row= mysql_fetch_array($result)) {
		$refs[$row['g_id']] = $row['reference'];
	}
	//get the g translation for non content pages
	$sql8= "select
		G.g,
		R.reference,
		R.g_id
	from
		".TABLE_PREFIX."g_click_data G,
		".TABLE_PREFIX."g_refs R
	where
		G.g = R.g_id
		AND
		course_id='$_SESSION[course_id]'";

	if(!$result8 = mysql_query($sql8,$db)){
		echo "query failed";
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}else{

		$title_refs = array();
		while ($row= mysql_fetch_array($result8)) {
			$title_refs2[$row['g']] = $row['reference'];

		}
	}
	//get the translations for the content id numbers
	$sql7 = "select
			C.title,
			C.content_id

		from
			".TABLE_PREFIX."content C

		where
			course_id='$_SESSION[course_id]'";
	if(!$result7 = mysql_query($sql7,$db)){
		echo "query failed";
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$title_refs = array();
	while ($row= mysql_fetch_array($result7)) {
		$title_refs[$row['content_id']] = $row['title'];

	}

	$name=ereg_replace(" ", "_", $_SESSION['course_title']);
	$name=ereg_replace("'", "", $name);
	header('Content-Type: text/csv');
	header('Content-Disposition: inline; filename="'.$name.'_tracking.csv"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$sqlall="select * from ".TABLE_PREFIX."g_click_data where course_id='$_SESSION[course_id]'";

	$result_all=mysql_query($sqlall, $db);
	$num_fields = mysql_num_fields($result_all);
	for($i=0; $i<$num_fields; $i++){
		if($i==($num_fields-1)){
			$this_row .= mysql_field_name($result_all,$i);
		}else{
			$this_row .= mysql_field_name($result_all,$i).',';
		}

	}
	$this_row .= "\n";
	while($row=mysql_fetch_array($result_all)){
		$this_row .= quote_csv($this_user[$row['member_id']]).",";
		$this_row .= quote_csv($row['course_id']).",";
		if($row['from_cid']=='' || $row['from_cid'] == '0'){
			$this_row .= '"0",';
		}else if ($title_refs[$row['from_cid']] == ''){
			$this_row .= quote_csv(_AT('deleted')).',';
		}else if ($title_refs[$row['from_cid']] != ''){
			$this_row .= quote_csv($title_refs[$row['from_cid']]).",";
		}else{
			$this_row .= '"0",';
		}
		if($row['to_cid']=='' || $row['to_cid'] == '0'){
			$this_row .= '"0",';
		}else if($title_refs[$row['to_cid']] == ''){
			$this_row .= quote_csv(_AT('deleted')).',';
		}else if($title_refs[$row['to_cid']] != ''){
			$this_row .= quote_csv($title_refs[$row['to_cid']]).",";
		}else{
			$this_row .= '"0",';
		}

		$this_row .= quote_csv(_AT($title_refs2[$row['g']])).",";
		$this_row.= AT_date(_AT('forum_date_format'),$row['timestamp'],	AT_DATE_UNIX_TIMESTAMP).",";
		$this_row.= quote_csv($row['duration'])."\n";

	}
	if (!@opendir(AT_CONTENT_DIR . 'export/')){
		mkdir(AT_CONTENT_DIR . 'export/', 0777);
	}

	$fp = @fopen(AT_CONTENT_DIR . 'export/'.$name.'_tracking.csv', 'w');
	if (!$fp) {
		$errors=array('CSV_FAILED', $name);
		$msg->printErrors($errors);
		exit;
	}
	@fputs($fp, $this_row); @fclose($fp);
	@readfile(AT_CONTENT_DIR . 'export/'.escapeshellcmd($name).'_tracking.csv');
	@unlink(AT_CONTENT_DIR . 'export/'.escapeshellcmd($name).'_tracking.csv');
	exit;


}

///////
require(AT_INCLUDE_PATH.'header.inc.php');

// Give the user two chances when deleting tracking data
if ($_GET['reset']==1){
	echo '<a name="warning"></a>';
	$warnings=array('DELETE_TRACKING', $_SERVER['PHP_SELF']);
	$msg->printWarnings($warnings);
	
	/* Since we do not know which choice will be taken, assume it No/Cancel, addFeedback('CENCELLED)
	 * If sent to results.php then OK, else if sent back here & if $_GET['reset']=2 then assumed choice was not taken
	 * ensure that addFeeback('CANCELLED') is properly cleaned up, see below
	 */
	$msg->addFeedback('CANCELLED');
	echo '<center><a href="'.$_SERVER['PHP_SELF'].'?reset=2">'._AT('yes_delete').'</a> | <a href="'.$_SERVER['PHP_SELF'].'">'._AT('no').'</a></center>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
} else if($_GET['reset']==2) {
	/* We must ensure that any previous feedback is flushed, 
	 * since AT_FEEDBACK_CANCELLED might be present
	 * if Yes/Delete was chosen above when reset was = 1
	 */
	$msg->deleteFeedback('CANCELLED'); // makes sure its not there 
		
	$sql_delete= "delete from ".TABLE_PREFIX."g_click_data where course_id='$_SESSION[course_id]'";
	if ($result_delete_track=mysql_query($sql_delete, $db)){
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	} else {
		$msg->addError('TRACKING_NOT_DELETED');
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}
/////////////////////////////
// Top of the page
$msg->printAll();

//This page is only for instructor/owners
if(!authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)){
	$msg->printInfos('NO_PERMISSION');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

//}
//see if tracking is turned on
$sql="SELECT tracking from ".TABLE_PREFIX."courses where course_id=$_SESSION[course_id]";
$result=mysql_query($sql, $db);
while($row= mysql_fetch_array($result)){
	if($row['tracking'] == "off"){
		if(authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)){
			$msg->addInfo('TRACKING_OFFIN');
		}else{
			$msg->addInfo('TRACKING_OFFST');
		}
	$msg->printInfos();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
	}
}

$msg->printWarnings();

?>
	<ul>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?stats=summary#show_pages"><?php echo _AT('g_show_page_stats'); ?></a>
	<br /><?php echo _AT('g_show_page_stats_desc'); ?>
	</li>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?stats=student#show_members"><?php echo _AT('g_show_member_stats'); ?></a>
	<br /><?php echo _AT('g_show_member_stats_desc'); ?>
	</li>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?csv=1"><?php echo _AT('g_download_tracking_csv'); ?></a>
	<br /><?php echo _AT('g_download_tracking_csv_desc'); ?>
	</li>
	<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?reset=1#warning"><?php echo _AT('g_reset_tracking'); ?></a>
	<br /><?php echo _AT('g_reset_tracking_desc'); ?>
	</li>
	</ul>

<hr />
<?php

// present the id picker
if($_GET['stats']=='student' || $_GET['member_id']){

	$sql = "select DISTINCT member_id from ".TABLE_PREFIX."g_click_data where course_id='$_SESSION[course_id]' order by member_id DESC";
	$result = mysql_query($sql, $db);

	//get the course enrollment
	$sql2="select * from ".TABLE_PREFIX."course_enrollment where course_id='$_SESSION[course_id]' AND approved='y'";
	$result2 = mysql_query($sql2, $db);
	while($row2 = mysql_fetch_array($result2)){
		$enrolled[$row2['member_id']] = $row2['member_id'];
	}
	?>
	<a name="show_members"></a>
	<table class="data static" rules="cols" summary="">
	<thead>
	<tr>
		<th><?php echo _AT('select_member'); ?></th>
	</tr>
	</thead>

	<tbody>
	<tr>
		<td><?php
			if($_GET['summary2'] == "summary") {
				$select_summary = ' checked="checked"';
			} else if($_GET['summary2'] == "raw") {
				$select_raw = ' checked="checked"';
			}
		?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>#show_members" method="GET">
	<input type="radio" name="summary2" id="summary2" value="summary" <?php echo $select_summary;  ?>><label for="summary2"><?php echo _AT('summary');  ?> </label>
	<input type="radio" id="summary" name="summary2" value="raw" <?php echo $select_raw;  ?>><label for="summary"><?php echo _AT('raw');  ?> </label>
	<select name="member_id">
	<?php
	while($row = mysql_fetch_array($result)){
		if($row['member_id'] == $enrolled[$row['member_id']]){

			echo '<option  value="'.$row['member_id'].'" ';
			if($_GET['member_id']==$row['member_id']){
				echo ' selected="selected"';
			}
			echo '>'.$this_user[$row['member_id']].'</option>'."\n";
		}
	}
	?>
	</select>
	<input type="submit" value="<?php echo _AT('view_tracking');  ?>" class="button" />
	</form>
	</td>
	</tr>
	</tbody>
	</table>
<?php
}

if($_GET['stats'] =="details" || $_GET['stats'] == "summary" || $_GET['g_id'] || $_GET['csv']== 1) {
	require(AT_INCLUDE_PATH.'lib/tracker_stats.inc.php');
}

else if ($_GET['summary2'] == "summary") {
	require(AT_INCLUDE_PATH.'lib/tracker_stats2.inc.php');
} 

else {
	require(AT_INCLUDE_PATH.'lib/tracker.inc.php');
}

	require(AT_INCLUDE_PATH.'footer.inc.php');

	
function quote_csv($line) {
	$line = str_replace('"', '""', $line);

	$line = str_replace("\n", '\n', $line);
	$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);

	return '"'.$line.'"';
}
?>