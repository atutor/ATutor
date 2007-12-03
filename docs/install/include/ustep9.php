<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: ustep2.php 6902 2007-04-13 18:19:10Z joel $

ignore_user_abort(true); 
@set_time_limit(0); 

if (!defined('AT_INCLUDE_PATH')) { exit; }
require('classes/TableConversion.class.php');
$_POST['db_login'] = urldecode($_POST['db_login']);
$_POST['db_password'] = urldecode($_POST['db_password']);


/**
 * Runs through the database and get all the courses' titles and languages out
 * @param	table_prefix is the database table prefix
 */
 function generateCourseLangTable($table_prefix){
	echo '<table class="data">';
	echo '<tr><th>Course Title</th><th>Course Primary Language</th></tr>';
	//Get all courses and their associated languages out
	$query = "SELECT a.title, a.course_id, l.char_set FROM ".$table_prefix."courses a left join ".$table_prefix."languages l ON l.language_code = a.primary_language";
	$result = mysql_query($query);
	if ($result && mysql_numrows($result) > 0){
		while ($row = mysql_fetch_assoc($result)){
			echo '<tr><td>';
			echo mb_convert_encoding($row['title'], "UTF-8", $row['char_set']);
			echo '</td><td>'.$row['char_set'].'</td></tr>';
		}
	}
	echo '</table>';
 }

/**
 * Get a list of all the encodings that are used in the ATutor db. 
 * @param	table prefix
 * @return	an array of encodings.
 */
function getListOfEncodings($table_prefix){
	//Get all courses and their associated languages out
	$query = "SELECT DISTINCT char_set FROM ".$table_prefix."languages";
	$list_encodings = array();
	$result = mysql_query($query);
	if ($result && mysql_numrows($result) > 0){
		while ($row = mysql_fetch_assoc($result)){
			$list_encodings[] = $row['char_set'];
		}
	}
	return $list_encodings;
}



unset($errors);
//check DB & table connection

$db = @mysql_connect($_POST['db_host'] . ':' . $_POST['db_port'], $_POST['db_login'], urldecode($_POST['db_password']));

