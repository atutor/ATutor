<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

if (isset($_POST['enroll'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['tab'] = $_POST['tab'];
	} else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=enroll'.SEP.'tab=0'.SEP.'course_id='.$course_id);
		exit;
	}
} else if (isset($_POST['unenroll'])) {
	// different from a plain delete. This removes from groups as well.
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['tab'] = $_POST['tab'];
	} else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=unenroll'.SEP.'tab=1'.SEP.'course_id='.$course_id);
		exit;	
	}
} else if (isset($_POST['role'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['tab'] = $_POST['tab'];
	} else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'mid'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: privileges.php?'.$text.SEP.'course_id='.$course_id);
		exit;
	}
} else if (isset($_POST['alumni'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['tab'] = $_POST['tab'];
	} else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=alumni'.SEP.'tab=2'.SEP.'course_id='.$course_id);
		exit;
	}
}

//filter stuff:

if ($_GET['reset_filter']) {
	unset($_GET);
}

$filter=array();

if (isset($_GET['role']) && ($_GET['role'] != '')) {
	$filter['role'] = intval($_GET['role']);
} 

if (isset($_GET['status']) && ($_GET['status'] != '')) {
	$filter['status'] = intval($_GET['status']);
} 

if (isset($_GET['group']) && ($_GET['group'] != '')) {
	$filter['group'] = intval($_GET['group']);
} 

require(AT_INCLUDE_PATH.'html/enroll_tab_functions.inc.php');
$tabs = get_tabs();	
$num_tabs = count($tabs);

for ($i=0; $i < $num_tabs; $i++) {
	if (isset($_POST['button_'.$i]) && ($_POST['button_'.$i] != -1)) { 
		$current_tab = $i;
		$_POST['current_tab'] = $i;
		break;
	}
}

//get present tab if specified
if ($_GET['current_tab']) {
	$current_tab = $_GET['current_tab'];
	$_POST['current_tab'] = $_GET['current_tab'];
}

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('login' => 1, 'first_name' => 1, 'second_name' => 1, 'last_name' => 1, 'email' => 1);

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
$view_select = intval($_POST['view_select']);

// the possible tabs. order matters.
$tabs = array('enrolled', 'assistants', 'alumni', 'pending_enrollment', 'not_enrolled');

$num_tabs = count($tabs);
if (isset($_REQUEST['tab'])) {
	$current_tab = intval($_REQUEST['tab']);
}

if (!isset($current_tab)) {
	$current_tab = 0;
}

if (isset($_GET['match']) && $_GET['match'] == 'one') {
	$checked_match_one = ' checked="checked"';
	$page_string .= SEP.'match=one';
} else {
	$_GET['match'] = 'all';
	$checked_match_all = ' checked="checked"';
	$page_string .= SEP.'match=all';
}

if (admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT, TRUE)) {
	$page_string .= SEP.'course_id='.$course_id;
}

if ($_GET['search']) {
	$page_string .= SEP.'search='.urlencode($_GET['search']);
	$search = $addslashes($_GET['search']);
	$search = explode(' ', $search);

	if ($_GET['match'] == 'all') {
		$predicate = 'AND ';
	} else {
		$predicate = 'OR ';
	}

	$sql = '';
	foreach ($search as $term) {
		$term = trim($term);
		$term = str_replace(array('%','_'), array('\%', '\_'), $term);
		if ($term) {
			$term = '%'.$term.'%';
			$sql .= "((M.first_name LIKE '$term') OR (M.second_name LIKE '$term') OR (M.last_name LIKE '$term') OR (M.email LIKE '$term') OR (M.login LIKE '$term')) $predicate";
		}
	}
	$sql = '('.substr($sql, 0, -strlen($predicate)).')';
	$search = $sql;
} else {
	$search = '1';
}


