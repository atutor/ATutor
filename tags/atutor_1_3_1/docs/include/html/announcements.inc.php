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

	echo '<small class="spacer">'.AT_date(_AT('announcement_date_format')).'</small>';

	echo '<h1><img src="images/icons/default/square-large-home.gif" class="menuimageh1" border="0" alt="" />'.$_SESSION[course_title];
	if (!$_SESSION['is_admin'] && !$_SESSION['enroll']) {
		echo '<small> - ';
		echo '<a href="enroll.php?course='.$_SESSION[course_id].'">'._AT('enroll').'</a></small>';
	}
	echo '</h1>';
	//help for content pages
	if (($_SESSION['is_admin']) && ($_SESSION['prefs'][PREF_EDIT] == 1)) {
		if ($_SESSION['prefs'][PREF_MENU]==1){
			$help[] = AT_HELP_ADD_ANNOUNCEMENT2;
		} else {
			$help[] = AT_HELP_ADD_ANNOUNCEMENT;
		}
		$help[] = AT_HELP_ADD_TOP_PAGE;


	}
	if ($_SESSION['prefs'][PREF_EDIT] == 1) {
		print_help($help);
	}
	if (($_SESSION['is_admin'] == 1) && ($_SESSION['prefs']['PREF_EDIT'] == 1)) {
		print_editorlg( _AT('add_announcement'), 'editor/add_news.php' , _AT('add_top_page') ,'editor/add_new_content.php');
	}

	/* cache $news here. */
	$sql = "SELECT N.* FROM ".TABLE_PREFIX."news N WHERE N.course_id=$_SESSION[course_id] ORDER BY date DESC";
	$result = mysql_query($sql, $db);
	if (mysql_num_rows($result) == 0) {
		echo '<i>'._AT('no_announcements').'</i>';
	} else {
		$news = array();
		while ($row = mysql_fetch_array($result)) {
			/* this can't be cached because it called _AT */
			$news[] = array('news_id'	=> $row['news_id'], 
							'course_id' => $row['course_id'],
							'date'		=> AT_date(	_AT('announcement_date_format'), 
													$row['date'], 
													AT_DATE_MYSQL_DATETIME),
 							'title'		=> $row['title'],
							'body'		=> $row['body'],
							'formatting'=> $row['formatting']);
		}

		echo '<table border="0" cellspacing="1" cellpadding="0" width="98%" summary="">';
		
		require(AT_INCLUDE_PATH.'lib/format_content.inc.php');

		foreach ($news as $x => $news_item) {
			echo '<tr>';
			echo '<td>';
			echo '<br /><h4>'.$news_item['title'];
			print_editor( _AT('edit'), 'editor/edit_news.php?aid='.$news_item['news_id'],
							_AT('delete'), 'editor/delete_news.php?aid='.$news_item['news_id']);
			echo '</h4>';

			


			/*
			if (($_SESSION['is_admin']) && ($_SESSION['prefs'][PREF_EDIT] == 1)) {
				echo '<img src="images/pen2.gif" border="0" class="menuimage12" alt="'._AT('editor_on').'" title="'._AT('editor_on').'" height="14" width="16" />';
				echo '<small class="bigspacer">(<a href="../../editor/edit_news.php?aid='.$news_item['news_id'].'">'._AT('edit').'</a>';
				echo ' | ';
				echo '<a href="../../editor/delete_news.php?aid='.$news_item['news_id'].'">'._AT('delete').'</a>)</small><br />';
			}
			*/
 
			$news_item['body'] = str_replace('CONTENT_DIR/', '', $news_item['body']);

			echo format_content($news_item['body'], $news_item['formatting']);

			echo '<br /><small class="date">'._AT('posted').' '.$news_item['date'].'</small>';
			echo '</td>';
			echo '</tr>';
			echo '<tr><td class="row3" height="1"><img src="../../images/clr.gif" height="1" width="1" alt="" /></td></tr>';
		}
		echo '</table>';
	}

?>
