<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

//check if user is in admin or user course properities page 
if ($_SESSION['course_id'] == -1) {
	$isadmin = 1;
} else {
	$isadmin = 0;
}

if ($_POST['cancel']) {
	if ($isadmin && $_REQUEST['show_courses']!="") {
		Header ('Location: '.$_base_href.'users/admin/course_categories.php?course='.$_REQUEST['course'].SEP.'this_course='.$_REQUEST['course'].SEP.'show_courses='.$_REQUEST['show_courses'].SEP.'current_cat='.$_REQUEST['current_cat'].SEP.'f='.AT_FEEDBACK_CANCELLED);
	} else if ($isadmin) {		
		Header ('Location: '.$_base_href.'admin/courses.php?f='.AT_FEEDBACK_CANCELLED);
	} else {
		Header ('Location: '.$_base_href.'tools/index.php?f='.AT_FEEDBACK_CANCELLED);
	}
	exit;
}
if($_REQUEST['course']){
	$course = intval($_REQUEST['course']);
}else{
	$course = intval($_REQUEST['course_id']);
}

if ($_POST['form_course']) {

  if ($_POST['form_title'] == '') {
	$errors[] = AT_ERROR_TITLE_EMPTY;
  }
  else {	
	$form_course_id = intval($_POST['form_course_id']);
	$form_notify	= intval($_POST['form_notify']);
	$form_hide		= intval($_POST['form_hide']);
	$form_instructor= intval($_POST['form_instructor']);

	/* if the access is changed from private to public/protected then automatically enroll all those waiting for approval. */
	if ( ($_POST['old_access'] == 'private') && ($_POST['form_access'] != 'private') ) {
		$sql = "UPDATE ".TABLE_PREFIX."course_enrollment SET approved='y' WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}

	if ($isadmin) {
		$quota  = intval($_POST['quota']);
		$filesize= intval($_POST['filesize']);
		$cat	= intval($_POST['category_parent']);
		$_POST['form_title']       = $addslashes($_POST['form_title']);
		$_POST['form_description'] = $addslashes($_POST['form_description']);

		if (intval($_POST['tracking'])) {
			$tracking = _AT('on');
		} else {
			$tracking = _AT('off');
		}

		$feedback[] = AT_FEEDBACK_COURSE_UPDATED;

		//if they checked 'other', set quota=entered value, if it is empty or negative, set to default (-2)
		if ($quota == '2') {
			if ($quota_entered=='' || empty($quota_entered) || $quota_entered<0 ) {
				$quota = AT_COURSESIZE_DEFAULT;				
			} else {
				$quota = floatval($quota_entered);
				$quota = megabytes_to_bytes($quota);
			}
		}

		//if they checked 'other', set filesize=entered value, if it is empty or negative, set to default 
		if ($filesize=='2') {
			if ($filesize_entered=='' || empty($filesize_entered) || $filesize_entered<0 ) {
				$filesize = AT_FILESIZE_DEFAULT;
				$feedback[] = AT_FEEDBACK_COURSE_DEFAULT_FSIZE;
			} else {
				$filesize = floatval($filesize_entered);
				$filesize = kilobytes_to_bytes($filesize);
			}
		}

		$sql	= "REPLACE INTO ".TABLE_PREFIX."course_enrollment VALUES ($form_instructor, $form_course_id, 'y', 0, '"._AT('instructor')."', 0)";
		$result = mysql_query($sql, $db);

		$sql	= "UPDATE ".TABLE_PREFIX."courses SET member_id='$form_instructor', access='$_POST[form_access]', title='$_POST[form_title]', description='$_POST[form_description]', cat_id='$_POST[category_parent]', content_packaging='$_POST[packaging]', notify=$form_notify, hide=$form_hide, cat_id = $cat, max_quota=$quota, max_file_size=$filesize, tracking='$_POST[tracking]', primary_language='$_POST[pri_lang]' WHERE course_id=$form_course_id";
		$result = mysql_query($sql, $db);
		if (!$result) {
			echo 'DB Error';
			exit;
		}
		cache_purge('system_courses','system_courses');

		if ($_REQUEST['show_courses']!="") {
			Header ('Location: '.$_base_href.'users/admin/course_categories.php?course='.$_REQUEST['course'].SEP.'this_course='.$_REQUEST['course'].SEP.'show_courses='.$_REQUEST['show_courses'].SEP.'current_cat='.$_REQUEST['current_cat'].SEP.'f='.urlencode_feedback($feedback));
			exit;
		} else {
			Header('Location: courses.php?f='.urlencode_feedback($feedback));
			exit;
		}

	} else {
		$_POST['form_title']       = $addslashes($_POST['form_title']);
		$_POST['form_description'] = $addslashes($_POST['form_description']);
		$_POST['pri_lang']         = $addslashes($_POST['pri_lang']);

		$sql = "UPDATE ".TABLE_PREFIX."courses SET access='$_POST[form_access]', title='$_POST[form_title]', description='$_POST[form_description]', cat_id='$_POST[category_parent]', content_packaging='$_POST[packaging]', notify=$form_notify, hide=$form_hide, primary_language='$_POST[pri_lang]' WHERE course_id=$form_course_id AND member_id=$_SESSION[member_id]";

		$result = mysql_query($sql, $db);

		if (!$result) {
			echo 'DB Error';
			exit;
		}
		$_SESSION['course_title'] = $_POST['form_title'];
		cache_purge('system_courses','system_courses');
		Header ('Location: '.$_base_href.'tools/index.php?f='.urlencode_feedback(AT_FEEDBACK_COURSE_PROPERTIES));
		exit;
	}
  }
}

if ($isadmin) { 
	$_user_location = 'admin';
	require(AT_INCLUDE_PATH.'header.inc.php'); 
} else {
	$title = _AT('course_properties');
	require(AT_INCLUDE_PATH.'header.inc.php');
	//echo '<h2>'._AT('course_properties').'</h2>';
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide" ><img src="images/icons/default/square-large-tools.gif" vspace="2" border="0"  class="menuimageh2" width="42" height="40" alt="" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/" class="hide" >'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/course-properties-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('course_properties');
	}
echo '</h3>';
}

