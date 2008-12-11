<?php require(dirname(__FILE__) . '/vitals.inc.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="<?php if ($missing_lang) { echo 'en'; } else { echo $req_lang; } ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php get_text('atutor_documentation'); ?></title>
<style type="text/css">
body { font-family: Verdana,Arial,sans-serif; font-size: x-small; margin: 0px; padding: 3px; background: #f4f4f4; color: #afafaf; }
ul, ol { list-style: none; padding-left: 0px; margin-left: -15px; }
li { margin-left: 19pt; padding-top: 2px; }
a { text-decoration: none; }
a:link, a:visited { color: #006699; }
a:hover { color: #66AECC; }
input { border: 0px; padding: 2px 5px 2px 5px; font-size: smaller; }
input[type=submit] { color: #999; background-color: #dfdfdf; padding: 1px; }
input[type=submit]:hover { color: #999; background-color: #eee; padding: 1px; }
form { padding: 0px; margin: 0px; display: inline; }
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
<input type="hidden" name="<?php echo $section; ?>" value="" />
<input type="hidden" name="<?php echo $req_lang; ?>" value="" />
<script type="text/javascript">
// <!--
if (top.name == 'popup') {
	document.write('<a href="javascript:top.close();"><?php get_text('close_popup'); ?></a> | ');
}
// -->
</script>

<a href="../index/<?php echo $req_lang; ?>" target="_top"><?php get_text('return_to_handbook'); ?></a> | 

<input type="text" name="query" /> <input type="submit" name="search" value="<?php get_text('search'); ?>" /> |  <a href="print.php?<?php echo $section; ?>&amp;<?php echo $req_lang; ?>" target="_top"><?php get_text('print_version'); ?></a>
			<script type="text/javascript">
			//<!--
			document.writeln(' | ');
			showTocToggle('<?php get_text('show_contents'); ?>' ,'<?php get_text('hide_contents'); ?>');
			if (top.name == 'popup') {
				toggleToc(true);
			}
			//-->
			</script>

</form>
</body>
</html>