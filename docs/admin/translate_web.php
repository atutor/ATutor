<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca						*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.		*/
/****************************************************************/

if (!$_REQUEST['f']) {
	$_REQUEST['f']	= 'en';
}

$page = 'translate';
$_user_location = 'admin';
$page_title = 'ATutor: LCMS: Translation';

$_INCLUDE_PATH = '../include/';

Header('Content-Type: text/html; charset='.$langs[$_SESSION['language']]['charset']);

require($_INCLUDE_PATH.'vitals.inc.php');
authenticate(USER_TRANS);
require('admin/language/languages.inc.php');

	$_SECTION[0][0] = 'Home';
	$_SECTION[0][1] = '/index.php';
	$_SECTION[1][0] = 'My ATutor';
	$_SECTION[1][1] = '/my/';
	$_SECTION[2][0] = 'Translate';

require ($_INCLUDE_PATH.'header.inc.php');
echo '<h3>ATutor Translator Site</h3>';

$db = mysql_connect('atutorsvn.rcat.utoronto.ca', 'dev_atutor_langs', 'd3v-L4n$s');
mysql_select_db('dev_atutor_langs', $db);

if (($_REQUEST['submit'] == 'Set') && authenticate(USER_ADMIN, USER_RETURN_CHECK)) {
	$_SESSION['language'] = $_REQUEST['t'];
} else if (($_REQUEST['release_submit'] == 'Set') && authenticate(USER_ADMIN, USER_RETURN_CHECK)) {
	$_SESSION['release'] = $_REQUEST['r'];
}
/* projects are defined in vitals.inc.php*/
$variables = array('_template','_msgs');

$_atutor_versions = get_atutor_versions();
$_atutor_versions[] = 'SVN';
rsort($_atutor_versions);

if (!isset($_SESSION['release'])) {
	$_SESSION['release'] = $_atutor_versions[0]; // 0: defaults to SVN, 1 to the next stable release
}

$version = str_replace('.', '_', $_SESSION['release']);


unset($langs);
$sql = "SELECT * FROM languages_$version ORDER BY english_name";
$result = mysql_query($sql, $db);
while ($row = mysql_fetch_assoc($result)) {
	$langs[$row['language_code']] = array('name' => $row['english_name'], 'charset' => $row['char_set']);
}

$_TABLE_PREFIX = '';
$_TABLE_SUFFIX = '_'.$version;

?>

<ol>
	<li><br />
		<form method="post" action="<?php echo $_SESSION['PHP_SELF']; ?>">
			<input type="hidden" name="f" value="en" />
			<table border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #cccccc;" >
			<tr>
				<td  bgcolor="#eeeeee" nowrap="nowrap"><h5 class="heading2">Version</h5></td>
				<td style="font-size:small; border-left: 1px solid #cccccc;"><?php
				if (!authenticate(USER_ADMIN, USER_RETURN_CHECK)) {
					$_atutor_versions = array_splice($_atutor_versions, 0, 2);
				}
				echo ' <select name="r">';
				foreach ($_atutor_versions as $release) {
					echo '<option value="'.$release.'"';
					if ($release == $_SESSION['release']) {
						echo ' selected="selected"';
					}
					echo '>'.$release.'</option>';
				}
				echo '</select>';
				
				?><input type="submit" name="release_submit" value="Set" class="submit" /></td>
			</tr>
			</table>
		</form><br />
	</li>
	<li><br />
	<form method="post" action="<?php echo $_SESSION['PHP_SELF']; ?>">
		<input type="hidden" name="f" value="en" />
		<table border="0" cellspacing="0" cellpadding="2" style="border: 1px solid #cccccc;" >
		<tr>
			<td  bgcolor="#eeeeee" nowrap="nowrap"><h5 class="heading2">Translate</h5></td>
			<td style="font-size:small; border-left: 1px solid #cccccc;">From <strong>English - iso-8859-1</strong>

			to
			<?php
			if (authenticate(USER_ADMIN, USER_RETURN_CHECK)) {
				echo ' <select name="t">';
				foreach ($langs as $val => $title) {
					echo '<option value="'.$val.'"';
					if ($val == $_SESSION['language']) {
						echo ' selected="selected"';
					}
					echo '>'.$title['name'].' - '.$title['charset'].'</option>';
				}
				echo '</select>';
			} else {
				echo '<strong>'.$langs[$_SESSION['language']]['name'].' - '.$langs[$_SESSION['language']]['charset'].'</strong> ';
			}
			?><input type="submit" name="submit" value="Set" class="submit" /></td>
		</tr>
		</table>
	</form><br /></li>


<?php

$atutor_test = '<a href="http://atutor.ca/atutor/translate/atutor/docs/" title="Open ATutor in a new window" target="new">';

require_once('translator.php');


?>