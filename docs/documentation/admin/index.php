<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html lang="en">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"" />
	<title>Administrator Documentation</title>
	<script>
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
<frameset rows="30,*" frameborder="0">
	<frame src="frame_header.php" name="header" title="header" scrolling="no" />
	<frameset cols="28%, *" frameborder="0" framespacing="3" id="frameset1">
		<frame frameborder="2" marginwidth="0" marginheight="0" src="frame_toc.php" name="toc" title="TOC" />
		<frame frameborder="2" src="<?php echo $body; ?>" name="body" title="blank" />
	</frameset>
</frameset>

<noframes>
	<h1>Administrator Documentation</h1>
	<p><a href="frame_toc.html">Table of Contents</a></p>
 </noframes>

</html>