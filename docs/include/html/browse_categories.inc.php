<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
$result = mysql_query($sql, $db);
if (mysql_num_rows($result) == 0) {
	$msg->addInfo('NO_CATEGORIES');
} else {
	while($row = mysql_fetch_assoc($result)){
		$current_cats[$row['cat_id']] = $row['cat_name'];
		$parent_cats[$row['cat_id']]  = $row['cat_parent'];
		$cat_cats[$row['cat_id']]     = $row['cat_id'];
	}
}

// count the number of courses in each category
$sql = "SELECT cat_id from ".TABLE_PREFIX."courses WHERE hide=0";
$result = mysql_query($sql, $db);

	while($row = mysql_fetch_array($result)){
		$cat_count[$row['cat_id']][$i] = $i++;
	}

function print_parent_cats($parent_cat_id, &$cats, $cat_row) {
	$my_cats = $cats[$parent_cat_id];
	$new_depth = ($old_depth - $depth);
	global $cat_count;
	echo '<ul>'."\n";
	foreach ($my_cats as $cat) {
		echo '<li><a href="'.$_SERVER['PHP_SELF'].'?current_cat='.$cat['cat_id'].SEP.'show_courses='.$cat['cat_id'].'">';
		if($cat['cat_id'] != $cat_row){
			echo $cat['cat_name'];
		}else{
			echo '<strong>'.$cat['cat_name'].'</strong>';
		}
		echo '</a> <small>';
		echo '( '.count($cat_count[$cat['cat_id']]).' )</small>';

		if (is_array($cats[$cat['cat_id']]) && ($cat['cat_id'] !== 0) ) {
			print_parent_cats($cat['cat_id'], $cats,  $cat_row, $depth+1);
		}
		echo '</li>'."\n";
	}
	echo '</ul>'."\n";
}

$cat_path_len = (strlen($current_cats[$_GET['prev_cat']]));
$path_len = strlen($_GET['cat_path']);
if($cat_path_len != ''){
	$cat_path_len = ($cat_path_len + 1);
}

if (!isset($_GET['current_cat'])) {
	$_GET['current_cat'] = 0;
}

if ($_GET['current_cat'] == 0){
	$_GET['cat_path'] = '';
}

$cat_path = $_GET['cat_path'];


$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
$result = mysql_query($sql, $db);

$msg->printErrors();

?>

<a href="<?php echo substr($_my_uri, 0, strlen($_my_uri)-1); ?>#category">
<img src="images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('cats_go_to_category'); ?>: ALT-c" /></a>

<table cellspacing="1" cellpadding="2" border="0" class="bodyline" summary="" width="95%" align="center">
<tr>
	<th width="50%" class="cyan"><?php echo _AT('cats_categories'); ?></th>
<?php
	echo '<th width="50%" class="cyan">'._AT('courses').': ';
	if($_GET['current_cat']){
		echo $current_cats[$_GET['current_cat']];
	}else if($_POST['cat_id']){
		echo $current_cats[$_POST['cat_id']];
	}else{
		echo  _AT('cats_uncategorized');
	}

	echo '</th>';
?>
</tr>
<tr>
	<td class="row1" width="50%" valign="top"><?php

