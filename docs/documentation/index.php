<?php
$parts = pathinfo($_SERVER['PHP_SELF']);
if (substr($parts['dirname'], -5) == 'admin') {
	$section = 'admin';
} else {
	$section = 'instructor';
}
$path = '../common/';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html lang="en">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"" />
	<title>Documentation</title>
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

<?php 
if (isset($_GET['p'])) {
	$body = $_GET['p'];
} else {
	$body = '0.0.introduction.php';
} 
?>
</head>
<frameset rows="24,*" frameborder="0">
	<frame src="<?php echo $path; ?>frame_header.php?<?php echo $section; ?>" name="header" title="header" scrolling="no" />
	<frameset cols="28%, *" frameborder="0" framespacing="2" id="frameset1">
		<frame frameborder="2" marginwidth="0" marginheight="0" src="<?php echo $path; ?>frame_toc.php?<?php echo $section; ?>" name="toc" title="TOC" />
		<frame frameborder="2" src="<?php echo $body; ?>" name="body" title="blank" />
	</frameset>
</frameset>

<noframes>
	<h1>Administrator Documentation</h1>
	<p><a href="frame_toc.html">Table of Contents</a></p>
 </noframes>

</html>