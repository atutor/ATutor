<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: header.tmpl.php 3825 2005-03-11 15:35:51 joel $
if (!defined('AT_INCLUDE_PATH')) { exit; }
/* available header.tmpl.php variables:
 * $this->lang_code			the ISO language code
 * SITE_NAME				the site name from the config file
 * $this->page_title		the name of this page to use in the <title>
 * $this->lang_charset		the ISO language character set
 * $this->content_base_href	the <base href> to use for this page
 * $this->base_path			the absolute path to this atutor installation
 * $this->rtl_css			if set, the path to the RTL style sheet
 * $this->icon			the path to a course icon
 * $this->banner_style		-deprecated-
 * $this->theme				the directory name of the current theme
 * $this->base_href			the full url to this atutor installation
 * $this->onload			javascript onload() calls
 * $this->img				the absolute path to this theme's images/ directory
 * $this->sequence_links	associative array of 'previous', 'next', and/or 'resume' links
 * $this->path				associative array of path to this page: aka bread crumbs
 * $this->rel_url			the relative url from the installation root to this page
 * $this->nav_courses		associative array of this user's enrolled courses
 * $this->section_title		the title of this section (course, public, admin, my start page)
 * $this->top_level_pages	associative array of the top level navigation
 * $this->current_top_level_page	the full path to the current top level page with file name
 * $this->sub_level_pages			associate array of sub level navigation
 * $this->back_to_page				if set, the path and file name to the part of this page (if parent is not a top level nav)
 * $this->current_sub_level_page	the full path to the current sub level page with file name
 * $this->guide				the full path and file name to the guide page
 * ======================================
 * top_level_pages           array(array('url', 'title'))     the top level pages. ATutor default creates tabs.
 * section_title             string                           the name of the current section. either name of the course, administration, my start page, etc.
 * page_title                string                           the title of the current page.
 * path                      array(array('url', 'title'))     the path to the current page.
 * back_to_page              array('url', 'title')            the link back to the part of the current page, if needed.
 * current_top_level_page    string                           full url to the current top level page in "top_leve_pages"
 * current_sub_level_page    string                           full url to the current sub level page in the "sub_level_pages"
 * sub_level_pages           array(array('url', 'title'))     the sub level pages.
 */

// will have to be moved to the header.inc.php
global $system_courses, $_custom_css, $db, $_base_path;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo $this->lang_code; ?>"> 

<head>
	<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2005 by http://atutor.ca" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
	<!--[if IE]>
	  <link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/ie_styles.css" type="text/css" />
	<![endif]-->
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
<?php echo $this->rtl_css; ?>
<?php if (isset($_SESSION['course_id']) && $system_courses[$_SESSION['course_id']]['rss']): ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $_SESSION['course_id']; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 1.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $_SESSION['course_id']; ?>-1" />
<?php endif; ?>
	<script src="<?php echo $this->base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
	<script language="javascript" type="text/javascript">
	//<!--
	jQuery.noConflict();
	//-->
	</script>
<?php echo $this->custom_css; ?>
</head>
<body onload="<?php echo $this->onload; ?>">
<script language="javascript" type="text/javascript">
//<!--
var newwindow;
function poptastic(url) {
	newwindow=window.open(url,'popup','height=600,width=600,scrollbars=yes,resizable=yes');
	if (window.focus) {newwindow.focus()}
}

function getexpirydate(nodays){
	var UTCstring;
	Today = new Date();
	nomilli=Date.parse(Today);
	Today.setTime(nomilli+nodays*24*60*60*1000);
	UTCstring = Today.toUTCString();
	return UTCstring;
}

function setcookie(name,value,duration){
	cookiestring=name+"="+escape(value)+";path=/;expires="+getexpirydate(duration);
	document.cookie=cookiestring;
	if(!getcookie(name)){
		return false;
	} else {
		return true;
	}
}

function getcookie(cookiename) {
	var cookiestring=""+document.cookie;
	var index1=cookiestring.indexOf(cookiename);
	if (index1==-1 || cookiename=="") return ""; 
	var index2=cookiestring.indexOf(';',index1);
	if (index2==-1) index2=cookiestring.length; 
	return unescape(cookiestring.substring(index1+cookiename.length+1,index2));
}

function setDisplay(objId) {
	var toc = document.getElementById(objId);

	var state = getcookie(objId);
	if (document.getElementById(objId) && state && (state == 'none')) {
		toggleToc(objId);
	}
}


