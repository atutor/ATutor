<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg 		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: 10.1.link_categories.php 4824 2005-06-08 19:27:33Z joel $


	require('../index.php');
	return;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html lang="en">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"" />
	<title>Administrator Documentation</title>
	<script type="text/javascript">
function set(cols) {
  var fs = document.getElementById('frameset1');
  if (fs) {
    //fs.rows = '40,*';
    fs.cols = cols;
  }
  return false;
}
</script>

<?php if (isset($_GET['p'])) {
	$body = $_GET['p'];
} else {
	$body = '0.0.introduction.php';
} ?>
</head>
<frameset rows="24,*" frameborder="0">
	<frame src="frame_header.php?admin" name="header" title="header" scrolling="no" />
	<frameset cols="28%, *" frameborder="0" framespacing="2" id="frameset1">
		<frame frameborder="2" marginwidth="0" marginheight="0" src="frame_toc.php?admin" name="toc" title="TOC" />
		<frame frameborder="2" src="<?php echo $body; ?>" name="body" title="blank" />
	</frameset>
</frameset>

<noframes>
	<h1>Administrator Documentation</h1>
	<p><a href="frame_toc.html">Table of Contents</a></p>
 </noframes>

</html>
