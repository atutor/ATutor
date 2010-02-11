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

require(AT_INCLUDE_PATH.'../mods/_core/cats_categories/lib/admin_categories.inc.php');

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

// calculate number of results found
$num_results = mysql_num_rows($courses_result);

while ($row = mysql_fetch_assoc($courses_result)) 
	if (in_array($row['course_id'], $existing_courses)) $num_results--;

if ($num_results > 0) mysql_data_seek($courses_result, 0);

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
	<div class="input-form" style="width:95%;">
		<div class="row">
			<h4><?php echo _AT('results_found', $num_results); ?></h4>
		</div>

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

		<div class="row">
		<table summary="" class="data" rules="cols" style="width: 95%; margin:auto;">
		
		<thead>
		<tr>
			<th scope="col"><input type="checkbox" value="<?php echo _AT('select_all'); ?>" id="all_add" title="<?php echo _AT('select_all'); ?>" name="selectall_add" onclick="CheckAll('add_ids[]', 'selectall_add');" /></th>
			<th scope="col"><?php echo _AT('title'); ?></th>
			<th scope="col"><?php echo _AT('category'); ?></th>
		</tr>
		</thead>

		<tfoot>
		<tr>
			<td colspan="4">
				<div class="buttons" style="float:left">
				<input type="submit" name="add" value="<?php echo _AT('add'); ?>" /> 
				</div>
			</td>
		</tr>
		</tfoot>

		<tbody>
<?php
if ($num_results == 0)
{
?>
		<tr>
			<td colspan="3"><?php echo _AT('none_found'); ?></td>
		</tr>
<?php 
}
else if ($row = mysql_fetch_assoc($courses_result)) 
	do {
		if (!in_array($row['course_id'], $existing_courses))
		{
	?>
		<tr onmousedown="document.form['a<?php echo $row['course_id']; ?>'].checked = !document.form['a<?php echo $row['course_id']; ?>'].checked; togglerowhighlight(this, 'a<?php echo $row['course_id']; ?>');" id="ra<?php echo $row['course_id']; ?>">
			<td width="10"><label for="ta<?php echo $row['course_id']; ?>"><input type="checkbox" name="add_ids[]" value="<?php echo $row['course_id']; ?>" id="a<?php echo $row['course_id']; ?>" onmouseup="this.checked=!this.checked" /></label></td>
			<td id="ta<?php echo $row['course_id']; ?>"><?php echo $row['title']; ?></td>
			<td><?php echo $cats[$row['cat_id']]; ?></td>
		</tr>
	<?php 
		}
	} while ($row = mysql_fetch_assoc($courses_result)); 
?>
		</tbody>
	</table>
	</div>
	
	<div class="row">
		&nbsp;
	</div>
<br style="clear:both;" />
</div>

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