$instructor_id = $system_courses[$course_id]['member_id'];
if ($current_tab == 0) {
	// enrolled
	$sql_cnt = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='y' AND M.member_id<>$instructor_id AND CE.privileges=0 AND $search";
	$sql	=  "SELECT CE.member_id, CE.privileges, CE.approved, M.login, M.first_name, M.second_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='y' AND M.member_id<>$instructor_id AND CE.privileges=0 AND $search
				ORDER BY $col $order";
} else if ($current_tab == 1) {
	// assistants
	$sql_cnt=  "SELECT COUNT(*) AS cnt
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND CE.approved='y' AND CE.privileges>0 AND $search";
	$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.second_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND CE.approved='y' AND CE.privileges>0 AND $search
				ORDER BY $col $order";

} else if ($current_tab == 3) {
	// pending
	if ($system_courses[$course_id]['access'] == 'private') {
		$sql_cnt = "SELECT COUNT(*) AS cnt 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='n' AND $search";

		$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.second_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='n' AND $search
				ORDER BY $col $order";
	} else {
		// not sure what this is about
		$sql_cnt = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members WHERE 0";
		$sql = "SELECT login FROM ".TABLE_PREFIX."members WHERE 0";
	}
} else if ($current_tab == 2) {
	// alumni
	$sql_cnt=  "SELECT COUNT(*) AS cnt
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='a' AND $search";
	$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.second_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='a' AND $search
				ORDER BY $col $order";
} else {
	// not sure what this is about
	$tmp_sql	=  "SELECT member_id FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course_id AND member_id<>$instructor_id ";
	$tmp_result = mysql_query($tmp_sql, $db);
	$course_enrollment = '';
	while ($row = mysql_fetch_assoc($tmp_result)) {
		$course_enrollment .= $row['member_id'] .',';
	}
	$course_enrollment .= $instructor_id;

	$sql_cnt=  "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members M WHERE M.status>1 AND M.member_id NOT IN ($course_enrollment) AND $search";

	$sql	=  "SELECT M.member_id, M.login, M.first_name, M.second_name, M.last_name, M.email FROM ".TABLE_PREFIX."members M WHERE M.member_id NOT IN ($course_enrollment) AND M.status>1 AND $search ORDER BY $col $order";
}

$results_per_page = 50;

$result = mysql_query($sql_cnt, $db);
$row = mysql_fetch_assoc($result);
$num_results = $row['cnt'];

$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;
$sql .= " LIMIT $offset, $results_per_page";

$enrollment_result = mysql_query($sql, $db);
$page_string .= SEP . 'tab='.$current_tab;
require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="hidden" name="tab" value="<?php echo $current_tab; ?>"/>
	<input type="hidden" name="course_id" value="<?php echo $course_id; ?>"/>
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

		<?php if (admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT, TRUE)): ?>
			<div class="row">
				<label for="course"><?php echo _AT('course'); ?></label><br/>
				<select name="course_id">
				<?php
				$sql = "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
				$result = mysql_query($sql, $db);
				while ($courses_row = mysql_fetch_assoc($result)) {
					if ($courses_row['course_id'] == $course_id) {
						echo '<option value="'.$courses_row['course_id'].'" selected="selected">'.$courses_row['title'].'</option>';
					} else {
						echo '<option value="'.$courses_row['course_id'].'">'.$courses_row['title'].'</option>';
					}
				}
				?></select>
			</div>
		<?php endif; ?>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('login_name').', '._AT('first_name').', '._AT('second_name').', '._AT('last_name') .', '._AT('email'); ?>)</label><br />
			<input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
			<br/>
			<?php echo _AT('search_match'); ?>:
			<input type="radio" name="match" value="all" id="match_all" <?php echo $checked_match_all; ?> /><label for="match_all"><?php echo _AT('search_all_words'); ?></label> <input type="radio" name="match" value="one" id="match_one" <?php echo $checked_match_one; ?> /><label for="match_one"><?php echo _AT('search_any_word'); ?></label>
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<?php print_paginator($page, $num_results, $page_string . SEP . $order .'='. $col, $results_per_page); ?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="selectform">
<input type="hidden" name="tab" value="<?php echo $current_tab; ?>" />
<input type="hidden" name="course_id" value="<?php echo $course_id; ?>"/>
<div style="width: 95%; margin-right: auto; margin-left: auto;">
<ul id="navlist">
	<?php for ($i = 0; $i< $num_tabs; $i++): ?>
		<?php if ($current_tab == $i): ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i.SEP; ?>course_id=<?php echo $course_id; ?>" class="active"><strong><?php echo _AT($tabs[$i]); ?></strong></a></li>
		<?php else: ?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?tab=<?php echo $i.SEP; ?>course_id=<?php echo $course_id; ?>"><?php echo _AT($tabs[$i]); ?></a></li>
		<?php endif; ?>
	<?php endfor; ?>
