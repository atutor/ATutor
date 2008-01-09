<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');

require_once(AT_INCLUDE_PATH.'lib/forums.inc.php');
require (AT_INCLUDE_PATH.'header.inc.php');
?>
<table class="data static" summary="" rules="rows">
<thead>
<tr>
	<th scope="col"><?php echo _AT('forum');        ?></th>
	<th scope="col"><?php echo _AT('num_threads'); ?></th>
	<th scope="col"><?php echo _AT('num_posts');    ?></th>
	<th scope="col"><?php echo _AT('last_post');    ?></th>
</tr>
</thead>
<?php
$shared  = array();
$general = array();
$all_forums = get_forums($_SESSION['course_id']);
//output course forums
$num_shared    = count($all_forums['shared']);
$num_nonshared = count($all_forums['nonshared']);
$num_groups = count($all_forums['group']);

if ($num_shared || $num_nonshared || $num_groups) {
	foreach ($all_forums as $shared => $forums) {
		if (($num_shared && $num_nonshared) || ($num_nonshared && $num_groups)) {
			if ($num_nonshared && ($shared == 'nonshared')) {
				echo '<tbody><tr>';
				echo '<th colspan="4">' . _AT('course_forums') . '</th>';
				echo '</tr>';
			} else if ($num_shared && ($shared == 'shared')) {
				echo '</tbody><tbody><tr>';
				echo '<th colspan="4">' . _AT('shared_forums') . '</th>';
				echo '</tr>';
			} else if ($num_groups && ($shared == 'group')) {
				echo '</tbody><tbody><tr>';
				echo '<th colspan="4">' . _AT('group_forums') . '</th>';
				echo '</tr>';
			}
		}

		foreach ($forums as $row) : ?>
			<tr>
				<td><a href="forum/index.php?fid=<?php echo $row['forum_id']; ?>"><?php echo $row['title']; ?></a> <?php

					if ($_SESSION['enroll']) {
						$sql	= "SELECT 1 AS constant FROM ".TABLE_PREFIX."forums_subscriptions WHERE forum_id=$row[forum_id] AND member_id=$_SESSION[member_id]";
						$result1 = mysql_query($sql, $db);
						echo ' [ ';
						if ($row1 = mysql_fetch_row($result1)) {
							echo '<a href="forum/subscribe_forum.php?fid='.$row['forum_id'].SEP.'us=1">'._AT('unsubscribe1').'</a>';
						} else {
							echo '<a href="forum/subscribe_forum.php?fid='.$row['forum_id'].'">'._AT('subscribe1').'</a>';
						}
						echo ' ]';
					} ?>
					<p><?php echo $row['description']; ?></p>
				</td>
				<td align="center" valign="top"><?php echo $row['num_topics']; ?></td>
				<td align="center" valign="top"><?php echo $row['num_posts']; ?></td>
				<td align="right" valign="top"><?php

					if ($row['last_post'] == '0000-00-00 00:00:00') {
						echo '<em>'._AT('na').'</em>';
					} else {
						echo $row['last_post'];
					} ?>
				</td>
			</tr><?php
		endforeach;
	}
	echo '</tbody>';
} else {
	echo '<tr><td class="row1" colspan="4"><em>'._AT('no_forums').'</em></td></tr>';
}
echo '</table>';

require (AT_INCLUDE_PATH.'footer.inc.php');
?>