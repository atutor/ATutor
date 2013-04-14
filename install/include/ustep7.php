<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

ignore_user_abort(true); 
@set_time_limit(0); 

if (!defined('AT_INCLUDE_PATH') || !defined('AT_UPGRADE_INCLUDE_PATH')) { exit; }

require('classes/TableConversion.class.php');
$_POST['db_login'] = urldecode($_POST['db_login']);
$_POST['db_password'] = urldecode($_POST['db_password']);
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

		/* 
		 * Check if version is > 1.6, if so, this entire step can be skipped
		 * OR if db table are already all in utf8.
		 */
		if (version_compare($_POST['step1']['old_version'], '1.6.1', '>') === TRUE ||
			check_db_default_charset($db) === TRUE) {
			$progress[] = 'Version <kbd><b>'.$_POST['step1']['old_version'].'</b></kbd> found.';
			$progress[] = 'UTF-8 Conversion is not needed, skipping.';
			print_feedback($progress);
			echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
			<input type="hidden" name="step" value="4" />';
			print_hidden(2);
			echo '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p></form>';
			return;
		}

		unset($_POST['submit']);
		if (isset($progress)) {
			print_feedback($progress);
		}

		$progress[] = 'Connected to database <b>'.$_POST['db_name'].'</b> successfully.';
		unset($errors);

		//If this is a Retry on step3 failure, load step2 post values back to jump right back to where it left off.
		if (isset($_POST['step2'])){
			$temp = $_POST;
			$_POST = $_POST['step2'];
			$_POST['step1'] = $temp['step1'];
		}

		//Conversion type set
		if ($_POST['con_step']=='2'){
			//Get list of unqiue encoding; skip utf8
			$char_encodings = array();
			foreach($_SESSION['course_info'] AS $course_id=>$temp){
				if (strtolower($temp['char_set'])!='utf-8' && strtolower($temp['char_set'])!='utf8' 
					&& !in_array($temp['char_set'], $char_encodings)){
					$char_encodings[] = $temp['char_set'];
				}
			}
			echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
			<input type="hidden" name="step" value="3" />';

			print_hidden(2);
			/*
			 * If we are converting the entire database, then all we need is just 1 language encoding;
			 * if we are converting each class individually, then we need to output all the classes and allow language 
			 * options to choose from
			 */
			$convert_type = $_POST['convert_type'];
			$recommanded_conversion = $_POST['recommanded_conversion'];
			$confirm_con_step = $_POST['confirm_con_step'];
			//check if the user-selected convert_type is the same as the recommanded conversion type, if not, give a warning msg
			if ($convert_type!=$recommanded_conversion && $confirm_con_step==""){
				//The html fragment that sets the suggested conversion type on bold 
				$suggestion = array('class="suggested"', 'checked="checked"');	
				$suggestion_skip="";
				$suggestion_all="";
				$suggestion_courses=""; 
				echo '<div><p>You have selected a different conversion option from the recommanded one.  Please verify your option and click "Yes, please continue." to continue.</p><p>Please be aware that invalid conversion may lose your data.</p></div>';
				generateCourseLangTable($_POST['tb_prefix'], $_SESSION['course_info']);
				//Print the selected options from the previous user option.
				switch($convert_type){
					case "all": 
						$suggestion_all[1] =& $suggestion[1] ;
						break;
					case "skip":
						$suggestion_skip[1] =& $suggestion[1];
						break;
					case "courses":
						$suggestion_courses[1] =& $suggestion[1];
						break;
				}
				switch($recommanded_conversion){
					case "all": 
						$suggestion_all[0] =& $suggestion[0] ;
						break;
					case "skip":
						$suggestion_skip[0] =& $suggestion[0];
						break;
					case "courses":
						$suggestion_courses[0] =& $suggestion[0];
						break;
				}


				echo '<div '.$suggestion_all[0].'><input type="radio" id="convert_all" name="convert_type" value="all" '.$suggestion_all[1].'/>';
				echo '<label for="convert_all">Convert all content</label></div>';

				echo '<div '.$suggestion_courses[0].'><input type="radio" id="convert_courses" name="convert_type" value="courses" '.$suggestion_courses[1].'/>';
				echo '<label for="convert_courses">Convert content by courses</label></div>';

				echo '<div '.$suggestion_skip[0].'><input type="radio" id="convert_skip" name="convert_type" value="skip" '.$suggestion_skip[1].'/>';
				echo '<label for="convert_skip">Skip conversion</label></div>';
			
				print_post_for_step9($_POST);
				echo '<input type="hidden" name="step" value="3" />';  
				echo '<input type="hidden" name="con_step" value="2" />'; 
				echo '<input type="hidden" name="confirm_con_step" value="true" />';
				echo '<p align="center"><input type="submit" class="button" value=" Yes, please continue. " name="submit" /></p></form>';
				return;
			}

			if ($convert_type=='all'){
				echo "<div><p>You have chosen the <strong>Convert all content</strong> option.  All ATutor's content will be converted to UTF-8 from the encoding listed below.</p></div>";
				echo "<div><p>Note: This might take up to several minutes, please be patient while you wait.</p></div><br/>";
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
				echo "<div><p>You have chosen the conversion by course.  Each of the following courses' content will be converted to UTF-8 with respect to its Course Primary Language. </p></div>";
				echo '<div><p>Notes:</p><ul>';
				echo '<li class="important">Please backup your ATutor database before clicking next.</li>';
				echo '<li>This conversion will convert only course related tables, other tables will be converted from the ATutor default (ISO-8859-1) to UTF-8.</li>';
				echo '<li>If the "Course Primary Language" listed below is not the language that you wish to convert from, please change the "Course Primary Language" under "Course Properties" to the language you want to convert from.</li>';
				echo '</ul></div>';
				generateCourseLangTable($_POST['tb_prefix'], $_SESSION['course_info']);
			} elseif ($convert_type=='skip'){
				//When 'skip' has been chosen, check if version# < 1.6, if so, convert database structure(not content); o/w skip to next step.
				if (version_compare($_POST['step1']['old_version'], '1.6', '<') === TRUE) {
					unset($progress);
					$progress[] = "Will not be converting database content.";
					$progress[] = "Will be converting database table column structure (charset, collation) to UTF-8.";
					print_feedback($progress);
					print_post_for_step9($_POST);
					echo '<input type="hidden" name="step" value="3" />'; 
					echo '<input type="hidden" name="con_step" value="3" />';
					echo '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p></form>';
					return;
				} elseif (version_compare($_POST['step1']['old_version'], '1.6.1', '<') === TRUE) {
					//Check if version#=1.6 if so, then this 1.6 database must already been upgraded before.
					//change all table to utf-8.
					$conversionDriver = new ConversionDriver($_POST['tb_prefix']);
					$conversionDriver->alter_all_charset();
					unset($progress);
					$progress[] = "All tables' charset are now in UTF-8.";
					print_feedback($progress);
					print_post_for_step9($_POST);
					echo '<input type="hidden" name="step" value="3" />'; 
					echo '<input type="hidden" name="con_step" value="4" />';
					echo '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p></form>';
					return;
				}
				echo "<div><p>Skipping UTF-8 Conversion.</p><p>No content will be converted.</p></div>";
				echo '<input type="hidden" name="step" value="4" />'; //skip to next step
				echo '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p></form>';
				return;
			} else {
				$errors[] = "No conversion type selected.";
				print_errors($errors);
				print_post_for_step9($_POST);
				echo '<input type="hidden" name="step" value="3" />';  
				echo '<p align="center"><input type="submit" class="button" value=" Retry &raquo;" name="submit" /></p></form>';
				return;
			}			
			print_post_for_step9($_POST);
			echo '<input type="hidden" name="convert_type" value="'.$convert_type.'"/>';
			echo '<input type="hidden" name="con_step" value="3" />';
			echo '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p></form>';
			return;
		} elseif ($_POST['con_step'] == '3'){
			//Check if this is a refresh request, if so, don't convert the db.
			if (isset($_SESSION['conversion_completed']) && $_SESSION['conversion_completed']==true){
				$progress[] ='Database has already been converted, click next to continue.';
				print_feedback($progress);
				echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
				<input type="hidden" name="step" value="3" />
				<input type="hidden" name="con_step" value="4" /> 
				<input type="hidden" name="upgrade_action" value="true" />';
				print_hidden(2);
				print_post_for_step9($_POST);
				echo '<p align="center"><input type="submit" class="button" value=" Next &raquo; " name="submit" /></p></form>';
				return;
			}

			$result = '';
			// Get course code to map encoding/charset
			if ($_POST['convert_type'] == 'all' || $_POST['convert_type'] == 'courses' ){			
				$query = "SELECT course_id, title FROM ".$_POST['tb_prefix']."courses";
				$result = mysql_query($query);
				if (mysql_num_rows($result) <= 0){
					return false;
				}				
			} else {
				//'Skip' was selected, convert table structure only				
				$sqlUtility = new SqlUtility();
				$sqlUtility->queryFromFile(AT_INCLUDE_PATH . 'install/db/atutor_convert_db_to_utf8.sql', $_POST['tb_prefix']);
				$progress[] = 'Database table structure has been converted to UTF-8.';
				print_feedback($progress);
				if (isset($errors)){
					print_errors($errors);
					echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
					<input type="hidden" name="step" value="3" />';
					print_hidden(2);
					print_post_for_step9($_POST);
					echo '<p align="center"><input type="submit" class="button" value=" Retry " name="submit" /></p></form>';
					return;
				}
				echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
					<input type="hidden" name="step" value="3" />';
				echo '<input type="hidden" name="con_step" value="4" />'; 
				print_hidden(2);
				print_post_for_step9($_POST);
				echo '<p align="center"><input type="submit" class="button" value=" Next &raquo; " name="submit" /></p></form>';				
				return;		
			}
			
			/* 
			 * redo_conversion SESSION variable keep tracks of the table that failed conversion.  
			 * If it is set, then run only those tables inside the redo_conversion SESSION variable.  
			 */
			if (isset($_SESSION['redo_conversion'])){
				unset($errors);
				foreach($_SESSION['redo_conversion'] as $course_title=>$class_obj){
					foreach($class_obj as $class_name=>$class_param){
						$temp_table = new $class_name ($class_param[0], $class_param[1], $class_param[2], $class_param[3]);
						if (!$temp_table->convert()){
							$errors[]= $course_title.': '.$class_param[0].$class_param[1].' was not converted.';
						} else {
							unset($_SESSION['redo_conversion'][$class_name]);
							$progress[] = "$class_param[1]  has now been converted.";
						}
					}
				}
			} else {
				/* 
				 * Convert course independent materials such as user information, categories 
				 * Decide which language to use
				 */
				$conversionDriver = new ConversionDriver($_POST['tb_prefix']);
				if (version_compare($_POST['step1']['old_version'], '1.6', '<') === TRUE) {
					$conversionDriver->convertTableBySysDefault();
				} 
				$conversionDriver->convertTableBySysDefault_161();

				/* 
				 * Loop through all the courses, and convert all the course's content 
				 * Flush out the process in the mean time.
				 */	
				echo '<div style="padding-left:1em; margin-top: 0.5em; margin-left:1em; overflow:auto; height:100px; border:1px solid #30b626;;">';
				while ($row = mysql_fetch_assoc($result)){
					$course_id = $row['course_id'];
					//Get charset
					if (isset($_POST['encoding_code'])&& $_POST['encoding_code']!=""){
						$char_set = $_POST['encoding_code'];
					} else {
						$char_set = $_SESSION['course_info'][$course_id]['char_set'];
					}
					$row['title'] = mb_convert_encoding($row['title'], "UTF-8", $char_set);

					//If this is already in UTF-8, skip conversion
					if (strtolower($char_set)=="utf-8" || strtolower($char_set)=="utf8"){
						$progress[] = 'Course ('.$row['title'].') <strong>has been skipped</strong>, course\'s content are already in UTF-8.';
						continue;
					}
					$progress[] = 'Course ('.$row['title'].') <strong>has been converted</strong> from '.$char_set;

					//Run through all ATutor table and convert only those rows with the above courses.
					//todo: implement a driver class inside the TableConversion class.
					ob_start();
					echo "Converting $row[title]...<br/>";
					ob_flush();
					flush();
					ob_end_flush();
					if (version_compare($_POST['step1']['old_version'], '1.6', '<') === TRUE) {
						$conversionDriver->convertTableByClass($row['title'], $char_set, $course_id);
					} 
					$conversionDriver->convertTableByClass_161($row['title'], $char_set, $course_id);
				}
				echo '</div>';
			}
			//Check if there are any errors, if not, jump to next step
			if (!$errors) {
				unset($_POST['submit']);
//				store_steps(1);
				//Will not allow refresh on this screen, because it will re-convert the database.
				$_SESSION['conversion_completed'] = true;

				print_feedback($progress);

				if (isset($errors)){
					print_errors($errors);
					echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
					<input type="hidden" name="step" value="3" />
					<input type="hidden" name="con_step" value="4" />';
					print_hidden(2);
					print_post_for_step9($_POST);
					echo '<p align="center"><input type="submit" class="button" value=" Retry " name="submit" /></p></form>';
					return;
				}

				echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
				<input type="hidden" name="step" value="3" />
				<input type="hidden" name="con_step" value="4" />
				<input type="hidden" name="upgrade_action" value="true" />';
				print_hidden(2);
				print_post_for_step9($_POST);
				echo '<p align="center"><input type="submit" class="button" value=" Next &raquo; " name="submit" /></p></form>';
				return;
			}
		} elseif ($_POST['con_step'] == '4'){
			//Convert the database charset to UTF8.
			if (!check_db_default_charset($db)){
				$sql = 'ALTER DATABASE `'.$_POST['db_name'].'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci';
				if (mysql_query($sql)===false){
					//Alter failed, probably the user doesn't have permission.
					$errors[] = 'Failed to change the database default character set to UTF-8.  Please set the database character set to UTF8 before continuing by using the following query: ALTER DATABASE `'.$_POST['db_name'].'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci.  To use ALTER DATABASE, you need the ALTER privilege on the database.';
				}
			}
			$progress[] ='Database default charset is now in UTF-8, click next to continue.';
			print_feedback($progress);
			echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
				<input type="hidden" name="step" value="4" />';
			print_hidden(2);
			print_post_for_step9($_POST);
			echo '<p align="center"><input type="submit" class="button" value=" Next &raquo; " name="submit" /></p></form>';				
			return;
		} else {
			/* If the installation has been stopped right after the conversion step.  The session variable
			 * that prevents refreshes will still be set, have to unset it to carry on the installation.
			 */
			if (isset($_SESSION['conversion_completed'])){
				unset($_SESSION['conversion_completed']);
			}

			$html = '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
			<input type="hidden" name="step" value="3" />';
//			store_steps(1);

			$html .= '<div><p>ATutor 2.0 upgrade requires you to convert ATutor content to UTF-8 if you are upgrading from a version earlier than ATutor 1.6.  The courses are listed below with their associated primary language.  </p><p>Please choose one of the conversion options listed below, the recommanded option(in blue) is already selected for you.</p><p>For a more detailed description for each of these conversions, please visit our <a href="http://wiki.atutor.ca/display/atutorwiki/UTF-8+Conversion" target="blank">ATutor Wiki page</a></p></div>';

			//The html fragment that sets the suggested conversion type on bold 
			$suggestion = array('class="suggested"', 'checked="checked"');	
			$suggestion_skip="";
			$suggestion_all="";
			$suggestion_courses=""; 
			$recommanded_conversion = "";

			$html .= '<table class="data">';
			$html .= '<tr><th>Course Title</th><th>Course Primary Language</th></tr>';

			//Get all courses and their associated language from $_POST
			$prev_language = "";  //Keep tracks of the course primary language in each loop.
			$is_same_language = true;
			if (isset($_SESSION['course_info'])){
				foreach ($_SESSION['course_info'] as $course_id=>$row){
					if ($prev_language==""){
						$prev_language = $row['char_set'];
					} elseif ($is_same_language==true) {
						if($prev_language != $row['char_set']){
							$is_same_language &= false;
						}
					}
					//Get title
					$query = 'SELECT title, primary_language FROM '.$_POST['tb_prefix'].'courses WHERE course_id='.$course_id;
					$result = mysql_query($query);
					if ($result && mysql_numrows($result) > 0){
						$rs_row = mysql_fetch_assoc($result);
						//if the char_set of the course is not defined, the array will be emptied.
						$html .= '<tr><td>';
						$html .= @mb_convert_encoding($rs_row['title'], "UTF-8", $row['char_set']);
						$html .= '</td><td>'.$row['char_set'];
						if ($row['char_set']==''){
							$errors[] = "<strong>$rs_row[title]</strong> has a primary language that does not appear to be installed on the system.  Before continuing, either set the primary language to one that is installed, or install the missing language pack.";
							$html .= ' <strong style="color:red;">Missing "'.$rs_row['primary_language'].'"</strong>';
						} 
						$html .= '</td></tr>';
					}
				}
			}
			if ($is_same_language == true){
				if (strtolower($prev_language)=="utf8" || strtolower($prev_language)=="utf-8" ){
					$suggestion_skip =& $suggestion;
					$recommanded_conversion = "skip";
				} else {
					$suggestion_all =& $suggestion;
					$recommanded_conversion = "all";
					$html .= '<input type="hidden" name="conv_all_char_set" value="'.$prev_language.'" />';
				}
			} else {
				$suggestion_courses =& $suggestion;
				$recommanded_conversion = "courses";
			}
			$html .= '</table><br/>';

			$html .= '<div><p><strong>Convert all content:</strong> Use this option if you are using just <i><u>one</u></i> non-UTF-8 language pack in your ATutor.</p>';
			$html .= '<p><strong>Convert content by courses:</strong> Use this option when you are using <i><u>more than one</u></i> UTF-8 or non-UTF-8 language pack in your ATutor.</p>';
			$html .= '<p><strong>Skip conversion:</strong> Use this option when you have <i><u>only UTF-8</u></i> language packs in your ATutor.</p>';
			$html .= '</div>';

			$html .= '<div '.$suggestion_all[0].'><input type="radio" id="convert_all" name="convert_type" value="all" '.$suggestion_all[1].'/>';
			$html .= '<label for="convert_all">Convert all content</label></div>';

			$html .= '<div '.$suggestion_courses[0].'><input type="radio" id="convert_courses" name="convert_type" value="courses" '.$suggestion_courses[1].'/>';
			$html .= '<label for="convert_courses">Convert content by courses</label></div>';

			$html .= '<div '.$suggestion_skip[0].'><input type="radio" id="convert_skip" name="convert_type" value="skip" '.$suggestion_skip[1].'/>';
			$html .= '<label for="convert_skip">Skip conversion</label></div>';
	
			//States flags for ustep9
			$html .= '<input type="hidden" name="con_step" value="2" />';
			$html .= '<input type="hidden" name="recommanded_conversion" value="'.$recommanded_conversion.'" />';
			if (isset($errors)){
				print_errors($errors);
				$html .= '<p align="center"><input type="submit" class="button" value=" Retry " name="submit" /></p>';
			} else {
				$html .= '<p align="center"><input type="submit" class="button" value=" Next &raquo;" name="submit" /></p>';
			}
			echo $html;
			print_hidden(2);
			print_post_for_step9($_POST);
			echo '</form>';			
			return;
		}
	}
}

	//Failed 
	if (isset($errors)) {
		print_errors($errors);
	}

	echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">
	<input type="hidden" name="step" value="3" />';
	store_steps(2);
	print_hidden(3);
	print_post_for_step9($_POST);
	echo '<p align="center"><input type="submit" class="button" value=" Retry " name="submit" /></p></form>';
	return;


