<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Harris Wong						*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

/* Constances, refer to /include/lib/constants.inc.php */
define('LINK_CAT_COURSE',	1);
define('LINK_CAT_GROUP',	2);
define('LINK_CAT_SELF',		3);


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
	/** Global variables */
	var $table;
	var $table_prefix;
	var $from_encoding;
	var $courseID;
	var $to_encoding;

	/**
	 * Constructor
	 * @param	table prefix
	 * @param	table is the table name of which we want to covert
	 * @param	from_encoding to convert the content from this encoding.
	 * @param	foreign_ID is the primary key/foreign key of the table.  The input will be primary key when
	 *			the table has a "course_id" column, foreign key when it doesn't.
	 */
	function ATutorTable($table_prefix, $table, $from_encoding, $foreign_ID){
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
			$sql .= "$column_name='$column_value'";
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
			$sql .= " WHERE $primary_key_col=$primary_key";
		}
//		echo "<hr/>";
		return $sql;
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
			//Generate SQL
			//echo (mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]))) ;
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
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
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['body'] = mb_convert_encoding($row['body'], $this->to_encoding, $this->from_encoding);
			//Convert sub post comment.
			$commentPosts =& new BlogPostsCommentsTable($this->table_prefix.'blog_posts_comments', $this->from_encoding, $row[$key_col]);
			$result &= $commentPosts->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
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
			$value_array['text'] = mb_convert_encoding($row['text'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
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
			$value_array['preferences'] = mb_convert_encoding($row['preferences'], $this->to_encoding, $this->from_encoding);
			$value_array['copyright'] = mb_convert_encoding($row['copyright'], $this->to_encoding, $this->from_encoding);
			$value_array['banner'] = mb_convert_encoding($row['banner'], $this->to_encoding, $this->from_encoding);
			/* The following should not needed to be converted after they are deprecated */
			$value_array['header'] = mb_convert_encoding($row['header'], $this->to_encoding, $this->from_encoding);
			$value_array['footer'] = mb_convert_encoding($row['footer'], $this->to_encoding, $this->from_encoding);			
			$value_array['banner_text'] = mb_convert_encoding($row['banner_text'], $this->to_encoding, $this->from_encoding);
			$value_array['banner_styles'] = mb_convert_encoding($row['banner_styles'], $this->to_encoding, $this->from_encoding);			

			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
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
			echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
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
		return $result;
	}
}

/**
 * Class for Faq Entries 
 * Used only by FaqTopicsTable
 * Foreign key = topic_id
 */
