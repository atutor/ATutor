<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

$page = 'sitemap';
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/forums.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<p><a href="index.php">'._AT('home').'</a>';

$_current_modules = array_slice($_pages[AT_NAV_COURSE], 1);
$_current_modules = array_merge($_current_modules, array_diff($_pages[AT_NAV_HOME],$_pages[AT_NAV_COURSE]));

foreach ($_current_modules as $module) {
	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="img-size-tree" />  <a href="'.$module.'">' . _AT($_pages[$module]['title_var']) . '</a>';

	if ($module == 'forum/list.php') {
		$forums = get_forums($_SESSION['course_id']);
		if (is_array($forums)) {
			foreach ($forums as $state=>$rows) {
				$count = 0;
				$num_forums = count($rows);
				foreach ($rows as $row) {
					$count++;
					echo '<br />';
					echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="img-size-tree" />';
					if ($count < $num_forums) {
						echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="img-size-tree" />';
					} else {
						echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="img-size-tree" />';
					}
					echo '<img src="images/'.$rtl.'tree/tree_horizontal.gif" alt="" class="img-size-tree" />';
					echo ' <a href="forum/index.php?fid='.$row['forum_id'].'">'.AT_print($row['title'], 'forums.title').'</a>';
				}
			} 
		} else {
			echo '<br />';
			echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="img-size-tree" />';
			echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="img-size-tree" />';
			echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="img-size-tree" />';
			echo _AT('no_forums');
		}
	}
}

echo '<br /><img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="img-size-tree" /> <a href="search.php">'._AT('search').'</a><br />';
echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="img-size-tree" /> <a href="help/">'._AT('help').'</a><br />';
echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="img-size-tree" /> '._AT('content').'<br />';

$contentManager->printSiteMapMenu();

echo '</p>';

require(AT_INCLUDE_PATH.'footer.inc.php');
?>