/** ---------------------------------------------------------------------------
 * Functions declaraion
 *  ---------------------------------------------------------------------------
 */
/**
 * Runs through the database and get all the courses' titles and languages out
 * @param		table_prefix is the database table prefix
 * @course_info	is the array that stores the course_id->[charset, encoding] mapping.
 */
 function generateCourseLangTable($table_prefix, $course_info){
	echo '<table class="data">';
	echo '<tr><th>Course Title</th><th>Course Primary Language</th></tr>';
	//Get all courses and their associated languages out	
	foreach ($course_info AS $course_id=>$row){
		$query = 'SELECT title FROM '.$table_prefix.'courses WHERE course_id='.$course_id;
		$result = mysql_query($query);
		if ($result && mysql_numrows($result) > 0){
			$rs_row = mysql_fetch_assoc($result);
		} else {
			return;
		}
		echo '<tr><td>';
		if ($row['char_set']==''){
			echo $rs_row['title'];
			echo '</td><td>Undefined</td></tr>';
		} else {
			echo mb_convert_encoding($rs_row['title'], "UTF-8", $row['char_set']);
			echo '</td><td>'.$row['char_set'].'</td></tr>';
		}		
	}
	echo '</table>';
 }

/**
 * This function prints out the post values that need to be carried over along
 * the entire step 9.
 * @param $_post the post parameter
 */
