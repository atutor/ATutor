<?php

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
//require (AT_INCLUDE_PATH.'header.inc.php');

$forum_id = $_GET['fid'];
$forum_title = $_GET['title'];

require('forum_post.php');
require(AT_INCLUDE_PATH.'classes/zipfile.class.php');



$zipfile = new zipfile();
$zipfile->add_file(file_get_contents(AT_CONTENT_DIR.'/exported_forum.html'), 'exported_forum.html');

foreach($filearr as $val) {
    $zipfile->add_file(file_get_contents(AT_CONTENT_DIR.'/'.$val), $val);
    unlink(AT_CONTENT_DIR.'/'.$val);
}

$zipfile->add_file(file_get_contents(AT_CONTENT_DIR.'/styles.css'), 'styles.css');
unlink(AT_CONTENT_DIR.'/styles.css');

// replaces spaces with underscores
if (stripos($forum_title, chr(32)) != false) {
    $forum_title = str_replace(chr(32), chr(95), $forum_title);
}
$zipfile->send_file($forum_title);
exit;

?>

<?php //require (AT_INCLUDE_PATH.'footer.inc.php'); ?>