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
require (AT_INCLUDE_PATH.'vitals.inc.php');
if($_POST['course']){
	$course = intval($_POST['course']);
}else if($_GET['course']){
	$course = intval($_GET['course']);
}

$title = _AT('course_enrolment');

require(AT_INCLUDE_PATH.'cc_html/header.inc.php');


if($submit && ($_FILES['file']['size'] < 1)){
	$errors[] = AT_ERROR_FILE_NOT_SELECTED;
	print_errors($errors);

}

?>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
<tr><th class="cyan"><?php echo _AT('list_import_course_list');  ?></th></tr>

<tr><td class="row1"><?php echo _AT('list_import_howto'); ?></td></tr>
<tr><td height="1" class="row2"></td></tr>
<tr><td class="row1" align="center">
<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<input type="hidden" name="import_course_list" value="1" />
<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
<input type="hidden" name="course" value="<?php echo $course; ?>" />
<label for="course_list"><?php echo _AT('import_course_list'); ?>: </label>
<input type="file" name="file" id="course_list" class="formfield" />
<input type="submit" name="submit" value="<?php echo _AT('list_import_course_list');  ?>" class="button" />
</form>
</td></tr>
</table>
<p><br /><a href="users/enroll_admin.php?course=<?php echo $course; ?>#results"><?php echo _AT('list_return_to_enrollment'); ?> </a> </p>
<?php

if($submit && ($_FILES['file']['size'] > 0)){
	echo '<hr /><br /><a name="results"></a>';
	echo '<h5>'._AT('list_import_results').'</h5>';

}
if($_POST['import_course_list']){
	if ($_FILES['file']['size'] > 0) {
		// check if ../content/import/ exists
		$import_path = '../content/import/';
		$content_path = '../content/';

		if (!is_dir($import_path)) {
			if (!@mkdir($import_path, 0700)) {
				$errors[] = AT_ERROR_IMPORTDIR_FAILED;
				print_errors($errors);
				exit;
			}
		}

		$import_path = '../content/import/'.$course.'/';
		if ($_FILES['file']['size'] > 0) {
			$fp = fopen($_FILES['file']['tmp_name'],'r');

			//create a member array for checking imported class lists
			$sql1 = "SELECT member_id, login, email from ".TABLE_PREFIX."members";
			if ($result1 = mysql_query($sql1,$db)){
				$this_email = array();
				$this_login = array();
				$this_member_id = array();
				while ($row3 = mysql_fetch_array($result1)){
					$this_member_id[$row3['email']] = $row3['member_id'];
					$this_login[$row3['email']] = $row3['login'];
					$this_email[$row3['email']] = $row3['email'];
				}
			}
			$existing_members = array();
			echo '<div class="results"><ul>';
			while ($data = fgetcsv($fp, 100000, ',')) {

				if($data[0] != '' && $data[1] != ''){
					echo '<li><strong>'.$data[0].' '.$data[1].'</strong><br />';
					$course_list[$data[0]] = $data;
					$this_user_login = strtolower($data[0].'_'.$data[1]);
					$existing_member = '';
					$sql55 = "SELECT member_id FROM ".TABLE_PREFIX."members WHERE email = '$data[2]'";
					$result9 = mysql_query($sql55,$db);
					if(mysql_num_rows($result9) > 0){
						while($row4 = mysql_fetch_array($result9)){
							$existing_member = $this_member_id[$data[2]];
						}
						echo _AT('list_email_exists', $data[2], $data[0], $data[1]);
					}else{
						$tmp_member_id = 1;
						echo _AT('list_creating_new_user', $this_user_login, $data[0], $data[1] );
					}

					if($data[2] == $this_email[$data[2]]){
						$existing_members[] = $data[2];
					}
					$i = 0;
					do{
						$i++;
						$this_user_login = $this_user_login.$i;
					} while (in_array ($this_user_login, $this_login));


     				if ($tmp_member_id){
						$sql4 = "INSERT INTO ".TABLE_PREFIX."members (member_id, login, password, email, first_name, last_name, gender, creation_date)VALUES ";
						$sql4 .= " (0, '$this_user_login', '$this_user_login', '$data[2]', '$data[0]', '$data[1]', '', NOW())";

						if($result = mysql_query($sql4,$db)){
							echo _AT('list_new_member_created');
						}else{
							echo _AT('list_member_already_exists');
						}
					}
					
					// get the new members id and add it to the enrolment table
					$sql25 = "SELECT member_id FROM ".TABLE_PREFIX."members WHERE email = '$data[2]'";
					$result10 = mysql_query($sql25,$db);
					if(count(mysql_num_rows) ==1){
						while($row = mysql_fetch_array($result10)){
							$new_member_id = $row['member_id'];
						}
					}else{
						$errors[] = AT_ERROR_LIST_IMPORT_FAILED;
						print_errors($errors);
					}
					if($existing_member != ''){
						$sql8 = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved) VALUES ('$existing_member', '$course', 'n')";
					}else{
						$sql8 = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved) VALUES ('$new_member_id', '$course', 'n')";

					}
					if($result8 = mysql_query($sql8,$db)){
						echo _AT('list_member_enrolled');
					}else{
						echo _AT('list_member_already_enrolled');
					}
					echo '<br /></li>';
				}else{
					$errors[] = AT_ERROR_LIST_IMPORT_FAILED;
					print_errors($errors);

				}
			}
			echo '</ul></div>';
		}
	}
}

require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
?>