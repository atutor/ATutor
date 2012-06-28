<?php
// Sanitize the input language code
$lang_code = trim($_GET['lang']);

if (!preg_match("/^[a-zA-Z]+(-)?([a-zA-Z0-9])*$/", $lang_code)) {
	unset($lang_code);
}

header('Location: index/index.php?'.$lang_code);
exit;
?>