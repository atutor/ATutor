<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Documentation Header</title>
<style type="text/css">
body {
    font-family: Verdana,Arial,sans-serif;
	font-size: x-small;
	margin: 0px;
	padding: 3px;
	background: #f4f4f4;
	color: #afafaf;
}

ul, ol {
	list-style: none;
	padding-left: 0px;
	margin-left: -15px;
}
li {
	margin-left: 19pt;
	padding-top: 2px;
}
a {
	text-decoration: none;
}
a:link, a:visited {
	color: #006699;
}
a:hover {
	color: #66AECC;
}

input {
	border: 0px;
	padding: 2px 5px 2px 5px;
	font-size: smaller;
}
input[type=submit] {
	color: #999;
	background-color: #dfdfdf;
	padding: 1px;
}
input[type=submit]:hover {
	color: #999;
	background-color: #eee;
	padding: 1px;
}
form {
	padding: 0px;
	margin: 0px;
	display: inline;
}

</style>
<script type="text/javascript">
// <!--
var currentPage;

function showTocToggle(show, hide) {
	if(document.getElementById) {
		document.writeln('<a href="javascript:toggleToc(false)">' +
		'<span id="showlink" style="display:none;">' + show + '</span>' +
		'<span id="hidelink">' + hide + '</span>'	+ '</a>');
	}
}

function toggleToc(override) {
	var showlink=document.getElementById('showlink');
	var hidelink=document.getElementById('hidelink');

	if (override && (hidelink.style.display == 'none')) {
		//alert(hidelink.style.display);
		top.show();
		hidelink.style.display='';
		showlink.style.display='none';
	} else if (!override && (hidelink.style.display == 'none')) {
		top.show();
		hidelink.style.display='';
		showlink.style.display='none';
	} else if (!override) {
		top.hide(); //('0, *');
		hidelink.style.display='none';
		showlink.style.display='';
	}
}
// -->
</script>
</head>
<body><form method="get" action="search.php" target="toc" onsubmit='toggleToc(true);false;'>
<?php if (isset($_GET['admin'])) : ?>
	<?php $section = 'admin'; ?>
	<input type="hidden" name="admin" value="" />
<?php elseif (isset($_GET['instructor'])): ?>
	<?php $section = 'instructor'; ?>
	<input type="hidden" name="instructor" value="" />
<?php else: ?>
	<?php $section = 'general'; ?>
	<input type="hidden" name="general" value="" />
<?php endif; ?>
<script type="text/javascript">
// <!--
if (top.name == 'popup') {
	document.write('<a href="javascript:top.close();">Close Pop-up</a> | ');
}
// -->
</script>
<input type="text" name="query" /> <input type="submit" name="search" value="Search" /> |  <a href="print.php?<?php echo $section; ?>" target="_top">Print Version</a>
			<script type="text/javascript">
			//<!--
			document.writeln(' | ');
			showTocToggle('Show Contents' ,'Hide Contents');
			if (top.name == 'popup') {
				toggleToc();
			}
			//-->
			</script>
</form>
</body>
</html>