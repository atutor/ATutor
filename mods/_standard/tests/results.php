<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id: results.php 10197 2010-09-16 16:18:25Z greg $
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_TESTS);

function sortByFullName($cell1, $cell2)
{
	global $order;
	
	if ($order == 'asc') return (strcmp($cell1['full_name'], $cell2['full_name']) > 0) ? 1 : -1;
	else return (strcmp($cell1['full_name'], $cell2['full_name']) < 0) ? 1 : -1;
}

$tid = intval($_REQUEST['tid']);

if (isset($_GET['delete'], $_GET['id'])) {
	$ids = implode(',', $_GET['id']);
	header('Location:delete_result.php?tid='.$tid.SEP.'rid='.$ids);
	exit;
} else if (isset($_GET['edit'], $_GET['id'])) {
	if (count($_GET['id']) > 1) {
		$msg->addError('SELECT_ONE_ITEM');
	} else {
		header('Location:view_results_manage.php?tid='.$tid.SEP.'rid='.$_GET['id'][0]);
		exit;
	}
} else if ((isset($_GET['edit']) || isset($_GET['delete'])) && !$_GET['id'] && !$_GET['asc'] && !$_GET['desc'] && !$_GET['filter'] && !$_GET['reset_filter']) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

if ($_GET['reset_filter']) {
	unset($_GET);
}

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('login' => 1, 'full_name' => 1, 'date_taken' => 1, 'fs' => 1, 'time_spent' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'login';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'login';
} else {
	// no order set
	$order = 'asc';
	$col   = 'login';
}

require(AT_INCLUDE_PATH.'header.inc.php');

if (isset($_GET['status']) && ($_GET['status'] != '') && ($_GET['status'] != 2)) {
	if ($_GET['status'] == 0) {
		$status = " AND R.final_score=''";
	} else {
		$status = " AND R.final_score<>''";
	}
	$page_string .= SEP.'status='.$_GET['status'];
} else {
	$status = '';
}

if ($_GET['user_type'] == 1 || $_GET['user_type'] == 2) {
	if ($_GET['user_type'] == 1) {
		$status = " AND R.member_id not like 'G_%' AND R.member_id > 0 ";
	} else {
		$status = " AND (R.member_id like 'G_%' OR R.member_id = 0) ";
	}
	$page_string .= SEP.'user_type='.$_GET['user_type'];
}

