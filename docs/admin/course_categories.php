<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
$section = 'users';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (!$_SESSION['s_is_super_admin']) { exit; }

if($_POST['cancel']){
	Header('Location: '.$_SERVER['PHP_SELF'].'?current_cat='.$_POST['category_parent'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}


require(AT_INCLUDE_PATH.'admin_html/header.inc.php');
echo '<h2>'._AT('cats_course_categories').'</h2><br />';
?>
<table cellspacing="0" cellpadding="0" border="0" class="bodyline" summary="" align="center" width="100%">
	<tr><th style="border-right:1pt solid gray;"><?php echo _AT('cats_category_name').' '.$current_cats[$_GET['current_cat']]; ?></th>
		<th><?php echo _AT('cats_edit_categories').' '.$current_cats[$_GET['current_cat']]; ?></th>
	</tr>
	<tr><td style="border-right:1pt solid gray;" width="40%">

	<!---replace with function -->
	<ul><li><a href="/browse.php?current_cat=0;show_courses=0"><strong>Uncategorized</strong></a> <small>( <a href="/browse.php?current_cat=0;this_category=0;show_courses=0#browse_top">11</a> )</small></li>
	<li><a href="/browse.php?current_cat=110;show_courses=110">Anthropology</a> <small>( 0 )</small><ul><li><a href="/browse.php?current_cat=127;show_courses=127">Science</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=126;show_courses=126">Social Science</a> <small>( 0 )</small></li>
	</ul></li>
	<li><a href="/browse.php?current_cat=111;show_courses=111">Botany</a> <small>( 0 )</small><ul><li><a href="/browse.php?current_cat=128;show_courses=128">Molecular Plant Biology</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=129;show_courses=129">Plant Physiology</a> <small>( 0 )</small></li>
	</ul></li>
	<li><a href="/browse.php?current_cat=112;show_courses=112">Chemistry</a> <small>( 0 )</small><ul><li><a href="/browse.php?current_cat=130;show_courses=130">Biological Chemistry</a> <small>( <a href="/browse.php?current_cat=130;this_category=130;show_courses=130#browse_top">1</a> )</small></li>
	<li><a href="/browse.php?current_cat=131;show_courses=131">Chemical Physics</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=132;show_courses=132">Environmental Chemistry</a> <small>( 0 )</small></li>
	</ul></li>
	<li><a href="/browse.php?current_cat=121;show_courses=121">Computer Science</a> <small>( <a href="/browse.php?current_cat=121;this_category=121;show_courses=121#browse_top">1</a> )</small><ul><li><a href="/browse.php?current_cat=133;show_courses=133">Information Systems</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=134;show_courses=134">Software Engineering</a> <small>( 0 )</small></li>
	</ul></li>
	<li><a href="/browse.php?current_cat=114;show_courses=114">Economics</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=115;show_courses=115">Geology</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=116;show_courses=116">History</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=117;show_courses=117">Linguistics</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=118;show_courses=118">Mathematics</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=119;show_courses=119">Philosophy</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=120;show_courses=120">Physics</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=122;show_courses=122">Psychology</a> <small>( <a href="/browse.php?current_cat=122;this_category=122;show_courses=122#browse_top">2</a> )</small></li>
	<li><a href="/browse.php?current_cat=123;show_courses=123">Sociology</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=136;show_courses=136">www</a> <small>( 0 )</small></li>
	<li><a href="/browse.php?current_cat=125;show_courses=125">Zoology</a> <small>( 0 )</small></li>
	</ul>
	<!---end replace function -->
		</td>
		<td colspan="2"><?php include(AT_INCLUDE_PATH.'html/cat_editor.inc.php'); ?></td>
	</tr>

</table>

<?php
require(AT_INCLUDE_PATH.'admin_html/footer.inc.php');
?>