<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay,Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

/**
 * Deletes an entire course or only its content.
 *
 * @param $course the course ID to delete.
 * @param $entire_course whether or not to delete the entire course.
 * @param #rel_path the relative path to the content directory.
 * @returm true
 */

function delete_course($course, $material, $rel_path) {
	global $db;

	// -- delete announcements/news
	if (($material === TRUE) || isset($material['news'])) {
		// news:
		$sql	= "DELETE FROM ".TABLE_PREFIX."news WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}
	
	// -- delete groups
	if (($material === TRUE) || isset($material['groups'])) {
		$sql	= "SELECT group_id FROM ".TABLE_PREFIX."groups WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."groups_members WHERE group_id=$row[group_id]";
			$result2 = mysql_query($sql, $db);

			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE group_id=$row[group_id]";
			$result2 = mysql_query($sql, $db);
		}
		$sql	= "DELETE FROM ".TABLE_PREFIX."groups WHERE course_id=$course";
		$result = mysql_query($sql, $db);

		// -- remove assoc between tests and groups:
	}


	// -- delete content
	if (($material === TRUE) || isset($material['content'])) {
		// related_content + content:
		$sql	= "SELECT * FROM ".TABLE_PREFIX."content WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$row[0]";
			$result2 = mysql_query($sql, $db);
		}

		$sql = "DELETE FROM ".TABLE_PREFIX."content WHERE course_id=$course";
		$result = mysql_query($sql,$db);

		$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."content";
		$result = @mysql_query($sql, $db);
	}

	// -- delete links
	if (($material === TRUE) || isset($material['links'])) {
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
	}

	// -- delete glossary terms
	if (($material === TRUE) || isset($material['glossary'])) {
		$sql	= "DELETE FROM ".TABLE_PREFIX."glossary WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}

	// -- delete polls
	if (($material === TRUE) || isset($material['polls'])) {
		$sql	= "SELECT poll_id FROM ".TABLE_PREFIX."polls WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$sql	 = "DELETE FROM ".TABLE_PREFIX."polls_members WHERE poll_id=$row[poll_id]";
			$result2 = mysql_query($sql, $db);
		}
		$sql	= "DELETE FROM ".TABLE_PREFIX."polls WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}
	

	// -- delete forums
	if (($material === TRUE) || isset($material['forums'])) {
		$sql = "SELECT * FROM ".TABLE_PREFIX."forums_courses WHERE course_id=$course";
		$f_result = mysql_query($sql, $db);
		while ($forum = mysql_fetch_assoc($f_result)) {
			$forum_id = $forum['forum_id'];
			$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
			$result = mysql_query($sql, $db);
			$row = mysql_fetch_assoc($result);
			if ($row['cnt'] == 1) {
				//debug('deleting non-shared forums');

				$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
				$result = mysql_query($sql, $db);
				while ($row = mysql_fetch_assoc($result)) {
					$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row[post_id]";
					$result2 = mysql_query($sql, $db);
				}

				$sql	= "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE forum_id=$forum_id";
				$result = mysql_query($sql, $db);

				$sql    = "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
				$result = mysql_query($sql, $db);

				$sql    = "DELETE FROM ".TABLE_PREFIX."forums WHERE forum_id=$forum_id";
				$result = mysql_query($sql, $db);
				
				$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
				$result = mysql_query($sql, $db);

			} else if (($row['cnt'] > 1) && (!isset($material['forums']))) {
				// shared forums only get unlinked if we're deleting an entire course!
				// shared forums do NOT get deleted when restoring a backup.
				//
				// this is a shared forum:
				// debug('unsubscribe all the students who will not be able to access this forum anymore.');
				$sql     = "SELECT course_id FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum[forum_id] AND course_id <> $course";
				$result2 = mysql_query($sql, $db);
				while ($row2 = mysql_fetch_assoc($result2)) {
					$courses[] = $row2['course_id'];
				}
				$courses_list = implode(',', $courses);

				// list of all the students who are in other courses as well
				$sql     = "SELECT member_id FROM ".TABLE_PREFIX."course_enrollment WHERE course_id IN ($courses_list)";
				$result2 = mysql_query($sql, $db);
				while ($row2 = mysql_fetch_assoc($result2)) {
					$students[] = $row2['member_id'];
				}

				$students_list = implode(',', $students);
				
				if ($students_list) {
					// remove the subscriptions
					$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum[forum_id]";
					$result2 = mysql_query($sql, $db);
					while ($row2 = mysql_fetch_array($result2)) {
						$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row2[post_id] AND member_id NOT IN ($students_list)";
						$result3 = mysql_query($sql, $db);
					}

					$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE forum_id=$forum[forum_id] AND member_id NOT IN ($students_list)";
					$result3 = mysql_query($sql, $db);
				}

				$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum[forum_id] AND course_id=$course";
				$result = mysql_query($sql, $db);
			}
		}

		$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."forums_threads";
		$result = mysql_query($sql, $db);
	}

	// -- delete tests + tests_answers + tests_questions + tests_questions_assoc + tests_questions_categories + tests_results + tests_groups
	if (($material === TRUE) || isset($material['tests'])) {
		$sql	= "SELECT test_id FROM ".TABLE_PREFIX."tests WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$row[test_id]";
			$result2 = mysql_query($sql, $db);
		
			$sql2	= "SELECT result_id FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[test_id]";
			$result2 = mysql_query($sql2, $db);
			while ($row2 = mysql_fetch_assoc($result2)) {
				$sql3	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row2[result_id]";
				$result3 = mysql_query($sql3, $db);
			}
			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[test_id]";
			$result2 = mysql_query($sql, $db);

			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE test_id=$row[test_id]";
			$result2 = mysql_query($sql, $db);
		}

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$course";
		$result = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$course";
		$result = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}

	// -- delete stats
	if (($material === TRUE) || isset($material['stats'])) {
		$sql = "DELETE FROM ".TABLE_PREFIX."course_stats WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}

	// -- delete files
	if (($material === TRUE) || isset($material['stats'])) {
		$path = AT_CONTENT_DIR . $course . '/';
		clr_dir($path);
	}

	$sql = "DELETE FROM ".TABLE_PREFIX."g_click_data WHERE course_id=$course";
	$result = mysql_query($sql, $db);

	if ($material === TRUE) {
		$path = AT_BACKUP_DIR . $course . '/';
		clr_dir($path);

		$path = AT_CONTENT_DIR . 'chat/' . $course . '/';
		clr_dir($path);

		// backups:
		$sql	= "DELETE FROM ".TABLE_PREFIX."backups WHERE course_id=$course";
		$result = mysql_query($sql, $db);

		// course_enrollment:
		$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course";
		$result = mysql_query($sql, $db);

		// courses:
		$sql = "DELETE FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
		$result = mysql_query($sql, $db);
	}

	return true;
}

?>