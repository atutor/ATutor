<?php
if (!defined('AT_INCLUDE_PATH')) { exit; }

/* available header.tmpl.php variables:
 * $this->lang_code			the ISO language code
 * SITE_NAME				the site name from the config file
 * $this->page_title		the name of this page to use in the <title>
 * $this->lang_charset		the ISO language character set
 * $this->content_base_href	the <base href> to use for this page
 * $this->base_path			the absolute path to this atutor installation
 * $this->rtl_css			if set, the path to the RTL style sheet
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
global $system_courses, $_custom_css;

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $this->lang_code; ?>">
<head>
	<title><?php echo SITE_NAME; ?> : <?php echo $this->page_title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
	<meta name="Generator" content="ATutor - Copyright 2005 by http://atutor.ca" />
	<base href="<?php echo $this->content_base_href; ?>" />
	<link rel="shortcut icon" href="<?php echo $this->base_path; ?>favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/print.css" type="text/css" media="print" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/styles.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->base_path.'themes/'.$this->theme; ?>/forms.css" type="text/css" />
	<?php echo $this->rtl_css; ?>
	<?php if ($system_courses[$_SESSION['course_id']]['rss']): ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_path; ?>get_rss.php?<?php echo $_SESSION['course_id']; ?>-2" />
	<link rel="alternate" type="application/rss+xml" title="<?php echo SITE_NAME; ?> - RSS 1.0" href="<?php echo $this->base_path; ?>get_rss.php?<?php echo $_SESSION['course_id']; ?>-1" />
	<?php endif; ?>
	<?php echo $this->custom_css; ?>
</head>
<body onload="setstates(); <?php echo $this->onload; ?>"><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000; <?php if ($this->rtl_css): ?>direction:rtl;<?php endif; ?>"></div>
<script language="JavaScript" src="<?php echo $this->base_path; ?>overlib.js" type="text/javascript"></script><script language="javascript" type="text/javascript">
//<!--

var newwindow;
function poptastic(url) {
	newwindow=window.open(url,'popup','height=700,width=700,scrollbars=yes,resizable=yes');
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
	return true;
}

function showTocToggle(objId, show, hide, key, selected) {
	if(document.getElementById) {
		if (key) {
			var accesskey = " accesskey='" + key + "' title='"+ show + "/" + hide + " Alt+"+ key +"'";
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
		toc.style.display = '';
		hidelink.style.display='';
		showlink.style.display='none';
	} else {
		toc.style.display = 'none';
		hidelink.style.display='none';
		showlink.style.display='';
	}
	setcookie(objId, toc.style.display, 1);
}
//-->
</script>
<!-- the bread crumbs -->
<div id="breadcrumbs">
	<div style="float: right;">
		<!-- hidden direct link to content -->
		<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#content" style="border: 0px;" accesskey="c"><img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_content'); ?> ALT+c" /></a>
		<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#menu" style="border: 0px;" accesskey="m"><img src="<?php echo $this->base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('goto_menu'); ?> ALT+m" /></a>
		<?php if (isset($_SESSION['member_id']) && $_SESSION['member_id']): ?>
			<!-- start the jump menu -->
			<?php if (empty($_GET)): ?>
				<form method="post" action="<?php echo $this->base_path; ?>bounce.php?p=<?php echo urlencode($this->rel_url); ?>" target="_top">
			<?php else: ?>
				<form method="post" action="<?php echo $this->base_path; ?>bounce.php" target="_top">
			<?php endif; ?>
			<label for="jumpmenu" accesskey="j"></label>
				<select name="course" id="jumpmenu" title="<?php echo _AT('jump'); ?>:  Alt-j">							
					<option value="0" id="start-page"><?php echo _AT('my_start_page'); ?></option>
					<optgroup label="<?php echo _AT('courses_below'); ?>">
						<?php foreach ($this->nav_courses as $this_course_id => $this_course_title): ?>
							<option value="<?php echo $this_course_id; ?>"><?php echo $this_course_title; ?></option>
						<?php endforeach; ?>
					</optgroup>
				</select> <input type="submit" name="jump" value="<?php echo _AT('jump'); ?>" id="jump-button" /></form>
			<!-- /end the jump menu -->
			<?php if ($_SESSION['is_super_admin']): ?>
				<a href="<?php echo $this->base_path; ?>bounce.php?admin"><?php echo _AT('return_to_admin_area'); ?></a> | 
			<?php endif; ?>
			<img src="<?php echo $this->img;?>user-star.gif" style="vertical-align: middle;" class="img-size-star" alt="" /><strong><?php echo $_SESSION['login']; ?></strong>  |
			<?php if ($_SESSION['course_id'] > -1): ?>
				<?php if (get_num_new_messages()): ?>
					<strong><a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?> - <?php echo get_num_new_messages(); ?></a></strong> | 
				<?php else: ?>
					<a href="<?php echo $this->base_path; ?>inbox/index.php"><?php echo _AT('inbox'); ?></a> | 
				<?php endif; ?>
			<?php endif; ?>
			<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> |
			<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a> |
			<a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a>
		<?php elseif ($_SESSION['course_id'] == -1): ?>
			<img src="<?php echo $this->img;?>user-star.gif" style="vertical-align: middle;" class="img-size-star" alt="" /><strong><?php echo $_SESSION['login']; ?></strong>  |
			<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> |
			<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a> |
			<a href="<?php echo $this->base_path; ?>logout.php"><?php echo _AT('logout'); ?></a>
		<?php else: ?>
			<a href="<?php echo $this->base_path; ?>browse.php"><?php echo _AT('browse_courses'); ?></a> | 
			<a href="<?php echo $this->base_path; ?>login.php?course=<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('login'); ?></a> | 
 			<a href="<?php echo $this->base_path; ?>search.php"><?php echo _AT('search'); ?></a> | 
			<a href="<?php echo $this->base_path; ?>help/index.php"><?php echo _AT('help'); ?></a>
		<?php endif; ?>
	</div>

	<span style="white-space:nowrap;font-size:smaller;padding-top:150px;">
	<?php if($_SESSION['course_id'] && $_SESSION['valid_user'] ){ ?>
 		<a href="<?php echo $this->base_path; ?>users/index.php"><?php echo _AT('my_start_page'); ?>: </a> '
	<?php }?>
	<?php echo $this->section_title; ?>:
	
	<?php foreach ($this->path as $page): ?>
		<a href="<?php echo $page['url']; ?>" title="<?php echo _AT('back_to').' '.$page['title']; ?>"><?php echo $page['title']; ?></a> &raquo; 
	<?php endforeach; ?> <?php echo $this->page_title; ?></span>
</div>

<div class="header">
	<!-- section title -->	
	<?php if ($_SESSION['valid_user']): 
		echo '<span style="font-size:small;font-weight:bold;padding-left:5px;">'.stripslashes(SITE_NAME).'</span>'; 
	else:
		echo '<br />';	
	endif; ?>
	<h1><?php echo $this->section_title; ?>
	<?php if (($_SESSION['course_id'] > 0) && ($_SESSION['enroll'] == AT_ENROLL_NO)) : ?>
		- <small><a href="<?php echo $this->base_path; ?>enroll.php?course=<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('enroll_me'); ?></a></small>
	<?php endif; ?></h1>

	<div id="topnavlistcontainer">
		<!-- the main navigation. in our case, tabs -->
		<ul id="topnavlist">
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
</div>

<!-- the sub navigation -->
<div style="text-align: right; padding-top: 5px; padding-right: 5px;"><small><?php echo $this->current_date; ?></small></div>
<?php if ($this->sub_level_pages): ?>
	<div id="sub-navigation">
		<?php if (isset($this->back_to_page)): ?>
			<a href="<?php echo $this->back_to_page['url']; ?>" id="back-to"><?php echo _AT('back_to'); ?> <?php echo $this->back_to_page['title']; ?></a> | 
		<?php endif; ?>

		<?php $num_pages = count($this->sub_level_pages); ?>
		<?php for($i=0; $i<$num_pages; $i++): ?>
			<?php if ($this->sub_level_pages[$i]['url'] == $this->current_sub_level_page): ?>
				<strong><?php echo $this->sub_level_pages[$i]['title']; ?></strong>
			<?php else: ?>
				<a href="<?php echo $this->sub_level_pages[$i]['url']; ?>"><?php echo $this->sub_level_pages[$i]['title']; ?></a>
			<?php endif; ?>
			<?php if ($i < $num_pages-1): ?>
				| 
			<?php endif; ?>
		<?php endfor; ?>
	</div>
<?php else: ?>
	<div id="sub-navigation">
		&nbsp;
	</div>
<?php endif; ?>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="99%">
	<tr>
		<?php if ($_SESSION['course_id'] > 0): ?>
			<td valign="top" width="100%">
		<?php else: ?>
			<td valign="top" width="100%" colspan="2">
		<?php endif; ?>

<!-- the page title -->
	<div style="text-align: right; padding-bottom: 10px; padding-right: 10px; float: right; margin-top: 10px; padding-right: 5px;">
		<?php if ($this->guide): ?>
			<a href="<?php echo $this->guide; ?>" id="guide" onclick="poptastic('<?php echo $this->guide; ?>'); return false;" target="_new"><em><?php echo $this->page_title; ?></em></a>
		<?php endif; ?>
		<?php if ($_SESSION['course_id'] > 0 && $system_courses[$_SESSION['course_id']]['side_menu']): ?>
			<script type="text/javascript" language="javascript">
			//<![CDATA[
			var state = getcookie("side-menu");
			if (state && (state == 'none')) {
				showTocToggle("side-menu", "<?php echo _AT('show'); ?>","<?php echo _AT('hide'); ?>", "", "show");
			} else {
				showTocToggle("side-menu", "<?php echo _AT('show'); ?>","<?php echo _AT('hide'); ?>", "", "hide");
			}

			//]]>
			</script>

		<?php endif; ?>
	</div>

	<div style="float:right;padding-top:7px;">
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
		&nbsp;
	</div>

	<h2 class="page-title"><?php echo $this->page_title; ?></h2>

<a name="content"></a>
<?php global $msg; $msg->printAll(); ?>
