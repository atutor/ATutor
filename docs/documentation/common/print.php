<?php require(dirname(__FILE__) . '/vitals.inc.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php echo $req_lang; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php get_text('doc_title'); ?></title>
	<link rel="stylesheet" href="styles.css" type="text/css" />
</head>
<body>
<?php
require('../'.$section.'/pages.inc.php');

echo '<a href="../'.$section.'/index.php?'.$req_lang.'" target="_top">';
get_text('back_to_chapters');
echo '</a>';

foreach ($_pages as $file => $title) {
	if (($req_lang != 'en') && (file_exists('../'.$section.'/'.$req_lang.'/'.$file))) {
		$string = file_get_contents('../'.$section.'/'.$req_lang.'/'.$file);
	} else if ($req_lang != 'en') {
		?>
		<div style="margin: 20px auto; border: 1px solid #aaf; padding: 4px; text-align: center; background-color: #eef;">
			<?php get_text('page_not_translated'); ?>
		</div>
		<?php
		$string = file_get_contents('../'.$section.'/'.$file);
	} else {
		$string = file_Get_contents('../'.$section.'/'.$file);
	}
	
	$patterns = array('#<a href="(?!http)([[:alnum:].?/_-]+)"([^>]*)>([^<]+)</a>#is',
					  '#<a href="http://([[:alnum:]./_-]+)"([^>]*)>([^<]+)</a>#i');

	$replacements = array('<strong><u>$3</u></strong>',
						  '<strong><u>$3</u></strong> [$1]');

	echo preg_replace($patterns, $replacements, $string);
}
?>
</body>
</html>