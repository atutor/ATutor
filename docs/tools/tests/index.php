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
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('test_manager');

global $savant;
$msg =& new Message($savant);

authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_TEST_MARK);


require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif"  class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
}
echo '</h2>';

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/test-manager-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('test_manager');
}

/* this session thing is a hack to temporarily prevent the en/dis editor link from affecting 'add poll' */
$old = $_SESSION['prefs']['PREF_EDIT'];
$_SESSION['prefs']['PREF_EDIT'] =1;

unset($editors);
$editors[] = array('priv' => AT_PRIV_TEST_CREATE, 'title' => _AT('add_test'), 'url' => 'tools/tests/add_test.php');
print_editor($editors , $large = false);
$_SESSION['prefs']['PREF_EDIT'] = $old;
echo '</h3>';

$msg->printAll();

/* get a list of all the tests we have, and links to create, edit, delete, preview */

$sql	= "SELECT *, UNIX_TIMESTAMP(start_date) AS us, UNIX_TIMESTAMP(end_date) AS ue FROM ".TABLE_PREFIX."tests WHERE course_id=$_SESSION[course_id] ORDER BY start_date DESC";
$result	= mysql_query($sql, $db);
$num_tests = mysql_num_rows($result);

if ($num_tests == 0) {
	echo '<p><em>'. _AT('no_tests') . '</em></p>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
?>

<br /><table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr>
	<th colspan="100%" class="cyan"><?php echo _AT('tests'); ?></th>
</tr>
<tr>
	<th scope="col" class="cat"><small><?php echo _AT('status'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('title'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('availability'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('test_type'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('questions'); ?></small></th>
	<?php $cols=6;
if (authenticate(AT_PRIV_TEST_MARK, AT_PRIV_RETURN)) {
	echo '<th scope="col" class="cat"><small>'._AT('results').'</small></th>';
	$cols++;
}	
if (authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_RETURN)) {
	echo '<th scope="col" class="cat"><small>'._AT('edit').' &amp; '._AT('delete').'</small></th>';
	$cols++;
}
echo '</tr>';

while ($row = mysql_fetch_assoc($result)) {
	$count++;
	echo '<tr>';
	echo '<td class="row1"><small>';
	if ( ($row['us'] <= time()) && ($row['ue'] >= time() ) ) {
		echo '<em>'._AT('ongoing').'</em>';
	} else if ($row['ue'] < time() ) {
		echo '<em>'._AT('expired').'</em>';
	} else if ($row['us'] > time() ) {
		echo '<em>'._AT('pending').'</em>';
	}
	echo '</small></td>';
	echo '<td class="row1"><small>'.$row['title'].'</small></td>';
	echo '<td class="row1"><small>'.AT_date('%j/%n/%y %G:%i', $row['start_date'], AT_DATE_MYSQL_DATETIME).'<br />'._AT('to_2').' ';
	echo AT_date('%j/%n/%y %G:%i', $row['end_date'], AT_DATE_MYSQL_DATETIME).'</small></td>';

	/* avman */				
	echo '<td class="row1"><small>';

	if ($row['automark'] == AT_MARK_INSTRUCTOR) {
		echo _AT('mark_instructor');
	} else if ($row['automark'] == AT_MARK_SELF) {
		echo _AT('self_marking');
	} else if ($row['automark'] == AT_MARK_UNMARKED) {
		echo _AT('dont_mark');
	}

	echo '<br />';
	echo '</small></td>';

	echo '<td class="row1"><small>';

	if (authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_RETURN)) {
		$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."tests_questions WHERE test_id=$row[test_id]";
		$result2= mysql_query($sql, $db);
		$row2	= mysql_fetch_array($result2);
		echo '&middot; <a href="tools/tests/questions.php?tid='.$row['test_id'].'">'.$row2[0]. ' '._AT('questions').'</a>';
		echo '<br />';
	}

	/************************/
	/* Preview				*/
	echo '&middot; <a href="tools/tests/preview.php?tid='.$row['test_id'].'">'._AT('preview').'</a>';
	echo'</small></td>';

	if (authenticate(AT_PRIV_TEST_MARK, AT_PRIV_RETURN)) {

			/************************/
			/* Unmarked				*/
			echo '<td class="row1"><small>';				
							
			$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[test_id] AND final_score=''";
			$result2= mysql_query($sql, $db);
			$row2	= mysql_fetch_array($result2);

			if ($row2[0] > 0) {
				$title = $row2[0].' '._AT('unmarked');
				$row2[0] = ' ('.$row2[0].')';
			} else {
				$row2[0] = '';
				$title   = '';
			}


			echo '&middot; <a href="tools/tests/results.php?tid='.$row['test_id'].'" title="'.$title.'">'._AT('submissions') . $row2[0];
			echo '</a><br />';								
			
			
			/************************/
			/* Results				*/			
			$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[test_id] AND final_score<>''";
			$result2= mysql_query($sql, $db);
			$row2	= mysql_fetch_array($result2);
			echo '&middot; <a href="tools/tests/results_all_quest.php?tid='.$row['test_id'].'">'._AT('statistics').'</a>';			
			echo '</small></td>';	

	}
	/************************/
	/* Edit/Delete			*/
	if (authenticate(AT_PRIV_TEST_CREATE, AT_PRIV_RETURN)) {
		echo '<td class="row1"><small>&middot; <a href="tools/tests/edit_test.php?tid='.$row['test_id'].'">'._AT('edit').'</a><br />&middot; <a href="tools/tests/delete_test.php?tid='.$row['test_id'].'">'._AT('delete').'</a></small></td>';
	}
	echo '</tr>';

	if ($count < $num_tests) {
		echo '<tr><td height="1" class="row2" colspan="'.$cols.'"></td></tr>';
	}
}

echo '</table>';
echo '<br />';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>