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
// $Id: grade_scale.php 7208 2008-05-28 16:07:24Z cindy $

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

require ('lib/gradebook.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table summary="" class="data" rules="cols" align="center" style="width: 70%;">

<thead>
<tr>
	<th scope="col"><?php echo _AT('title'); ?></th>
	<th scope="col"><?php echo _AT('your_mark'); ?></th>
	<th scope="col"><?php echo _AT('class_avg'); ?></th>
	<th scope="col"><?php echo _AT('due_date'); ?></th>
	<th scope="col"><?php echo _AT('completed_date'); ?></th>
	<th scope="col"><?php echo _AT('time_spent'); ?></th>
</tr>
</thead>

<tbody>
<?php

$sql = "(SELECT g.gradebook_test_id, g.id, type, t.title, t.end_date due_date, grade_scale_id, t.result_release ".
				" FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t".
				" WHERE g.type='ATutor Test'".
				" AND g.id = t.test_id".
				" AND t.course_id=".$_SESSION["course_id"].
				" ORDER BY t.title) ".
				" UNION (SELECT g.gradebook_test_id, g.id, g.type, a.title, a.date_due due_date, grade_scale_id, '' result_release ".
				" FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."assignments a".
				" WHERE g.type='ATutor Assignment'".
				" AND g.id = a.assignment_id".
				" AND a.course_id=".$_SESSION["course_id"].
				" ORDER BY title)";
				" UNION (SELECT gradebook_test_id, id, type, title, due_date, grade_scale_id, '' result_release ".
				" FROM ".TABLE_PREFIX."gradebook_tests".
				" WHERE course_id=".$_SESSION["course_id"].
				" ORDER BY title)";
$result = mysql_query($sql, $db) or die(mysql_error());

if (mysql_num_rows($result) == 0)
{
?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php 
}
else
{
	while ($row = mysql_fetch_assoc($result))
	{
		$sql_grade = "SELECT grade FROM ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id=".$row["gradebook_test_id"]." AND member_id=".$_SESSION["member_id"];
		$result_grade = mysql_query($sql_grade, $db) or die(mysql_error());
		$row_grade = mysql_fetch_assoc($result_grade);
		
		if (mysql_num_rows($result_grade) == 0)
			$grade = "";
		else
		{
			$grade = $row_grade["grade"];
		}
		
		if ($row["type"] == "ATutor Test")
		{
			// get "completed date" and "time spent"
			if ($grade <> "")
			{
				$sql_tr = "SELECT R.result_id, R.date_taken, (UNIX_TIMESTAMP(R.end_time) - UNIX_TIMESTAMP(R.date_taken)) AS diff FROM ".TABLE_PREFIX."tests_results R WHERE R.status=1 AND R.test_id=".$row["id"]." AND R.member_id='".$_SESSION[member_id]."'";
				$result_tr = mysql_query($sql_tr, $db) or die(mysql_error());
				$row_tr = mysql_fetch_assoc($result_tr);
			}
?>
		<tr>
<?php 
			if ( ($grade != '') && (($row['result_release']==AT_RELEASE_IMMEDIATE) || ($row['result_release']==AT_RELEASE_MARKED)) )
				echo '			<td><a href="tools/view_results.php?tid='.$row['id'].'&amp;rid='.$row_tr['result_id'].'">'.$row["title"].'</a></td>'."\n\r";
			else
				echo '			<td>'.$row["title"].'</td>'."\n\r";
?>
			<td><?php echo ($grade=="") ? _AT("na") : $grade; ?></td>
			<td><?php echo get_class_avg($row["gradebook_test_id"]); ?></td>
			<td><?php echo $row["due_date"]; ?></td>
			<td><?php echo ($grade=="") ? _AT("na") : $row_tr["date_taken"]; ?></td>
			<td><?php echo ($grade=="") ? _AT("na") : get_human_time($row_tr['diff']); ?></td>
		</tr>
<?php 
		}
		else
		{
?>
		<tr>
			<td><?php echo $row["title"]; ?></td>
			<td><?php echo ($grade=="") ? _AT("na") : $grade; ?></td>
			<td><?php echo get_class_avg($row["gradebook_test_id"]); ?></td>
			<td><?php echo $row["due_date"]; ?></td>
			<td><?php echo ($grade=="") ? _AT("pending") : _AT("completed"); ?></td>
			<td><?php echo _AT("na"); ?></td>
		</tr>
<?php 
		}
	}
}
?>

</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