class FaqEntriesTable extends ATutorTable{
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'entry_id';
			//Convert all neccessary entries
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
			$value_array['title'] = mb_convert_encoding($row['title'], $this->to_encoding, $this->from_encoding);
			$value_array['description'] = mb_convert_encoding($row['description'], $this->to_encoding, $this->from_encoding);
			//Convert faq entries
			$forumThread=& new ForumsThreadsTable($this->table_prefix, 'forums_threads', $this->from_encoding, $row[$key_col]);
			$result &= $forumThread->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result = mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
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
	 */
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE owner_type='.LINK_CAT_COURSE.' AND owner_id='.$this->foreign_ID;
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
	 */
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE owner_type='.LINK_CAT_COURSE.' AND owner_id='.$this->foreign_ID;
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
			$key_col = 'file_id';
			//Convert all neccessary entries
			$value_array['file_name'] = mb_convert_encoding($row['file_name'], $this->to_encoding, $this->from_encoding);
			$value_array['description'] = mb_convert_encoding($row['description'], $this->to_encoding, $this->from_encoding);
			//Convert faq entries
			$forumThread=& new FilesCommentsTable($this->table_prefix, 'files_comments', $this->from_encoding, $row[$key_col]);
			$result &= $forumThread->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
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
	function convert(){
		$rs = $this->getContent();
		$result = true;
		while ($rs!=false && $row = mysql_fetch_assoc($rs)){
			//Store the key for updating purposes.
			$key_col = 'commend_id';
			//Convert all neccessary entries
			$value_array['comment'] = mb_convert_encoding($row['comment'], $this->to_encoding, $this->from_encoding);
			//Generate SQL
			echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
		}
		return $result;
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
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
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
 * Class for Links Categories
 * Associated with Folders, Groups
 */
class LinksCategoriesTable extends ATutorTable{
	/*
	 * Overrider
	 * owner_id means category_id, owner_type refers to the different link type defined in the constants.inc.php.
	 */
	function getContent(){
		$sql = 'SELECT * FROM `'.$this->table_prefix.$this->table.'` WHERE owner_type='.LINK_CAT_COURSE.' AND owner_id='.$this->foreign_ID;
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
			$key_col = 'cat_id';
			//Convert all neccessary entries
			$value_array['name'] = mb_convert_encoding($row['name'], $this->to_encoding, $this->from_encoding);
			//Convert links table
			$groups =& new LinksTable($this->table_prefix, 'links', $this->from_encoding, $row[$key_col]);
			$result &= $groups->convert();
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
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
			//Generate SQL
			echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
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
			//Generate SQL
			//echo $this->generate_sql($value_array, $key_col, $row[$key_col]);
			$result &= mysql_query($this->generate_sql($value_array, $key_col, $row[$key_col]));
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

/** Testing */
/*
$db = @mysql_connect('localhost' . ':' . '3306', 'atutor', 'atutor');
mysql_select_db('atutor_155', $db);
$_POST['tb_prefix'] = 'at_';
$char_set  = 'BIG-5';
$course_id = '5';
$cat_id = '2';
			$temp_table =& new CourseCategoriesTable($_POST['tb_prefix'], 'course_cats', 'ISO-8859-1', $cat_id);
			$temp_table->convert();
			echo "<hr/>";			
			$temp_table =& new LinksCategoriesTable($_POST['tb_prefix'], 'links_categories', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";			
			$temp_table =& new FoldersTable($_POST['tb_prefix'], 'folders', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";			
			$temp_table =& new FilesTable($_POST['tb_prefix'], 'files', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";			
			$temp_table =& new AssignmentsTable($_POST['tb_prefix'], 'assignments', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new BackupsTable($_POST['tb_prefix'], 'backups', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new BlogPostsTable($_POST['tb_prefix'], 'blog_posts', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new ContentTable($_POST['tb_prefix'], 'content', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new CoursesTable($_POST['tb_prefix'], 'courses', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";			
			$temp_table =& new CourseEnrollmentTable($_POST['tb_prefix'], 'course_enrollment', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";			
			$temp_table =& new ExternalResourcesTable($_POST['tb_prefix'], 'external_resources', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new FaqTopicsTable($_POST['tb_prefix'], 'faq_topics', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>"; 
			$temp_table =& new ForumsTable($_POST['tb_prefix'], 'forums', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";			
			$temp_table =& new GlossaryTable($_POST['tb_prefix'], 'glossary', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new GroupsTypesTable($_POST['tb_prefix'], 'groups_types', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new MessagesSentTable($_POST['tb_prefix'], 'messages_sent', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new NewsTable($_POST['tb_prefix'], 'news', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new PollsTable($_POST['tb_prefix'], 'polls', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new ReadingListTable($_POST['tb_prefix'], 'reading_list', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new TestsTable($_POST['tb_prefix'], 'tests', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
			$temp_table =& new TestQuestionsTable($_POST['tb_prefix'], 'tests_questions', $char_set, $course_id);
			$temp_table->convert();
			
			echo "<hr/>";
			$temp_table =& new TestsQuestionsCategoriesTable($_POST['tb_prefix'], 'tests_questions_categories', $char_set, $course_id);
			$temp_table->convert();
			echo "<hr/>";
*/

/**
 * This function is used for printing variables for debugging.
 * @access  public
 * @param   mixed $var	The variable to output
 * @param   string $title	The name of the variable, or some mark-up identifier.
 * @author  Joel Kronenberg
 */
/*
function debug($var, $title='') {
	echo '<pre style="border: 1px black solid; padding: 0px; margin: 10px;" title="debugging box">';
	if ($title) {
		echo '<h4>'.$title.'</h4>';
	}
	
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();

	$str = str_replace('<', '&lt;', $str);

	$str = str_replace('[', '<span style="color: red; font-weight: bold;">[', $str);
	$str = str_replace(']', ']</span>', $str);
	$str = str_replace('=>', '<span style="color: blue; font-weight: bold;">=></span>', $str);
	$str = str_replace('Array', '<span style="color: purple; font-weight: bold;">Array</span>', $str);
	echo $str;
	echo '</pre>';
}
*/
?>
