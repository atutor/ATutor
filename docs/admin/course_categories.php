<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');

require(AT_INCLUDE_PATH.'vitals.inc.php');

$onload = 'onload="document.form.category_name.focus()"';

if (!$_SESSION['s_is_super_admin']) {
	exit;
}

if($_POST['cancel'] ||$_GET['cancel']){
	Header('Location: course_categories.php?current_cat='.$_POST['category_parent'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}


$sql = "SELECT * from ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
$result = mysql_query($sql);
if(mysql_num_rows($result) != 0){
	while($row = mysql_fetch_array($result)){
		$current_cats[$row['cat_id']] = $row['cat_name'];
		$parent_cats[$row['cat_id']] =  $row['cat_parent'];
		$cat_cats[$row['cat_id']] = $row['cat_id'];

	}
}

function print_course_cats($parent_cat_id, &$cats, $cat_row, $depth=0) {
	$my_cats = $cats[$parent_cat_id];
	global $parent_cats;
	if (!is_array($my_cats)) {
		return;
	}
	foreach ($my_cats as $cat) {
		if($_GET['add']){
			if($cat['cat_id'] != $_GET['current_cat']){
				echo '<option value="'.$cat['cat_id'].'"';
				echo '>';
				echo str_pad('', $depth, '-');
				echo $cat['cat_name'].'</option>'."\n";
			}else if($_GET['current_cat'] == $cat['cat_id']){
				echo '<option value="'.$cat['cat_id'].'"';
				echo ' selected="selected">';
				echo str_pad('', $depth, '-');
				echo $cat['cat_name'].'</option>'."\n";
			}
		}else if($_GET['edit']){
			if($cat['cat_id'] != $_GET['current_cat'] && $parent_cats[$_GET['current_cat']] != $cat['cat_id']){
				echo '<option value="'.$cat['cat_id'].'"';
				echo '>';
				echo str_pad('', $depth, '-');
				echo $cat['cat_name'].'</option>'."\n";
			}else if($parent_cats[$_GET['current_cat']] == $cat['cat_id']){
				echo '<option value="'.$cat['cat_id'].'"';
				echo ' selected="selected">';
				echo str_pad('', $depth, '-');
				echo $cat['cat_name'].'</option>'."\n";
			}
		}
		print_course_cats($cat['cat_id'], $cats,  $cat_row, $depth+1);
	}
}

if($_GET['show_courses']){
	$show_courses = $_GET['show_courses'];
}else if($_POST['show_courses']){
	$show_courses = $_POST['show_courses'];
}else{
	$show_courses = $_GET['current_cat'];
}

if($_POST['add']){
	$sql = "SELECT * from ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result)){
		$current_cats[$row['cat_id']] = $row['cat_name'];
	}
	if($_POST[category_name] != ''){
			if(!in_array($_POST['category_name'],  $current_cats)){
				$sql = "INSERT into ".TABLE_PREFIX." course_cats VALUES(0, '$_POST[category_name]', '$_POST[category_parent]')";
				$result = mysql_query($sql);
				if(!$result){
					$errors[] = AT_ERROR_CAT_NOT_INSERTED;
				}else{
					Header('Location: course_categories.php?current_cat='.$_POST['category_parent'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CAT_ADDED));
					exit;
				}
			}else{
				$infos[] = AT_INFOS_NO_CATEGORIES;
			}

	}else{
		$errors[] = AT_ERROR_CAT_NO_NAME;
	}

}


if($_GET['delete'] == 1){
	$_GET['current_cat'] = intval($_GET['current_cat']);

	$sql = "SELECT *FROM ".TABLE_PREFIX."course_cats WHERE cat_parent = '$_GET[current_cat]'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0){
		$errors[] = AT_ERROR_CAT_HAS_SUBS;
	}else{
		if($_GET['confirm'] == 1){
			$sql="DELETE from ".TABLE_PREFIX."course_cats WHERE cat_id = '$_GET[current_cat]'";
			$result = mysql_query($sql);
			$sql2 = "UPDATE ".TABLE_PREFIX."course_cats SET cat_parent = '0' WHERE cat_parent = '$_GET[current_cat]' " ;
			$result2 = mysql_query($sql2);
			$sql3 = "UPDATE ".TABLE_PREFIX."courses SET cat_id = '0' WHERE cat_id = '$_GET[current_cat]' " ;
			$result3 = mysql_query($sql3);
			if(!$result2){
				$errors[] = AT_ERROR_CAT_UPDATE_FAILED;
			}
			if(!$result3){
				$errors[] = AT_ERROR_CAT_UPDATE_FAILED;
			}
			if(!$result){
					$errors[] = AT_ERROR_CAT_DELETE_FAILED;
			}else{
				Header('Location: course_categories.php?current_cat='.$parent_cats[$_GET['current_cat']].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CAT_DELETED));
				exit;
			}

		}else{
			$warnings[] = array(AT_WARNING_DELETE_CAT_CATEGORY , $current_cats[$_GET['current_cat']]);
		}
	}
}

