<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }

require_once(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
require_once(AT_INCLUDE_PATH.'lib/admin_categories.inc.php');

if (!isset($isadmin, $course_id, $db)) {
	return;	
}

if (isset($_POST['form_course'])) {
	$row['course_id']			= $_POST['course_id'];
	$row['title']				= $_POST['title'];
	$row['primary_language']	= $_POST['primary_language'];
	$row['member_id']			= $_POST['member_id'];
	$row['description']			= $_POST['description'];
	$row['cat_id']				= $_POST['cat_id'];
	$row['content_packaging']	= $_POST['content_packaging'];

	$row['access']				= $_POST['access'];
	$row['notify']				= $_POST['notify'];

	$row['max_quota']			= $_POST['max_quota'];
	$row['max_file_size']		= $_POST['max_file_size'];
	$row['tracking']			= $_POST['tracking'];

	$row['created_date']		= date('Y-m-d');
	$row['primary_language']    = $_POST['pri_lang'];

} else if ($course_id) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$course_id";
	$result = mysql_query($sql, $db);
	if (!($row	= mysql_fetch_assoc($result))) {
		echo _AT('no_course_found');
		return;
	}
} else {
	//new course defaults
	$row['content_packaging']	= 'top';
	$row['access']				= 'protected';
	$row['notify']				= '';
	$row['hide']				= '';

	$row['max_quota']			= AT_COURSESIZE_DEFAULT;
	$row['max_file_size']		= AT_FILESIZE_DEFAULT;
	$row['tracking']			= 'off';

	$row['primary_language']	= $_SESSION['lang'];
	$row['created_date']		= date('Y-m-d');
}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="course_form">
	<input type="hidden" name="form_course" value="true" />
	<input type="hidden" name="course_id" value="<?php echo $course_id; ?>" />
	<input type="hidden" name="old_access" value="<?php echo $row['access']; ?>" />
	<input type="hidden" name="created_date" value="<?php echo $row['created_date']; ?>" />
	<input type="hidden" name="show_courses" value="<?php echo $_GET['show_courses']; ?>" />
	<input type="hidden" name="current_cat" value="<?php echo $_GET['current_cat']; ?>" />

<?php if ($isadmin && $course_id) : ?>

<?php
	$sql_instructor	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=".$row['member_id'];
	$result_instructor= mysql_query($sql_instructor, $db);
	if (!($row_instructor = mysql_fetch_array($result_instructor))) {
		echo _AT('no_user_found');
	}