function setstates() {
	return;
	var objId = "side-menu";
	var state = getcookie(objId);
	if (document.getElementById(objId) && state && (state == 'none')) {
		toggleToc(objId);
	}

	var objId = "toccontent";
	var state = getcookie(objId);
	if (document.getElementById(objId) && state && (state == 'none')) {
		toggleToc(objId);
	}

}

function showTocToggle(objId, show, hide, key, selected) {
	if(document.getElementById) {
		if (key) {
			var accesskey = " accesskey='" + key + "' title='"+ show + "/" + hide + " Alt - "+ key +"'";
		} else {
			var accesskey = "";
		}

		if (selected == 'hide') {
			document.writeln('<a href="javascript:toggleToc(\'' + objId + '\')" ' + accesskey + '>' +
			'<span id="' + objId + 'showlink" style="display:none;">' + show + '</span>' +
			'<span id="' + objId + 'hidelink">' + hide + '</span>'	+ '</a>');
		} else {
			document.writeln('<a href="javascript:toggleToc(\'' + objId + '\')" ' + accesskey + '>' +
			'<span id="' + objId + 'showlink">' + show + '</span>' +
			'<span id="' + objId + 'hidelink" style="display:none;">' + hide + '</span>'	+ '</a>');
		}
	}
}

function toggleToc(objId) {
	var toc = document.getElementById(objId);
	if (toc == null) {
		return;
	}
	var showlink=document.getElementById(objId + 'showlink');
	var hidelink=document.getElementById(objId + 'hidelink');

	if (hidelink.style.display == 'none') {
		document.getElementById('contentcolumn').id="contentcolumn_shiftright";
		jQuery("[id="+objId+"]").slideDown("slow");
		hidelink.style.display='';
		showlink.style.display='none';
	} else {
		document.getElementById('contentcolumn_shiftright').id="contentcolumn";
		jQuery("[id="+objId+"]").slideUp("slow");
		hidelink.style.display='none';
		showlink.style.display='';
	}
	setcookie(objId, hidelink.style.display, 1);
}

// toggle content folder in side menu "content navigation"
function toggleFolder(cid)
{
	if (jQuery("#tree_icon"+cid).attr("src") == tree_collapse_icon) {
		jQuery("#tree_icon"+cid).attr("src", tree_expand_icon);
		jQuery("#tree_icon"+cid).attr("alt", "<?php echo _AT('expand'); ?>");
		setcookie("c<?php echo $_SESSION['course_id'];?>_"+cid, null, 1);
	}
	else {
		jQuery("#tree_icon"+cid).attr("src", tree_collapse_icon);
		jQuery("#tree_icon"+cid).attr("alt", "<?php echo _AT('collapse'); ?>");
		setcookie("c<?php echo $_SESSION['course_id'];?>_"+cid, "1", 1);
	}
	
	jQuery("#folder"+cid).slideToggle();
}

// toggle elements in side menu
function elementToggle(elem, title)
{
	element_collapse_icon = "<?php echo $_base_path; ?>images/mswitch_minus.gif";
	element_expand_icon = "<?php echo $_base_path; ?>images/mswitch_plus.gif";
	
	if (jQuery(elem).attr("src") == element_collapse_icon) {
		jQuery(elem).attr("src", element_expand_icon);
		jQuery(elem).attr("alt", "<?php echo _AT('show'). ' '; ?>"+ title);
		jQuery(elem).attr("title", "<?php echo _AT('show'). ' '; ?>"+ title);
		setcookie("m_"+title, 0, 1);
	}
	else {
		jQuery(elem).attr("src", element_collapse_icon);
		jQuery(elem).attr("alt", "<?php echo _AT('collapse'); ?>");
		jQuery(elem).attr("alt", "<?php echo _AT('hide'). ' '; ?>"+ title);
		jQuery(elem).attr("title", "<?php echo _AT('hide'). ' '; ?>"+ title);
		setcookie("m_"+title, null, 1);;
	}
	
	jQuery(elem).parent().next().slideToggle();
}

