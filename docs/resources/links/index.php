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

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_section[0][0] = _AT('resources');
$_section[0][1] = 'resources/';
$_section[1][0] = _AT('links_db');
$_section[1][1] = 'resources/links/';

require('mysql.php');			// Access to all the database functions
require('myheadfoot.php');		 // The header and footer files

// Need to set these constant variables:
$ADMIN_MODE = false;			// Set true for admin version
$SEE_ALL_SUBMISSIONS = true;		// Set false to show submissions in this category only
$SITE_URL = 'resources/links/index.php';
$FULL_ADMIN_ACCESS = true;		// True to allow admin to create categories
$TOP_CAT_NAME = _AT('newest_links');			// Name of the top "category"



if (authenticate(AT_PRIV_LINKS, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
	$ADMIN_MODE = true;
}

// Open the database
$db2 = new MySQL;
if(!$db2->init()) {
	$errors[]=AT_ERROR_NO_DB_CONNECT;
	print_errors($errors);
	exit;
}

if ($_GET['add']) {
	$_section[2][0] = _AT('add_new_link');
} else if ($_GET['edit_link']) {
	$_section[2][0] = _AT('edit_link');
}


if ((!$_GET['view']) && (!$adminpass)) {
	breadcrumbs($_GET['viewCat']); // has to be here because of the include.
	require(AT_INCLUDE_PATH.'header.inc.php');
	
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<h2><img src="images/icons/default/square-large-resources.gif" class="menuimage" vspace="2" width="42" height="38" border="0" alt="" /> <a href="resources/index.php?g=11">'._AT('resources').'</a></h2>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1 && $_SESSION['prefs'][PREF_CONTENT_ICONS] == 2) {
		echo '<h2><a href="resources/index.php?g=11">'._AT('resources').'</a></h2>';
	}

	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<h3><img src="images/icons/default/links-large.gif" width="42" height="38"  class="menuimageh3" border="0" alt="" /> <a href="resources/links/index.php?g=11">'._AT('links_db').'</a></h3>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1 && $_SESSION['prefs'][PREF_CONTENT_ICONS] == 2) {
		echo '<h3><a href="resources/links/index.php?g=11"><a href="resources/links/index.php?g=11">'._AT('links_db').'</a></h3>';
	}
}

function show_submissions_list($CatID)
{
	global $SEE_ALL_SUBMISSIONS;
	global $TOP_CAT_NAME;
	global $db2;

	if ($SEE_ALL_SUBMISSIONS) {
		$sub = $db2->get_Submissions();
	} else {
		// Need to replace with function to show only for this CatID
		$sub = $db2->get_Submissions();
	}

	if(!empty($sub))
	{
		echo '<ul class="list">';
		while ( list ( $key,$val ) = each ($sub))
		{
			$Url		= stripslashes($val['Url']);
			$LinkName	= stripslashes($val['LinkName']);
			$Desc		= stripslashes($val['Description']);
			$Name		= stripslashes($val['SubmitName']);
			$Email		= stripslashes($val['SubmitEmail']);
			$SDate		= stripslashes($val['SubmitDate']);
			$LinkID		= stripslashes($val['LinkID']);
			$LinkCatID	= stripslashes($val['CatID']);

			if(!empty($LinkCatID)) {
				$LinkCatName = $db2->get_CatNames($LinkCatID);
			} else {
				$LinkCatName = $TOP_CAT_NAME;
			}

			print '<li>';
			print "<a href=\"$Url\" target=\"_blank\"><b>$LinkName</b></a> - $Desc<br />\n";
			print "<small class=\"spacer\">URL: $Url</small>\n";
			// Print submitter name and email
			if ($Name != '') {
				print " <small class=\"spacer\">("._AT('name').": <a href=\"mailto:$Email\">$Name</A> - $Email)</small><br />\n";
			}
			
			// Print category
			print " <small class=\"spacer\">"._AT('category').": $LinkCatName</small><br />\n";

			print "<small>[";
			// Link to approve a sumbission
			print "<a href=\"$_SERVER[PHP_SELF]?CatID=$CatID".SEP."approve=$LinkID\">"._AT('approve')."</a> ";

			// Link to delete a sumbission
			print "<a href=\"$_SERVER[PHP_SELF]?CatID=$CatID".SEP."delete_link=$LinkID\">"._AT('delete')."</a> ";

			// Link to edit a sumbission
			print "<a href=\"$_SERVER[PHP_SELF]?CatID=$CatID".SEP."edit_link=$LinkID\">"._AT('edit')."</a>";
			print "]</small>";
			print "</li>";
		}
		print "</ul>\n";
	}
	return;
}

