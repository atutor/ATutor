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
$_include_path = '../include/';
require($_include_path.'vitals.inc.php');
require($_include_path.'lib/filemanager.inc.php');
if ($_POST['form_course'])
{
	$_POST['form_notify']	= intval($_POST['form_notify']);
	$_POST['form_hide']		= intval($_POST['form_hide']);
	$_POST['form_title']	= trim($_POST['form_title']);

	if ($_POST['form_title'] == '') {
		$errors[]=AT_ERROR_SUPPLY_TITLE;
	} else {
	
		$sql2	= "SELECT preferences FROM ".TABLE_PREFIX."theme_settings WHERE theme_id='4'";
		$result2	= mysql_query($sql2, $db);
		while($row = mysql_fetch_array($result2)){
			$course_default_prefs = $row['preferences'];
		}
	 	$_POST['form_notify'] = intval($_POST['form_notify']);

		//$sql = "INSERT INTO ".TABLE_PREFIX."courses VALUES (0, $_SESSION[member_id], '$_POST[category_parent]', '$_POST[packaging]', '$_POST[form_access]', NOW(), '$_POST[form_title]', '$_POST[form_description]', $_POST[form_notify], $MaxCourseSize, $MaxFileSize, $_POST[form_hide], '$course_default_prefs', '', '', '', 'off')";

		$sql = "INSERT INTO ".TABLE_PREFIX."courses VALUES (0, $_SESSION[member_id], '$_POST[category_parent]', '$_POST[packaging]', '$_POST[form_access]', NOW(), '$_POST[form_title]', '$_POST[form_description]', $_POST[form_notify], '".AT_COURSESIZE_DEFAULT."', $MaxFileSize, $_POST[form_hide], '$course_default_prefs', '', '', '', 'off')";

		$result = mysql_query($sql, $db);

		if (!$result) {
			echo 'DB Error';
			exit;
		}

		$course_id = mysql_insert_id();

		$sql	= "INSERT INTO ".TABLE_PREFIX."course_enrollment VALUES($_SESSION[member_id], $course_id, 'y')";
		$result	= mysql_query($sql, $db);

		// create the ./contents/COURSE_ID directory
		$path = '../content/'.$course_id.'/';
		
		//check to see if directories are owned by the web server user
		//if not, make them world writable.


		//$result = @mkdir($path, 0700);
		$result = @mkdir($path, 0700);

		/* insert some default content: */
		$_SESSION['is_admin'] = 1;
		$cid = $contentManager->addContent($course_id,
											0,
											0,
											_AT('welcome_to_atutor'),
											_AT('this_is_content'),
											'',
											'',
											1,
											date('Y-m-d H:00:00'));

		$announcement = _AT('default_announcement');
		
		$sql	= "INSERT INTO ".TABLE_PREFIX."news VALUES (0, $course_id, $_SESSION[member_id], NOW(), 1, '"._AT('welcome_to_atutor')."', '$announcement')";
		$result = mysql_query($sql,$db);


		cache_purge('system_courses','system_courses');
		Header ('Location: ../bounce.php?course='.$course_id.SEP.'f='.urlencode_feedback(AT_FEEDBACK_COURSE_CREATED));
		exit;
	}
}

require($_include_path.'cc_html/header.inc.php'); 


/* varify that this user has status to create courses: */
	$sql	= "SELECT status FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	$row	= mysql_fetch_array($result);
	$status	= $row['status'];
	if ($status != 1) {
		$errors[]=AT_ERROR_CREATE_NOPERM;
		print_errors($errors);
		require($_include_path.'cc_html/footer.inc.php');
		exit;
	}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="course_form">
<input type="hidden" name="form_course" value="true" />

<h2><?php  echo _AT('create_course'); ?></h2>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="">
<tr>
	<td colspan="2" class="cat"><h4><?php  echo _AT('course_information'); ?></h4></td>
</tr>
<tr>
	<td nowrap="nowrap" class="row1" align="right"><b><?php  echo _AT('course_name'); ?>:</b></td>
	<td class="row1"><input type="text" id="title" name="form_title" class="formfield" size="40" /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td valign="top" class="row1" align="right"><b><?php  echo _AT('description'); ?>:</b></td>
	<td class="row1"><textarea id="description" cols="45" rows="4" class="formfield" name="form_description"></textarea></td>
</tr>

<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td class="row1" align="right"><b><?php echo _AT('category'); ?></b>:</td>
	<td class="row1">
<?php
	$sql5 = "SELECT * FROM ".TABLE_PREFIX."course_cats";
	$result5 = mysql_query($sql5);
	if(mysql_num_rows($result5) == 0){
		echo _AT('cats_uncategorized');
	}else{	
		$cats = array();

		echo '<select name="category_parent">';
		while($row5 = mysql_fetch_assoc($result5)){
			$cats[$row5['cat_parent']][] = $row5;
		}
		echo '<option value="0"> - '._AT('cats_uncategorized').' - </option>';
		print_course_cats(0, $cats, $cat_row);
		echo '</select>';
	} 
?>
	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><b><?php  echo _AT('content_packaging'); ?>:</b></td>
	<td class="row1">
	
	<input type="radio" name="packaging" value="none" id="none" /><label for="none"><?php echo _AT('content_packaging_none'); ?></label><br /><br />

	<input type="radio" name="packaging" value="top" id="top" checked="checked" /><label for="top"><?php  echo _AT('content_packaging_top'); ?></label><br /><br />

	<input type="radio" name="packaging" value="all" id="all" /><label for="all"><?php  echo _AT('content_packaging_all'); ?></label><br /><br />

	</td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" valign="top" align="right"><b><?php  echo _AT('access'); ?>:</b></td>
	<td class="row1"><input type="radio" name="form_access" value="public" id="pub" onclick="disableNotify();" /><label for="pub"><b><?php echo _AT('public'); ?>: </b></label><?php echo  _AT('about_public'); ?><br /><br />

	<input type="radio" name="form_access" value="protected" id="prot" checked="checked" onclick="disableNotify();" /><label for="prot"><b><?php  echo _AT('protected'); ?>: </b></label><?php echo  _AT('about_protected'); ?>

	<br /><br />
	<input type="radio" name="form_access" value="private" id="priv" onclick="enableNotify();" /><label for="priv"><b><?php  echo _AT('private'); ?>: </b></label><?php echo  _AT('about_private'); ?><br />

	<input type="checkbox" name="form_notify" id="form_notify" value="1" disabled="disabled" checked="checked" /><label for="form_notify"><?php  echo _AT('email_approvals'); ?></label>.

	<br />

	<input type="checkbox" name="form_hide" id="form_hide" value="1" disabled="disabled" /><label for="form_hide"><?php  echo _AT('hide_course'); ?></label>.

	<br /><br /></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="center" colspan="2"><input type="submit" name="submit" class="button" value=" <?php echo  _AT('create_course'); ?> Alt-s" accesskey="s" /></td>
</tr>
</table>
</form>

<script type="text/javascript">
<!--
function enableNotify()
{
	document.course_form.form_notify.disabled = false;
	document.course_form.form_hide.disabled   = false;
}

function disableNotify()
{
	document.course_form.form_notify.disabled = true;
	document.course_form.form_hide.disabled	  = true;
}

//-->
</script>

<?php
require($_include_path.'cc_html/footer.inc.php'); 
?>