function printSubmenuHeader(title)
{
	if (getcookie("m_"+title) == "0")
	{
		image = "<?php echo $_base_path?>images/mswitch_plus.gif";
		alt_text = "<?php echo _AT('show'); ?>" + title;
	}
	else
	{
		image = "<?php echo $_base_path?>images/mswitch_minus.gif";
		alt_text = "<?php echo _AT('hide'); ?>" + title;
	}
	
	document.writeln('<h4 class="box">'+
	'	<input src="'+image+'"' + 
	'	       onclick="elementToggle(this, \''+title+'\'); return false;"' +
	'	       alt="'+ alt_text + '" ' +
	'	       title="'+ alt_text + '"' +
	'	       style="float:right" type="image" />'+ title +
	'</h4>');
}
//-->
</script>
<div class="page_wrapper">
<div id="header">
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" accesskey="c">
	<img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?> ALT+c" /></a>		

	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#menu<?php echo $_REQUEST['cid']  ?>"  accesskey="m"><img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_menu'); ?> ALT+m" /></a>
	<div id="top-links"> <!-- top help/search/login links -->
		<?php if (isset($_SESSION['member_id']) && $_SESSION['member_id']): ?>
			<?php if(!$this->just_social): ?>
			<!-- start the jump menu -->
			<?php if (empty($_GET)): ?>
				<form method="post" action="<?php echo $this->base_path; ?>bounce.php?p=<?php echo urlencode($this->rel_url); ?>" target="_top">
			<?php else: ?>
				<form method="post" action="<?php echo $this->base_path; ?>bounce.php" target="_top">
			<?php endif; ?>
			<label for="jumpmenu" accesskey="j"></label>
				<select name="course" id="jumpmenu" title="<?php echo _AT('jump'); ?>:  Alt-j">							
					<option value="0"><?php echo _AT('my_start_page'); ?></option>
					<optgroup label="<?php echo _AT('courses_below'); ?>">
						<?php foreach ($this->nav_courses as $this_course_id => $this_course_title): ?>
							<option value="<?php echo $this_course_id; ?>"><?php echo $this_course_title; ?></option>
						<?php endforeach; ?>
					</optgroup>
				</select> <input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" class="button" /> </form>
			<!-- /end the jump menu -->
			<?php endif; ?>

			<?php if ($_SESSION['is_super_admin']): ?>
				<a href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('return_to_admin_area'); ?></a> | 
			<?php endif; ?>

			<?php if ($_SESSION['course_id'] > -1): ?>
				<?php if (get_num_new_messages()): ?>
					<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> (<?php echo get_num_new_messages(); ?>)</a> 
				<?php else: ?>
					<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php if(!$this->just_social): ?>
			<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> 
		<?php endif; ?>
		<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a>
	</div>
	<?php if (!empty($this->icon)) { // if a course icon is available, display it here.  ?>
		<a href="<?php echo $this->base_path.url_rewrite('index.php'); ?>"><img src="<?php echo $this->icon; ?>" class="headicon" alt="<?php echo  _AT('home'); ?>" /></a>	
	<?php } ?>



	<?php
	// If there is a custom course banner in the file manager called banner.html, display it here
	@readfile(AT_CONTENT_DIR . $_SESSION['course_id'].'/banner.txt'); 

	/*
	and example banner.html file might look like:
	<div style="width: 760px; height: 42px; background: white;"><img src="http://[mysite]/atutor15rc3/banners/kart-camb.jpg"></div>
	*/

	?>
	<!-- section title -->
	<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): 
		echo '<div style="font-size:small;font-weight:bold;padding-left:1em;color:white;">'.stripslashes(SITE_NAME).'</div>'; 
	else:
		echo '<br />';	
	endif; ?>
	<h1 id="section-title"><?php echo $this->section_title; ?>
		<?php if ((isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) && ($_SESSION['enroll'] == AT_ENROLL_NO)) : ?> 
			- <small><a href="<?php echo $this->base_path; ?>enroll.php?course=<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('enroll_me'); ?></a></small>
		<?php endif; ?>
	</h1>


</div>

<div id="topnavlistcontainer">
<!-- the main navigation. in our case, tabs -->
	<ul id="topnavlist">
		<?php $accesscounter = 0; //initialize ?>
		<?php foreach ($this->top_level_pages as $page): ?>
			<?php ++$accesscounter; $accesscounter = ($accesscounter == 10 ? 0 : $accesscounter); ?>
			<?php $accesskey_text = ($accesscounter < 10 ? 'accesskey="'.$accesscounter.'"' : ''); ?>
			<?php $accesskey_title = ($accesscounter < 10 ? ' Alt+'.$accesscounter : ''); ?>
			<?php if ($page['url'] == $this->current_top_level_page): ?>
				<li><a href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'] . $accesskey_title; ?>" class="active"><?php echo $page['title']; ?></a></li>
			<?php else: ?>
				<li><a href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'] . $accesskey_title; ?>"><?php echo $page['title']; ?></a></li>
			<?php endif; ?>
			<?php $accesscounter = ($accesscounter == 0 ? 11 : $accesscounter); ?>
		<?php endforeach; ?>
	</ul>