//$post_steps = $post_steps;
function print_post_for_step9($post_steps){
	echo '<input type="hidden" name="db_login" value="'.urlencode($post_steps['db_login']).'" />';
	echo '<input type="hidden" name="db_password" value="'.urlencode($post_steps['db_password']).'" />';
	echo '<input type="hidden" name="db_host" value="'.$post_steps['db_host'].'" />';
	echo '<input type="hidden" name="db_name" value="'.$post_steps['db_name'].'" />';
	echo '<input type="hidden" name="db_port" value="'.$post_steps['db_port'].'" />';
	echo '<input type="hidden" name="tb_prefix" value="'.$post_steps['tb_prefix'].'" />';
	echo '<input type="hidden" name="old_version" value="'.$post_steps['old_version'].'" />';
	echo '<input type="hidden" name="new_version" value="'.$post_steps['new_version'].'" />';
}


/**
 * This function checks if the database's and tbale's default charset are UTF-8.
 * @return true if db default charset is UTF-8, false otherwise.
 */
function check_db_default_charset($db){
	//Check database's default charset
	$sql = "SHOW CREATE DATABASE `$_POST[db_name]`";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if (!preg_match('/CHARACTER SET utf8/i', $row['Create Database'])){
		return false;
	} 

	//check tables
	$sql = "SHOW TABLES";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_row($result)){
		$row[0];
		$sql2 = "SHOW CREATE TABLE `$row[0]`";
		$result2 = mysql_query($sql2, $db);
		$row2 = mysql_fetch_row($result);

		if (!preg_match('/DEFAULT CHARSET\=utf8/i', $row[1])){
			return false;
		} 			
	}

	return true;
}
?>