if ($isadmin) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
} else if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
	$course = $_SESSION['course_id'];
} else {
	return;
}
$result = mysql_query($sql, $db);
if (!($row	= mysql_fetch_assoc($result))) {
	echo _AT('no_course_found');
	return;
}
$cat_row = $row['cat_id'];

?>
<a name="content"></a>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?course='.$_GET['course']; ?>" name="course_form">
<input type="hidden" name="form_course" value="true" />
<input type="hidden" name="form_course_id" value="<?php echo $course; ?>" />
<input type="hidden" name="old_access" value="<?php echo $row['access']; ?>" />
<input type="hidden" name="course" value="<?php echo $_GET['course']; ?>" />
<input type="hidden" name="show_courses" value="<?php echo $_GET['show_courses']; ?>" />
<input type="hidden" name="current_cat" value="<?php echo $_GET['current_cat']; ?>" />

<?php if ($isadmin) { echo '<h2>'._AT('course_properties').'</h2>'; } ?>

<?php
if (isset($_GET['f'])) { 
	$f = intval($_GET['f']);
	if ($f <= 0) {
		/* it's probably an array */
		$f = unserialize(urldecode($_GET['f']));
	}
	print_feedback($f);
}
if (isset($errors)) { print_errors($errors); }
if(isset($warnings)){ print_warnings($warnings); }

if ($isadmin) { 		
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
		<td class="row1"><?php echo AT_date('%F %j, %Y', $row['created_date'], AT_DATE_MYSQL_TIMESTAMP_14); ?>
		
		</td>
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
<?php } ?>

<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php echo _AT('course_properties'); ?></th>
</tr>
<tr>
	<td class="row1" align="right" nowrap="nowrap"><strong><label for="title"><?php echo  _AT('title'); ?>:</label></strong></td>
	<td class="row1"><input type="text" id="title" name="form_title" class="formfield" size="40" value="<?php echo stripslashes(htmlspecialchars($row['title'])); ?>" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td nowrap="nowrap" class="row1" align="right"><strong><label for="pri_lang"><?php  echo _AT('primary_language'); ?>:</label></strong></td>
	<td class="row1"><select name="pri_lang" id="pri_lang"><?php

						foreach($available_languages as $lang_code => $lang_info) {
							echo '<option value="'.$lang_code.'"';
							if ($lang_code == $row['primary_language']) {
								echo ' selected="selected"';
							}
							echo '>'.$lang_info[3].'</option>';
						}

					?></select></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>

