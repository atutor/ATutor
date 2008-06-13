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
// $Id: users.php 7208 2008-01-09 16:07:24Z greg $
$page = "gradebook";

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

require_once("lib/gradebook.inc.php");

if (isset($_GET['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: gradebook_tests.php');
	exit;
} 
else if (isset($_GET['save'])) 
{
	foreach($_GET as $key => $value)
	{
		if (preg_match('/^grade_(.*)_(.*)$/', $key, $matches) > 0)
		{
			$sql = "SELECT grade_scale_id FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id = ". $matches[1];
			$result	= mysql_query($sql, $db) or die(mysql_error());
			$row = mysql_fetch_assoc($result);

			$sql = "UPDATE ".TABLE_PREFIX."gradebook_detail SET grade='".get_mark_by_grade($row["grade_scale_id"], $value)."' WHERE gradebook_test_id = ". $matches[1]." AND member_id=". $matches[2];
			$result	= mysql_query($sql, $db) or die(mysql_error());
		}
	}
}

$orders = array('asc' => 'desc', 'desc' => 'asc');

if (isset($_GET['asc'])) 
{
	$order = 'asc';
	$order_col = $_GET['asc'];
} 
else if (isset($_GET['desc'])) {
	$order = 'desc';
	$order_col = $_GET['desc'];
} else {
	// no order set
	$order = 'asc';
	$order_col   = 'name';
}

if ($_GET['reset_filter']) {
	unset($_GET);
}

// Initialize all applicable tests array and all enrolled students array
$all_tests = array();
$all_students = array();

// generate test array
$sql = "(SELECT g.gradebook_test_id, t.test_id, t.title, 1 is_atutor_test from ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t WHERE g.test_id = t.test_id AND t.course_id=".$_SESSION["course_id"]." ORDER BY t.title) UNION (SELECT gradebook_test_id, test_id, title, 0 is_atutor_test FROM ".TABLE_PREFIX."gradebook_tests WHERE course_id=".$_SESSION["course_id"]." ORDER BY title)";
$result	= mysql_query($sql, $db) or die(mysql_error());
while ($row = mysql_fetch_assoc($result))
{
	$no_error = true;
	
	if($row["is_atutor_test"])
	{
		$studs_take_num = get_studs_take_more_than_once($_SESSION["course_id"], $row["test_id"]);
		
		foreach ($studs_take_num as $student => $num)
		{
				if ($no_error) $no_error = false;
				
				$f = array('ADD_TEST_INTO_GRADEBOOK',
								$row['title'], 
								$student . ": " . $num . " times");
				$msg->addFeedback($f);
		}
	}
	
	if ($no_error) array_push($all_tests, $row);
}

// generate students array
$sql_students = "SELECT m.first_name, m.last_name, e.member_id FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."course_enrollment e WHERE m.member_id = e.member_id AND e.course_id=".$_SESSION["course_id"]." AND e.approved='y' AND e.role<>'Instructor'";
if ($order_col == "name")
{
	$sql_students .= " ORDER BY m.first_name ".$order.",m.last_name ".$order;
}
$result	= mysql_query($sql_students, $db) or die(mysql_error());

while ($row = mysql_fetch_assoc($result))
	array_push($all_students, $row);
// end of initialization

// Creates arrays for filtered test/student
$selected_tests = array();
$selected_students = array();
$grades = array();

// generate test array
if ($_GET["filter"] && $_GET["gradebook_test_id"]<>0)
{
	foreach ($all_tests as $test)
	{
		if ($test["gradebook_test_id"] == $_GET["gradebook_test_id"])
		{
			$selected_tests[0]["gradebook_test_id"] = $test["gradebook_test_id"];
			$selected_tests[0]["title"] = $test["title"];
			$selected_tests[0]["is_atutor_test"] = $test["is_atutor_test"];
		}
	}
}
else
	$selected_tests = $all_tests;

// generate students array
if ($_GET["filter"] && $_GET["member_id"]<>0)
{
	foreach ($all_students as $student)
	{
		if ($student["member_id"] == $_GET["member_id"])
		{
			$selected_students[0]["member_id"] = $student["member_id"];
			$selected_students[0]["first_name"] = $student["first_name"];
			$selected_students[0]["last_name"] = $student["last_name"];
		}
	}
	
	$sql_students = "SELECT first_name, last_name, member_id FROM ".TABLE_PREFIX."members WHERE member_id=" . $_GET["member_id"];
}
else
	$selected_students = $all_students;

// generate grade 2-dimentional array
foreach ($selected_tests as $selected_test)
	foreach($selected_students as $selected_student)
	{
		$sql = "SELECT grade FROM ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id=".$selected_test["gradebook_test_id"]." AND member_id=".$selected_student["member_id"];
		$result = mysql_query($sql, $db) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		
		$grades[$selected_test["gradebook_test_id"]][$selected_student["member_id"]] = $row["grade"];
	}

// sort grade
if ((isset($_GET["asc"]) || isset($_GET["desc"])) && $order_col <> "name")
{
	$sort = '$grades['.$order_col.'], SORT_'.strtoupper($order).', $selected_students, SORT_'.strtoupper($order);
	
	foreach($selected_tests as $test)
	{
		if ($test["gradebook_test_id"] <> $order_col)
			$sort .= ', $grades['.$test["gradebook_test_id"].'], SORT_'.strtoupper($order);
	}
	$sort='array_multisort('.$sort.');';
	eval($sort);
}
// end of initialization
$num_students = count($selected_students);
$results_per_page = 50;
$num_pages = max(ceil($num_students / $results_per_page), 1);

$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printFeedbacks();

if (count($selected_tests)==0)
{
	echo _AT('empty_gradebook');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
	
		<div class="row">
		<label for="select_gid"><?php echo _AT("name") ?></label><br />
			<select name="gradebook_test_id" id="select_gid">
				<option value="0"><?php echo _AT('all') ?></option>
				<option value="-1"></option>
<?php
	foreach($all_tests as $test)
	{
		echo '			<option value="'.$test[gradebook_test_id]. '"';
		
		if ($test[gradebook_test_id]==$_GET["gradebook_test_id"])
			echo ' SELECTED ';
		echo '>'.$test["title"].'</option>'."\n\r";
	}
?>
			</select>
		</div>

		<div class="row">
			<label for="select_mid"><?php echo _AT("students") ?></label><br />
			<select name="member_id" id="select_mid">
				<option value="0"><?php echo _AT('all') ?></option>
				<option value="-1"></option>
<?php
	foreach($all_students as $student)
	{
		echo '			<option value="'.$student[member_id].'"';
		if ($student[member_id]==$_GET["member_id"])
			echo ' SELECTED ';
		echo '>'.$student[first_name].' '.$student[last_name].'</option>'."\n\r";
	}
?>
			</select>
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="filter" value="<?php echo $_GET[filter]?>">
<input type="hidden" name="gradebook_test_id" value="<?php echo $_GET[gradebook_test_id]?>">
<input type="hidden" name="member_id" value="<?php echo $_GET[member_id]?>">


<?php print_paginator($page, $num_students, $sql_students, $results_per_page); ?>

<table summary="" class="data" rules="rows">

<thead>
<tr>
	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF'] .'?'.$orders[$order].'=name&filter='.$_GET[filter].'&member_id='.$_GET[member_id].'&gradebook_test_id='.$_GET[gradebook_test_id]; ?>"><?php echo _AT('name'); ?></a></th>
<?php 
foreach ($selected_tests as $selected_test)
{
?>
	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF'] .'?'.$orders[$order].'='.$selected_test[gradebook_test_id].'&filter='.$_GET[filter].'&member_id='.$_GET[member_id].'&gradebook_test_id='.$_GET[gradebook_test_id]; ?>"><?php echo $selected_test[title]; ?></a></th>
<?php 
}
?>
	<th scope="col"></th>
</tr>

<tr>
	<td></td>
<?php 
$has_edit_button = false;
foreach ($selected_tests as $selected_test)
{
	if (!$selected_test["is_atutor_test"])
	{
		$has_edit_button = true;
?>
	<td style="text-align:center"><a href="<?php echo $_SERVER['PHP_SELF']. '?edit=c_'.$selected_test['gradebook_test_id'].'&filter='.$_GET["filter"].'&member_id='.$_GET["member_id"].'&gradebook_test_id='.$_GET["gradebook_test_id"]; ?>"><?php echo _AT("edit"); ?></a></td>
<?php 
	}
	else
	{
		echo "	<td></td>";
	}
}
if ($has_edit_button) echo "	<td></td>";
?>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="<?php echo count($selected_tests)+2; ?>">
		<div class="row buttons">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" />
		</div>
	</td>
</tr>
</tfoot>

<?php 
if ($num_students == 0)
{
?>
	<tr>
		<td colspan="<?php echo count($selected_tests)+1; ?>"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php 
}
else
{
	echo "	<tbody>\n\r";
	if ($offset + $results_per_page > $num_students) $end_pos = $num_students;
	else $end_pos = $offset + $results_per_page;
	
	$tabindex_input = 1;
	$tabindex_edit = 2;
	for ($i=$offset; $i < $end_pos; $i++)
	{
		echo "		<tr>\n\r";
		echo "			<td>".$selected_students[$i]["first_name"]." " . $selected_students[$i]["last_name"]."</td>\n\r";

		foreach ($selected_tests as $selected_test)
		{
			$sql = "SELECT grade FROM ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id=".$selected_test["gradebook_test_id"]." AND member_id=".$selected_students[$i]["member_id"];
			$result = mysql_query($sql, $db) or die(mysql_error());
			$row = mysql_fetch_assoc($result);
			
			if ($_GET["edit"]=="c_".$selected_test["gradebook_test_id"] || $_GET["edit"]=="r_".$selected_students[$i]["member_id"] && !$selected_test["is_atutor_test"])
			{
				echo "			<td><input type='text' name='grade_".$selected_test["gradebook_test_id"]."_".$selected_students[$i]["member_id"]."' value='".$row["grade"]."' tabindex='".$tabindex_input."'></td>\n\r";
			}
			else
			{
				if ($row["grade"]=="")
					echo "			<td style='text-align:center'>"._AT("na")."</td>\n\r";
				else
					echo "			<td style='text-align:center'>".$row["grade"]."</td>\n\r";
			}
		}
		
		if ($has_edit_button)
			echo "			<td style='text-align:center'><a href=\"". $_SERVER['PHP_SELF']. "?edit=r_".$selected_students[$i]['member_id'].'&filter='.$_GET["filter"].'&member_id='.$_GET["member_id"].'&gradebook_test_id='.$_GET["gradebook_test_id"]."\" tabindex='".$tabindex_edit."'>". _AT("edit") ."</a></td>\n\r";
		echo "		</tr>\n\r";
	}
	echo "	</tbody>\n\r";
}
?>
</table>

</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>