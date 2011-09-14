<?php require(dirname(__FILE__) . '/vitals.inc.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php if ($missing_lang) { echo 'en'; } else { echo $req_lang; } ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php get_text('atutor_documentation'); ?></title>
	<base target="body" />
<style>
body { font-family: Verdana,Arial,sans-serif; font-size: x-small; margin: 0px; padding: 0px; background: #fafafa; margin-left: -5px; }
ul, ol { list-style: none; padding-left: 0px; margin-left: -15px; }
li { margin-left: 19pt; padding-top: 2px; }
a { background-repeat: no-repeat; background-position: 0px 1px; padding-left: 12px; text-decoration: none; }
a.tree { background-image: url('folder.gif'); }
a.leaf { background-image: url('paper.gif'); }
a:link, a:visited { color: #006699; }
a:hover { color: #66AECC; }
</style>
</head>
<body>
<?php
require('../'.$section.'/pages.inc.php');
if (($req_lang != 'en') && (file_exists('../'.$section.'/'.$req_lang.'/pages.inc.php'))) {
	require('../'.$section.'/'.$req_lang.'/pages.inc.php');
}

echo '<a href="frame_toc.php?'.$section.'" target="_self">';
get_text('back_to_contents');
echo '</a>';

if ($_GET['query']) {
	$_GET['query'] = str_replace(',', ' ', $_GET['query']);
	$_GET['query'] = str_replace('"', '', $_GET['query']);

	if (strlen($_GET['query']) > 3) {
		$_GET['query'] = strtolower($_GET['query']);

		$search_terms = explode(' ', $_GET['query']);

		$results = array();
		if ($req_lang == 'en') {
			$files = glob('../'.$section . '/*.php');
		} else {
			$files = glob('../'.$section . '/'.$req_lang.'/*.php');
		}
		if (is_array($files)) {
			foreach ($files as $filename) {
			
				$count = 0;
				$filename = basename($filename);
				
				if ($req_lang == 'en') {
					$contents = strtolower(file_get_contents('../'.$section.'/'.$filename));
				} else {
					$contents = strtolower(file_get_contents('../'.$section. '/'.$req_lang.'/'.$filename));
				}

				foreach ($search_terms as $term) {
					$term = trim($term);
					if ($term) {
						$count += substr_count($contents, $term);
					}
				}
				if ($count) {
					$results[$filename] = $count;
				}
			}
		}

		if ($results) {
			arsort($results);
			echo '<ol>';
			foreach ($results as $file => $count) 
			{
				if (($req_lang != 'en') && (file_exists('../'.$section.'/'.$req_lang.'/'.$file))) 
					$full_file = '../'.$section.'/'.$req_lang.'/'.$file;
				else
					$full_file = '../'.$section.'/'.$file;

				echo '<li><a href="'.$full_file.'?'.$req_lang.'" class="leaf" target="body">'.$_pages[$file].'</a></li>';
			}
			echo '</ol>';
		} else {
			echo '<p style="padding: 8px;">';
			get_text('no_results_found');
			echo '</p>';
		}
	} else {
		echo '<p style="padding: 8px;">';
		get_text('search_term_longer_3_chars');
		echo '</p>';
	}
}
?>
</body>
</html>