?>
<table cellspacing="1" cellpadding="0" border="0" summary="" width="95%" summary="" align="center">
<tr>
<td width="47%" valign="top">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="98%" align="center">
	<tr>
		<th width="50%" colspan="2" class="cyan"><?php echo _AT('course_information'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right"><strong><?php  echo _AT('title'); ?>:</strong></td>
		<td class="row1"><?php echo $row['title']; ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td class="row1" align="right"><strong><?php  echo _AT('course_id'); ?>:</strong></td>
		<td class="row1"><?php echo $row['course_id']; ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td class="row1" nowrap="nowrap" align="right"><strong><?php  echo _AT('created_date'); ?>:</strong></td>
		<td class="row1"><?php echo AT_date('%F %j, %Y', $row['created_date'], AT_DATE_MYSQL_TIMESTAMP_14); ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td class="row1" align="right"><strong><?php  echo _AT('notify'); ?>:</strong></td>
		<td class="row1"><?php echo ($row['notify'] ? _AT('yes') : _AT('no')); ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td class="row1" align="right"><strong><?php  echo _AT('enrolled'); ?>:</strong></td>
		<td class="row1"><?php 
			$sql	  = "SELECT COUNT(*) FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$row[course_id] AND approved='y'";
			$c_result = mysql_query($sql, $db);
			$c_row	  = mysql_fetch_array($c_result);

			echo ($c_row[0]-1);
		?></td>
	</tr>
	</table>
</td><td width="47%">
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="98%" align="center">
	<tr>
		<th colspan="2" class="cyan"><?php  echo _AT('instructor_information'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right"><strong><?php  echo _AT('username'); ?>:</strong></td>
		<td class="row1"><?php 
			echo '<a href="admin/profile.php?member_id='.$row['member_id'].'">'.get_login($row['member_id']).'</a>';
		?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
	<td class="row1" align="right"><strong><?php echo _AT('email_address'); ?>:</strong></td>
		<td class="row1"><a href="mailto:<?php echo $row_instructor['email']; ?>"><?php echo $row_instructor['email']; ?></a></td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td class="row1" align="right"><strong><?php echo _AT('real_name'); ?>:</strong></td>
		<td class="row1"><?php echo $row_instructor['first_name'].' '.$row_instructor['last_name']; ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td class="row1" align="right"><strong><?php echo _AT('member_id'); ?>:</strong></td>
		<td class="row1"><?php echo $row_instructor['member_id']; ?></td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr>
		<td colspan="2" class="row1" align="center"><?php echo '<a href="admin/courses.php?member_id='.$row['member_id'].'"><strong>'. _AT('view_courses_taught') .'</strong></a>'; ?></td>
	</tr>
	</table>

</td>
</tr>
</table>

<br />
<?php endif; ?>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('course_properties'); ?></th>
</tr>
<?php if ($isadmin) { ?>
<tr>
	<td class="row1" align="right" nowrap="nowrap"><strong><label for="inst"><?php echo  _AT('instructor'); ?>:</label></strong></td>
	<td class="row1"><select name="instructor" id="inst">
		<?php 
		//see include/lib/filemanager.inc.php
		output_instructors($row['member_id']); ?>
		</select>
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php } ?>
<tr>
	<td class="row1" align="right" nowrap="nowrap"><strong><label for="title"><?php echo  _AT('title'); ?>:</label></strong></td>
	<td class="row1"><input type="text" id="title" name="title" class="formfield" size="40" value="<?php echo stripslashes(htmlspecialchars($row['title'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td nowrap="nowrap" class="row1" align="right"><strong><label for="pri_lang"><?php  echo _AT('primary_language'); ?>:</label></strong></td>
	<td class="row1"><?php $languageManager->printDropdown($row['primary_language'], 'pri_lang', 'pri_lang'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><strong><label for="description"><?php echo _AT('description'); ?>:</label></strong></td>
	<td class="row1"><textarea id="description" cols="45" rows="4" class="formfield" name="description"><?php echo $row['description']; ?></textarea></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td class="row1" align="right"><strong><label for="cat"><?php echo _AT('category'); ?>:</label></strong></td><td class="row1">
<?php
	$categories = get_categories();

	if (is_array($categories)) {

		echo '<select name="category_parent" id="cat">';
		echo '<option value="0">&nbsp;&nbsp;&nbsp;[ '._AT('cats_uncategorized').' ]&nbsp;&nbsp;&nbsp;</option>';
		echo '<option value="0"></option>';

		select_categories($categories, 0, $row['cat_id'], false);

		echo '</select>';
	} else {
		echo _AT('no_cats').'<span id="cat"></span>';
	}
?>
</td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right" nowrap="nowrap"><strong><?php  echo _AT('content_packaging'); ?>:</strong></td>
	<td class="row1"><?php
		
		switch ($row['content_packaging']) {
			case 'none':
					$none = ' checked="checked"';
					break;

			case 'top':
					$top	 = ' checked="checked"';
					break;

			case 'all':
					$all	= ' checked="checked"';
					break;
		}
?>
		<label><input type="radio" name="content_packaging" value="none" id="none" <?php echo $none; ?> /><?php echo _AT('content_packaging_none'); ?></label><br />
		<label><input type="radio" name="content_packaging" value="top" id="ctop"  <?php echo $top; ?> /><?php  echo _AT('content_packaging_top'); ?></label><br />
		<label><input type="radio" name="content_packaging" value="all" id="all" <?php echo $all; ?> /><?php  echo _AT('content_packaging_all'); ?></label><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td  class="row1" valign="top" align="right"><strong><?php echo  _AT('access'); ?>:</strong></td>
<?php
		switch ($row['access']) {
			case 'public':
					$pub = ' checked="checked"';
					$disable = 'disabled="disabled"'; // disable the nofity box
					break;

			case 'protected':
					$prot	 = ' checked="checked"';
					$disable = 'disabled="disabled"'; // disable the nofity box
					break;

			case 'private':
					$priv	= ' checked="checked"';
					break;
		}

		if ($row['notify']) {
			$notify = ' checked="checked"';
		}

		if ($row['hide']) {
			$hide = ' checked="checked"';
		}
?>
	<td class="row1"><input type="radio" name="access" value="public" id="pub" onclick="disableNotify();" <?php echo $pub; ?> /><label for="pub"><strong> <?php echo  _AT('public'); ?>: </strong></label><?php echo  _AT('about_public'); ?><br /><br />

		<input type="radio" name="access" value="protected" id="prot" onclick="disableNotify();" <?php echo $prot; ?> /><label for="prot"><strong><?php echo  _AT('protected'); ?>:</strong></label> <?php echo  _AT('about_protected'); ?>

		<br /><br />
		<input type="radio" name="access" value="private" id="priv" onclick="enableNotify();" <?php echo $priv; ?> /><label for="priv"><strong><?php echo  _AT('private'); ?>:</strong></label> <?php echo  _AT('about_private'); ?>
		<br />
		<input type="checkbox" name="notify" id="notify" value="1" <?php
			echo $disable;
			echo $notify; ?> /><label for="notify"><?php echo  _AT('email_approvals'); ?></label>
		<br />
		<input type="checkbox" name="hide" id="hide" value="1" <?php
		echo $disable;
		echo $hide; ?> /><label for="hide"><?php echo  _AT('hide_course'); ?></label>.

		<br />
		<br /></td>
</tr>

<?php if (!$course_id) : ?>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" valign="top" align="right"><strong><label for="initial_content"><?php echo _AT('initial_content'); ?></label>:</strong></td>
		<td class="row1">
		<select name="initial_content" id="initial_content" size="5">
			<option value="0"><?php echo _AT('empty'); ?></option>
			<option value="1"><?php echo _AT('create_basic'); ?></option>

			<?php 
			$Backup =& new Backup($db);

			if ($isadmin) {
				$sql	= "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
			} else {
				$sql	= "SELECT course_id, title FROM ".TABLE_PREFIX."courses WHERE member_id=$_SESSION[member_id] ORDER BY title";
			}

			$result = mysql_query($sql, $db);

			if ($course = mysql_fetch_assoc($result)) {
				echo '<option value="0">'. _AT('restore').':</option>';

				do {
					$Backup->setCourseID($course['course_id']);
					$list = $Backup->getAvailableList();

					if (!empty($list)) { 
						echo '<optgroup label="'.$course['title'].'">';
						foreach ($list as $list_item) {
							echo '<option value="'.$list_item['backup_id'].'_'.$list_item['course_id'].'">'.$list_item['file_name'].' - '.get_human_size($list_item['file_size']).'</option>';
						}
						echo '</optgroup>';
					}
				} while ($course = mysql_fetch_assoc($result));
			}
			?>
			</select>
		</td>
	</tr>

<?php endif; // !$course_id ?>

<?php if ($isadmin) : ?>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right">
		<?php 
			$help = array(AT_HELP_COURSE_QUOTA, AT_KBYTE_SIZE, AT_KBYTE_SIZE*AT_KBYTE_SIZE); 		
			print_popup_help($help); 
		?>
		<strong><?php  echo _AT('course_quota'); ?>:</strong></td>
		<td class="row1"><?php 
		if ($row['max_quota'] == AT_COURSESIZE_UNLIMITED) { 
			$c_unlim = ' checked="checked" ';
			$c_oth2 = ' disabled="disabled" ';
		} elseif ($row['max_quota'] == AT_COURSESIZE_DEFAULT) {
			$c_def = ' checked="checked" ';
			$c_oth2 = ' disabled="disabled" ';
		} else {
			$c_oth = ' checked="checked" ';
			$c_oth2 = '';
		}

		$course_size = dirsize(AT_CONTENT_DIR . $course_id.'/');
		if ($course_size < AT_KBYTE_SIZE) {
			$course_size = round($course_size);
			$course_size = $course_size .' '._AT('bytes'); 
		} else {
			$course_size = round(bytes_to_megabytes($course_size),2) .' '._AT('megabytes'); 
		}

		if ($course_id) {
			echo _AT('current_course_size') .': '.$course_size.'<br />'; 
		}
		?>

		<input type="radio" id="c_default" name="quota" value="<?php echo AT_COURSESIZE_DEFAULT; ?>" onclick="disableOther();" <?php echo $c_def;?> /><label for="c_default"> <?php echo _AT('default') . ' ('.get_human_size($MaxCourseSize).')'; ?></label> <br />
		<input type="radio" id="c_unlim" name="quota" value="<?php echo AT_COURSESIZE_UNLIMITED; ?>" onclick="disableOther();" <?php echo $c_unlim;?>/><label for="c_unlim"> <?php echo _AT('unlimited'); ?></label> <br />
		<input type="radio" id="c_other" name="quota" value="2" onclick="enableOther();" <?php echo $c_oth;?>/><label for="c_other"> <?php echo _AT('other'); ?> </label> - 
		<input type="text" id="quota_entered" name="quota_entered" class="formfieldR" <?php echo $c_oth2?> value="<?php if ($row['max_quota']!=AT_COURSESIZE_UNLIMITED && $row['max_quota']!=AT_COURSESIZE_DEFAULT) { echo get_human_size($row['max_quota']); } ?>" size="4" /> 
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><strong><?php  echo _AT('max_file_size'); ?>:</strong></td>
		<td class="row1">
	<?php 
		$max_allowed = megabytes_to_bytes(substr(ini_get('upload_max_filesize'), 0, -1));

		if ($row['max_file_size'] == AT_FILESIZE_DEFAULT) { 
			$f_def = ' checked="checked" ';
			$f_oth2 = ' disabled="disabled" ';
		} elseif ($row['max_file_size'] == AT_FILESIZE_SYSTEM_MAX) {
			$f_max = ' checked="checked" ';
			$f_oth2 = ' disabled="disabled" ';
		} else {
			$f_oth = ' checked="checked" ';
			$f_oth2 = '';
		}
	?>
		<input type="radio" id="f_default" name="filesize" value="<?php echo AT_FILESIZE_DEFAULT; ?>" onclick="disableOther2();" <?php echo $f_def;?> /><label for="f_default"> <?php echo _AT('default') . ' ('.get_human_size($MaxFileSize).')'; ?></label> <br />
		<input type="radio" id="f_maxallowed" name="filesize" value="<?php echo AT_FILESIZE_SYSTEM_MAX; ?>" onclick="disableOther2();" <?php echo $f_max;?>/><label for="f_maxallowed"> <?php echo _AT('max_file_size_system') . ' ('.get_human_size($max_allowed).')'; ?></label> <br />
		<input type="radio" id="f_other" name="filesize" value="2" onclick="enableOther2();" <?php echo $f_oth;?>/><label for="f_other"> <?php echo _AT('other'); ?> </label> - 
		<input type="text" id="filesize_entered" name="filesize_entered" class="formfieldR" <?php echo $f_oth2?> value="<?php if ($row['max_file_size']!=AT_FILESIZE_DEFAULT && $row['max_file_size']!=AT_FILESIZE_SYSTEM_MAX) { echo get_human_size($row['max_file_size']); } ?>" size="4" />
		</td>	

	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><strong><?php  echo _AT('tracking'); ?>:</strong></td>
		<td class="row1">		<?php
			if($row['tracking'] == 'on'){
				$on = ' checked="checked" ';
			} else {
				$off = ' checked="checked" ';
			}
			?>
			<input type="radio" name="tracking" value="off" id="toff" <?php echo $off; ?> /><label for="toff"><?php  echo _AT('off'); ?></label> <input type="radio" name="tracking" value="on" id="ton"<?php echo $on; ?> /><label for="ton"><?php  echo _AT('on'); ?></label>
		</td>
	</tr>

<?php endif; ?>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td  class="row1" colspan="2" align="center"><input type="submit" name="submit" class="button" value="<?php echo  _AT('submit'); ?>" accesskey="s" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel');?>" class="button" /></td>
</tr>
</table>
</form>


<?php if ($isadmin) : ?>
	<p style="width: 95%"><small class="spacer">* <?php echo _AT('default_max'); ?></small></p>
<?php endif; ?>

<script language="javascript" type="text/javascript">
<!--
function enableNotify() {
	document.course_form.notify.disabled = false;
	document.course_form.hide.disabled = false;
}

function disableNotify() {
	document.course_form.notify.disabled = true;
	document.course_form.hide.disabled = true;
}

function enableOther()		{ document.course_form.quota_entered.disabled = false; }
function disableOther()		{ document.course_form.quota_entered.disabled = true; }
function enableOther2()		{ document.course_form.filesize_entered.disabled = false; }
function disableOther2()	{ document.course_form.filesize_entered.disabled = true; }

// -->
</script>