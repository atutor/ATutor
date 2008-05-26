<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Harris Wong						*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

/* Constances, refer to /include/lib/constants.inc.php */
/* files */
define('WORKSPACE_COURSE',     1); // aka Course Files
define('WORKSPACE_PERSONAL',   2); // aka My Files
define('WORKSPACE_ASSIGNMENT', 3);
define('WORKSPACE_GROUP',      4);
/* links */
define('LINK_CAT_COURSE',	1);
define('LINK_CAT_GROUP',	2);
define('LINK_CAT_SELF',		3);

/**
 * This class handles different types of conversions for the ATutor tables.
 * Table entries based on the course's Primary Language will be converted by convertTableByClass($,$)
 * Table entries based on the System Defualt Language will be converted by convertTableBySysDefualt()
 * @access			public
 * @author			Harris Wong
 * @precondition	MySQL connected, mbstring lib enabled.
 * @date			Dec 12, 2007
 */
 class ConversionDriver{
	 /** variable */
	var $sys_default_lang;
	var $table_prefix;

	 /** 
	  * Constructor
	  * @param	table_prefix
	  */
	 function ConversionDriver($table_prefix){
		 $this->sys_default_lang = 'iso-8859-1';
		 $this->table_prefix = $table_prefix;
	 }

	 /**
	  * This function runs all the table that uses the system default language
	  */
	 function convertTableBySysDefault(){
		global $errors;

		$temp_table =& new CourseCategoriesTable($this->table_prefix, 'course_cats', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'course_cats was not converted.';
			$_SESSION['redo_conversion'][$course_title]['CourseCategoriesTable'] = array($this->table_prefix, 'course_cats', $char_set, $course_id);
		}

		$temp_table =& new MembersTable($this->table_prefix, 'members', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'members was not converted.';
			$_SESSION['redo_conversion'][$course_title]['MembersTable'] = array($this->table_prefix, 'members', $char_set, $course_id);
		}
	 }

	/**
	 * This function runs all the table that uses the system default language
	 * Particular for 1.6.1, since 1.6 didn't convret all table
	 */
	function convertTableBySysDefault_161(){
		global $errors;

		$temp_table =& new AdminsTable($this->table_prefix, 'admins', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'admins was not converted.';
			$_SESSION['redo_conversion'][$course_title]['AdminsTable'] = array($this->table_prefix, 'admins', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new AdminLogTable($this->table_prefix, 'admin_log', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'admin_log was not converted.';
			$_SESSION['redo_conversion'][$course_title]['AdminLogTable'] = array($this->table_prefix, 'admin_log', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new AutoEnrollTable($this->table_prefix, 'auto_enroll', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'auto_enroll was not converted.';
			$_SESSION['redo_conversion'][$course_title]['AutoEnrollTable'] = array($this->table_prefix, 'auto_enroll', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new AutoEnrollCoursesTable($this->table_prefix, 'auto_enroll_courses', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'auto_enroll_courses was not converted.';
			$_SESSION['redo_conversion'][$course_title]['AutoEnrollCourses'] = array($this->table_prefix, 'auto_enroll_courses', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new ConfigTable($this->table_prefix, 'config', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'config was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ConfigTable'] = array($this->table_prefix, 'config', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new CourseAccessTable($this->table_prefix, 'course_access', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'course_access was not converted.';
			$_SESSION['redo_conversion'][$course_title]['CourseAccessTable'] = array($this->table_prefix, 'course_access', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new CourseStatsTable($this->table_prefix, 'course_stats', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'course_stats was not converted.';
			$_SESSION['redo_conversion'][$course_title]['CourseStatsTable'] = array($this->table_prefix, 'course_stats', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new FeedsTable($this->table_prefix, 'feeds', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'feeds was not converted.';
			$_SESSION['redo_conversion'][$course_title]['FeedsTable'] = array($this->table_prefix, 'feeds', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new FileStorageGroupsTable($this->table_prefix, 'file_storage_groups', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'file_storage_groups was not converted.';
			$_SESSION['redo_conversion'][$course_title]['FileStorageGroupsTable'] = array($this->table_prefix, 'file_storage_groups', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new ForumsAccessedTable($this->table_prefix, 'forums_accessed', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'forums_accessed was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ForumsAccessedTable'] = array($this->table_prefix, 'forums_accessed', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new ForumsCoursesTable($this->table_prefix, 'forums_courses', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'forums_courses was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ForumsCoursesTable'] = array($this->table_prefix, 'forums_courses', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new ForumsGroupsTable($this->table_prefix, 'forums_groups', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'forums_groups was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ForumsGroupsTable'] = array($this->table_prefix, 'forums_groups', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new ForumsSubscriptionsTable($this->table_prefix, 'forums_subscriptions', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'forums_subscriptions was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ForumsSubscriptionsTable'] = array($this->table_prefix, 'forums_subscriptions', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new GroupsMembersTable($this->table_prefix, 'groups_members', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'groups_members was not converted.';
			$_SESSION['redo_conversion'][$course_title]['GroupsMembersTable'] = array($this->table_prefix, 'groups_members', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new HandbookNotesTable($this->table_prefix, 'handbook_notes', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'handbook_notes was not converted.';
			$_SESSION['redo_conversion'][$course_title]['HandbookNotesTable'] = array($this->table_prefix, 'handbook_notes', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new InstructorApprovalsTable($this->table_prefix, 'instructor_approvals', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'instructor_approvals was not converted.';
			$_SESSION['redo_conversion'][$course_title]['InstructorApprovalsTable'] = array($this->table_prefix, 'instructor_approvals', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new LanguagesTable($this->table_prefix, 'languages', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'languages was not converted.';
			$_SESSION['redo_conversion'][$course_title]['LanguagesTable'] = array($this->table_prefix, 'languages', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new LanguagePagesTable($this->table_prefix, 'language_pages', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'language_pages was not converted.';
			$_SESSION['redo_conversion'][$course_title]['LanguagePagesTable'] = array($this->table_prefix, 'language_pages', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new LanguageTextTable($this->table_prefix, 'language_text', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'language_text was not converted.';
			$_SESSION['redo_conversion'][$course_title]['LanguageTextTable'] = array($this->table_prefix, 'language_text', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new MailQueueTable($this->table_prefix, 'mail_queue', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'mail_queue was not converted.';
			$_SESSION['redo_conversion'][$course_title]['MailQueueTable'] = array($this->table_prefix, 'mail_queue', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new MasterListTable($this->table_prefix, 'master_list', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'master_list was not converted.';
			$_SESSION['redo_conversion'][$course_title]['MasterListTable'] = array($this->table_prefix, 'master_list', $this->sys_default_lang, $course_id);
		}
	
		$temp_table =& new MemberTrackTable($this->table_prefix, 'member_track', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'member_track was not converted.';
			$_SESSION['redo_conversion'][$course_title]['MemberTrackTable'] = array($this->table_prefix, 'member_track', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new ModulesTable($this->table_prefix, 'modules', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'modules was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ModulesTable'] = array($this->table_prefix, 'modules', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new PollsMembersTable($this->table_prefix, 'polls_members', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'polls_members was not converted.';
			$_SESSION['redo_conversion'][$course_title]['PollsMembersTable'] = array($this->table_prefix, 'polls_members', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new RelatedContentTable($this->table_prefix, 'related_content', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'related_content was not converted.';
			$_SESSION['redo_conversion'][$course_title]['RelatedContentTable'] = array($this->table_prefix, 'related_content', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new TestsGroupsTable($this->table_prefix, 'tests_groups', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'tests_groups was not converted.';
			$_SESSION['redo_conversion'][$course_title]['TestsGroupsTable'] = array($this->table_prefix, 'tests_groups', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new TestsQuestionsAssocTable($this->table_prefix, 'tests_questions_assoc', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'tests_questions_assoc was not converted.';
			$_SESSION['redo_conversion'][$course_title]['TestsQuestionsAssocTable'] = array($this->table_prefix, 'tests_questions_assoc', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new TestsResultsTable($this->table_prefix, 'tests_results', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'tests_results was not converted.';
			$_SESSION['redo_conversion'][$course_title]['TestsResultsTable'] = array($this->table_prefix, 'tests_results', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new ThemesTable($this->table_prefix, 'themes', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'themes was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ThemesTable'] = array($this->table_prefix, 'themes', $this->sys_default_lang, $course_id);
		}

		$temp_table =& new UsersOnlineTable($this->table_prefix, 'users_online', $this->sys_default_lang);
		if (!$temp_table->convert()){
			$errors[]= $this->table_prefix.'users_online was not converted.';
			$_SESSION['redo_conversion'][$course_title]['UsersOnlineTable'] = array($this->table_prefix, 'users_online', $this->sys_default_lang, $course_id);
		}
	}

	 /**
	  * This function runs through all the table that are class dependent.
	  */
	 function convertTableByClass($course_title, $char_set, $course_id){
		 global $errors;
		//Run through all ATutor table and convert only those rows with the above courses.
		//todo: implement a driver class inside the TableConversion class.
		$temp_table =& new AssignmentsTable($this->table_prefix, 'assignments', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'assignments was not converted.';
			$_SESSION['redo_conversion'][$course_title]['AssignmentsTable'] = array($this->table_prefix, 'assignments', $char_set, $course_id);
		}

		$temp_table =& new BackupsTable($this->table_prefix, 'backups', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'backups was not converted.';
			$_SESSION['redo_conversion'][$course_title]['BackupsTable'] = array($this->table_prefix, 'backups', $char_set, $course_id);
		}

		$temp_table =& new BlogPostsTable($this->table_prefix, 'blog_posts', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'blog_posts was not converted.';
			$_SESSION['redo_conversion'][$course_title]['BlogPostsTable'] = array($this->table_prefix, 'blog_posts', $char_set, $course_id);
		}
		
		$temp_table =& new ContentTable($this->table_prefix, 'content', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'content was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ContentTable'] = array($this->table_prefix, 'content', $char_set, $course_id);
		}
		
		$temp_table =& new CoursesTable($this->table_prefix, 'courses', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'courses was not converted.';
			$_SESSION['redo_conversion'][$course_title]['CoursesTable'] = array($this->table_prefix, 'courses', $char_set, $course_id);
		}
		
		$temp_table =& new CourseEnrollmentTable($this->table_prefix, 'course_enrollment', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'course_enrollment was not converted.';
			$_SESSION['redo_conversion'][$course_title]['CourseEnrollmentTable'] = array($this->table_prefix, 'course_enrollment', $char_set, $course_id);
		}

		$temp_table =& new ExternalResourcesTable($this->table_prefix, 'external_resources', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'external_resources was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ExternalResourcesTable'] = array($this->table_prefix, 'external_resources', $char_set, $course_id);
		}

		$temp_table =& new FaqTopicsTable($this->table_prefix, 'faq_topics', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'faq_topics was not converted.';
			$_SESSION['redo_conversion'][$course_title]['FaqTopicsTable'] = array($this->table_prefix, 'faq_topics', $char_set, $course_id);
		}
		
		$temp_table =& new FoldersTable($this->table_prefix, 'folders', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'folders was not converted.';
			$_SESSION['redo_conversion'][$course_title]['FoldersTable'] = array($this->table_prefix, 'folders', $char_set, $course_id);
		}

		$temp_table =& new FilesTable($this->table_prefix, 'files', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'files was not converted.';
			$_SESSION['redo_conversion'][$course_title]['FilesTable'] = array($this->table_prefix, 'files', $char_set, $course_id);
		}
		
		$temp_table =& new ForumsTable($this->table_prefix, 'forums', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'forums was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ForumsTable'] = array($this->table_prefix, 'forums', $char_set, $course_id);
		}

		$temp_table =& new GlossaryTable($this->table_prefix, 'glossary', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'glossary was not converted.';
			$_SESSION['redo_conversion'][$course_title]['GlossaryTable'] = array($this->table_prefix, 'glossary', $char_set, $course_id);
		}

		$temp_table =& new GroupsTypesTable($this->table_prefix, 'groups_types', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'groups_types was not converted.';
			$_SESSION['redo_conversion'][$course_title]['GroupsTypesTable'] = array($this->table_prefix, 'groups_types', $char_set, $course_id);
		}

		$temp_table =& new LinksCategoriesTable($this->table_prefix, 'links_categories', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'links_categories was not converted.';
			$_SESSION['redo_conversion'][$course_title]['LinksCategoriesTable'] = array($this->table_prefix, 'links_categories', $char_set, $course_id);
		}

		$temp_table =& new MessagesTable($this->table_prefix, 'messages', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'messages was not converted.';
			$_SESSION['redo_conversion'][$course_title]['MessagesTable'] = array($this->table_prefix, 'messages', $char_set, $course_id);
		}

		$temp_table =& new MessagesSentTable($this->table_prefix, 'messages_sent', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'messages_sent was not converted.';
			$_SESSION['redo_conversion'][$course_title]['MessagesSentTable'] = array($this->table_prefix, 'messages_sent', $char_set, $course_id);
		}

		$temp_table =& new NewsTable($this->table_prefix, 'news', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'news was not converted.';
			$_SESSION['redo_conversion'][$course_title]['NewsTable'] = array($this->table_prefix, 'news', $char_set, $course_id);
		}

		$temp_table =& new PollsTable($this->table_prefix, 'polls', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'polls was not converted.';
			$_SESSION['redo_conversion'][$course_title]['PollsTable'] = array($this->table_prefix, 'polls', $char_set, $course_id);
		}

		$temp_table =& new ReadingListTable($this->table_prefix, 'reading_list', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'reading_list was not converted.';
			$_SESSION['redo_conversion'][$course_title]['ReadingListTable'] = array($this->table_prefix, 'reading_list', $char_set, $course_id);
		}

		$temp_table =& new TestsTable($this->table_prefix, 'tests', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'tests was not converted.';
			$_SESSION['redo_conversion'][$course_title]['TestsTable'] = array($this->table_prefix, 'tests', $char_set, $course_id);
		}

		$temp_table =& new TestQuestionsTable($this->table_prefix, 'tests_questions', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'tests_questions was not converted.';
			$_SESSION['redo_conversion'][$course_title]['TestQuestionsTable'] = array($this->table_prefix, 'tests_questions', $char_set, $course_id);
		}

		$temp_table =& new TestsQuestionsCategoriesTable($this->table_prefix, 'tests_questions_categories', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'tests_questions_categories was not converted.';
			$_SESSION['redo_conversion'][$course_title]['TestsQuestionsCategoriesTable'] = array($this->table_prefix, 'tests_questions_categories', $char_set, $course_id);
		}
	 }

	 /**
	  * This function runs through all the table that are class dependent.
	  * Particular for the tables that haven't been converted during 1.5.5 to 1.6
	  */
	 function convertTableByClass_161($course_title, $char_set, $course_id){
		 global $errors;
		//Run through all ATutor table and convert only those rows with the above courses.
		//todo: implement a driver class inside the TableConversion class.
		$temp_table =& new MessagesTable($this->table_prefix, 'messages', $char_set, $course_id);
		if (!$temp_table->convert()){
			$errors[]= $course_title.': '.$this->table_prefix.'messages was not converted.';
			$_SESSION['redo_conversion'][$course_title]['MessagesTable'] = array($this->table_prefix, 'messages', $char_set, $course_id);
		}
	 }

	 /**
	  * This function will alter all table's charset to UTF-8
	  */
	 function alter_all_charset(){
		 global $errors;
		 $sql = 'SHOW TABLES';
		 $result = mysql_query($sql);
		 if (mysql_numrows($result) > 0) {
			 while ($row = mysql_fetch_array($result)){
				 $sql = 'ALTER TABLE `'.$row[0].'` CONVERT TO CHARACTER SET utf8';
				 mysql_query($sql);
			 }
		 }
	 }
 }


/**
* This class will handle utf8 conversion on all tables associated with a specific course.
* This class can be potentially upgraded to a automated table parser to optimize codes, instead of having 
* different abstract classes for each individual table inside ATutor.  
* Note: Keeping in mind that this class will not be used a lot after 1.6 conversion.  
* @access			public
* @author			Harris Wong
* @precondition		MySQL connected, mbstring lib enabled.
* @date				Nov 28, 2007
*/
class ATutorTable{
	/** variables */
	var $table;
	var $table_prefix;
	var $from_encoding;
	var $courseID;
	var $to_encoding;

	/**
	 * Constructor
	 * @param	table prefix
	 * @param	table is the table name of which we want to covert
	 * @param	from_encoding is the encoding which the content will be converted from.
	 * @param	foreign_ID is the primary key/foreign key of the table.  $foreign_ID will be the primary key when
	 *			the table has a "course_id" column, foreign key when it doesn't.  
	 *			foreign_ID is an empty string if this table does not depend on courses, such as members, 
	 *			course categories tables.
	 */
	function ATutorTable($table_prefix, $table, $from_encoding, $foreign_ID=''){
		$this->table_prefix = $table_prefix;
		$this->table = $table;
		$this->from_encoding = $from_encoding;
		$this->foreign_ID= $foreign_ID;
		$this->to_encoding = "UTF-8";
		//check if mb_string library is enabled, die o/w
		 if (!extension_loaded('mbstring')){
			 die("Please have mbstring library enabled");
		 }
		
		//Alter table
		$this->alterTable();
	}


	/**
	 * alterTable
	 * Perform mysql ALTER table function, to switch to UTF-8 tables.
	 */
	function alterTable(){
		$query = 'ALTER TABLE `'.$this->table_prefix.$this->table.'` CONVERT TO CHARACTER SET utf8';
		mysql_query($query);
	}


	/**
	 * getContent
	 * This method will get all the contents from this table with the given courseID.
	 * @param courseDependent = false when this table isn't related to course encoding, true if it is related (default)
	 * @return	result set, and null on failure or 0 rows
	 */
	function getContent($courseDependent = true){
		if ($courseDependent) {
			$sql = "SELECT * FROM `".$this->table_prefix.$this->table."` WHERE course_id=".$this->foreign_ID;
		} else {
			$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table;
		}
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	/**
	 * convert
	 * Abstract class that convert the table contents to UTF8
	 * @return mysql_query's return object
	 */
	function convert(){/* Abstract */}
	
	
	/**
	 * executeSQL
	 * This runs the sql statement
	 * @param value_array contains all the new values mapped by their column names
	 * @param primary_key is the primary key of this table.
	 */
	function generate_sql($value_array, $primary_key_col, $primary_key){
		$sql = "UPDATE `".$this->table_prefix.$this->table."` SET ";
		$i = 1;
		foreach($value_array as $column_name=>$column_value){
			$column_value = mysql_real_escape_string($column_value);
			$column_name = mysql_real_escape_string($column_name);
			$sql .= "`$column_name`='$column_value'";
			if ($i < sizeof($value_array)) {
				$sql .= ', ';
			}
			$i++;
		}
		//If there are more than 1 key
		if (is_array($primary_key_col)){
			$j = 1;
			$sql .= " WHERE ";
			foreach ($primary_key_col as $k=>$v){
				$v = mysql_real_escape_string($v);
				$sql .= $v.'='.$primary_key[$k];
				if ($j < sizeof($primary_key_col)){
					$sql .= " AND ";
				}
				$j++;
			}
		} else {
			$sql .= " WHERE `$primary_key_col`=";
			if (preg_match('/^[0-9]+$/', $primary_key)==1){
				$sql .= $primary_key;
			} else {
				//prim key is a string, put it around a pair of quotes
				$sql .= "'$primary_key'";
			}
		}
//		echo "<hr/>";
		return $sql;
	}
}


/**
 * Class for Admins
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class AdminsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'login';
			//Convert all neccessary entries
			$value_array['real_name'] = mb_convert_encoding($row['real_name'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
			echo mysql_error();
		}
		return $result;
	}
}

/**
 * Class for AdminLog
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class AdminLogTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'time';
			//Convert all neccessary entries
			$value_array['operation'] = mb_convert_encoding($row['operation'], $this->to_encoding, $this->from_encoding);
			$value_array['table'] = mb_convert_encoding($row['table'], $this->to_encoding, $this->from_encoding);
			$value_array['details'] = mb_convert_encoding($row['details'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/** 
 * Class for Assignments
 */
class AssignmentsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'assignment_id';
			//Convert all neccessary entries
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);

			//Convert Folders table, that is related to assignments.
			$folders =& new FoldersTable($this->table_prefix, 'folders', $this->from_encoding, $row[$key_col]);
			$result &= $folders->convert(WORKSPACE_ASSIGNMENT);
			//Convert Files table, that is related to assignments.
			$files_table =& new FilesTable($this->table_prefix, 'files', $this->from_encoding, $row[$key_col]);
			$result &= $files_table->convert(WORKSPACE_ASSIGNMENT);

			//Generate SQL
			//echo (mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]))) ;
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		} 
		//Needs to alter related tables
		if (!$rs) {			
			new FoldersTable($this->table_prefix, 'folders', '');
			new FilesTable($this->table_prefix, 'files', '');
		}
		return $result;
	}
}

/**
 * Class for auto_enroll
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class AutoEnrollTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'auto_enroll_id';
			//Convert all neccessary entries
			$value_array['associate_string'] = mb_convert_encoding($row['associate_string'], $this->to_encoding, $this->from_encoding);
			$value_array['name'] = mb_convert_encoding($row['name'], $this->to_encoding, $this->from_encoding);

			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for auto_enroll_courses
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class AutoEnrollCoursesTable extends ATutorTable{
	//Nothing to convert in this table except the table structure.
	function convert(){
		return true;
	}
}


/** 
 * Class for Backups
 */
class BackupsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'backup_id';
			//Convert all neccessary entries
			$value_array['date'] = $row['date'];
			$value_array['description'] = mb_convert_encoding($row['description'], $this->to_encoding, $this->from_encoding);
			$value_array['system_file_name'] = mb_convert_encoding($row['system_file_name'], $this->to_encoding, $this->from_encoding);
			$value_array['file_name'] = mb_convert_encoding($row['file_name'], $this->to_encoding, $this->from_encoding);
			$value_array['contents'] = mb_convert_encoding($row['contents'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/** 
 * Class for Blog posts
 */
class BlogPostsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'post_id';
			//Convert all neccessary entries
			$value_array['date'] = $row['date'];
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['body'] = mb_convert_encoding($row['body'], $this->to_encoding, $this->from_encoding);
			//Convert sub post comment.
			$commentPosts =& new BlogPostsCommentsTable($this->table_prefix, 'blog_posts_comments', $this->from_encoding, $row[$key_col]);
			$result &= $commentPosts->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		//Needs to alter related tables
		if (!$rs) {			
			new BlogPostsCommentsTable($this->table_prefix, 'blog_posts_comments', '');
		}
		return $result;
	}
}

/** 
 * Class for Blog posts comments
 * Used only by BlogPostsTable
 * Foreign key = post_id
 */
class BlogPostsCommentsTable extends ATutorTable{
	//Overrider
	function getContent(){
		$sql = "SELECT * FROM `".$this->table_prefix.$this->table."` WHERE post_id=".$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'comment_id';
			//Convert all neccessary entries
			$value_array['date'] = $row['date'];
			$value_array['text'] = mb_convert_encoding($row['text'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for config
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class ConfigTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for course_access
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class CourseAccessTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for course_stats
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class CourseStatsTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for Content
 */
class ContentTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'content_id';
			//Convert all neccessary entries
			$value_array['last_modified'] = $row['last_modified'];
			$value_array['keywords'] = mb_convert_encoding($row['keywords'], $this->to_encoding, $this->from_encoding);
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['text'] = mb_convert_encoding($row['text'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Courses
 */
class CoursesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'course_id';
			//Convert all neccessary entries
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['description'] = mb_convert_encoding($row['description'], $this->to_encoding, $this->from_encoding);
//			$value_array['preferences'] = mb_convert_encoding($row['preferences'], $this->to_encoding, $this->from_encoding);
			$value_array['copyright'] = mb_convert_encoding($row['copyright'], $this->to_encoding, $this->from_encoding);
			$value_array['banner'] = mb_convert_encoding($row['banner'], $this->to_encoding, $this->from_encoding);
			/* The following should not needed to be converted after they are deprecated */
//			$value_array['header'] = mb_convert_encoding($row['header'], $this->to_encoding, $this->from_encoding);
//			$value_array['footer'] = mb_convert_encoding($row['footer'], $this->to_encoding, $this->from_encoding);			
//			$value_array['banner_text'] = mb_convert_encoding($row['banner_text'], $this->to_encoding, $this->from_encoding);
//			$value_array['banner_styles'] = mb_convert_encoding($row['banner_styles'], $this->to_encoding, $this->from_encoding);			

			//Generate SQL
//			echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Courses enrollment
 */
class CourseEnrollmentTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'course_id';
			$key_col2 = 'member_id';
			//Convert all neccessary entries
			$value_array['role'] = mb_convert_encoding($row['role'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, array($key_col, $key_col2), array($row[$key_col], $row[$key_col2]));
			$result &= mysql_query($this->generate_sql($value_array, array($key_col, $key_col2), array($row[$key_col], $row[$key_col2])));
		}
		return $result;
	}
}


/**
 * Class for Course Categories
 * Course Categories are created by admins, the language encoding should be based on
 * the admin's language setting for >= 1.5.1
 * Otherwise, default it to iso-8859-1.
 * Note: This class is independent from courses
 */
class CourseCategoriesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'cat_id';
			//Convert all neccessary entries
			$value_array['cat_name'] = mb_convert_encoding($row['cat_name'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for External resources
 */
class ExternalResourcesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'resource_id';
			//Convert all neccessary entries
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['author'] = mb_convert_encoding($row['author'], $this->to_encoding, $this->from_encoding);
			$value_array['publisher'] = mb_convert_encoding($row['publisher'], $this->to_encoding, $this->from_encoding);
			$value_array['comments'] = mb_convert_encoding($row['comments'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Faq topics
 */
class FaqTopicsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'topic_id';
			//Convert all neccessary entries
			$value_array['name'] = mb_convert_encoding($row['name'], $this->to_encoding, $this->from_encoding);
			//Convert faq entries
			$faqEntries =& new FaqEntriesTable($this->table_prefix, 'faq_entries', $this->from_encoding, $row[$key_col]);
			$faqEntries->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		//Needs to alter related tables
		if (!$rs) {			
			new FaqEntriesTable($this->table_prefix, 'faq_entries', '');
		}
		return $result;
	}
}

/**
 * Class for Faq Entries 
 * Used only by FaqTopicsTable
 * Foreign key = topic_id
 */
class FaqEntriesTable extends ATutorTable{
	//Overrider
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE topic_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'entry_id';
			//Convert all neccessary entries
			$value_array['revised_date'] = $row['revised_date'];
			$value_array['question'] = mb_convert_encoding($row['question'], $this->to_encoding, $this->from_encoding);
			$value_array['answer'] = mb_convert_encoding($row['answer'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for feeds
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class FeedsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'feed_id';
			//Convert all neccessary entries
			$value_array['url'] = mb_convert_encoding($row['url'], $this->to_encoding, $this->from_encoding);

			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for Forums 
 */
class ForumsTable extends ATutorTable{
	//Overrider
	function getContent(){
		$sql = 'SELECT this_forum.* FROM `'.$this->table_prefix.$this->table.'` this_forum NATURAL JOIN `'.$this->table_prefix.'forums_courses` this_course WHERE this_course.course_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'forum_id';
			//Convert all neccessary entries
			$value_array['last_post'] = $row['last_post'];
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['description'] = mb_convert_encoding($row['description'], $this->to_encoding, $this->from_encoding);
			//Convert faq entries
			$forumThread=& new ForumsThreadsTable($this->table_prefix, 'forums_threads', $this->from_encoding, $row[$key_col]);
			$result &= $forumThread->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result = mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		//Needs to alter related tables
		if (!$rs) {			
			new ForumsThreadsTable($this->table_prefix, 'forums_threads', '');
		}
		return $result;
	}
}

/**
 * Class for forums_accessed
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class ForumsAccessedTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for forums_courses
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class ForumsCoursesTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for forums_groups
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class ForumsGroupsTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for forums_subscriptions
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class ForumsSubscriptionsTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for Forums threads
 * Used only by ForumsTable
 * Foreign key = forum_id
 */
class ForumsThreadsTable extends ATutorTable{
	//Overrider
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE forum_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'post_id';
			//Convert all neccessary entries
			$value_array['last_comment'] = $row['last_comment'];
			$value_array['subject'] = mb_convert_encoding($row['subject'], $this->to_encoding, $this->from_encoding);
			$value_array['body'] = mb_convert_encoding($row['body'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Folders
 * Associated with Groups, Links
 */
 class FoldersTable extends ATutorTable{
	/*
	 * Overrider
	 * owner_id means category_id, owner_type refers to the different link type defined in the constants.inc.php.
	 * @param	$owner_type are defined by the constances, which are course, groups, self
	 */
	function getContent($owner_type){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE owner_type='.$owner_type.' AND owner_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	/*
	 * @param	$owner_type are defined by the constances, which are course, groups, self; defaulted to be WORKSPACE_COURSE
	 */
	function convert($owner_type=WORKSPACE_COURSE){
		$rs = $this->getContent($owner_type);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'folder_id';
			//Convert all neccessary entries
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
 }

/**
 * Class for Files
 * Associated with Groups, Links
 */
 class FilesTable extends ATutorTable{
	/*
	 * Overrider
	 * owner_id means category_id, owner_type refers to the different link type defined in the constants.inc.php.
	 * @param	$owner_type are defined by the constances, which are course, groups, self
	 */
	function getContent($owner_type){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE owner_type='.$owner_type.' AND owner_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	/*
	 * @param	$owner_type are defined by the constances, which are course, groups, self; defaulted to be WORKSPACE_COURSE
	 */
	function convert($owner_type=WORKSPACE_COURSE){
		$rs = $this->getContent($owner_type);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'file_id';
			//Convert all neccessary entries
			$value_array['date'] = $row['date'];
			$value_array['file_name'] = mb_convert_encoding($row['file_name'], $this->to_encoding, $this->from_encoding);
			$value_array['description'] = mb_convert_encoding($row['description'], $this->to_encoding, $this->from_encoding);
			//Convert faq entries
			$filesComments=& new FilesCommentsTable($this->table_prefix, 'files_comments', $this->from_encoding, $row[$key_col]);
			$result &= $filesComments->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		//Needs to alter related tables
		if (!$rs) {			
			new FilesCommentsTable($this->table_prefix, 'files_comments', '');
		}
		return $result;
	}
 }


/**
 * Class for Files comments 
 * Used only by FilesTable
 * Foreign key = file_id
 */
class FilesCommentsTable extends ATutorTable{
	//Overrider
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE file_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'comment_id';
			//Convert all neccessary entries
			$value_array['date'] = $row['date'];
			$value_array['comment'] = mb_convert_encoding($row['comment'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for file_storage_groups
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class FileStorageGroupsTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for Glossary 
 */
class GlossaryTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'word_id';
			//Convert all neccessary entries
			$value_array['word'] = mb_convert_encoding($row['word'], $this->to_encoding, $this->from_encoding);
			$value_array['definition'] = mb_convert_encoding($row['definition'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Group types
 */
class GroupsTypesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'type_id';
			//Convert all neccessary entries
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			
			//Convert group table
			$groups =& new GroupsTable($this->table_prefix, 'groups', $this->from_encoding, $row[$key_col]);
			$result &= $groups->convert();
			//Convert links table, that has owner_type=group
			$linkscats =& new LinksCategoriesTable($this->table_prefix, 'links_categories', $this->from_encoding, $row[$key_col]);
			$result &= $linkscats->convert(LINK_CAT_GROUP);
			//Convert folder tables, that has owner_type=group
			$folders =& new FoldersTable($this->table_prefix, 'folders', $this->from_encoding, $row[$key_col]);
			$result &= $folders->convert(WORKSPACE_GROUP);
			//Convert file tables, that has owner_type=group
			$files_table =& new FilesTable($this->table_prefix, 'files', $this->from_encoding, $row[$key_col]);
			$result &= $files_table->convert(WORKSPACE_GROUP);

			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		//Needs to alter related tables
		if (!$rs) {			
			new GroupsTable($this->table_prefix, 'groups', '');
			new LinksCategoriesTable($this->table_prefix, 'links_categories', '');
			new FoldersTable($this->table_prefix, 'folders', '');
			new FilesTable($this->table_prefix, 'files', '');
		}
		return $result;
	}
}

/**
 * Class for Groups
 * Used only by GroupTypesTable
 * Foreign key = type_id
 */
class GroupsTable extends ATutorTable{
	//Overrider
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE type_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'group_id';
			//Convert all neccessary entries
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['description'] = mb_convert_encoding($row['description'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for groups_members
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class GroupsMembersTable extends ATutorTable{
	function convert(){
		//nothing to convert
		return true;
	}
}

/**
 * Class for handbooks_notes
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class HandbookNotesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'note_id';
			//Convert all neccessary entries
			$value_array['section'] = mb_convert_encoding($row['section'], $this->to_encoding, $this->from_encoding);
			$value_array['page'] = mb_convert_encoding($row['page'], $this->to_encoding, $this->from_encoding);
			$value_array['note'] = mb_convert_encoding($row['note'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for instructor_approvals
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class InstructorApprovalsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'member_id';
			//Convert all neccessary entries
			$value_array['notes'] = mb_convert_encoding($row['notes'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for lanaguages
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class LanguagesTable extends ATutorTable{
	function convert(){
		//will only have english language remains.
		return true;
	}
}

/**
 * Class for lanaguage_pages
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class LanguagePagesTable extends ATutorTable{
	function convert(){
		//will only have iso88591, which is in ascii
		return true;
	}
}

/**
 * Class for language_text
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class LanguageTextTable extends ATutorTable{
	function convert(){
		//will only have iso88591, which is in ascii
		return true;
	}
}

/**
 * Class for Links Categories
 * Links' owner_id can be of courses, groups, self.
 */
class LinksCategoriesTable extends ATutorTable{
	/*
	 * Overrider
	 * owner_id means category_id, owner_type refers to the different link type defined in the constants.inc.php.
	 * @param	$owner_type are defined by the constances
	 */
	function getContent($owner_type){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE owner_type='.$owner_type.' AND owner_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	/*
	 * @param	$owner_type are defined by the constances, which are course, groups, self; defaulted to be LINK_CAT_COURSE
	 */
	function convert($owner_type=LINK_CAT_COURSE){
		$rs = $this->getContent($owner_type);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'cat_id';
			//Convert all neccessary entries
			$value_array['name'] = mb_convert_encoding($row['name'], $this->to_encoding, $this->from_encoding);
			//Convert links table
			$linkscats =& new LinksTable($this->table_prefix, 'links', $this->from_encoding, $row[$key_col]);
			$result &= $linkscats->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		//Needs to alter related tables
		if (!$rs) {			
			new LinksTable($this->table_prefix, 'links', '');
		}
		return $result;
	}
}

/**
 * Class for Links
 * Used only by LinksCategoriesTable
 * Foreign key = cat_id
 */
 class LinksTable extends ATutorTable{
	//Overrider
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE cat_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'link_id';
			//Convert all neccessary entries
			$value_array['LinkName'] = mb_convert_encoding($row['LinkName'], $this->to_encoding, $this->from_encoding);
			$value_array['Description'] = mb_convert_encoding($row['Description'], $this->to_encoding, $this->from_encoding);
			$value_array['SubmitName'] = mb_convert_encoding($row['SubmitName'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
 }

/**
 * Class for mail_queue
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class MailQueueTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'mail_id';
			//Convert all neccessary entries
			$value_array['to_name'] = mb_convert_encoding($row['to_name'], $this->to_encoding, $this->from_encoding);
			$value_array['from_name'] = mb_convert_encoding($row['from_name'], $this->to_encoding, $this->from_encoding);
			$value_array['subject'] = mb_convert_encoding($row['subject'], $this->to_encoding, $this->from_encoding);
			$value_array['body'] = mb_convert_encoding($row['body'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for master_list
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class MasterListTable extends ATutorTable{
	function convert(){
		//nothong to convert
		return true;
	}
}

/**
 * Class for Members 
 * Note: This class is independent from courses
 */
class MembersTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'member_id';
			//Convert all neccessary entries
			$value_array['first_name'] = mb_convert_encoding($row['first_name'], $this->to_encoding, $this->from_encoding);
			$value_array['second_name'] = mb_convert_encoding($row['second_name'], $this->to_encoding, $this->from_encoding);
			$value_array['last_name'] = mb_convert_encoding($row['last_name'], $this->to_encoding, $this->from_encoding);
			$value_array['address'] = mb_convert_encoding($row['address'], $this->to_encoding, $this->from_encoding);
			$value_array['city'] = mb_convert_encoding($row['city'], $this->to_encoding, $this->from_encoding);
			$value_array['province'] = mb_convert_encoding($row['province'], $this->to_encoding, $this->from_encoding);
			$value_array['creation_date'] = $row['creation_date'];
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for member_track
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class MemberTrackTable extends ATutorTable{
	function convert(){
		//nothong to convert
		return true;
	}
}

/**
 * Class for Messages
 */
class MessagesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true; 
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'message_id';
			//Convert all neccessary entries
			$value_array['date_sent'] = $row['date_sent'];
			$value_array['subject'] = mb_convert_encoding($row['subject'], $this->to_encoding, $this->from_encoding);
			$value_array['body'] = mb_convert_encoding($row['body'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Messages Sent
 */
class MessagesSentTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true; 
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'message_id';
			//Convert all neccessary entries
			$value_array['date_sent'] = $row['date_sent'];
			$value_array['subject'] = mb_convert_encoding($row['subject'], $this->to_encoding, $this->from_encoding);
			$value_array['body'] = mb_convert_encoding($row['body'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for modules
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class ModulesTable extends ATutorTable{
	function convert(){
		//nothong to convert
		return true;
	}
}


/**
 * Class for News
 */
class NewsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'news_id';
			//Convert all neccessary entries
			$value_array['date'] = $row['date'];
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['body'] = mb_convert_encoding($row['body'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Polls
 */
class PollsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'poll_id';
			//Convert all neccessary entries
			$value_array['created_date'] = $row['created_date'];
			$value_array['question'] = mb_convert_encoding($row['question'], $this->to_encoding, $this->from_encoding);
			$value_array['choice1'] = mb_convert_encoding($row['choice1'], $this->to_encoding, $this->from_encoding);
			$value_array['choice2'] = mb_convert_encoding($row['choice2'], $this->to_encoding, $this->from_encoding);
			$value_array['choice3'] = mb_convert_encoding($row['choice3'], $this->to_encoding, $this->from_encoding);
			$value_array['choice4'] = mb_convert_encoding($row['choice4'], $this->to_encoding, $this->from_encoding);
			$value_array['choice5'] = mb_convert_encoding($row['choice5'], $this->to_encoding, $this->from_encoding);
			$value_array['choice6'] = mb_convert_encoding($row['choice6'], $this->to_encoding, $this->from_encoding);
			$value_array['choice7'] = mb_convert_encoding($row['choice7'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}

/**
 * Class for PollsMembers
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class PollsMembersTable extends ATutorTable{
	function convert(){
		//nothong to convert
		return true;
	}
}

/**
 * Class for RelatedContent
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class RelatedContentTable extends ATutorTable{
	function convert(){
		//nothong to convert
		return true;
	}
}

/**
 * Class for Readlig list
 */
class ReadingListTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'reading_id';
			//Convert all neccessary entries
			$value_array['comment'] = mb_convert_encoding($row['comment'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Tests
 */
class TestsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'test_id';
			//Convert all neccessary entries
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['instructions'] = mb_convert_encoding($row['instructions'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for Test questions
 */
class TestQuestionsTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'question_id';
			//Convert all neccessary entries
			$value_array['question'] = mb_convert_encoding($row['question'], $this->to_encoding, $this->from_encoding);
			$value_array['feedback'] = mb_convert_encoding($row['feedback'], $this->to_encoding, $this->from_encoding);
			$value_array['question'] = mb_convert_encoding($row['question'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_0'] = mb_convert_encoding($row['choice_0'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_1'] = mb_convert_encoding($row['choice_1'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_2'] = mb_convert_encoding($row['choice_2'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_3'] = mb_convert_encoding($row['choice_3'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_4'] = mb_convert_encoding($row['choice_4'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_5'] = mb_convert_encoding($row['choice_5'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_6'] = mb_convert_encoding($row['choice_6'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_7'] = mb_convert_encoding($row['choice_7'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_8'] = mb_convert_encoding($row['choice_8'], $this->to_encoding, $this->from_encoding);
			$value_array['choice_9'] = mb_convert_encoding($row['choice_9'], $this->to_encoding, $this->from_encoding);
			$value_array['option_0'] = mb_convert_encoding($row['option_0'], $this->to_encoding, $this->from_encoding);
			$value_array['option_1'] = mb_convert_encoding($row['option_1'], $this->to_encoding, $this->from_encoding);
			$value_array['option_2'] = mb_convert_encoding($row['option_2'], $this->to_encoding, $this->from_encoding);
			$value_array['option_3'] = mb_convert_encoding($row['option_3'], $this->to_encoding, $this->from_encoding);
			$value_array['option_4'] = mb_convert_encoding($row['option_4'], $this->to_encoding, $this->from_encoding);
			$value_array['option_5'] = mb_convert_encoding($row['option_5'], $this->to_encoding, $this->from_encoding);
			$value_array['option_6'] = mb_convert_encoding($row['option_6'], $this->to_encoding, $this->from_encoding);
			$value_array['option_7'] = mb_convert_encoding($row['option_7'], $this->to_encoding, $this->from_encoding);
			$value_array['option_8'] = mb_convert_encoding($row['option_8'], $this->to_encoding, $this->from_encoding);
			$value_array['option_9'] = mb_convert_encoding($row['option_9'], $this->to_encoding, $this->from_encoding);

			//Convert links table
			$tests_answers =& new TestsAnswersTable($this->table_prefix, 'tests_answers', $this->from_encoding, $row[$key_col]);
			$result &= $tests_answers->convert();

			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		//Needs to alter related tables
		if (!$rs) {			
			new TestsAnswersTable($this->table_prefix, 'tests_answers', '');
		}
		return $result;
	}
}


/**
 * Class for Test answers
 * Used only by TestQuestionTable
 * Foreign key = question_id, since question is one-to-many answers mapping.
 */
 class TestsAnswersTable extends ATutorTable{
	//Overrider
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE question_id='.$this->foreign_ID;
		$result = mysql_query($sql);
		if ($result && mysql_num_rows($result)>0){
			return $result;
		}
		return false;
	}

	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col1 = 'question_id';
			$key_col2 = 'result_id';
			$key_col3 = 'member_id';
			//Convert all neccessary entries
			$value_array['answer'] = mb_convert_encoding($row['answer'], $this->to_encoding, $this->from_encoding);
			$value_array['notes'] = mb_convert_encoding($row['notes'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, array($key_col1, $key_col2, $key_col3), 
				array($row[$key_col1], $row[$key_col2], $row[$key_col3])));
		}
		return $result;
	}
 }

/**
 * Class for tests_groups
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class TestsGroupsTable extends ATutorTable{
	function convert(){
		//nothong to convert
		return true;
	}
}

/**
 * Class for tests_questions_assoc
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class TestsQuestionsAssocTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'test_id';
			$key_col2 = 'question_id';
			//Convert all neccessary entries
			$value_array['weight'] = mb_convert_encoding($row['weight'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, array($key_col, $key_col2), array($row[$key_col], $row[$key_col2])));
		}
		return $result;
	}
}

/**
 * Class for Tests questions category
 */
class TestsQuestionsCategoriesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'category_id';
			//Convert all neccessary entries
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for tests_results
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class TestsResultsTable extends ATutorTable{
	function convert(){
		//nothong to convert
		return true;
	}
}


/**
 * Class for themes
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class ThemesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'title';
			//Convert all neccessary entries
			$value_array['extra_info'] = mb_convert_encoding($row['extra_info'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}


/**
 * Class for users_online
 * Default language iso-8859-1.
 * Note: This class is independent from courses
 */
class UsersOnlineTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent(false);
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'member_id';
			//Convert all neccessary entries
			$value_array['login'] = mb_convert_encoding($row['login'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
	}
}
?>