$sql = "SELECT * from ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
$result = mysql_query($sql);
//echo mysql_num_rows($result);
if(mysql_num_rows($result) != 0){
	while($row = mysql_fetch_array($result)){
		$current_cats[$row['cat_id']] = $row['cat_name'];
		$parent_cats[$row['cat_id']] =  $row['cat_parent'];
		$cat_cats[$row['cat_id']] = $row['cat_id'];

	}
}

$cat_path_len = (strlen($current_cats[$_GET['prev_cat']]));
$path_len = strlen($_GET['cat_path']);
if($cat_path_len != ''){
	$cat_path_len = ($cat_path_len + 1);
}

if($_POST['edit'] == 1 && !$_POST['cancel']){

	$sql = "UPDATE ".TABLE_PREFIX."course_cats SET cat_parent = '$_POST[category_parent]' , cat_name = '$_POST[category_name]' WHERE cat_id = '$_POST[cat_id]' " ;
	$result = mysql_query($sql);
	if(!$result){
		$errors[] = AT_ERRORS_CAT_UPDATE_FAILED;
	}else{

			Header('Location: course_categories.php?current_cat='.$_POST['cat_id'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CAT_UPDATE_SUCCESSFUL));
			exit;


	}
}

require(AT_INCLUDE_PATH.'admin_html/header.inc.php');

$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name ";
$result = mysql_query($sql);

echo '<h2><a href="'.$_SERVER['PHP_SELF'].'">'._AT('cats_course_categories').'</a></h2>';

if($_GET['delete'] && !$_GET['confirm']){
	echo '<h3>'._AT('cats_delete_categories').'</h3>';
}


if($_GET['edit'] == 1){
	?>
	<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="form">
	<input type="hidden" name="show_courses" value="<? echo $show_courses; ?>" />
	<input type="hidden" name="edit" value="1" />
	<input type="hidden" name="cat_id" value="<?php echo $_GET['current_cat'] ?>" />
	<input type="hidden" name="parent_cat" value="<?php echo $_GET['parent_cat']; ?>" />
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr><th colspan="2"><?php echo _AT('cats_edit_categories').' '.$current_cats[$_GET['current_cat']]; ?></th></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td class="row1"><label for="category_name"><?php echo _AT('cats_category_name'); ?></label>:</td><td class="row1"><input type="text" id="category_name" name="category_name" value="<?php echo $current_cats[$_GET['current_cat']]; ?>"  class="formfield"/></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td class="row1"><label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label>:</td><td class="row1"><select name="category_parent"  id="category_parent" class="formfield">
	<?php
		$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats";
		$result = mysql_query($sql);

		$cats = array();

		while($row = mysql_fetch_assoc($result)){
			$cats[$row['cat_parent']][] = $row;
		}

		$cat_row = $parent_cats[$_GET['current_cat']];
		echo '<option value="0"> - '._AT('cats_none').' - </option>';
		unset($cats[$_GET['current_cat']]);
		print_course_cats(0, $cats, $cat_row);
	?>
	</select>
	</td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td colspan="2" class="row1" align="right">
	<input type="submit" name="submit" value="<?php echo _AT('cats_edit_categories'); ?>" class="button" accesskey="s" />
	<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button" />
	</td></tr>
	</table>
	</form>
	<?php
} else if($_GET['add'] == 1) {
	?>
	<form action ="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" name="form">
	<input type="hidden" name="show_courses" value="<? echo $show_courses ?>" />
	<input type="hidden" name="add" value="1" />
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
	<tr><th colspan="2"><?php echo _AT('cats_add_categories'); ?>
	<?php
		if($_GET['current_cat']){
			echo _AT('to').' '.$current_cats[$_GET['current_cat']];
		}
	?>
	</th></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td class="row1"><label for="category_name"><?php echo _AT('cats_new_category_name'); ?></label>:</td><td class="row1"><input type="text" name="category_name" value="" class="formfield" id="category_name" /></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td class="row1"><label for="category_parent"><?php echo _AT('cats_parent_category'); ?></label>:</td><td class="row1"><select name="category_parent"  id="category_parent" class="formfield">
	<?php

		$sql = "SELECT * FROM ".TABLE_PREFIX."course_cats ORDER BY cat_name";
		$result = mysql_query($sql);

		$cats = array();

		while($row = mysql_fetch_assoc($result)){
			$cats[$row['cat_parent']][] = $row;
		}

		$cat_row = $_GET['current_cat'];
		echo '<option value="0"> -'._AT('cats_none').' - </option>'."\n";
		print_course_cats(0, $cats, $cat_row);
	?>
	</select>
	</td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td colspan="2" class="row1" align="right">
	<input type="submit" name="submit" value="<?php echo _AT('cats_add_categories'); ?>" class="button" accesskey="s">
	<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" class="button">
	</td></tr>
	</table>
	</form>

<?php
}
?>
<?php

require(AT_INCLUDE_PATH.'html/browse_categories.inc.php');

require(AT_INCLUDE_PATH.'admin_html/footer.inc.php');
?>