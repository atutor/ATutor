<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_COURSES);

if (isset($_GET['view'], $_GET['id'])) {
	header('Location: instructor_login.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: edit_course.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['backups'], $_GET['id'])) {
	header('Location: backup/index.php?course='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: delete_course.php?course='.$_GET['id']);
	exit;
}  else if (isset($_GET['delete']) || isset($_GET['backups']) || isset($_GET['edit']) || isset($_GET['view'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$page_string = '';

if ($_GET['reset_filter']) {
	unset($_GET);
}

$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('title' => 1, 'login' => 1, 'access' => 1, 'created_date' => 1, 'cat_name' => 1);
$_access = array('public', 'protected', 'private');

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'title';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'title';
} else {
	// no order set
	$order = 'asc';
	$col   = 'title';
}

if (isset($_GET['access']) && ($_GET['access'] != '') && isset($_access[$_GET['access']])) {
	$access = 'C.access = \'' . $_access[$_GET['access']].'\'';
	$page_string .= SEP.'access='.$_GET['access'];
} else {
	$access = '1';
}

if ($_GET['search']) {
	$page_string .= SEP.'search='.urlencode($_GET['search']);
	$search = $addslashes($_GET['search']);
	$search = str_replace(array('%','_'), array('\%', '\_'), $search);
	$search = '%'.$search.'%';
	$search = "((C.title LIKE '$search') OR (C.description LIKE '$search'))";
} else {
	$search = '1';
}

// get number of courses on the system
$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."courses C WHERE 1 AND $access AND $search";
$result = mysql_query($sql, $db);
$row = mysql_fetch_assoc($result);
$num_results = $row['cnt'];

$results_per_page = 100;
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

${'highlight_'.$col} = ' style="background-color: #fff;"';

$sql    = "SELECT COUNT(*) AS cnt, approved, course_id FROM ".TABLE_PREFIX."course_enrollment WHERE approved='y' OR approved='a' GROUP BY course_id, approved";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	if ($row['approved'] == 'y') {
		$row['cnt']--; // remove the instructor
	}
	$enrolled[$row['course_id']][$row['approved']] = $row['cnt'];
}

$sql	= "SELECT C.*, M.login, T.cat_name FROM ".TABLE_PREFIX."members M INNER JOIN ".TABLE_PREFIX."courses C USING (member_id) LEFT JOIN ".TABLE_PREFIX."course_cats T USING (cat_id) WHERE 1 AND $access AND $search ORDER BY $col $order LIMIT $offset, $results_per_page";
$result = mysql_query($sql, $db);

$num_rows = mysql_num_rows($result);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div class="input-form">
		<div class="row">
			<h3><?php echo _AT('results_found', $num_results); ?></h3>
		</div>

		<div class="row">
			<?php echo _AT('access'); ?><br />

			<input type="radio" name="access" value="0" id="s0" <?php if ($_GET['access'] == 0) { echo 'checked="checked"'; } ?> /><label for="s0"><?php echo _AT('public'); ?></label> 

			<input type="radio" name="access" value="1" id="s1" <?php if ($_GET['access'] == 1) { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('protected'); ?></label> 

			<input type="radio" name="access" value="2" id="s2" <?php if ($_GET['access'] == 2) { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('private'); ?></label>

			<input type="radio" name="access" value="" id="s" <?php if ($_GET['access'] == '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
		</div>

		<div class="row">
			<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('title').', '._AT('description'); ?>)</label><br />
			<input type="text" name="search" id="search" size="20" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
		</div>

		<div class="row buttons">
			<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>" />
			<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>" />
		</div>
	</div>
</form>

<div class="paging">
	<ul>
	<?php for ($i=1; $i<=$num_pages; $i++): ?>
		<li>
			<?php if ($i == $page) : ?>
				<a class="current" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string.SEP.$order.'='.$col; ?>"><em><?php echo $i; ?></em></a>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string.SEP.$order.'='.$col; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
	</ul>
</div>

<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table class="data" summary="" rules="cols">
<colgroup>
	<?php if ($col == 'title'): ?>
		<col />
		<col class="sort" />
		<col span="6" />
	<?php elseif($col == 'login'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="5" />
	<?php elseif($col == 'access'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="4" />
	<?php elseif($col == 'created_date'): ?>
		<col span="4" />
		<col class="sort" />
		<col span="3" />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><a href="admin/courses.php?<?php echo $orders[$order]; ?>=title<?php echo $page_string; ?>"><?php echo _AT('title');               ?></a></th>
	<th scope="col"><a href="admin/courses.php?<?php echo $orders[$order]; ?>=login<?php echo $page_string; ?>"><?php echo _AT('Instructor');          ?></a></th>
	<th scope="col"><a href="admin/courses.php?<?php echo $orders[$order]; ?>=access<?php echo $page_string; ?>"><?php echo _AT('access');             ?></a></th>
	<th scope="col"><a href="admin/courses.php?<?php echo $orders[$order]; ?>=created_date<?php echo $page_string; ?>"><?php echo _AT('created_date'); ?></a></th>
	<th scope="col"><a href="admin/courses.php?<?php echo $orders[$order]; ?>=cat_name<?php echo $page_string; ?>"><?php echo _AT('category'); ?></a></th>
	<th scope="col"><?php echo _AT('enrolled'); ?></th>
	<th scope="col"><?php echo _AT('alumni'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="8"><input type="submit" name="view" value="<?php echo _AT('view'); ?>" /> 
					<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
					<input type="submit" name="backups" value="<?php echo _AT('backups'); ?>" /> 
					<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<?php if ($num_rows): ?>
	<?php while ($row = mysql_fetch_assoc($result)): ?>
		<tr onmousedown="document.form['m<?php echo $row['course_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['course_id']; ?>">
			<td><input type="radio" name="id" value="<?php echo $row['course_id']; ?>" id="m<?php echo $row['course_id']; ?>" /></td>
			<td><label for="m<?php echo $row['course_id']; ?>"><?php echo AT_print($row['title'], 'courses.title'); ?></label></td>
			<td><?php echo AT_print($row['login'],'members.login'); ?></td>
			<td><?php echo _AT($row['access']); ?></td>
			<td><?php echo $row['created_date']; ?></td>
			<td><?php echo ($row['cat_name'] ? $row['cat_name'] : '-')?></td>
			<td><?php echo ($enrolled[$row['course_id']]['y'] ? $enrolled[$row['course_id']]['y'] : 0); ?></td>
			<td><?php echo ($enrolled[$row['course_id']]['a'] ? $enrolled[$row['course_id']]['a'] : 0); ?></td>
		</tr>
	<?php endwhile; ?>
<?php else: ?>
	<tr>
		<td colspan="8"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>