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
/////////////////////////////
//Display the g_data bar chart for the member selected
//get the translations to the gdata numbers first
if (!defined('AT_INCLUDE_PATH')) { exit; }

// NOTE: this script should not be altered. its use will soon be deprecated.


$sql5 = "select * from ".TABLE_PREFIX."g_refs";
$result = mysql_query($sql5, $db);
$refs = array();
while ($row= mysql_fetch_array($result)) {
	$refs[$row['g_id']] = $row['reference'];
}
/* this if-statement doesn't make any sense: */
if ($_GET['member_id']){
	$this_member = $_GET['member_id']; 
} else {
	$this_member=$_SESSION['member_id'];
	if(!authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)){
		$_GET['member_id'] = $_SESSION['member_id'];
	}
}

$sql2 = "SELECT	g, count(*) AS cnt
		FROM ".TABLE_PREFIX."g_click_data
		WHERE member_id=$this_member AND course_id='$_SESSION[course_id]'
		GROUP BY g
		ORDER BY cnt DESC";

if ($result7 = mysql_query($sql2, $db)) {
	while($row2 = mysql_fetch_array($result7)) {
			$nav_total = ($nav_total + $row2["cnt"]);
	}
}

if (($result = mysql_query($sql2, $db)) && $_GET['member_id']) {
	echo '<h3>'._AT('nav_tendencies').' '.$this_user[$this_member].'</h3>';

	echo '<table class="data static" rules="cols" summary="">';
	echo '<thead>';
	echo '<tr>';
		echo '<th>' . _AT('access_method') . '</th>';
		echo '<th>' . _AT('count') . '</th>';
	echo '</tr>';
	echo '</thead>';

	echo '<tbody>';
	while($row = mysql_fetch_array($result)){
		echo '<tr>';
		echo '<td>';
		foreach($refs AS $key => $value){
			if($key==$row["g"]){
				echo _AT($value);
			}
		}
		echo '</td>';
		
		echo '<td><img src="images/bar.gif" height="12" width="'.((($row["cnt"]/$nav_total)*100)*3).'" alt="" />' . $row["cnt"] . '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '<br /><br />';


////////////////////////////
//Show the member's click path
	echo '<a name="access"></a>';
	echo '<h3>'._AT('nav_path').' '.$this_user[$this_member].'</h3>';
	echo '<table class="data static" rules="cols" summary="">';
	echo '<thead>';
	echo '<tr>';
		echo '<th>' . _AT('access_method') . '</th>';
		echo '<th>' . _AT('page_viewed')   . '</th>';
		echo '<th>' . _AT('duration')      . '</th>';
		echo '<th>' . _AT('date')          . '</th>';
	echo '</tr>';
	echo '</thead>';

	if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."g_click_data WHERE course_id=$_SESSION[course_id] AND member_id='$_GET[member_id]'";
	}
	else {
		$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."g_click_data WHERE course_id=$_SESSION[course_id] AND member_id='$_SESSION[member_id]'";
	}
	
	//create the paginator
	if (!$result = mysql_query($sql, $db)) {
		echo _AT('page_error');
	} else {
		$num_rows = mysql_fetch_assoc($result);
		
		$sql3="SELECT 
				".TABLE_PREFIX."content.title,
				".TABLE_PREFIX."content.content_id,
				".TABLE_PREFIX."g_click_data.to_cid,
				".TABLE_PREFIX."g_click_data.g,
				".TABLE_PREFIX."g_click_data.duration,
				".TABLE_PREFIX."g_click_data.timestamp AS t
			FROM
				".TABLE_PREFIX."content, 
				".TABLE_PREFIX."g_click_data
			WHERE 
				".TABLE_PREFIX."content.content_id=".TABLE_PREFIX."g_click_data.to_cid
				AND
				".TABLE_PREFIX."g_click_data.member_id=$this_member
				AND
				".TABLE_PREFIX."g_click_data.course_id=$_SESSION[course_id]";
		
		$sql4="select
				".TABLE_PREFIX."g_click_data.g,
				".TABLE_PREFIX."g_click_data.member_id, 
				".TABLE_PREFIX."g_click_data.to_cid, 
				".TABLE_PREFIX."g_click_data.duration,
				".TABLE_PREFIX."g_click_data.timestamp AS t
			from 
				".TABLE_PREFIX."g_click_data 
			where 
				".TABLE_PREFIX."g_click_data.to_cid=0 
				AND
				".TABLE_PREFIX."g_click_data.member_id=$this_member
				AND
				".TABLE_PREFIX."g_click_data.course_id=$_SESSION[course_id]
			order by
				t DESC";
		
		if ($result=mysql_query($sql3, $db)) {
			while($row=mysql_fetch_assoc($result)){
				$this_data[$row['t']]= $row;
				$page_rows++;
			}
		}
		//$num_records = count($this_data);
		if ($result2 = mysql_query($sql4, $db)) {
			while ($row=mysql_fetch_assoc($result2)) {
				$row['title'] = $refs[$row['g']];
				$this_data[$row['t']] = $row;
				$tool_rows++;
			}
		}
				
		$num_records = ($num_records+count($this_data));	
		//$num_records = $num_rows['cnt'];

		$num_per_page = 30;
		if (!$_GET['page']) {
			$page = 1;
		} else {
			$page = intval($_GET['page']);
		}
		$start = ($page-1)*$num_per_page;
		$num_pages = ceil($num_records/$num_per_page);
		echo '<tbody>';
		echo '<tr>';
		echo '<td>'._AT('page').': ';
			for ($i=1; $i<=$num_pages; $i++) {
				if ($i == $page) {
					echo $i;
				} else {
					echo '<a href="' . $_SERVER['PHP_SELF'] . '?coverage=raw' . SEP . 'member_id=' . $_GET["member_id"] . SEP . 'page=' . $i . '#access">' . $i . '</a>';
				}

				if ($i<$num_pages){
					echo ' <span class="spacer">|</span> ';
				}
			}
		echo '</td>';
		echo '</tr>';
	}

if($this_data){
	ksort($this_data);
	$current = current($this_data);
	$pre_time = $current[t];
	$q = '';
	foreach ($this_data AS $key => $value) {
		$this_page = $p;
		if ($q >= $start && $q < ($start+$num_per_page)) {
			$diff = $value['duration']; // - $pre_time);
			$that_g = $refs[$value['g']];
	
			if ($that_g != '') {
				echo '<tr>';
				if ($that_g == _AT('g_session_start')) {
					echo '<td>';
				} 
				else {
					echo '<td>';
				}

				echo _AT($that_g);
				echo '</td>';

				if ($that_g == _AT('g_session_start')) {
					echo '<td>';
				} else {
					echo '<td>';
				}

				if (substr($value['title'], 0 ,2) == "g_" ) {
					echo _AT($value['title']);
				}
				else {
					echo $value['title'];
				}
				
				echo '</td>';
				if ($that_g == _AT('g_session_start')) {
					echo '<td>';
				}else{
					echo '<td>';
				}
	
				if ($diff > 60*45) {
					/* time out */
					echo _AT('na');
					$session_time='';
				} else {
					$this_time=date('i:s', $diff);
					echo ' '.$this_time;
					$session_time=($session_time+$diff);
				}
				$remainder = $diff / 60;
				echo '</td>';

				if ($that_g == _AT('g_session_start')) {
					echo '<td>';
				} else {
					echo '<td>';
				}

				echo $that_date;
				echo '</td>';
				echo '</tr>';
			}
			
			}

		$that_date=date("M-j-y g:i:s:a", $value[t]);
		$that_g=$refs[$value['g']];
		$that_title=$value['title']."&nbsp;";
		$pre_time = $value['t'];
		$q++;
		}
}
	echo '<tr>';
	echo '<td>';

	if ($start_date>0 && $start_date!=$pre_time) {
		echo _AT('g_session_start').' '.date("F j, Y,  g:i a", $start_date).' '._AT('session_end').' '.date("F j, Y,  g:i a", $pre_time).'     ('._AT('duration').':'.date('i \m\i\n s \s\e\c',($pre_time-$start_date)).')';
	}

	else {
		//echo _AT('invalid_session');
	}
	echo '</td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';
}
?>