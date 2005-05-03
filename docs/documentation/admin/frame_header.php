<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ATutor 1.5 Administrator Documentation</title>

	<style>
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
</style>
<script type="text/javascript">

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

	if (override || (hidelink.style.display == 'none')) {
		top.set('28%, *');
		hidelink.style.display='';
		showlink.style.display='none';
	} else {
		top.set('0, *');
		hidelink.style.display='none';
		showlink.style.display='';
	}
}
</script>
</head>
<body>
<form method="get" action="search.php" target="toc" onsubmit='toggleToc(true);false;'>
<script language="javascript">
if (top.name == 'popup') {
	document.write('<a href="javascript:top.close();">Close Pop-up</a> | ');
}
</script>
<input type="text" name="query" /> <input type="submit" name="search" value="Search" /> |  <a href="">Print Version</a>
			<script type="text/javascript" language="javascript">
			//<![CDATA[
			document.writeln(' | ');
			showTocToggle('Show Contents' ,'Hide Contents');
			if (top.name == 'popup') {
				toggleToc();
			}
			//]]>
			</script>
</form>
</body>
</html>