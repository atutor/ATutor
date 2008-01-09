<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
/////////////////////////////
//Display the g_data bar chart for the member selected
if (!defined('AT_INCLUDE_PATH')) { exit; }

// NOTE: this script should not be altered. its use will soon be deprecated.


//get the summary data for all pages

//get the translations for the g numbers
$to_cid = $_GET['to_cid'];
$stats = $_GET['stats']; 
$g_id = $_GET['g_id'];
$sql5 = "select * from ".TABLE_PREFIX."g_refs";
	$result = mysql_query($sql5, $db);
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

	if(!$result8 = mysql_query($sql8, $db)){
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}else{

		$title_refs = array();
		while ($row= mysql_fetch_assoc($result8)) {
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
	if(!$result7 = mysql_query($sql7, $db)){
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$title_refs = array();
	while ($row= mysql_fetch_array($result7)) {
		$title_refs[$row['content_id']] = $row['title'];

	}

//get tools ATutor tools traffic

$sql9="SELECT 
			G.to_cid,
			G.g,
			R.g_id,
			R.reference

		from
			".TABLE_PREFIX."g_click_data G,
			".TABLE_PREFIX."g_refs R
		where
			G.to_cid = 0
			AND
			course_id='$_SESSION[course_id]'";
	$title_tools = array();
	$result9 = mysql_query($sql9, $db);
	while ($row= mysql_fetch_array($result9)) {
			if($row['g'] == $row['g_id']){
				$title_tools[$row['g_id']] = $row['reference'];
				$tool_grefs[$row['g_id']] = $row['g_id'];
				$gcount[$row['g_id']]++;
			}
	}
$sql10 = "select count(g) from ".TABLE_PREFIX."g_click_data where course_id='$_SESSION[course_id]' GROUP BY g";
$result10 = mysql_query($sql10, $db);
while($row=mysql_fetch_array($result10)){
	$thiscount[]=$row;

}

if($_GET['stats']="summary" && !$to_cid &&!$_GET['csv'] && !$_GET['g_id']){

	$sql12= "select to_cid, g, AVG(duration) AS t, count(g) as c from ".TABLE_PREFIX."g_click_data where to_cid='0' AND course_id='$_SESSION[course_id]' GROUP BY g";

	if($result12=mysql_query($sql12, $db)){
		while($row=mysql_fetch_array($result12)){
			if($row['g']){
				$nav_total = ($nav_total + $row['c']);
			}
			if($row['to_cid']==0){
				$that_time[$row['g']]= $row['t'];
			}
		}
	}else{
		echo _AT('unknown_error');
	}
?>
	<br />
	<a name="show_pages"></a>
	<h3><?php  echo  _AT('tool_summary'); ?></h3>
	<table class="data static" rules="cols" summary="">
	<thead>
	<tr>
		<th><?php echo _AT('at_tools');     ?></th>
		<th><?php echo _AT('hit_count');    ?></th>
		<th><?php echo _AT('avg_duration'); ?></th>
		<th><?php echo _AT('details');      ?></th>
	</tr>
	</thead>

	<tbody>
<?php
		//this array needs to be created from the database 
		//(eventually add new field to g_refs table called "timed" values true/false
		$timed_tools=array(14=>14, 15=>15, 16=>16, 17=>17, 18=>18, 20=>20, 21=>21, 23=>23, 27=>27, 28=>28, 29=>29, 31=>31, 32=>32, 35=>35);
		
		foreach($title_tools as $key=>$value) {
			$tool_names[$key] = $gcount[$key];
		}

		if (is_array($tool_names)) {
			arsort($tool_names);
		
			foreach($tool_names as $key=>$value) {
				echo '<tr>';
					echo '<td>' . _AT($title_tools[$key]) . '</td>';
					echo '<td><img src="images/bar.gif" height="12" width="' . ((($gcount[$key]/$nav_total)*100)*2) . '" alt="" />' . $value . '</td>';

				$that_avgtime='';
				if($timed_tools[$key]==$key) {  
					$that_avgtime=number_format((number_format($that_time[$key], 1  )/$gcount[$key]),1);
				}

					echo '<td>';
					if($that_avgtime) {
						echo $that_avgtime;
					} else {
						echo _AT('na');
					}
					echo '</td>';

					echo '<td><a href="' . $_SERVER['PHP_SELF'] . '?g_id=' . $key . '#show_pages">' . _AT('details') . '</a></td>';
				echo '</tr>';
			}
		}
?>
	</tbody>
	</table>

	<br /><br />

	<h3><?php  echo  _AT('page_stats'); ?></h3>
	<table class="data static" rules="cols" summary="">
	<thead>
	<tr>
		<th><?php echo _AT('page_title');   ?></th>
		<th><?php echo _AT('hit_count');    ?></th>
		<th><?php echo _AT('avg_duration'); ?></th>
		<th><?php echo _AT('details');      ?></th>
	</tr>
	</thead>
<?php
	//get content page traffic
	$sql6 = "SELECT G.to_cid, count(*) AS pages, G.g
		FROM ".TABLE_PREFIX."g_click_data G
		WHERE G.to_cid <> 0	AND	course_id='$_SESSION[course_id]'
		GROUP BY G.to_cid";

	$result6 = mysql_query($sql6, $db);

	if(!$result6) {
		echo "query failed";
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	$sql11 = "SELECT to_cid, AVG(duration) AS t FROM ".TABLE_PREFIX."g_click_data WHERE course_id='$_SESSION[course_id]' GROUP BY to_cid";
	$result11 = mysql_query($sql11, $db);

	if ($result11) {
		while($row = mysql_fetch_array($result11)) {
			$this_time[$row['to_cid']]= $row['t'];
		}
	} 
	
	else {
		echo _AT('unknown_error');
	}

	$max_bar_width='180';
	$result9 = mysql_query($sql6, $db);
	
	while($row = mysql_fetch_array($result9)) {
		$total_hits=($total_hits + $row["pages"]);
	}
	if($total_hits) {
		$bar_factor = ($max_bar_width/$total_hits);
	}

	if ($result6 = mysql_query($sql6, $db)) {

		echo '<tbody>';

		while($row = mysql_fetch_array($result6)) {
			if($title_refs[$row['to_cid']] != '') {
				echo '<tr>';
					echo '<td>' . $title_refs[$row['to_cid']] . '</td>';
					echo '<td><img src="images/bar.gif" height="12" width="' . ($row["pages"]*$bar_factor) . '" alt="" />' . $row["pages"] . '</td>';

					$this_avgtime=(number_format($this_time[$row['to_cid']], 1  )/$row["pages"]);

					echo '<td>' . number_format($this_avgtime, 1) . '</td>';
					echo '<td><a href="' . $_SERVER['PHP_SELF'] . '?stats=details' . SEP . 'to_cid=' . $row['to_cid'] . '#show_pages">' . _AT('details') . '</a></td>';
				echo '</tr>';

			}
		}
		echo '<tbody>';
	}

	echo '</table>';
}  //end summary

//get the rawdata for a single page
if(authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)){
	$sql3="select
		".TABLE_PREFIX."content.title,
		".TABLE_PREFIX."content.content_id,
		".TABLE_PREFIX."g_click_data.member_id as m,
		".TABLE_PREFIX."g_click_data.to_cid,
		".TABLE_PREFIX."g_click_data.g,
		".TABLE_PREFIX."g_click_data.timestamp AS t
	from
		".TABLE_PREFIX."content,
		".TABLE_PREFIX."g_click_data
	where
		".TABLE_PREFIX."content.content_id=".TABLE_PREFIX."g_click_data.to_cid
		AND
		".TABLE_PREFIX."g_click_data.to_cid=$to_cid
		AND
		".TABLE_PREFIX."g_click_data.course_id=$_SESSION[course_id]";



	$result3=mysql_query($sql3, $db);
	if($result3){
		while($row=mysql_fetch_array($result3)){
			$this_data[$row["t"]]= $row;
			$this_user[$row["t"]]= $row['m'];
		}
		ksort($this_data);
		$current = current($this_data);
		$pre_time = $current[t];

	}

}


if($to_cid) {
	?>
	<a name="show_pages"></a>
	<p>
		[<a href="<?php echo $_SERVER['PHP_SELF'].'?stats=summary';?>#show_pages"><?php echo _AT('back_to_summary'); ?></a>]
	</p>

	<h3><?php echo _AT('access_stats'); ?>: <?php echo $current['title']; ?></h3>


	<table class="data static" rules="cols" summary="">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('access_method'); ?></th>
		<th scope="col"><?php echo _AT('count'); ?></th>
	</tr>
	</thead>

<?php
	//get the number of clicks per g
	$sql2 = "select
			g,
			count(*) AS cnt
		from
			".TABLE_PREFIX."g_click_data
		where
			to_cid=$to_cid
			AND
			course_id='$_SESSION[course_id]'
		group by
			 g";
	
	if($result2 = mysql_query($sql2, $db)){
		echo '<tbody>';
		while($row = mysql_fetch_array($result2)){
			echo '<tr>';
			echo '<td>';
			foreach($refs AS $key => $value){
				if($key==$row["g"]){
					echo _AT($value);
				}
			}
			echo '</td>';
			echo '<td><img src="images/bar.gif" height="12" width="' . ($row["cnt"]*2) . '" alt="" />' . $row["cnt"] . '</td>';
			echo '</tr>';
		}

	}
	echo '</tbody>';
	echo '</table>';
	echo '<br />';

	//////////////
	$sql4="select
		".TABLE_PREFIX."g_click_data.g,
		".TABLE_PREFIX."g_click_data.member_id AS m,
		".TABLE_PREFIX."g_click_data.to_cid,
		".TABLE_PREFIX."g_click_data.timestamp AS t
	from
		".TABLE_PREFIX."g_click_data
	where
		".TABLE_PREFIX."g_click_data.to_cid=0
		AND
		".TABLE_PREFIX."g_click_data.to_cid=$to_cid
		AND
		".TABLE_PREFIX."g_click_data.course_id=$_SESSION[course_id]
		GROUP BY 
		m
		";
	$result4 = mysql_query($sql4, $db);

	if($result4){

		if($this_data){
			echo '<br />';
			echo '<a name="show_pages"></a>';
			echo '<h3>'._AT('pages_stats', $current["title"]).'</h3>';
		
			echo '<table class="data static" rules="cols" summary="">';
			echo '<thead>';
			echo '<tr>';
				echo '<th scope="col">' . _AT('access_method') . '</th>';
				echo '<th scope="col">' . _AT('duration_sec')  . '</th>';
				echo '<th scope="col">' . _AT('date')          . '</th>';
				echo '<th scope="col">' . _AT('student_id')    . '</th>';
			echo '</tr>';
			echo '<thead>';			
			echo '<tbody>';
			foreach($this_data AS $key => $value){
				if(!$start_date){
					$start_date=$pre_time;
				}
				$diff = abs($value[t] - $pre_time);
				if ($diff > 60*45) {
					$end_date=$value[t];
					echo '<tr>';
					echo '<td>';
					if($start_date>0 && $start_date!=$pre_time){
						echo _AT('session_start').' '.date("F j, Y,  g:i a", $start_date).' '._AT('session_end').' '.date("F j, Y,  g:i a", $pre_time).'     ('._AT('duration').':'.date('i \m\i\n s \s\e\c',($pre_time-$start_date)).')';
						
						echo '</td>';
						echo '</tr>';
					}
					else if($value[g]==19) {
						//don't do anything if its a logout
					} 
					else {
						echo _AT('invalid_session');
					}
					$start_date='';
				}

				else {
					if (!$start_date) {
						$start_date=$value[t];
					}
				}
				echo '<tr>';
				echo '<td>';
				$that_g=$refs[$value['g']];
				echo _AT($that_g);
				echo '</td>';
				echo '<td>';

				if ($diff > 60*45) {
					echo _AT('na');
					$session_time='';

				}else{
					$this_time=date('i.s', $diff);
					echo ' '.$this_time;
					$session_time=($session_time+$diff);
				}
				$remainder = $diff / 60;
				echo '</td>';
				echo '<td>';
				echo $that_date;
				echo '</td>';
				echo '<td>'.$this_user[$value['m']].'</td>';
				echo '</tr>';
				$that_date=date("M-j-y g:i:s:a", $value[t]);
				$that_title=$value[title]."&nbsp;";
				$pre_time = $value['t'];
			}
			echo '</tbody>';
			echo '</table>';
		}
	}
}  /// end page detail

if($_GET['g_id']){
	$sql14 = "select member_id, login, first_name, last_name from ".TABLE_PREFIX."members";
	$result14=mysql_query($sql14, $db);
	while($row=mysql_fetch_array($result14)){
		if($row['first_name'] && $row['last_name']){
			$this_user[$row['member_id']]= $row['first_name'].' '. $row['last_name'];
		}else{
			$this_user[$row['member_id']]= $row['login'];
		}

	}
	$sql13 = "select *, timestamp as t from ".TABLE_PREFIX."g_click_data where to_cid='0' AND g='$_GET[g_id]' AND course_id='$_SESSION[course_id]'";
	$result13 = mysql_query($sql13, $db);
	echo '<a name="show_pages"></a>';
	echo '<h3>'._AT('tools_details').' ('._AT($title_refs2[$g_id]).')</h3>';
	echo '<p>[<a href="'.$_SERVER['PHP_SELF'].'?stats=summary#show_pages">'._AT('back_to_summary'),'</a>]</p>';
	
	echo '<table class="data static" rules="cols" summary="">';
	echo '<thead>';
	echo '<tr>';
		echo '<th scope="col">' . _AT('origin_page')  . '</th>';
		echo '<th scope="col">' . _AT('duration_sec') . '</th>';
		echo '<th scope="col">' . _AT('date')         . '</th>';
		echo '<th scope="col">' . _AT('student_id')   . '</th>';
	echo '</tr>';
	echo '</thead>';

	echo '<tbody>';

	while ($row=mysql_fetch_array($result13)){
		echo '<tr>';
		if ($row['from_cid'] == 0) {
			echo '<td>'._AT($title_refs2[$row['g']]).'</td>';
		} 
		else if ($title_refs[$row['from_cid']] != '') {
			echo '<td>'.$title_refs[$row['from_cid']].'</td>';

		}

		if ($title_refs[$row['from_cid']] != '' || $row['from_cid'] == 0) {
			echo '<td>'.$row['duration'].'</td>';
			echo '<td>'.date("M-j-y g:i:s:a",$row['t'] ).'</td>';
			echo '<td>'.$this_user[$row['member_id']].'</td>';
		}
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
}
?>