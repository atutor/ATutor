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

if (!defined('AT_INCLUDE_PATH')) { exit; }

$existing_courses = array();

$cats	= array();
$cats[0] = _AT('cats_uncategorized');

$sql = "SELECT cat_id, cat_name FROM ".TABLE_PREFIX."course_cats";
$result = mysql_query($sql,$db);
while($row = mysql_fetch_array($result)) {
	$cats[$row['cat_id']] = $row['cat_name'];
}

// display existing courses if auto_enroll_id is given
// don't display this section when creating new record
if ($auto_enroll_id > 0)
{
	$sql_courses = "SELECT auto_enroll_courses.auto_enroll_courses_id auto_enroll_courses_id, 
	                       auto_enroll_courses.course_id,
	                       courses.cat_id,
	                       courses.title title
	                  FROM " . TABLE_PREFIX."auto_enroll_courses auto_enroll_courses, " . TABLE_PREFIX ."courses courses 
	                 where auto_enroll_courses.auto_enroll_id=".$auto_enroll_id .
	               "   and auto_enroll_courses.course_id = courses.course_id";

	$result_courses = mysql_query($sql_courses, $db) or die(mysql_error());
	
	if (mysql_num_rows($result_courses) > 0)
	{
?>
	<div class="row">
		<h4><label for="courses_table"><?php echo _AT('course_to_auto_enroll'); ?></label><br /></h4>
	</div>

	<div class="row">
		<table summary="" class="data" rules="cols" align="left" style="width: 70%;">
		
		<thead>
		<tr>
			<th scope="col"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall_delete" onclick="CheckAll('delete_ids[]', 'selectall_delete');" /></th>
			<th scope="col"><?php echo _AT('title'); ?></th>
			<th scope="col"><?php echo _AT('category'); ?></th>
		</tr>
		</thead>

		<tfoot>
		<tr>
			<td colspan="4">
				<div class="buttons">
				<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /> 
				</div>
			</td>
		</tr>
		</tfoot>

		<tbody>
<?php
if ($row_courses = mysql_fetch_assoc($result_courses)): 
	do {
		$existing_courses[] = $row_courses["course_id"];
	?>
			<tr onmousedown="document.form['m<?php echo $row_courses['auto_enroll_courses_id']; ?>'].checked = !document.form['m<?php echo $row_courses['auto_enroll_courses_id']; ?>'].checked; togglerowhighlight(this, 'm<?php echo $row_courses['auto_enroll_courses_id']; ?>');" id="rm<?php echo $row_courses['auto_enroll_courses_id']; ?>">
				<td width="10"><label for="m<?php echo $row_courses['title']; ?>"><input type="checkbox" name="delete_ids[]" value="<?php echo $row_courses['auto_enroll_courses_id']; ?>" id="m<?php echo $row_courses['auto_enroll_courses_id']; ?>" onmouseup="this.checked=!this.checked" /></label></td>
				<td><?php echo $row_courses['title']; ?></td>
				<td><?php echo $cats[$row_courses['cat_id']]; ?></td>
			</tr>
	<?php } while ($row_courses = mysql_fetch_assoc($result_courses)); ?>
<?php else: ?>
			<tr>
				<td colspan="3"><?php echo _AT('none_found'); ?></td>
			</tr>
<?php endif; ?>
		</tbody>
	</table>
	</div>
<?php
	}
}
// end of displaying existing auto enroll courses
?>

	<div class="row">
		<h4><label for="courses_table"><?php echo _AT('add_courses'); ?></label><br /></h4>
	</div>
	
<?php
require(AT_INCLUDE_PATH.'lib/admin_categories.inc.php');

if ($_POST['reset_filter']) { unset($_POST); }

$page_string = '';

if (isset($_POST['access']) && in_array($_POST['access'], array('public','private','protected'))) {
	$page_string .= SEP.'access='.$_POST['access'];
	$sql_access = "='{$_POST['access']}'";
} else {
	$sql_access     = '<>-1';
	$_POST['access'] = '';
}

if (isset($_POST['category']) && ($_POST['category'] > -1)) {
	$_POST['category'] = intval($_POST['category']);
	$page_string .= SEP.'category='.$_POST['category'];
	$sql_category = '='.$_POST['category'];
} else {
	$sql_category     = '<>-1';
	$_POST['category'] = -1; // all (because 0 = uncategorized)
}

if (isset($_POST['include']) && $_POST['include'] == 'one') {
	$checked_include_one = ' checked="checked"';
	$page_string .= SEP.'include=one';
} else {
	$_POST['include'] = 'all';
	$checked_include_all = ' checked="checked"';
	$page_string .= SEP.'include=all';
}

if (!empty($_POST['search'])) {
	$page_string .= SEP.'search='.urlencode($stripslashes($_POST['search']));
	$search = $addslashes($_POST['search']);
	$search = explode(' ', $search);

	if ($_POST['include'] == 'all') {
		$predicate = 'AND ';
	} else {
		$predicate = 'OR ';
	}

	$sql_search = '';
	foreach ($search as $term) {
		$term = trim($term);
		$term = str_replace(array('%','_'), array('\%', '\_'), $term);
		if ($term) {
			$term = '%'.$term.'%';
			$sql_search .= "((title LIKE '$term') OR (description LIKE '$term')) $predicate";
		}
	}
	$sql_search = '('.substr($sql_search, 0, -strlen($predicate)).')';
} else {
	$sql_search = '1';
}