</div>

<div style="background-color:#E6E6E6; font-size:0.85em; padding-top: 5px; border-bottom:1px solid black; height:2em;">
	<!-- the sub navigation -->
	<div style="float: right; padding-right: 5px; text-transform: lowercase;">
		<?php if (isset($_SESSION['valid_user']) && $_SESSION['valid_user']): ?>					
			<strong><?php echo get_display_name($_SESSION['member_id']); ?></strong> | <a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a>
		<?php else: ?>
			 <a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('login'); ?></a> | <a href="<?php echo $this->base_path; ?>registration.php"><?php echo _AT('register'); ?></a>
		<?php endif; ?>
	</div>

	<?php if ($this->sub_level_pages): ?>
	<!--	<div id="sub-navigation">
			<?php if (isset($this->back_to_page)): ?>
				<a href="<?php echo $this->back_to_page['url']; ?>" id="back-to"><?php echo _AT('back_to').' '.$this->back_to_page['title']; ?></a> | 
			<?php endif; ?>

			<?php $num_pages = count($this->sub_level_pages); ?>
			<?php for ($i=0; $i<$num_pages; $i++): ?>
				<?php if ($this->sub_level_pages[$i]['url'] == $this->current_sub_level_page): ?>
					<strong><?php echo $this->sub_level_pages[$i]['title']; ?></strong>
				<?php else: ?>
					<a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo $this->sub_level_pages[$i]['title']; ?></a>
				<?php endif; ?>
				<?php if ($i < $num_pages-1): ?>
					|
				<?php endif; ?>
			<?php endfor; ?>
		</div> -->
	<?php else: ?>
		<!-- <div id="sub-navigation">
			&nbsp;
		</div> -->
	<?php endif; ?>
</div>

<div style="padding:3px;">
<?php if (isset($_SESSION["prefs"]["PREF_SHOW_BREAD_CRUMBS"]) && $_SESSION["prefs"]["PREF_SHOW_BREAD_CRUMBS"]) { ?>
	<!-- the bread crumbs -->
	<div id="breadcrumbs">
		<?php foreach ($this->path as $page): ?>
			<a href="<?php echo $page['url']; ?>"><?php echo htmlspecialchars($page['title'], ENT_COMPAT, "UTF-8"); ?></a> > 
		<?php endforeach; ?> <?php echo $this->page_title; ?>
	</div>
<?php } ?>

	<?php if (isset($this->guide) && isset($_SESSION["course_id"]) && $this->guide && ($_SESSION["prefs"]["PREF_SHOW_GUIDE"] || $_SESSION["course_id"] == "-1")) : ?>
		<a href="<?php echo $this->guide; ?>" id="guide" onclick="poptastic('<?php echo $this->guide; ?>'); return false;" target="_new"><em><?php echo $this->page_title; ?></em></a>
	<?php endif; ?>
</div>