//get test info
$sql	= "SELECT out_of, anonymous, random, title FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
$result	= mysql_query($sql, $db);
if (!($row = mysql_fetch_array($result))){
	$msg->printErrors('ITEM_NOT_FOUND');
	require (AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
$out_of = $row['out_of'];
$anonymous = $row['anonymous'];
$random = $row['random'];
$title = $row['title'];

//count total
$sql	= "SELECT count(*) as cnt FROM ".TABLE_PREFIX."tests_results R LEFT JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE R.test_id=$tid AND R.status=1";
$result	= mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$num_sub = $row['cnt'];

//get results based on filtre and sorting
if ($anonymous == 1) {
	// Keep login, full_name and fs fields even if not used: ORDER BY relies upon them
	$sql	= "SELECT R.*, (UNIX_TIMESTAMP(R.end_time) - UNIX_TIMESTAMP(R.date_taken)) AS time_spent, '' AS login, '' AS full_name, R.final_score+0.0 AS fs FROM ".TABLE_PREFIX."tests_results R WHERE R.test_id=$tid AND R.status=1 $status ";
} else {	
//	$sql	= "SELECT R.*, M.login, (UNIX_TIMESTAMP(R.end_time) - UNIX_TIMESTAMP(R.date_taken)) AS time_spent, CONCAT(M.first_name, ' ', M.second_name, ' ', M.last_name) AS full_name, R.final_score+0.0 AS fs FROM ".TABLE_PREFIX."tests_results R LEFT JOIN  ".TABLE_PREFIX."members M USING (member_id) WHERE R.test_id=$tid AND R.status=1 $status ORDER BY $col $order, R.final_score $order";
	//added by Indirect
	$sql	= "SELECT R.*, login, (UNIX_TIMESTAMP(R.end_time) - UNIX_TIMESTAMP(R.date_taken)) AS time_spent, R.final_score+0.0 AS fs FROM ".TABLE_PREFIX."tests_results R LEFT JOIN  ".TABLE_PREFIX."members M USING (member_id) WHERE R.test_id=$tid AND R.status=1 $status ";
}

// handle order by full_name separately
if ($col <> 'full_name') $sql .= " ORDER BY $col $order";

if ($anonymous <> 1) 
{
	if ($col <> 'full_name')
		$sql .= ", R.final_score $order";
	else
		$sql .= " ORDER BY R.final_score $order";
}

$result = mysql_query($sql, $db);

if ($anonymous == 1) {
	$guest_text = '<strong>'._AT('anonymous').'</strong>';
} else {
	$guest_text = '- '._AT('guest').' -';
}
while ($row = mysql_fetch_assoc($result)) {
	$full_name = AT_print(get_display_name($row['member_id']), 'members.full_name');
	$row['full_name'] = $full_name ? $full_name : $guest_text;
	$row['login']     = $row['login']     ? $row['login']     : $guest_text;
	$rows[$row['result_id']] = $row;
}

if ($col == "full_name") usort($rows, "sortByFullName");

$num_results = mysql_num_rows($result);

//count unmarked: no need to do this query if filtre is already getting unmarked
if (isset($_GET['status']) && ($_GET['status'] != '') && ($_GET['status'] == 0)) {
	$num_unmarked = $num_results;
} else {
	$sql		= "SELECT count(*) as cnt FROM ".TABLE_PREFIX."tests_results R, ".TABLE_PREFIX."members M WHERE R.test_id=$tid AND R.status=1 AND R.member_id=M.member_id AND R.final_score=''";
	$result	= mysql_query($sql, $db);
	$row = mysql_fetch_array($result);
	$num_unmarked = $row['cnt'];
}

?>
<!--h3><?php //echo AT_print($row['title'], 'tests.title'); ?></h3><br / -->
<div id="container">
<h3><?php echo AT_print($title, 'tests.title'); ?></h3><br />
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />

	<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('filter'); ?></legend>
		<div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('status'); ?><br />
			<input type="radio" name="status" value="1" id="s0" <?php if ($_GET['status'] == 1) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('marked_label', $num_sub - $num_unmarked); ?></label> 
			<input type="radio" name="status" value="0" id="s1" <?php if ($_GET['status'] == 0) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('unmarked_label', $num_unmarked); ?></label> 
			<input type="radio" name="status" value="2" id="s2" <?php if (!isset($_GET['status']) || ($_GET['status'] != 0 && $_GET['status'] != 1)) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('all_label', $num_sub); ?></label> 

		</div>

		<div class="row">
			<?php echo _AT('user_type'); ?><br />
			<input type="radio" name="user_type" value="1" id="u0" <?php if ($_GET['user_type'] == 1) { echo 'checked="checked"'; } ?> /><label for="u0"><?php echo _AT('registered_members'); ?></label> 
			<input type="radio" name="user_type" value="2" id="u1" <?php if ($_GET['user_type'] == 2) { echo 'checked="checked"'; } ?> /><label for="u1"><?php echo _AT('guests'); ?></label> 
			<input type="radio" name="user_type" value="0" id="u2" <?php if (!isset($_GET['user_type']) || ($_GET['user_type'] != 1 && $_GET['user_type'] != 2)) { echo 'checked="checked"'; } ?> /><label for="u2"><?php echo _AT('all'); ?></label> 
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
		</fieldset>
	</div>
</form>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />

<table class="data" summary="" rules="cols">
<colgroup>
	<?php if ($col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="4" />
	<?php elseif ($col == 'full_name'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($col == 'date_taken'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'time_spent'): ?>
		<col span="4" />
		<col class="sort" />
		<col span="1" />
	<?php elseif($col == 'fs'): ?>
		<col span="5" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>
	<th scope="col"><a href="mods/_standard/tests/results.php?tid=<?php echo $tid.$page_string.SEP.$orders[$order]; ?>=login"><?php echo _AT('login_name'); ?></a></th>
	<th scope="col"><a href="mods/_standard/tests/results.php?tid=<?php echo $tid.$page_string.SEP.$orders[$order]; ?>=full_name"><?php echo _AT('full_name'); ?></a></th>
	<th scope="col"><a href="mods/_standard/tests/results.php?tid=<?php echo $tid.$page_string.SEP.$orders[$order]; ?>=date_taken"><?php echo _AT('date_taken'); ?></a></th>
	<th scope="col"><a href="mods/_standard/tests/results.php?tid=<?php echo $tid.$page_string.SEP.$orders[$order]; ?>=time_spent"><?php echo _AT('time_spent'); ?></a></th>
	<th scope="col"><a href="mods/_standard/tests/results.php?tid=<?php echo $tid.$page_string.SEP.$orders[$order]; ?>=fs"><?php echo _AT('mark'); ?></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('view_mark_test'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php if ($rows): ?>
	<?php foreach ($rows as $row): ?>
		<tr onmousedown="document.form['r<?php echo $row['result_id']; ?>'].checked = !document.form['r<?php echo $row['result_id']; ?>'].checked; togglerowhighlight(this, 'r<?php echo $row['result_id']; ?>');" id="rr<?php echo $row['result_id']; ?>">
			<td><input type="checkbox" name="id[]" value="<?php echo $row['result_id']; ?>" id="r<?php echo $row['result_id']; ?>" onmouseup="this.checked=!this.checked" /></td>
			<td><?php echo $row['login']; ?></td>
			<td><?php 
				if ($anonymous == 0 && $row['member_id']){
					echo $row['full_name']; 
				} else {
					echo $guest_text; // no need in AT_print(): $guest_text is a trusted _AT() output
				}
				?></td>
			<td><?php $startend_date_format=_AT('startend_date_format'); echo AT_date( $startend_date_format, $row['date_taken'], AT_DATE_MYSQL_DATETIME); ?></td>
			<td><?php echo get_human_time($row['time_spent']); ?></td>

			<td align="center">
				<?php if ($out_of) {
					if ($random) {
						$out_of = get_random_outof($tid, $row['result_id']);
					}

					if ($row['final_score'] != '') { 
						echo $row['final_score'].'/'.$out_of;
					} else {
						echo _AT('unmarked');
					}
				} else {
					echo _AT('na');
				}
				?>
			</td>
		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>
</div>
<script language="JavaScript" type="text/javascript">
//<!--
function CheckAll() {
	for (var i=0;i<document.form.elements.length;i++)	{
		var e = document.form.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox')) {
			e.checked = document.form.selectall.checked;
			togglerowhighlight(document.getElementById("r" + e.id), e.id);
		}
	}
}

function togglerowhighlight(obj, boxid) {
	if (document.getElementById(boxid).checked) {
		obj.className = 'selected';
	} else {
		obj.className = '';
	}
}
//-->
</script>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>