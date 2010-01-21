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
// $Id: test_intro.php 9034 2009-12-14 19:47:30Z cindy $
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_result_functions.inc.php');

// test authentication
if (isset($_GET['tid']))
	$tid = intval($_GET['tid']);
else if (isset($_POST['tid']))
	$tid = intval($_POST['tid']);

if (isset($_REQUEST['cid']))
{
	$cid = intval($_REQUEST['cid']);
	$msg->addInfo('PRETEST');
}

// make sure max attempts not reached, and still on going
$sql		= "SELECT *, UNIX_TIMESTAMP(start_date) AS start_date2, UNIX_TIMESTAMP(end_date) AS end_date2 FROM ".TABLE_PREFIX."tests WHERE test_id=".$tid." AND course_id=".$_SESSION['course_id'];
$result = mysql_query($sql, $db);
$test_row = mysql_fetch_assoc($result);
/* check to make sure we can access this test: */
if (!$test_row['guests'] && ($_SESSION['enroll'] == AT_ENROLL_NO || $_SESSION['enroll'] == AT_ENROLL_ALUMNUS)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('NOT_ENROLLED');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
if (!$test_row['guests'] && !authenticate_test($tid)) {
	header('Location: my_tests.php');
	exit;
}

// checks one/all questions per page, and forward user to the correct one
if (isset($_POST['cancel'])) 
{
	if (isset($cid))
	{
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printInfos(array('PRETEST_FAILED', $test_row['title']));
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	//Retrieve last visited page
	if (isset($_SESSION['last_visited_page'])){
		$_last_visited_page = $_SESSION['last_visited_page'];
		unset($_SESSION['last_visited_page']);
	} else {
		$_last_visited_page = url_rewrite('mods/_standard/tests/my_tests.php', AT_PRETTY_URL_IS_HEADER);
	}

	$msg->addFeedback('CANCELLED');	
	header('Location: '.$_last_visited_page);
	exit;
} 
else if (isset($_POST['submit'])) 
{
	$guest_name = $addslashes(trim($_POST["guest_name"]));
	$organization = $addslashes(trim($_POST["organization"]));
	$location = $addslashes(trim($_POST["location"]));
	$role = $addslashes(trim($_POST["role"]));
	$focus = $addslashes(trim($_POST["focus"]));
	
	if ($guest_name <> "" || $organization <> "" || $location <> "" || $role <> "" || $focus <> "")
	{
		$guest_id = get_next_guest_id();

		$sql	= "INSERT INTO ".TABLE_PREFIX."guests (guest_id, name, organization, location, role, focus)
						 VALUES ('$guest_id', '$guest_name', '$organization', '$location', '$role', '$focus')";
		$result = mysql_query($sql, $db);
		$result_id = mysql_insert_id($db);
	}
	$gid_str = (isset($guest_id)) ? SEP."gid=".$guest_id : "";
	if (isset($cid)) $gid_str .= SEP.'cid='.$cid;

	if ($test_row['display']) {
		header('Location: '.url_rewrite('mods/_standard/tests/take_test_q.php?tid='.$tid.$gid_str, AT_PRETTY_URL_IS_HEADER));
	} else {
		header('Location: '.url_rewrite('mods/_standard/tests/take_test.php?tid='.$tid.$gid_str, AT_PRETTY_URL_IS_HEADER));
	}
	exit;
}

/* 
 * If max attempted reached, then stop it.
 * @3300
 */
$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."tests_results WHERE status=1 AND test_id=".$tid." AND member_id='".$_SESSION['member_id']."'";
if ( (($test_row['start_date2'] > time()) || ($test_row['end_date2'] < time())) || 
   ( ($test_row['num_takes'] != AT_TESTS_TAKE_UNLIMITED) && ($takes['cnt'] >= $test_row['num_takes']) )  ) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printInfos('MAX_ATTEMPTS');
	
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php');

// get number of attempts
$sql    = "SELECT COUNT(test_id) AS cnt FROM ".TABLE_PREFIX."tests_results WHERE status=1 AND test_id=$tid AND member_id='".$_SESSION['member_id']."'";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$num_takes = $row['cnt'];
} else {
	$num_takes = 0;
}

$sql	= "SELECT COUNT(*) AS num_questions FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
if (!$test_row['random'] || $test_row['num_questions'] > $row['num_questions']) {
	$test_row['num_questions'] = $row['num_questions'];
}

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<?php if (isset($cid)) { ?><input type="hidden" name="cid" value="<?php echo $cid; ?>" /> <?php } ?>

<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo $test_row['title']; ?></legend><div class="row">

<?php if ($test_row['guests'] && $test_row['show_guest_form'] && !$_SESSION['member_id']): ?>
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT("test_description"); ?></legend><div class="row">
<?php endif; ?>
	<table>
<?php if ($test_row['description']<>""): ?>
		<tr>
			<td><?php echo _AT('test_description'); ?></td>
			<td><?php echo empty($test_row['description']) ? '&nbsp;' : $test_row['description']; ?></td>
		</tr>
<?php endif; ?>

		<tr>
			<td><?php echo _AT('questions'); ?></td>
			<td><?php echo $test_row['num_questions']; ?></td>
		<tr>

		<tr>
			<td><?php echo _AT('out_of'); ?></td>
			<td><?php echo $test_row['out_of']; ?></td>
		<tr>
	
		<tr>
			<td><?php echo _AT('attempts'); ?></td>
			<td><?php echo $num_takes; ?> / <?php echo ($test_row['num_takes'] == AT_TESTS_TAKE_UNLIMITED) ? _AT('unlimited') : $test_row['num_takes']; ?></td>
		<tr>
			
		<tr>
			<td><?php echo _AT('start_date'); ?></td>
			<td><?php echo AT_date(	_AT('startend_date_long_format'), $test_row['start_date'], AT_DATE_MYSQL_DATETIME); ?></td>
		<tr>

		<tr>
			<td><?php echo _AT('end_date'); ?></td>
			<td><?php echo AT_date(	_AT('startend_date_long_format'), $test_row['end_date'], AT_DATE_MYSQL_DATETIME); ?></td>
		<tr>

		<tr>
			<td><?php echo _AT('anonymous'); ?></td>
			<td><?php echo $test_row['anonymous'] ? _AT('yes') : _AT('no'); ?></td>
		<tr>

		<tr>
			<td><?php echo _AT('display'); ?></td>
			<td><?php echo $test_row['display'] ? _AT('one_question_per_page') : _AT('all_questions_on_page'); ?></td>
		<tr>

		<tr>
			<td><?php echo _AT('instructions'); ?></td>
			<td><?php echo nl2br($test_row['instructions']); ?></td>
		<tr>
		</table>
<?php if ($test_row['guests'] && !$_SESSION['member_id']): ?>
	</fieldset>
<?php endif; ?>

<?php if (($test_row['guests']) && $test_row['show_guest_form'] && !$_SESSION['member_id']): ?>
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT("guest_information").' ('._AT('optional').')'; ?></legend><div class="row">

	<table class="none" width="100%">
	<tr>
		<td width="20%"><label for="guest_name" style="float:right;"><?php echo _AT('guest_name'); ?></label></td>
		<td width="80%"><input id="guest_name" name="guest_name" size="50" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['guest_name'])); ?>"/></td>
	</tr>

	<tr>
		<td><label for="organization" style="float:right;"><?php echo _AT('organization'); ?></label></td>
		<td><input id="organization" name="organization" size="50" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['organization'])); ?>" /></td>
	</tr>

	<tr>
		<td><label for="location" style="float:right;"><?php echo _AT('location'); ?></label></td>
		<td><input id="location" name="location" size="50" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['location'])); ?>" /></td>
	</tr>

	<tr>
		<td><label for="role" style="float:right;"><?php echo _AT('role'); ?></label></td>
		<td><input id="role" name="role" size="50" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['role'])); ?>" /></td>
	</tr>

	<tr>
		<td><label for="focus" style="float:right;"><?php echo _AT('focus'); ?></label></td>
		<td><input id="focus" name="focus"  size="50" type="text" value="<?php echo stripslashes(htmlspecialchars($_POST['focus'])); ?>" /></td>

	</tr>
	</table>

	</fieldset>



<?php endif; ?>

	
	<div class="row buttons">
		<input type="submit" name="submit" value=" <?php echo _AT('start_test'); ?> " accesskey="s" class="button"/>
		<input type="submit" name="cancel" value=" <?php echo _AT('cancel'); ?> "  class="button" />
	</div>

	</fieldset>
</div>

</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>