$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE access $sql_access AND cat_id $sql_category AND $sql_search AND hide=0 ORDER BY title";
$courses_result = mysql_query($sql, $db);

// get the categories <select>, if there are any.
// we need ob_start/ob_clean, because select_categories() outputs directly.
// we do this so that if there are no categories, then the option doesn't appear.
ob_start();
select_categories(get_categories(), 0, $_POST['category'], false);
$categories_select = ob_get_contents();
ob_clean();

$has_categories = false;
if ($categories_select != '<option value="0"></option>') {
	$has_categories = true;
}

?>
			<div class="row">
				<?php echo _AT('access'); ?><br />
				<input type="radio" name="access" value="private" id="s1" <?php if ($_POST['access'] == 'private') { echo 'checked="checked"'; } ?> /><label for="s1"><?php echo _AT('private'); ?></label> 

				<input type="radio" name="access" value="protected" id="s2" <?php if ($_POST['access'] == 'protected') { echo 'checked="checked"'; } ?> /><label for="s2"><?php echo _AT('protected'); ?></label>

				<input type="radio" name="access" value="public" id="s3" <?php if ($_POST['access'] == 'public') { echo 'checked="checked"'; } ?> /><label for="s3"><?php echo _AT('public'); ?></label>

				<input type="radio" name="access" value="" id="s" <?php if ($_POST['access'] == '') { echo 'checked="checked"'; } ?> /><label for="s"><?php echo _AT('all'); ?></label>
			</div>

		<?php if ($has_categories): ?>
			<div class="row">
				<label for="category"><?php echo _AT('category'); ?></label><br/>
				<select name="category" id="category">
					<option value="-1">- - - <?php echo _AT('cats_all'); ?> - - -</option>
					<option value="0" <?php if ($_POST['category'] == 0) { echo 'selected="selected"'; } ?>>- - - <?php echo _AT('cats_uncategorized'); ?> - - -</option>
					<?php echo $categories_select; ?>
				</select>
			</div>
		<?php endif; ?>

			<div class="row">
				<label for="search"><?php echo _AT('search'); ?> (<?php echo _AT('title').', '._AT('description'); ?>)</label><br />

				<input type="text" name="search" id="search" size="40" value="<?php echo htmlspecialchars($_POST['search']); ?>" />
				<br/>
				<?php echo _AT('search_match'); ?>:
				<input type="radio" name="include" value="all" id="match_all" <?php echo $checked_include_all; ?> /><label for="match_all"><?php echo _AT('search_all_words'); ?></label> 
				<input type="radio" name="include" value="one" id="match_one" <?php echo $checked_include_one; ?> /><label for="match_one"><?php echo _AT('search_any_word'); ?></label>
			</div>

			<div class="row buttons">
				<input type="submit" name="filter" value="<?php echo _AT('filter'); ?>"/>
				<input type="submit" name="reset_filter" value="<?php echo _AT('reset_filter'); ?>"/>
			</div>

		<table summary="" class="data" rules="cols" align="left" style="width: 70%;">
		
		<thead>
		<tr>
			<th scope="col"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all" title="<?php echo _AT('select_all'); ?>" name="selectall_add" onclick="CheckAll('add_ids[]', 'selectall_add');" /></th>
			<th scope="col"><?php echo _AT('title'); ?></th>
			<th scope="col"><?php echo _AT('category'); ?></th>
		</tr>
		</thead>

		<tfoot>
		<tr>
			<td colspan="4">
				<div class="buttons">
				<input type="submit" name="add" value="<?php echo _AT('add'); ?>" /> 
				</div>
			</td>
		</tr>
		</tfoot>

		<tbody>
<?php
$num_of_rows = 0;
if ($row = mysql_fetch_assoc($courses_result)) ?>
	<?php
	do {
		if (!in_array($row['course_id'], $existing_courses))
		{
			$num_of_rows++;
	?>
				<tr onmousedown="document.form['a<?php echo $row['course_id']; ?>'].checked = !document.form['a<?php echo $row['course_id']; ?>'].checked; togglerowhighlight(this, 'a<?php echo $row['course_id']; ?>');" id="ra<?php echo $row['course_id']; ?>">
					<td width="10"><label for="a<?php echo $row['title']; ?>"><input type="checkbox" name="add_ids[]" value="<?php echo $row['course_id']; ?>" id="a<?php echo $row['course_id']; ?>" onmouseup="this.checked=!this.checked" /></label></td>
					<td><?php echo $row['title']; ?></td>
					<td><?php echo $cats[$row['cat_id']]; ?></td>
				</tr>
	<?php 
		}
	} while ($row = mysql_fetch_assoc($courses_result)); 
	
if ($num_of_rows == 0)
{ 
?>
			<tr>
				<td colspan="3"><?php echo _AT('none_found'); ?></td>
			</tr>
<?php 
} 
?>
		</tbody>
	</table>

<script language="JavaScript" type="text/javascript">
//<!--
function CheckAll(element_name, selectall_name) {
//	alert(document.form.elements.length);
	for (var i=0;i<document.form.elements.length;i++)	{
		var e = document.form.elements[i];
		if ((e.name == element_name) && (e.type=='checkbox')) {
			e.checked = eval("document.form." + selectall_name + ".checked");
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