if (!$db) {
	$error_no = mysql_errno();
	if ($error_no == 2005) {
		$errors[] = 'Unable to connect to database server. Database with hostname '.$_POST['db_host'].' not found.';
	} else {
		$errors[] = 'Unable to connect to database server. Wrong username/password combination.';
	}
} else {
	if (!mysql_select_db($_POST['db_name'], $db)) {
		$errors[] = 'Unable to connect to database <b>'.$_POST['db_name'].'</b>.';
	}

	$sql = "SELECT VERSION() AS version";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	if (version_compare($row['version'], '4.0.2', '>=') === FALSE) {
		$errors[] = 'MySQL version '.$row['version'].' was detected. ATutor requires version 4.0.2 or later.';
	}

	if (!$errors) {
		print_progress($step);

		unset($_POST['submit']);
		if (isset($progress)) {
			print_feedback($progress);
		}

		$progress[] = 'Connected to database <b>'.$_POST['db_name'].'</b> successfully.';
		unset($errors);

		//Conversion type set
		if ($_POST['con_step']=='2'){
			$char_encodings = getListOfEncodings($_POST['tb_prefix']);
			echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
			<input type="hidden" name="step" value="2" />';
			store_steps(1);
			print_hidden(2);
			/*
			 * If we are converting the entire database, then all we need is just 1 language encoding;
			 * if we are converting each class individually, then we need to output all the classes and allow language 
			 * options to choose from
			 */
			$convert_type = $_POST['convert_type'];
			if ($convert_type=='all'){
				echo "<div><p>You have chosen the <strong>Convert all content</strong> option.  All the ATutor's content will be converted to UTF-8 from the encoding listed below. </p></div>";
				if (!isset($_POST['conv_all_char_set']) || trim($_POST['conv_all_char_set'])=='') {
					echo '<div><p>We are sorry, we cannot determine which language to convert from as it appears to us that your ATutor has at least one course that uses non-UTF-8 languages.  Please note that this option will convert the entire ATutor database from ONE language encoding to UTF-8.  If you have more than one non-UTF-8 languages, we recommand you to use the "Convert contents by course" option from the previous page.  If you wish to continue, please select one of the following encoding to convert your contents from.</p></div>';
				} else {
					echo '<div><p>The system will convert your ATutor tables from <strong>'.$_POST['conv_all_char_set'].'</strong> to UTF-8.  If you wish to convert your ATutor from another Language encoding, you may select a different language encoding listed below.</p></div>';
				}

				echo "<div><p>Note: This might take up to several minutes depends on the size of the database.</p></div><br/>";

				echo "<div><label>Convert From: </label><select name='encoding_code'>";
				foreach ($char_encodings as $index=>$encoding){
					$selected='';
					if ($encoding==$_POST['conv_all_char_set']){
						$selected = 'selected="selected" ';
					}
					echo "<option value='$encoding' $selected>$encoding</option>";
				}
				echo "</select></div>";
			} elseif ($convert_type=='courses'){
				echo "<div><p>You have chosen the conversion by course.  Each of the following course's content will be converted to UTF-8 with respect to its Course Primary Language. </p></div>";
				echo '<div><p>Notes:</p><ul>';
				echo '<li class="important">Please backup your ATutor database before clicking next.</li>';
				echo '<li>This conversion will convert only course related tables, other tables will be converted from the ATutor default (ISO-8859-1) to UTF-8.</li>';
				echo '<li>If the "Course Primary Language" listed below is not the language that you wish to convert your course from, please log back into your old ATutor as that course instructor, and change the "Course Primary Language" under "Course Properties" to the language you wanted.</li>';
				echo '</ul></div>';
				generateCourseLangTable($_POST['tb_prefix']);
			} elseif ($convert_type=='skip'){
				echo "<div><p>Skipping UTF-8 Conversion.</p><p>No contents will be converted.</p></div>";
				echo '<input type="hidden" name="step" value="3" />'; //skip to database
				echo '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p></form>';
				return;
			} else {
				$errors[] = "No conversion type selected.";
				print_errors($errors);
				echo '<input type="hidden" name="db_login" value="'.urlencode($_POST['db_login']).'" />';
				echo '<input type="hidden" name="db_password" value="'.urlencode($_POST['db_password']).'" />';
				echo '<input type="hidden" name="db_host" value="'.$_POST['db_host'].'" />';
				echo '<input type="hidden" name="db_name" value="'.$_POST['db_name'].'" />';
				echo '<input type="hidden" name="db_port" value="'.$_POST['db_port'].'" />';
				echo '<input type="hidden" name="tb_prefix" value="'.$_POST['tb_prefix'].'" />';
				echo '<input type="hidden" name="old_version" value="'.$_POST['old_version'].'" />';
				echo '<input type="hidden" name="new_version" value="'.$_POST['new_version'].'" />';
				echo '<input type="hidden" name="step" value="2" />';  
				echo '<p align="center"><input type="submit" class="button" value=" Retry &raquo;" name="submit" /></p></form>';
				return;
			}
			
			echo '<input type="hidden" name="db_login" value="'.urlencode($_POST['db_login']).'" />';
			echo '<input type="hidden" name="db_password" value="'.urlencode($_POST['db_password']).'" />';
			echo '<input type="hidden" name="db_host" value="'.$_POST['db_host'].'" />';
			echo '<input type="hidden" name="db_name" value="'.$_POST['db_name'].'" />';
			echo '<input type="hidden" name="db_port" value="'.$_POST['db_port'].'" />';
			echo '<input type="hidden" name="tb_prefix" value="'.$_POST['tb_prefix'].'" />';
			echo '<input type="hidden" name="old_version" value="'.$_POST['old_version'].'" />';
			echo '<input type="hidden" name="new_version" value="'.$_POST['new_version'].'" />';
			echo '<input type="hidden" name="convert_type" value="'.$convert_type.'"/>';
			echo '<input type="hidden" name="con_step" value="3" />';
			echo '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p></form>';
			return;
		} elseif ($_POST['con_step'] == '3'){
			$result = '';
			// Run sql conversion codes.
			if ($_POST['convert_type'] == 'all'){			
//				if (isset($_POST['encoding_code'])&& $_POST['encoding_code']!=""){
//					$char_set = $_POST['encoding_code'];
//					$errors = mysql_utf8_convertDB($db, $encoding_code, $errors);
//					$progress[] = "Converted database from <strong>$encoding_code</strong> to UTF8 successfully.";
//				} else {
//					$errors = "Conversion type was not specified.";
//				}
				$query = "SELECT DISTINCT course_id, title FROM ".$_POST['tb_prefix']."courses";
				$result = mysql_query($query);
				if (mysql_num_rows($result) <= 0){
					return false;
				}
			} else if ($_POST['convert_type']=='courses'){
				$query = "SELECT a.course_id, a.title, l.char_set FROM ".$_POST['tb_prefix']."courses a left join ".$_POST['tb_prefix']."languages l ON l.language_code = a.primary_language";
				$result = mysql_query($query);
				if (mysql_num_rows($result) <= 0){
					return false;
				}
			}
			while ($row = mysql_fetch_assoc($result)){
				$course_id = $row['course_id'];
				//Get charset
				if (isset($_POST['encoding_code'])&& $_POST['encoding_code']!=""){
					$char_set = $_POST['encoding_code'];
				} else {
					$char_set = $row['char_set'];
				}
				$row['title'] = mb_convert_encoding($row['title'], "UTF-8", $char_set);

				//If this is already in UTF-8, skip conversion
				if (strtolower($char_set)=="utf-8" || strtolower($char_set)=="utf8"){
					$progress[] = 'Course ('.$row['title'].') <strong>has been skipped</strong>, course\'s contents are already in UTF-8.';
					continue;
				}

				$progress[] = 'Course ('.$row['title'].') <strong>has been converted</strong> from '.$char_set;
				//Run through all ATutor table and convert only those rows with the above courses.
				$temp_table =& new AssignmentsTable($_POST['tb_prefix'], 'assignments', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'assignments was not converted.';

				$temp_table =& new BackupsTable($_POST['tb_prefix'], 'backups', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'backups was not converted.';

				$temp_table =& new BlogPostsTable($_POST['tb_prefix'], 'blog_posts', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'blog_posts was not converted.';
				
				$temp_table =& new ContentTable($_POST['tb_prefix'], 'content', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'content was not converted.';
				
				$temp_table =& new CoursesTable($_POST['tb_prefix'], 'courses', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'courses was not converted.';
				
				$temp_table =& new CourseEnrollmentTable($_POST['tb_prefix'], 'course_enrollment', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'course_enrollment was not converted.';
				
				$temp_table =& new ExternalResourcesTable($_POST['tb_prefix'], 'external_resources', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'external_resources was not converted.';

				$temp_table =& new FaqTopicsTable($_POST['tb_prefix'], 'faq_topics', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'faq_topics was not converted.';
				
				$temp_table =& new ForumsTable($_POST['tb_prefix'], 'forums', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'forums was not converted.';

				$temp_table =& new GlossaryTable($_POST['tb_prefix'], 'glossary', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'glossary was not converted.';

				$temp_table =& new GroupsTypesTable($_POST['tb_prefix'], 'groups_types', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'groups_types was not converted.';

				$temp_table =& new MessagesSentTable($_POST['tb_prefix'], 'messages_sent', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'messages_sent was not converted.';

				$temp_table =& new NewsTable($_POST['tb_prefix'], 'news', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'news was not converted.';

				$temp_table =& new PollsTable($_POST['tb_prefix'], 'polls', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'polls was not converted.';

				$temp_table =& new ReadingListTable($_POST['tb_prefix'], 'reading_list', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'reading_list was not converted.';

				$temp_table =& new TestsTable($_POST['tb_prefix'], 'tests', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'tests was not converted.';

				$temp_table =& new TestQuestionsTable($_POST['tb_prefix'], 'tests_questions', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'tests_questions was not converted.';

				$temp_table =& new TestsQuestionsCategoriesTable($_POST['tb_prefix'], 'tests_questions_categories', $char_set, $course_id);
				if (!$temp_table->convert())
					$errors[]= $row['title'].': '.$_POST['tb_prefix'].'tests_questions_categories was not converted.';
			}
			//Check if there are any errors, if not, jump to next step
			if (!$errors) {
				unset($_POST['submit']);
				store_steps(1);
				print_feedback($progress);

				echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
				<input type="hidden" name="step" value="3" />
				<input type="hidden" name="upgrade_action" value="true" />';
				echo '<input type="hidden" name="db_login" value="'.urlencode($_POST['db_login']).'" />';
				echo '<input type="hidden" name="db_password" value="'.urlencode($_POST['db_password']).'" />';
				echo '<input type="hidden" name="db_host" value="'.$_POST['db_host'].'" />';
				echo '<input type="hidden" name="db_name" value="'.$_POST['db_name'].'" />';
				echo '<input type="hidden" name="db_port" value="'.$_POST['db_port'].'" />';
				echo '<input type="hidden" name="tb_prefix" value="'.$_POST['tb_prefix'].'" />';
				echo '<input type="hidden" name="old_version" value="'.$_POST['old_version'].'" />';
				echo '<input type="hidden" name="new_version" value="'.$_POST['new_version'].'" />';
				echo '<p align="center"><input type="submit" class="button" value=" Next &raquo; " name="submit" /></p></form>';
				return;
			}
		} else{
			echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
			<input type="hidden" name="step" value="2" />';
			store_steps(1);
			print_hidden(2);

			echo '<div><p>Upgrading requires you to convert ATutor contents to UTF-8.  The courses are listed below with their associated primary language.  </p><p>Please choose one of the conversion options listed below, the recommanded option(in blue) is already selected for you.</p><p>For a more detailed description for each of these conversions, please visit our <a href="http://wiki.atutor.ca/display/atutorwiki/UTF-8+Conversion">ATutor Wiki page</a></p></div>';

			//The html fragment that sets the suggested conversion type on bold 
			$suggestion = array('class="suggested"', 'checked="checked"');	
			$suggestion_skip="";
			$suggestion_all="";
			$suggestion_courses=""; 

			echo '<table class="data">';
			echo '<tr><th>Course Title</th><th>Course Primary Language</th></tr>';
			//Get all courses and their associated languages out
			$query = "SELECT a.title, a.course_id, l.char_set FROM ".$_POST['tb_prefix']."courses a left join ".$_POST['tb_prefix']."languages l ON l.language_code = a.primary_language";
			$result = mysql_query($query);
			$prev_language = "";  //Keep tracks of the course primary language in each loop.
			$is_same_language = true;
			if ($result && mysql_numrows($result) > 0){
				while ($row = mysql_fetch_assoc($result)){
					if ($prev_language==""){
						$prev_language = $row['char_set'];
					} elseif ($is_same_language==true) {
						if($prev_language != $row['char_set']){
							$is_same_language &= false;
						}
					}
					echo '<tr><td>';
					echo mb_convert_encoding($row['title'], "UTF-8", $row['char_set']);
					echo '</td><td>'.$row['char_set'].'</td></tr>';
				}
			}
			if ($is_same_language == true){
				if (strtolower($prev_language)=="utf8" || strtolower($prev_language)=="utf-8" ){
					$suggestion_skip =& $suggestion;
				}
				$suggestion_all =& $suggestion;
				echo '<input type="hidden" name="conv_all_char_set" value="'.$prev_language.'" />';
			} else {
				$suggestion_courses =& $suggestion;
			}
			echo '</table><br/>';

			echo '<div><p><strong>Convert all content:</strong> Use this option if you are using just <i><u>one</u></i> non-UTF-8 language in your ATutor.</p>';
			echo '<p><strong>Convert contents by courses:</strong> Use this option when you are using <i><u>more than one</u></i> UTF-8 or non-UTF-8 languages in your ATutor.</p>';
			echo '<p><strong>Skip conversion:</strong> Use this option when you have <i><u>only UTF-8</u></i> contents in your ATutor.</p>';
			echo '</div>';

			echo '<div '.$suggestion_all[0].'><input type="radio" id="convert_all" name="convert_type" value="all" '.$suggestion_all[1].'/>';
			echo '<label for="convert_all">Convert all content</label></div>';

			echo '<div '.$suggestion_courses[0].'><input type="radio" id="convert_courses" name="convert_type" value="courses" '.$suggestion_courses[1].'/>';
			echo '<label for="convert_courses">Convert contents by courses</label></div>';

			echo '<div '.$suggestion_skip[0].'><input type="radio" id="convert_skip" name="convert_type" value="skip" '.$suggestion_skip[1].'/>';
			echo '<label for="convert_skip">Skip conversion</label></div>';

			echo '<input type="hidden" name="db_login" value="'.urlencode($_POST['db_login']).'" />';
			echo '<input type="hidden" name="db_password" value="'.urlencode($_POST['db_password']).'" />';
			echo '<input type="hidden" name="db_host" value="'.$_POST['db_host'].'" />';
			echo '<input type="hidden" name="db_name" value="'.$_POST['db_name'].'" />';
			echo '<input type="hidden" name="db_port" value="'.$_POST['db_port'].'" />';
			echo '<input type="hidden" name="tb_prefix" value="'.$_POST['tb_prefix'].'" />';
			echo '<input type="hidden" name="old_version" value="'.$_POST['old_version'].'" />';
			echo '<input type="hidden" name="new_version" value="'.$_POST['new_version'].'" />';
			echo '<input type="hidden" name="con_step" value="2" />';
			echo '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p></form>';
			return;
		}
	}
}

	//Failed 
	if (isset($errors)) {
		print_errors($errors);
	}

	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
	<input type="hidden" name="step" value="2" />';
	store_steps(1);
	print_hidden(2);
	echo '<p align="center"><input type="submit" class="button" value=" Retry " name="submit" /></p></form>';
	return;
?>