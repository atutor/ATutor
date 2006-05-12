<?php

function forums_delete($course) {
	global $db;

	$sql = "SELECT * FROM ".TABLE_PREFIX."forums_courses WHERE course_id=$course";
	$f_result = mysql_query($sql, $db);
	while ($forum = mysql_fetch_assoc($f_result)) {
		$forum_id = $forum['forum_id'];
		$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_assoc($result);
		if ($row['cnt'] == 1) {
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

		} else if ($row['cnt'] > 1) {
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


	// delete the groups
	require_once(AT_INCLUDE_PATH . 'lib/forums.inc.php');


	$sql = "SELECT G.group_id FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types T USING (type_id) WHERE T.course_id=$course";
	$group_result = mysql_query($sql, $db);
	while ($group_row = mysql_fetch_assoc($group_result)) {
		$group_id = $group_row['group_id'];

		$sql = "SELECT forum_id FROM ".TABLE_PREFIX."forums_groups WHERE group_id=$group_id";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			delete_forum($row['forum_id']);
		}

		$sql = "DELETE FROM ".TABLE_PREFIX."forums_groups WHERE group_id=$group_id";
		$result = mysql_query($sql, $db);
	}

	$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."forums_threads";
	$result = mysql_query($sql, $db);
}

?>