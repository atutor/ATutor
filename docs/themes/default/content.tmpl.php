<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; } ?>

<style>
fieldset#shortcuts {
	float: right;
	background-color: #FAFAFA;
	border: 1px solid #AAAAAA;
	margin: -5px 5px 5px 5px;
	padding-right: 10px;
	padding-bottom: 5px;
	padding-left: 10px;
}

legend {
	padding-right: 10px;
	padding-left: 10px;
}

fieldset {
	margin-bottom: 10px;
}
#shortcuts legend {
	color: orange;
	background-color: white;
}
#shortcuts ul {
	position: relative;
	margin-top: 0px;
	margin-bottom: 0px;
	margin-left: 0px;
	list-style-type:  none;
	padding-left: 0px; 
}
</style>

<script language="javascript">

function showTocToggle(show,hide) {
	if(document.getElementById) {
		document.writeln(' - <span class=\'toctoggle\'><a href="javascript:toggleToc()" class="internal">' +
		'<span id="showlink" style="display:none;">' + show + '</span>' +
		'<span id="hidelink">' + hide + '</span>'
		+ '</a></span>');
	}
}

function toggleToc() {
	var tocmain = document.getElementById('toc');
	var toc = document.getElementById('toccontent');
	var showlink=document.getElementById('showlink');
	var hidelink=document.getElementById('hidelink');
	if(toc.style.display == 'none') {
		toc.style.display = tocWas;
		hidelink.style.display='';
		showlink.style.display='none';
		tocmain.className = '';

	} else {
		tocWas = toc.style.display;
		toc.style.display = 'none';
		hidelink.style.display='none';
		showlink.style.display='';
		tocmain.className = 'tochidden';
	}
}
</script>

<?php if ($this->table_of_contents): ?>
	<div id="toc">
		<h4><?php echo _AT('contents'); ?> <script type="text/javascript">
//<![CDATA[
showTocToggle("show","hide")
//]]>
</script></h4>
		<div style="margin-left: -15px;" id="toccontent">
		<?php echo $this->table_of_contents; ?>
		</div>
	</div>
<?php endif; ?>
<?php if (authenticate(AT_PRIV_CONTENT, AT_PRIV_RETURN)): ?>
<fieldset id="shortcuts"><legend>Shortcuts</legend>
	<ul>
		<?php foreach ($this->shortcuts as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
</fieldset>
<?php endif; ?>


<div id="content_text">
	<?php echo $this->body; ?>
</div>