<div id="contentwrapper">
	<?php if ((isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0) && $system_courses[$_SESSION['course_id']]['side_menu']): ?>
		<div id="leftcolumn">
			<script type="text/javascript">
			//<![CDATA[
			var state = getcookie("side-menu");
			if (state && (state == 'none')) {
				document.writeln('<a name="menu"></a><div style="display:none;" id="side-menu">');
			} else {
				document.writeln('<a name="menu"></a><div id="side-menu">');
			}
			//]]>
			</script>

			<?php require(AT_INCLUDE_PATH.'side_menu.inc.php'); ?>

			<script type="text/javascript">
			//<![CDATA[
				document.writeln('</div>');
			//]]>
			</script>
		</div>
	<?php endif; ?>

	<div id="contentcolumn"
		<?php if ((isset($_SESSION['course_id']) && $_SESSION['course_id'] <= 0) && isset($this->side_menu) && !$this->side_menu): ?>
			style="margin-left:0.5em;width:99%;"
		<?php endif; ?>
		>

		<?php if (isset($_SESSION['course_id']) && $_SESSION['course_id'] > 0): ?>
		<div id="menutoggle">

			<?php if ($_SESSION['course_id'] > 0 && $system_courses[$_SESSION['course_id']]['side_menu']): ?>
				<script type="text/javascript" language="javascript">
				//<![CDATA[
				var state = getcookie("side-menu");
				if (state && (state == 'none')) {
					showTocToggle("side-menu", "<?php echo _AT('show'); ?>","<?php echo _AT('hide'); ?>", "", "show");
				} else {
					document.getElementById('contentcolumn').id="contentcolumn_shiftright";
					showTocToggle("side-menu", "<?php echo _AT('show'); ?>","<?php echo _AT('hide'); ?>", "", "hide");
				}
				//]]>
				</script>
			<?php endif; ?>
		</div>

		<div id="sequence-links">
		<?php if ($_SESSION["prefs"]["PREF_SHOW_NEXT_PREVIOUS_BUTTONS"]) { ?>
			<?php if ($this->sequence_links['resume']): ?>
					<a style="color:white;" href="<?php echo $this->sequence_links['resume']['url']; ?>" accesskey="."><img src="<?php echo $this->img; ?>resume.gif" border="0" title="<?php echo _AT('resume').': '.$this->sequence_links['resume']['title']; ?> Alt+." alt="<?php echo $this->sequence_links['resume']['title']; ?> Alt+." class="img-size-ascdesc" /></a>
			<?php else:
				if ($this->sequence_links['previous']): ?>
					<a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," accesskey=","><img src="<?php echo $this->img; ?>previous.gif" border="0" alt="<?php echo _AT('previous_topic').': '. $this->sequence_links['previous']['title']; ?> Alt+," class="img-size-ascdesc" /></a>
				<?php endif;
				if ($this->sequence_links['next']): ?>
					<a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." accesskey="."><img src="<?php echo $this->img; ?>next.gif" border="0" alt="<?php echo _AT('next_topic').': '.$this->sequence_links['next']['title']; ?> Alt+." class="img-size-ascdesc" /></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php } ?>
			&nbsp;
		</div>
		<?php endif; ?>

	<!-- the page title -->
	<a name="content" title="<?php echo _AT('content'); ?>"></a>
	<h2 class="page-title"><?php echo $this->page_title; ?></h2>
	<?php global $msg; $msg->printAll(); $_base_href;?>

	<?php if (count($this->sub_level_pages) > 0): ?>


<!-- <div id="topnavlistcontainer">
	<ul id="topnavlist">
		<?php $accesscounter = 0; //initialize ?>
		<?php foreach ($this->top_level_pages as $page): ?>
			<?php ++$accesscounter; $accesscounter = ($accesscounter == 10 ? 0 : $accesscounter); ?>
			<?php $accesskey_text = ($accesscounter < 10 ? 'accesskey="'.$accesscounter.'"' : ''); ?>
			<?php $accesskey_title = ($accesscounter < 10 ? ' Alt+'.$accesscounter : ''); ?>
			<?php if ($page['url'] == $this->current_top_level_page): ?>
				<li><a href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'] . $accesskey_title; ?>" class="active"><?php echo $page['title']; ?></a></li>
			<?php else: ?>
				<li><a href="<?php echo $page['url']; ?>" <?php echo $accesskey_text; ?> title="<?php echo $page['title'] . $accesskey_title; ?>"><?php echo $page['title']; ?></a></li>
			<?php endif; ?>
			<?php $accesscounter = ($accesscounter == 0 ? 11 : $accesscounter); ?>
		<?php endforeach; ?>
	</ul>
</div> -->





		<div id="subnavlistcontainer">
			<ul id="subnavlist">
			<?php if (isset($this->back_to_page)): ?>
				<a href="<?php echo $this->back_to_page['url']; ?>">
				<img border="0" width="10" height="11" alt="<?php echo _AT('back_to').' '.$this->back_to_page['title']; ?>" src="<?php echo $this->base_href; ?>images/arrowicon.gif" style="float:left;"/></a>&nbsp;
			<?php endif; ?>

			<?php $num_pages = count($this->sub_level_pages); ?>
			<?php for ($i=0; $i<$num_pages; $i++): ?>
				
				<?php if ($this->sub_level_pages[$i]['url'] == $this->current_sub_level_page): ?>
				<li><?php echo $this->sub_level_pages[$i]['title']; ?></li>
				<?php else: ?>
					<li><a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo $this->sub_level_pages[$i]['title']; ?></a></li>
				<?php endif; ?>
				<?php if ($i < $num_pages-1): ?>
					&nbsp;
				<?php endif; ?>
			<?php endfor; ?>
			</ul>
		</div>

	<?php endif; ?>


<!-- the main navigation. in our case, tabs -->