function start_page($CatID="",$title="",$msg="")
{
	global $_my_uri;
	
	if(!empty($msg)) {
		print_feedback($msg);
	}

	print_warnings($warnings);

	if (authenticate(AT_PRIV_LINKS, AT_PRIV_RETURN) && !$_SESSION['prefs'][PREF_EDIT]) {
		$help[] = array(AT_HELP_ENABLE_EDITOR, $_my_uri);
	}

  	if(authenticate(AT_PRIV_LINKS, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
		$help[] = AT_HELP_CREATE_LINKS;
	}
	$help[] = AT_HELP_CREATE_LINKS1;

	print_help($help);
	echo '<p><strong><em>'._AT('links_windows').'</em></strong></p>';

	print '<center><form action="'.$_SERVER['PHP_SELF'].'" method="post">';
	print '<input type="text" name="KeyWords" size="20" class="formfield" /> <input type="submit" name="Search" value="'._AT('search_links').'" class="button" />';
	print '<input type="hidden" name="CatID" value="'.$CatID.'" />';
	print '</form></center>';	
	return;
}

function start_browse($CatID='')
{
	global $db2;
	global $ADMIN_MODE;
	global $TOP_CAT_NAME;
	global $SITE_URL;

	$data	= $db2->get_Cats($CatID);

	if ($CatID != 0){
		$links	= $db2->get_Links($CatID);
	} else {
		$links = $db2->get_Links('-1');	// get the new links
	}

	$OurCatID = $CatID;

	if(empty($CatID) || ($CatID == '0'))
	{
		$currentID = 'top';
		$currentName = $TOP_CAT_NAME;
	} else {
		$currentID = $CatID;
		$currentName = $db2->get_CatNames($CatID);
	}

	// Print list of sub categories
	if(!empty($data))
	{
		$data_cnt = count ($data);
		$data_left = $data_cnt >> 1;

		print '<center>';
		print '<table border="0" cellpadding="2" cellspacing="0" summary=""><tr><td width="50%" align="left" valign="top">';

		while ( list ( $key,$val ) = each ($data))
		{
			$CatID = stripslashes($val["CatID"]);
			$CatName = stripslashes($val["CatName"]);
			$LinksInCat = $db2->get_TotalLinksInCat_cnt($CatID);

			print "<a href=\"$SITE_URL?viewCat=$CatID\"><b><span class=\"catname\">".AT_print($CatName, 'resource_categories.CatName')."</span></b></a>";
			if ($ADMIN_MODE) {
				echo ' <small>( <a href="resources/links/edit_cat.php?CatID='.$CatID.'">'._AT('edit').'</a>, <a href="resources/links/delete_cat.php?CatID='.$CatID.'">'._AT('delete').'</a> )</small>';
			}
			print ' <em><small>('.$LinksInCat.')</small></em>';
			$db2->get_ChildrenInt($CatID);
			$children = $db2->TRAIL;
			if (!empty($children))
			{
				print '<br />';
				$counter = 0;
				while (( list ( $child_key,$child_val ) = each ($children)) && ($counter < 3))
				{
					$Child_CatID = stripslashes($child_val["CatID"]);
					$Child_CatName = stripslashes($child_val["CatName"]);
					if ($counter == 2) {
						print ", <a href=\"$SITE_URL?viewCat=$Child_CatID\"><span class=\"catname\">".AT_print($Child_CatName, 'resource_categories.CatName')."</span></a>...";
					} else if ($counter == 0) {
						print "<a href=\"$SITE_URL?viewCat=$Child_CatID\"><span class=\"catname\">".AT_print($Child_CatName, 'resource_categories.CatName')."</span></a>";
					} else {
						print ", <a href=\"$SITE_URL?viewCat=$Child_CatID\"><span class=\"catname\">".AT_print($Child_CatName, 'resource_categories.CatName')."</span></a>";
					}

					$counter ++;
				}
				echo '</span>';
			} 
			echo '<br />';
			$data_cnt--;
			if ($data_cnt == $data_left) {
				echo '</td><td width="50%" align="left" valign="top">';
			}
		}
		print '</td></tr></table>';
		print '</center>';
	}
	$CatID = $OurCatID;	// restore CatID

	print "<h3>$currentName:</h3>\n";
	// Print list of links
	print "<ul>\n";
	if(!empty($links))
	{
		while ( list ( $key,$val ) = each ($links))
		{
			$Url		= stripslashes($val["Url"]);
			$LinkName	= stripslashes($val["LinkName"]);
			$Desc		= stripslashes($val["Description"]);
			$LinkID		= stripslashes($val["LinkID"]);
			$Hits		= stripslashes($val["hits"]);
			$SDate		= stripslashes($val["SubmitDate"]);

			print "<li>";
			print "<a href=\"$SITE_URL?view=$LinkID\" target=\"_new\" class=\"catname\"><b>".AT_print($LinkName, 'resource_links.LinkName')."</b></a> - <small>$Desc</small>\n";
			print "<span class=\"spacer\"><small>["._AT('hits').": $Hits\n";
			print _AT('added').": $SDate]</small></span><br />\n";
			if ($ADMIN_MODE) {
				$Name		= stripslashes($val["SubmitName"]);
				$Email		= stripslashes($val["SubmitEmail"]);
				
				// Print submitter name and email
				if ($Name != '') {
					print " <small>("._AT('name').": <a 	href=\"mailto:$Email\">".AT_print($Name,'resource_links.SubmitName')."</A> - $Email)</small><br />\n";
				}

				// Link to disapprove a sumbission
				print "<small>[<a href=\"$_SERVER[PHP_SELF]?CatID=$CatID".SEP."disapprove=$LinkID\">"._AT('disapprove')."</a> ";

				// Link to edit a sumbission
				print "<a href=\"$_SERVER[PHP_SELF]?CatID=$CatID".SEP."edit_link=$LinkID\">"._AT('edit')."</a>]</small>";
			}
			print "</li>\n";
		}
	} else {
		echo '<li>'._AT('no_links').'</li>';
	}
	print "</ul>\n";

	if ($CatID != 0)
	{
		print "<p align=\"center\"><br />";
		print " <a href=\"$SITE_URL?add=$currentID\">"._AT('suggest_new_link')."</a> ";
		print "</p>\n";
	}
	if ($ADMIN_MODE) {
		print "\n<hr />\n";
		print '<img src="images/pen3.gif" height="28" width="32" alt="'._AT('editor').'"  title="'._AT('editor').'"  align="left" class="menuimage11" />';
		print "<h1>"._AT('submissions')."</h1>\n";

		show_submissions_list($CatID);
		$CatID = $OurCatID;	// restore CatID
			
		// Show form to add a subcategory
		print "\n<hr />\n";
		print "<center>
		<form action=\"$_SERVER[PHP_SELF]\" method=\"post\">
		<input type=\"hidden\" name=\"CatID\" value=\"$CatID\" />
		<input type=\"hidden\" name=\"add_new_cat\" value=\"true\" />
		<strong>"._AT('new_category').":</strong> <input name=\"NewCatName\" size=\"40\" class=\"formfield\" />
		<input type=\"submit\" class=\"button\" name=\"add_cat\" value=\" "._AT('create')." \" accesskey=\"s\"/>
		</form>
		</center>\n";
	}

	// Print the footer

	if (authenticate(AT_PRIV_LINKS, AT_PRIV_RETURN)) {
		echo '<br /><p><small class="spacer" title="'._AT('links_pending').'">(';
		echo $db2->get_approved_cnt();
		echo '/';
		echo $db2->get_not_approved_cnt();
		echo ')</small></p>';
	}

	return;
}

// Print drop-down box for available categories
function show_cat_selection($SelName = "CatID", $IncludeTop = true, $SecSel = "NULL")
{
	global $db2;
	global $ADMIN_MODE;
	global $TOP_CAT_NAME;

	print '<select name="'.$SelName.'" id="cat">';

	$secs = $db2->get_AllCats();

	if(!empty($secs))
	{
		while (list ($key, $val) = each ($secs))
		{
			// Run for all sections:
			$CatID		= $val["CatID"];
			$CatName	= $val["CatName"];

			if ($CatID == $SecSel) {$sel = "selected ";} else {$sel = "";}
			print "<option $sel value=\"$CatID\">$CatName</option>\n";
		}
	}
	print "</select>\n";

	return;
}

function show_edit_link($LinkID="",$title="",$msg="") 
{
	global $db2;
	global $TOP_CAT_NAME;
	global $FULL_ADMIN_ACCESS;

	print_header($CatID,$title,$msg);

	$thislink = $db2->get_OneLink($LinkID);
	if (empty($thislink)) {
		print "<p>"._AT('bad_link') ."</p>
		<HR noshade>
		</form></p>
		</html>\n";
		return;
	}

	while ( list ( $key,$val ) = each ($thislink))
	{
		$CatID		= stripslashes($val["CatID"]);
		$Url		= stripslashes($val["Url"]);
		$LinkName	= stripslashes($val["LinkName"]);
		$Desc		= stripslashes($val["Description"]);
		$Name		= stripslashes($val["SubmitName"]);
		$Email		= stripslashes($val["SubmitEmail"]);
		$SDate		= stripslashes($val["SubmitDate"]);
	}
 
	if(!empty($CatID))
	{
		$LinkCatName = $db2->get_CatNames($CatID);
	} else {
		$LinkCatName = "$TOP_CAT_NAME";
	}

	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="LinkID" value="<?php echo $LinkID; ?>" />
	<p>
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" align="center" summary="">
	<tr>
		<td class="cat" colspan="2"><img src="images/pen2.gif" border="0" class="menuimage12" alt="<?php echo _AT('editor_on'); ?>" title="<?php echo _AT('editor_on'); ?>" height="14" width="16" /><?php  echo _AT('edit_resource_in'); ?>: <b><?php echo $LinkCatName; ?></td>
	</tr>
	<tr>
		<td class="row1" align="right"><label for="url"><b><?php  echo _AT('url'); ?>:</b></label></td>
		<td class="row1"><input name="Url" class="formfield" size="40" value="<?php echo $Url; ?>" id="url" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><label for="title"><b><?php  echo _AT('title'); ?>:</b></label></td>
		<td class="row1"><input name="LinkName" class="formfield" size="40" value="<?php echo $LinkName; ?>" id="title" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="desc"><b><?php  echo _AT('description'); ?>:</b></label></td>
		<td class="row1"><textarea name="Description" class="formfield" rows="5" cols="45" id="desc"><?php echo $Desc; ?></textarea></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="name"><b><?php  echo _AT('your_name'); ?>:</b></label></td>
		<td class="row1"><input name="SubmitName" class="formfield" value="<?php echo $Name ?>" size="40" id="name" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="email"><b><?php  echo _AT('your_email'); ?>:</b></label></td>
		<td class="row1"><input name="SubmitEmail" class="formfield" value="<?php echo $Email ?>" size="40" id="email" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="cat"><b><?php  echo _AT('category'); ?>:</b></label></td>
		<td class="row1" valign="top"><?php show_cat_selection("CatID", True, $CatID); ?><br /><br /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" colspan="2" align="center"><input type="submit" name="update" value="<?php  echo _AT('update_resources'); ?> Alt-s" class="button" accesskey="s" /> <input type="reset" value="<?php echo _AT('reset');?> " class="button" /></td>
	</tr>
	</table>
	</p>
	</form>
	<?php

	return;
}

function show_add_link($add = "NULL", $CatName = "unknown")
{
	global $db2;
	global $TOP_CAT_NAME;
	global $FULL_ADMIN_ACCESS;
	global $UserName;		// Cookie
	global $UserEmail;		// Cookie

	$help[] = AT_HELP_ADD_RESOURCE;
	$help[] = AT_HELP_ADD_RESOURCE1;
	print_help($help);
	?><h3><?php echo _AT('add_link_in'); ?> <?php echo $CatName ?>:</h3>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="CatID" value="<?php echo $add ?>" />

	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" align="center" summary="">
	<tr>
		<th class="left" colspan="2"><?php print_popup_help(AT_HELP_ADD_RESOURCE_MINI); ?><?php echo _AT('add_new_resource'); ?></th>
	</tr>
	<tr>
		<td class="row1" align="right"><label for="url"><b><?php echo _AT('url'); ?>:</b></label></td>
		<td class="row1"><input name="Url" class="formfield" size="40" value="http://" id="url" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right"><label for="title"><b><?php echo _AT('title'); ?>:</b></label></td>
		<td class="row1"><input name="LinkName" class="formfield" size="40" id="title" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="desc"><b><?php echo _AT('description'); ?>:</b></label></td>
		<td class="row1"><textarea name="Description" class="formfield" rows="5" cols="45" id="desc"></textarea></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="name"><b><?php echo _AT('your_name'); ?>:</b></label></td>
		<td class="row1"><input name="SubmitName" class="formfield" value="<?php echo $UserName ?>" size="40" id="name" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td class="row1" align="right" valign="top"><label for="email"><b><?php  echo _AT('your_email'); ?>:</b></label></td>
		<td class="row1"><input name="SubmitEmail" class="formfield" value="<?php echo $UserEmail ?>" size="40" id="email" /></td>
	</tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr><td height="1" class="row2" colspan="2"></td></tr>
	<tr>
		<td colspan="2" class="row1" align="center"><input type="submit" name="suggest" class="button" value="<?php  echo _AT('submit_resource'); ?> Alt-s" accesskey="s" /></td>
	</tr>
	</table>
	</form>
	<?php
	return;
}

// Mail the admin anytime a new link is submitted
function mail_new_link($postData = '')
{
	global $db2;
	global $ADMIN_EMAIL;
	global $_template;
	if( (empty($_POST)) or (!is_array($_POST)) ) { return false; } 
	if ($ADMIN_EMAIL == '') { return false; }

	$CatID = $_POST["CatID"];
	$Url = addslashes($_POST["Url"]);
	$Description = addslashes($_POST["Description"]);
	$LinkName = addslashes($_POST["LinkName"]);
	$SubmitName = addslashes($_POST["SubmitName"]);
	$SubmitEmail = addslashes($_POST["SubmitEmail"]);
	$SuggestNewCategory = addslashes($_POST["SuggestNewCategory"]);
	$SubmitDate =  date("Y-m-d");

	// Get category information
	$secs = $db2->get_CatNames($CatID);
	$CatName = _AT('unknown');
	if (!empty($secs)) {
		$CatName = $secs;
	}

	$Subject = _AT('new_link').": ";
	$Subject .= substr($LinkName, 0, 60);
	if ($LinkName != substr($LinkName, 0, 60)) {
		$LinkName .= "...";
	}
	$Subject = trim($Subject);

	$Body = _AT('user').' '.$SubmitName.'" <'.$SubmitEmail.'> '._AT('user2').' '. $CatName.":\n\n";
	$Body .= "$LinkName "._AT('at')." <$Url>\n\n";
	$Body .= "$Description\n\n";
	if ($SuggestNewCategory != ''){
		$Body .= _AT('new_cat_suggested').": $SuggestNewCategory\n\n";
	}
	if ($AUTOAPPROVEQUE) {
		$Body .= _AT('link_auto_approved')."\n";
	} else {
		$Body .= _AT('link_needs_approval')."\n";
		$Body .= _AT('use2')." $_SERVER[PHP_SELF]"._AT('use2')."\n";
	}
	
	$From = "$SubmitName<".$SubmitEmail.">";

	// Send the email notice if email defined
	if ($ADMIN_EMAIL) {
		admin_mail($ADMIN_EMAIL, $Subject, $Body, $From);
	}

	return;
}

//	*****************************************************************

$query = getenv('QUERY_STRING');

if( ($_REQUEST['viewCat']) || ( (!$_POST) && (!$query) ) )
{
	start_page($_REQUEST['viewCat']);
	start_browse($_REQUEST['viewCat']);
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
} else if ($_REQUEST['view']) {
	$db2->increment_count($_REQUEST['view']);
	exit;
} else if($_REQUEST['add']) {
	if (($add == "top") || empty($_REQUEST['add'])) { 
		$add = 0; 
		$CatName = $TOP_CAT_NAME;
	} else {
		$CatName = stripslashes($db2->get_CatNames($_REQUEST['add']));
		if (empty($CatName)) { $CatName = $TOP_CAT_NAME; }
	}

	$junk = "";	
	print_header($_REQUEST['add'],$title,$junk);
	show_add_link($_REQUEST['add'], $CatName);

	require(AT_INCLUDE_PATH.'footer.inc.php');  
	exit;

} else if($_REQUEST['add_new_cat']) {
	$junk = "";
	$err_msg = "";
	if ($ADMIN_MODE && $FULL_ADMIN_ACCESS) {
		if(!$db2->add_cat($_POST,$err_msg))
		{
			$title = _AT('cat_create_error');
			$msg = _AT('cat_not_created')." ".$err_msg;
		} else {
			$title = _AT('cat_created');
			$msg = _AT('sub_created');
		}
	} else {
		$title = _AT('cat_create_error');
		$msg = _AT('cat_not_authorized');
	}
	start_page($_REQUEST['CatID'],$title,$msg);
	start_browse($_REQUEST['CatID']);
	require(AT_INCLUDE_PATH.'footer.inc.php');  
	exit;

} else if ($_REQUEST['suggest']) {
	$junk = "";
	$err_msg = "";
	if(!$db2->suggest($_POST,$err_msg))
	{
		$title = _AT('suggestion_error');
		$msg = _AT('suggestion_not_accepted').": ".$err_msg;
	} else {
		$title = _AT('suggestion_submitted');
		$msg = _AT('suggestion_submitted_approval');
		mail_new_link($_POST);
	}
	start_page($_REQUEST['CatID'],$title,$msg);
	start_browse($_REQUEST['CatID']);
	require(AT_INCLUDE_PATH.'footer.inc.php');  
	exit;

} elseif ($_REQUEST['update']) {
	$junk = "";
	$err_msg = "";
	if ($ADMIN_MODE) {
		if(!$db2->update($_POST,$err_msg))
		{
			$title = _AT('update_error');
			$msg = _AT('update_failed').": ".$err_msg;
		} else {
			$title = _AT('updated');
			$msg = _AT('update_submitted');
		}
	} else {
		$title = _AT('update_error');
		$msg = _AT('not_authorized');
	}
	start_page($_REQUEST['CatID'],$title,$msg);
	start_browse($_REQUEST['CatID']);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;

} elseif ($_REQUEST['approve']) {
	if ($ADMIN_MODE) {
		if(!$db2->approve($_REQUEST['approve'],$err_msg))
		{
			$title = _AT('approval_error');
			$msg = $err_msg;
		} else 	{
			$title = _AT('approved');
			$msg = _AT('suggestion_approved');
		}
	} else {
		$title = _AT('approval_error');
		$msg = _AT('not_authorized');
	}
	start_page($_REQUEST['CatID'],$title,$msg);
	start_browse($_REQUEST['CatID']);
	require(AT_INCLUDE_PATH.'footer.inc.php');  
	exit;

} elseif ($_REQUEST['disapprove']) {
	if ($ADMIN_MODE) {
		if(!$db2->disapprove($_REQUEST['disapprove'],$err_msg))
		{
			$title = _AT('disapproval_error');
			$msg = $err_msg;
		} else 	{
			$title = _AT('disapproved');
			$msg = _AT('link_disapproved');
		}
	} else {
		$title = _AT('disapproval_error');
		$msg = _AT('not_authorized');
	}
	start_page($_REQUEST['CatID'],$title,$msg);
	start_browse($_REQUEST['CatID']);
	require(AT_INCLUDE_PATH.'footer.inc.php');  
	exit;

} elseif ($_REQUEST['delete_link']) {
	if ($ADMIN_MODE) {
		if(!$db2->delete_link($_REQUEST['delete_link'],$err_msg))
		{
			$title = _AT('sub_delete_error');
			$msg = $err_msg;
		} else 	{
			$title = _AT('deleted');
			$msg = _AT('suggestion_deleted');
		}
	} else {
		$title = _AT('sub_delete_error');
		$msg = _AT('not_authorized');
	}
	start_page($_REQUEST['CatID'],$title,$msg);
	start_browse($_REQUEST['CatID']);
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;

} elseif ($_REQUEST['edit_link']) {
	show_edit_link($_REQUEST['edit_link'],$_REQUEST['title'],$msg);
	require(AT_INCLUDE_PATH.'footer.inc.php');  
	exit;

} elseif ($_REQUEST['KeyWords']) {
	//start_page();
	$CatID_temp = $_REQUEST['CatID'];
	$hits = $db2->search($_REQUEST['KeyWords']);
	if( (!$hits) or (empty($hits)) )
	{
		$junk = "";
		$title = _AT('search_results');
		$msg =  _AT('no_matches');
		start_page($CatID_temp,$title,$msg);
	} else {

		$total = count($hits);
		$title = _AT('search_results');
		$msg = _AT('search_returns').' '. $total.' '._AT('search_matches');
		$junk = "";
		//	start_page($junk,$title,$msg); 
		start_page($CatID_temp,$title,$msg); 
		while ( list ($key,$hit) = each ($hits))
		{
			if(!empty($hit))
			{
				$LinkID = $hit["LinkID"];
				$LinkName = stripslashes($hit["LinkName"]);
				$LinkDesc = stripslashes($hit["Description"]);
				$LinkURL = stripslashes($hit["Url"]);
				$CatID = $hit["CatID"];
				$CatName = stripslashes($db2->get_CatNames($CatID));
				print "<DL>\n";
				print "<DT><A HREF=\"$LinkURL\" TARGET=\"_NEW\">$LinkName</A>\n";
				print "<DD>$LinkDesc\n";
				print "<DD><A HREF=\"$_SERVER[PHP_SELF]?viewCat=$CatID\">$CatName</A>\n";
				print "</DL>\n";
			}
		}
	}
	print "<p><hr />\n";
	start_browse($CatID_temp);
	
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
} else {
	// Something terribly bad happened - start fresh
	start_page('', '', '');
	start_browse('');
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

?>