<?php if ($isadmin) { ?>
<tr>
	<td class="row1" align="right" nowrap="nowrap"><strong><label for="inst"><?php echo  _AT('instructor'); ?>:</label></strong></td>
	<td class="row1"><select name="form_instructor" id="inst">
		
		<?php output_instructors($row['member_id']); ?>
		</select>
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<?php } ?>

<tr>
	<td class="row1" valign="top" align="right"><strong><label for="description"><?php echo _AT('description'); ?>:</label></strong></td>
	<td class="row1"><textarea id="description" cols="45" rows="4" class="formfield" name="form_description"><?php echo $row['description']; ?></textarea></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td class="row1" align="right"><strong><label for="cat"><?php echo _AT('category'); ?>:</label></strong></td><td class="row1">
<?php
	$categories = get_categories();

	if (is_array($categories)) {

		echo '<select name="category_parent" id="cat">';
		echo '<option value="0">&nbsp;&nbsp;&nbsp;[ '._AT('cats_uncategorized').' ]&nbsp;&nbsp;&nbsp;</option>';
		echo '<option value="0"></option>';

		select_categories($categories, 0, $cat_row, false);

		echo '</select>';
	} else {
		echo _AT('cats_uncategorized').'<span id="cat"></span>';
	}
?>
</td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right" nowrap="nowrap"><strong><?php  echo _AT('content_packaging'); ?>:</strong></td>
	<td class="row1">
	
<?php

