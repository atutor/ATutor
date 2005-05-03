<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>ATutor 1.5 Administrator Documentation</title>
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
	/* white-space: pre; */
	/* background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/folder.gif'); */
	/* background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/tree.gif'); */
	/* 	background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/leaf.gif'); */
	background-repeat: no-repeat;
	background-position: 0px 1px;
	padding-left: 12px;
	text-decoration: none;
}
a.tree {
	/* background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/tree.gif'); */
	background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/folder.gif');
}

a.leaf {
	/* background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/leaf.gif'); */
	background-image: url('http://www.h2.dion.ne.jp/~rubyzbox/onepoint/paper.gif');
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

<a href="frame_toc.php" target="_self">Back to Contents</a>
<?php

function scandir($dir, $no_dots=FALSE) {
   $files = array();
   $dh  = @opendir($dir);
   if ($dh!=FALSE) {
       while (false !== ($filename = readdir($dh))) {
           $files[] = $filename;
       }

       if ($no_dots) {
           while(($ix = array_search('.',$files)) > -1)
                   unset($files[$ix]);
           while(($ix = array_search('..',$files)) > -1)
                   unset($files[$ix]);
       }

       sort($files);

   }
   return $files;
}

if ($_GET['query']) {
	$files = scandir('.');
	$results = array();
	foreach ($files as $file) {
		if (($file == '.') || ($file == '..') || ($file == '.svn') || !is_file($file) || ($file == 'frame_toc.php')) {
			continue;
		}

		$contents = file_get_contents($file);
		$count = substr_count($contents, $_GET['query']);
		if ($count) {
			$results[$file] = $count;
		}
	}
	arsort($results);
	echo '<ol>';
	foreach ($results as $file => $count) {
		echo '<li><a href="'.$file.'" class="leaf" target="body">'.$file.' ('.$count.') </a></li>';
	}
	echo '</ol>';
}
?>
</body>
</html>