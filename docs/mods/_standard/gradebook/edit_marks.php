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

define('AT_INCLUDE_PATH', '../../../include/');
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
		$value = $addslashes($value);
		if (preg_match('/^grade_(.*)_(.*)$/', $key, $matches) > 0)
		{
			$sql = "SELECT grade_scale_id FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id = ". $matches[1];
			$result	= mysql_query($sql, $db) or die(mysql_error());
			$row = mysql_fetch_assoc($result);

			$sql = "REPLACE ".TABLE_PREFIX."gradebook_detail SET gradebook_test_id = ". $matches[1].", member_id=". $matches[2].", grade='".get_mark_by_grade($row["grade_scale_id"], $value)."'";
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
$sql = "(SELECT g.gradebook_test_id, g.id, g.type, t.title".
				" FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t".
				" WHERE g.type='ATutor Test'".
				" AND g.id = t.test_id".
				" AND t.course_id=".$_SESSION["course_id"]." ORDER BY title)".
				" UNION (SELECT g.gradebook_test_id, g.id, g.type, a.title".
				" FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."assignments a".
				" WHERE g.type='ATutor Assignment'".
				" AND g.id = a.assignment_id".
				" AND a.course_id=".$_SESSION["course_id"]." ORDER BY title)".
				" UNION (SELECT gradebook_test_id, id, type, title".
				" FROM ".TABLE_PREFIX."gradebook_tests".
				" WHERE course_id=".$_SESSION["course_id"]." ORDER BY title)";
$result	= mysql_query($sql, $db) or die(mysql_error());

while ($row = mysql_fetch_assoc($result))
{
	$no_error = true;
	
	if($row["type"]=="ATutor Test")
	{
		$studs_take_num = get_studs_take_more_than_once($_SESSION["course_id"], $row["id"]);
		
		foreach ($studs_take_num as $member_id => $num)
		{
			if ($no_error) $no_error = false;
			$error_msg .= get_display_name($member_id) . ": " . $num . " times<br>";
		}
				
		if (!$no_error)
		{
			$f = array('ADD_TEST_INTO_GRADEBOOK',
							$row['title'], 
							$error_msg);
			$msg->addFeedback($f);
		}
	}
	
	if ($no_error) array_push($all_tests, $row);
}

// generate students array
$sql_students = "SELECT m.first_name, m.last_name, e.member_id FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."course_enrollment e WHERE m.member_id = e.member_id AND e.course_id=".$_SESSION["course_id"]." AND e.approved='y' AND e.role!='Instructor'";
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
//$csv_content .= $selected_students[$i]["first_name"]." " . $selected_students[$i]["last_name"];

$selected_students = array();
$grades = array();

// generate test array
if (($_GET["filter"] || $_GET["download"]) && $_GET["gradebook_test_id"]<>0)
{
	foreach ($all_tests as $test)
	{
		if ($test["gradebook_test_id"] == $_GET["gradebook_test_id"])
		{
			$selected_tests[0]["gradebook_test_id"] = $test["gradebook_test_id"];
			$selected_tests[0]["title"] = $test["title"];
			$selected_tests[0]["type"] = $test["type"];
		}
	}
}
else
	$selected_tests = $all_tests;

// generate students array
if (($_GET["filter"] || $_GET["download"]) && $_GET["member_id"]<>0)
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

// generate table & csv head
$table_head = "<thead>\n\r";
$table_head .= "<tr>\n\r";

if ($_GET[filter] <> "")
	$query_str = '&amp;filter='.$_GET[filter];

if ($_GET[member_id] <> "")
	$query_str .= '&amp;member_id='.$_GET[member_id];

if ($_GET[gradebook_test_id] <> "")
	$query_str .= '&amp;gradebook_test_id='.$_GET[gradebook_test_id];

$table_head .= "	<th scope='col'><a href='". $_SERVER['PHP_SELF'] .'?'.$orders[$order].'=name'.$query_str."'>". _AT('name')."</a></th>\n\r";

$csv_content = _AT('name');

foreach ($selected_tests as $selected_test)
{
	$table_head .= "	<th scope='col'><a href='". $_SERVER['PHP_SELF'] ."?".$orders[$order]."=".$selected_test[gradebook_test_id].$query_str."'>". $selected_test[title]."</a></th>\n\r";
	$csv_content .= ",".$selected_test[title];
}
$table_head .= "	<th scope='col'></th>\n\r";
$table_head .= "</tr>\n\r";

$csv_content .= "\n";

$table_head .= "<tr>\n\r";
$table_head .= "	<td></td>\n\r";

$has_edit_button = false;
foreach ($selected_tests as $selected_test)
{
	if ($selected_test["type"] == "External" || $selected_test["type"] == "ATutor Assignment")
	{
		$has_edit_button = true;
		$table_head .= "	<td style='text-align:center'><a href='". $_SERVER['PHP_SELF']. '?edit=c_'.$selected_test['gradebook_test_id'].$query_str."'>". _AT("edit")."</a></td>\n\r";
	}
	else
	{
		$table_head .= "	<td></td>\n\r";
	}
}
if ($has_edit_button) $table_head .= "	<td></td>";
$table_head .= "</tr>\n\r";
$table_head .= "</thead>\n\r";

// generate table & csv content
if ($num_students > 0)
{
	$table_content = "	<tbody>\n\r";
	if ($offset + $results_per_page > $num_students) $end_pos = $num_students;
	else $end_pos = $offset + $results_per_page;
	
	$tabindex_input = 1;
	$tabindex_edit = 2;
	
	for ($i=$offset; $i < $end_pos; $i++)
	{
		$table_content .= "		<tr>\n\r";
		$table_content .= "			<td>".$selected_students[$i]["first_name"]." " . $selected_students[$i]["last_name"]."</td>\n\r";

		$csv_content .= $selected_students[$i]["first_name"]." " . $selected_students[$i]["last_name"];

		foreach ($selected_tests as $selected_test)
		{
			$sql = "SELECT grade FROM ".TABLE_PREFIX."gradebook_detail WHERE gradebook_test_id=".$selected_test["gradebook_test_id"]." AND member_id=".$selected_students[$i]["member_id"];
			$result = mysql_query($sql, $db) or die(mysql_error());
			$row = mysql_fetch_assoc($result);
			
			$row["grade"] = htmlspecialchars($row["grade"]);   // handle html special chars
			
			if ($_GET["edit"]=="c_".$selected_test["gradebook_test_id"] || $_GET["edit"]=="r_".$selected_students[$i]["member_id"] && ($selected_test["type"]=="External" || $selected_test["type"]=="ATutor Assignment"))
			{
				$table_content .= "			<td><input type='text' name='grade_".$selected_test["gradebook_test_id"]."_".$selected_students[$i]["member_id"]."' value=\"".$row["grade"]."\" tabindex='".$tabindex_input."' /></td>\n\r";
				$csv_content .= ",".$row["grade"];
			}
			else
			{
				if ($row["grade"]=="")
				{
					$table_content .= "			<td style='text-align:center'>"._AT("na")."</td>\n\r";
					$csv_content .= ",". _AT("na");
				}
				else
				{
					$table_content .= "			<td style='text-align:center'>".$row["grade"]."</td>\n\r";
					$csv_content .= ",".$row["grade"];
				}
			}
		}
		
		if ($has_edit_button)
			$table_content .= "			<td style='text-align:center'><a href=\"". $_SERVER['PHP_SELF']. "?edit=r_".$selected_students[$i]['member_id'].$query_str."\" tabindex='".$tabindex_edit."'>". _AT("edit") ."</a></td>\n\r";

		$table_content .= "		</tr>\n\r";
		$csv_content .= "\n";
	}
	
	$table_content .= "	</tbody>\n\r";
}

// download csv file
if ($_GET['download'])
{
	if ($num_students == 0)
	{
		require (AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('ITEM_NOT_FOUND');
		require (AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	header('Content-Type: application/x-excel');
	header('Content-Disposition: inline; filename="grades.csv"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	
	echo $csv_content;
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printFeedbacks();

if (count($selected_tests)==0)
{
	echo '<div class="toolcontainer">'._AT('empty_gradebook').'</div>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
?>
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form" id="jump-area">
	
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('search'); ?></legend>
		<div class="row">
		<label for="select_gid"><?php echo _AT("name") ?></label><br />
			<select name="gradebook_test_id" id="select_gid">
				<option value="0"><?php echo _AT('all') ?></option>
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
			<input type="submit" name="download" value="<?php echo _AT('download_test_csv'); ?>" />
		</div>
	</fieldset>
	</div>

</form>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="filter" value="<?php echo $_GET[filter]?>" />
<input type="hidden" name="gradebook_test_id" value="<?php echo $_GET[gradebook_test_id]?>" />
<input type="hidden" name="member_id" value="<?php echo $_GET[member_id]?>" />
<input type="hidden" name="p" value="<?php echo $page; ?>" />

<?php print_paginator($page, $num_students, $sql_students, $results_per_page); ?>

<table summary="" class="data" rules="all">

<?php 
echo $table_head;
?>
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
	echo $table_content;
}
?>
</table>

</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>