//echo $row['content_packaging'];

		switch ($row['content_packaging'])
		{

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


	<input type="radio" name="packaging" value="none" id="none" <?php echo $none; ?> /><label for="none"><?php echo _AT('content_packaging_none'); ?></label><br /><br />

	<input type="radio" name="packaging" value="top" id="ctop"  <?php echo $top; ?> /><label for="ctop"><?php  echo _AT('content_packaging_top'); ?></label><br /><br />

	<input type="radio" name="packaging" value="all" id="all" <?php echo $all; ?> /><label for="all"><?php  echo _AT('content_packaging_all'); ?></label><br /><br />

	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td  class="row1" valign="top" align="right"><strong><?php echo  _AT('access'); ?>:</strong></td>
<?php
		switch ($row['access'])
		{

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
	<td class="row1"><input type="radio" name="form_access" value="public" id="pub" onclick="disableNotify();" <?php echo $pub; ?> /><label for="pub"><strong> <?php echo  _AT('public'); ?>: </strong></label><?php echo  _AT('about_public'); ?><br /><br />

		<input type="radio" name="form_access" value="protected" id="prot" onclick="disableNotify();" <?php echo $prot; ?> /><label for="prot"><strong><?php echo  _AT('protected'); ?>:</strong></label> <?php echo  _AT('about_protected'); ?>

		<br /><br />
		<input type="radio" name="form_access" value="private" id="priv" onclick="enableNotify();" <?php echo $priv; ?> /><label for="priv"><strong><?php echo  _AT('private'); ?>:</strong></label> <?php echo  _AT('about_private'); ?>
		<br />
		<input type="checkbox" name="form_notify" id="form_notify" value="1" <?php
			echo $disable;
			echo $notify; ?> /><label for="form_notify"><?php echo  _AT('email_approvals'); ?></label>
		<br />
		<input type="checkbox" name="form_hide" id="form_hide" value="1" <?php
		echo $disable;
		echo $hide; ?> /><label for="form_hide"><?php echo  _AT('hide_course'); ?></label>.

		<br />
		<br /></td>
</tr>

<?php 
	if ($isadmin) { ?>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right">
	<?php 
		$help = array(AT_HELP_COURSE_QUOTA, AT_KBYTE_SIZE, AT_KBYTE_SIZE*AT_KBYTE_SIZE); 		
		print_popup_help($help); 
	?>
	<strong><?php  echo _AT('course_quota'); ?>:</strong></td>
	<td class="row1">
<?php 
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

	$course_size = dirsize('../../content/'.$course.'/');
	if ($course_size < AT_KBYTE_SIZE) {
		$course_size = round($course_size);
		$course_size = $course_size .' '._AT('bytes'); 
	} else {
		$course_size = round(bytes_to_megabytes($course_size),2) .' '._AT('megabytes'); 
	}

?>
	<?php echo _AT('current_course_size') .': '.$course_size; ?><br />
	<input type="radio" id="c_default" name="quota" value="<?php echo AT_COURSESIZE_DEFAULT; ?>" onclick="disableOther();" <?php echo $c_def;?> /><label for="c_default"> <?php echo _AT('default') . ' ('.bytes_to_megabytes($MaxCourseSize).' '._AT('megabytes').')'; ?></label> <br />
	<input type="radio" id="c_unlim" name="quota" value="<?php echo AT_COURSESIZE_UNLIMITED; ?>" onclick="disableOther();" <?php echo $c_unlim;?>/><label for="c_unlim"> <?php echo _AT('unlimited'); ?></label> <br />
	<input type="radio" id="c_other" name="quota" value="2" onclick="enableOther();" <?php echo $c_oth;?>/><label for="c_other"> <?php echo _AT('other'); ?> </label> - 
	<input type="text" id="quota_entered" name="quota_entered" class="formfieldR" <?php echo $c_oth2?> value="<?php if ($row['max_quota']!=AT_COURSESIZE_UNLIMITED && $row['max_quota']!=AT_COURSESIZE_DEFAULT) { echo round(bytes_to_megabytes(intval($row['max_quota']))); } ?>" size="4" /> <?php echo _AT('megabytes'); ?> 
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
	<input type="radio" id="f_default" name="filesize" value="<?php echo AT_FILESIZE_DEFAULT; ?>" onclick="disableOther2();" <?php echo $f_def;?> /><label for="f_default"> <?php echo _AT('default') . ' ('.bytes_to_kilobytes($MaxFileSize).' '._AT('kilobytes').')'; ?></label> <br />
	<input type="radio" id="f_maxallowed" name="filesize" value="<?php echo AT_FILESIZE_SYSTEM_MAX; ?>" onclick="disableOther2();" <?php echo $f_max;?>/><label for="f_maxallowed"> <?php echo _AT('max_file_size_system') . ' ('.bytes_to_kilobytes($max_allowed).' '._AT('kilobytes').')'; ?></label> <br />
	<input type="radio" id="f_other" name="filesize" value="2" onclick="enableOther2();" <?php echo $f_oth;?>/><label for="f_other"> <?php echo _AT('other'); ?> </label> - 
	<input type="text" id="filesize_entered" name="filesize_entered" class="formfieldR" <?php echo $f_oth2?> value="<?php if ($row['max_file_size']!=AT_FILESIZE_DEFAULT && $row['max_file_size']!=AT_FILESIZE_SYSTEM_MAX) { echo round(bytes_to_kilobytes(intval($row['max_file_size']))); } ?>" size="4" /> <?php echo _AT('kilobytes'); ?> 
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

<?php } ?>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td  class="row1" colspan="2" align="center"><input type="submit" name="submit" class="button" value="<?php echo  _AT('update_properties'); ?>" accesskey="s" /> - <input type="submit" name="cancel" value="<?php echo _AT('cancel');?>" class="button" /></td>
</tr>
</table>
</form>


<?php if ($isadmin) { ?>		<p style="width: 95%"><small class="spacer">* <?php echo _AT('default_max'); ?></small></p> <?php } ?>

<script language="javascript" type="text/javascript">
<!--
function enableNotify()
{
	document.course_form.form_notify.disabled = false;
	document.course_form.form_hide.disabled = false;
}

function disableNotify()
{
	document.course_form.form_notify.disabled = true;
	document.course_form.form_hide.disabled = true;
}

function enableOther()		{ document.course_form.quota_entered.disabled = false; }

function disableOther()		{ document.course_form.quota_entered.disabled = true; }

function enableOther2()		{ document.course_form.filesize_entered.disabled = false; }

function disableOther2()	{ document.course_form.filesize_entered.disabled = true; S}

// -->
</script>
