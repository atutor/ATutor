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
if (!defined('AT_INCLUDE_PATH')) { exit; }

if (!$course) { exit; }

		// course_enrollment:
		$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('enrolled').': '.mysql_affected_rows($db)."\n";

		// news:
		$sql	= "DELETE FROM ".TABLE_PREFIX."news WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('announcements').': '.mysql_affected_rows($db)."\n";
		//echo $sql;

		// related_content + content:
		$sql	= "SELECT * FROM ".TABLE_PREFIX."content WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."content_learning_concepts WHERE content_id=$row[0]";
			$result2 = mysql_query($sql, $db);
	
			$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$row[0]";
			$result2 = mysql_query($sql, $db);
		}

		$sql = "DELETE FROM ".TABLE_PREFIX."content WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('content').':                            '.mysql_affected_rows($db)."\n";

		$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."content";
		$result = mysql_query($sql, $db);

		/************************************/
		// course stats:
		$sql = "DELETE FROM ".TABLE_PREFIX."course_stats WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('course_stats').':                  '.mysql_affected_rows($db)."\n";

		/************************************/
		// links:
		$sql	= "SELECT * FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		$total_links = 0;
		while ($row = mysql_fetch_array($result)) {
			$sql = "DELETE FROM ".TABLE_PREFIX."resource_links WHERE CatID=$row[0]";
			$result2 = mysql_query($sql, $db);
			$total_links += mysql_affected_rows($db);
		}
		$sql	= "DELETE FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('resource_categories').':                '.mysql_affected_rows($db)."\n";
		echo _AT('resource_links').':                     '.$total_links."\n";

		/************************************/
		// glossary:
		$sql	= "DELETE FROM ".TABLE_PREFIX."glossary WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('glossary_terms').':                     '.mysql_affected_rows($db)."\n";

		/************************************/
		/* forum */
		$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row[post_id]";
			$result2 = mysql_query($sql, $db);

			$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE post_id=$row[post_id]";
			$result2 = mysql_query($sql, $db);
		}

		/************************************/
		$sql = "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('forum_threads').':                      '.mysql_affected_rows($db)."\n";

		$sql = "DELETE FROM ".TABLE_PREFIX."forums WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('forums').':                             '.mysql_affected_rows($db)."\n";

		$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."forums_threads";
		$result = mysql_query($sql, $db);

		$sql = "DELETE FROM ".TABLE_PREFIX."preferences WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('preferences').':                        '.mysql_affected_rows($db)."\n";

		$sql = "DELETE FROM ".TABLE_PREFIX."g_click_data WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		// no feedback for this item.


		// tests + tests_questions + tests_answers + tests_results:
		$sql	= "SELECT test_id FROM ".TABLE_PREFIX."tests WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE test_id=$row[0]";
			$result2 = mysql_query($sql, $db);
	
			$sql2	= "SELECT result_id FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[0]";
			$result2 = mysql_query($sql2, $db);
			while ($row2 = mysql_fetch_array($result2)) {
				$sql3	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row2[0]";
				$result3 = mysql_query($sql3, $db);
			}

			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[0]";
			$result2 = mysql_query($sql, $db);
		}

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests WHERE course_id=$course";
		$result = mysql_query($sql, $db);

		echo _AT('tests').':                              '.mysql_affected_rows($db)."\n";

		// files:
		$path = '../content/'.$course.'/';
		clr_dir($path);

		// courses:
		$sql = "DELETE FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo '<b>'._AT('course').': '.mysql_affected_rows($db).' '._AT('always_one').'</b>'."\n";


?>