if (is_array($current_cats)){
	$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
	$result4 = mysql_query($sql, $db);
	$cats = array();
	$cats[0][] = array('cat_id' => 0, 'cat_name' => _AT('cats_uncategorized'), 'cat_parent' => 0);
	while($row4 = mysql_fetch_assoc($result4)){
		$cats[$row4['cat_parent']][] = $row4;
	}
	$cat_row = $_GET['current_cat'];
	print_parent_cats(0, $cats, $cat_row);
	echo '</td>';
	echo '<td class="row1" valign="top" width="50%"> <a name="category"></a>&nbsp;';

	$sql= "SELECT * FROM ".TABLE_PREFIX."courses WHERE hide=0 AND cat_id=".$_GET['current_cat']." ORDER BY title";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) > 0) {
		if($_GET['current_cat'] == ''){
			$_GET['current_cat'] = 0;
		}
		echo ' <small>(<a href="'.$_SERVER['PHP_SELF'].'?current_cat='.$_GET['current_cat'].SEP.'this_category='.$_GET['current_cat'].SEP.'show_courses='.$_GET['current_cat'].'#browse_top">'._AT('browse_courses').'</a> )</small><br /><br />'."\n";

		echo '<ul>'."\n";
		while ($row = mysql_fetch_array($result)){
			echo '<li><a href="'.$_SERVER['PHP_SELF'].'?course='.$row['course_id'].SEP.'this_course='.$row['course_id'].SEP.'show_courses='.$show_courses.SEP.'current_cat='.$_GET['current_cat'].'#browse_top">'.$row['title'].'</a></li>'."\n";
		}
		while ($row = mysql_fetch_array($result)){
			echo '<li><a href="'.$_SERVER['PHP_SELF'].'?course='.$row['course_id'].SEP.'this_course='.$row['course_id'].SEP.'show_courses='.$show_courses.SEP.'current_cat='.$_GET['current_cat'].'#browse_top">'.$row['title'].'</a></li>'."\n";
		}
		echo '</ul>'."\n";
	} else {
		echo _AT('cats_no_course');
	}
	echo '</td>';
} else {
	//$infos[] = _AT('cats_no_categories');
	echo '<td class="row1">';
	$msg->printInfos();
	echo '</td>';
}
	echo '</tr>'."\n";
	echo '<tr><td height="1" class="row2" colspan="3">';
	if (file_exists(AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS1.0.xml")) {
		echo '&nbsp;<a href="'.$_base_href.'get_feed.php?course=0'.SEP.'type=browse_courses_feed'.SEP.'version=RSS1.0"><img src="'.$_base_href.'images/rss_feed1.jpg" alt="RSS1.0" border="0" /></a>';
	}
	if (file_exists(AT_CONTENT_DIR."feeds/0/browse_courses_feed.RSS2.0.xml")) {
		echo '&nbsp;<a href="'.$_base_href.'get_feed.php?course=0'.SEP.'type=browse_courses_feed'.SEP.'version=RSS2.0"><img src="'.$_base_href.'images/rss_feed.jpg" alt="RSS2.0" border="0" /></a>';
	}
	echo '</td></tr>'."\n";

	echo '</table>'."\n";

///////////////
// Display long version course list


if($_GET['this_course'] != '' || $_GET['this_category'] != '' ){
	echo '<br /><table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">'."\n";

	echo '<tr><th class="cyan" colspan="2"><a name="browse_top"></a>'._AT('courses').': ';

	if($_GET['current_cat'] == 0){
	echo  _AT('cats_uncategorized');
		echo $current_cats[$_GET['current_cat']];
	}else if($_GET['current_cat'] != ''){
		echo $current_cats[$_GET['current_cat']];
	}else if($_POST['cat_id'] != ''){
		echo $current_cats[$_POST['cat_id']];
	}

	echo '</th></tr>'."\n";
?>

	<tr>
		<th class="cat"><?php echo _AT('course_name'); ?></th>
		<th class="cat"><?php echo _AT('description'); ?></th>
	</tr>
<?php

	if($_GET['this_course'] != ''){
		$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE  hide=0 AND course_id = '$_GET[this_course]' ORDER BY title";
	}else if($_GET['this_category'] != ''){
		$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE  hide=0 AND cat_id = '$_GET[this_category]' ORDER BY title";
	}else{
		$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE  hide=0 ORDER BY title";
	}
	//echo $sql;
	$result = mysql_query($sql,$db);
	$num = mysql_num_rows($result);
	if ($row = mysql_fetch_array($result)) {
		do {
			echo '<tr><td class="row1" width="150" valign="top"><strong>';
			echo '<a href="bounce.php?course='.$row[course_id].'">'.$system_courses[$row[course_id]][title].'</a>';

			echo '</strong></td><td class="row1" valign="top">';
			echo '<small>';
			echo $row[description];

				echo '<br /><br />&middot; '. _AT('access').': ';
			$pending = '';
			switch ($row['access']){
				case 'public':
					echo _AT('public');
					break;
				case 'protected':
					echo _AT('protected');
					break;
				case 'private':
					echo _AT('private');
					break;
			}
			
			echo '<br />&middot; '. _AT('category').': ';
			if($row['cat_id'] != 0){
				echo $current_cats[$row['cat_id']];

			}else{
				echo _AT('cats_uncategorized');
			}
			$sql	  = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='y'";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_array($c_result);

			/* minus 1 because the instructor doesn't count */
			echo '<br />&middot; '._AT('enrolled').': '.max(($c_row[0]-1), 0).', ';


   			$sql	  = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='a'";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_array($c_result);
			echo _AT('alumni') . ': ' . $c_row[0] . '<br />';

			echo '&middot; '. _AT('created').': '.$row[created_date].'<br />';
			if ($_SESSION['valid_user'] === true) {
				echo '&middot; <a href="users/contact_instructor.php?course='.$row['course_id'].SEP.'from_browse=1">'._AT('contact_instructor').'</a><br />';
			}
			echo '&middot; <a href="'.$_base_path.'enroll_browse.php?course='.$row['course_id'].SEP.'browse=1">'._AT('enroll').'</a>';
			echo '</small></td>';
			echo '</tr>'."\n";
			if ($count < $num-1) {
				echo '<tr><td height="1" class="row2" colspan="3"></td></tr>'."\n";
			}
			$count++;
		} while ($row = mysql_fetch_array($result));
	} else {
		echo '<tr><td class=row1 colspan=3><i>'._AT('cats_no_course').'</i></td></tr>'."\n";
	}
	

	echo '</table>'."\n";
}
?>
