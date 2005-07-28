<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ATutor Handbook</title>
	<base target="body" />
<style>
body {
    font-family: Verdana,Arial,sans-serif;
	font-size: x-small;
	margin: 0px;
	padding: 0px;
	background: #fafafa;
	margin-left: -5px;
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
	background-repeat: no-repeat;
	background-position: 0px 1px;
	padding-left: 12px;
	text-decoration: none;
}
a.tree {
	background-image: url('folder.gif');
}

a.leaf {
	background-image: url('paper.gif');
}
a:link, a:visited {
	color: #006699;
}
a:hover {
	color: #66AECC;
}
</style>
</head>
<body>

<?php
if (isset($_GET['admin'])) {
	$section = 'admin';
} else if (isset($_GET['instructor'])) {
	$section = 'instructor';
} else {
	$section = 'general';
}
require('../'.$section.'/pages.inc.php');

echo '<a href="frame_toc.php?'.$section.'" target="_self">Back to Contents</a>';

if ($_GET['query']) {
	$_GET['query'] = str_replace(',', ' ', $_GET['query']);
	$_GET['query'] = str_replace('"', '', $_GET['query']);

	if (strlen($_GET['query']) > 3) {
		$_GET['query'] = strtolower($_GET['query']);

		$search_terms = explode(' ', $_GET['query']);

		$results = array();
		foreach ($_pages as $file => $title) {
			$count = 0;
			$contents = strtolower(file_get_contents('../'.$section.'/'.$file));
			foreach ($search_terms as $term) {
				$term = trim($term);
				if ($term) {
					$count += substr_count($contents, $term);
				}
			}
			if ($count) {
				$results[$file] = $count;
			}
		}

		if ($results) {
			arsort($results);
			echo '<ol>';
			foreach ($results as $file => $count) {
				echo '<li><a href="../'.$section.'/'.$file.'" class="leaf" target="body">'.$_pages[$file].'</a></li>';
			}
			echo '</ol>';
		} else {
			echo '<p style="padding: 8px;">No results found.</p>';
		}
	} else {
		echo '<p style="padding: 8px;">Search term must be longer than 3 characters.</p>';
	}
}
?>
</body>
</html>