</ul>
</div>

<table class="data" style="width:95%;" summary="" rules="cols">
<colgroup>
	<?php if ($col == 'login'): ?>
		<col />
		<col class="sort" />
		<col span="4" />
	<?php elseif($col == 'first_name'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($col == 'second_name'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'last_name'): ?>
		<col span="4" />
		<col class="sort" />
		<col />
	<?php elseif($col == 'email'): ?>
		<col span="5" />
		<col class="sort" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col" align="left"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall" onclick="CheckAll();" /></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $orders[$order]; ?>=login<?php echo $page_string;?>"><?php echo _AT('login_name'); ?></a></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $orders[$order]; ?>=first_name<?php echo $page_string;?>"><?php echo _AT('first_name'); ?></a></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $orders[$order]; ?>=second_name<?php echo $page_string;?>"><?php echo _AT('second_name'); ?></a></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $orders[$order]; ?>=last_name<?php echo $page_string;?>"><?php echo _AT('last_name'); ?></a></th>

	<th scope="col"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php echo $orders[$order]; ?>=email<?php echo $page_string;?>"><?php echo _AT('email'); ?></a></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
		<?php if ($current_tab == 0): ?>
			<input type="submit" name="role"     value="<?php echo _AT('privileges');  ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove');    ?>" /> 
			<input type="submit" name="alumni"   value="<?php echo _AT('mark_alumni'); ?>" />
		<?php elseif ($current_tab == 1): ?>
			<input type="submit" name="role" value="<?php echo _AT('privileges'); ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove'); ?>" /> 

		<?php elseif ($current_tab == 2): ?>
			<input type="submit" name="enroll"   value="<?php echo _AT('enroll'); ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove'); ?>" />
		
		<?php elseif ($current_tab == 3): ?>
			<input type="submit" name="enroll" value="<?php echo _AT('enroll'); ?>" /> 
			<input type="submit" name="unenroll" value="<?php echo _AT('remove'); ?>" />

		<?php elseif ($current_tab == 4): ?>
			<input type="submit" name="enroll"   value="<?php echo _AT('enroll'); ?>" /> 

		<?php endif; ?></td>
</tr>
</tfoot>
<tbody>
<?php if ($num_results): ?>
	<?php while ($row = mysql_fetch_assoc($enrollment_result)): ?>
		<tr onmousedown="document.selectform['m<?php echo $row['member_id']; ?>'].checked = !document.selectform['m<?php echo $row['member_id']; ?>'].checked;">
			<td><input type="checkbox" name="id[]" value="<?php echo $row['member_id']; ?>" id="m<?php echo $row['member_id']; ?>" onmouseup="this.checked=!this.checked" title="<?php echo AT_print($row['login'], 'members.login'); ?>" /></td>
			<td><?php echo AT_print($row['login'], 'members.login'); ?></td>
			<td><?php echo AT_print($row['first_name'], 'members.name'); ?></td>
			<td><?php echo AT_print($row['second_name'], 'members.name'); ?></td>
			<td><?php echo AT_print($row['last_name'], 'members.name'); ?></td>
			<td><?php echo AT_print($row['email'], 'members.email'); ?></td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<script language="JavaScript" type="text/javascript">
//<!--
function CheckAll() {
	for (var i=0;i<document.selectform.elements.length;i++)	{
		var e = document.selectform.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox'))
			e.checked = document.selectform.selectall.checked;
	}
}
//-->
</script>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>