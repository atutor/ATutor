<?php
echo '<html><body>';
require('ErrorMessage.class.php');
require('WarningMessage.class.php');
require('InfoMessage.class.php');
require('FeedbackMessage.class.php');

$base_href = 'http://localhost/~Jay/docs/';
global $_base_path;
global $_base_href;
echo $_base_path;
echo $_base_href;

echo '<br />';
require($base_href . 'include/lib/output.inc.php');

$error = new ErrorMessage('Error', $base_href);
$warn = new WarningMessage('Warning', $base_href);
$info = new InfoMessage('Info', $base_href);
$feed = new FeedbackMessage('Feedback', $base_href);

echo getTranslatedCodeStr('AT_ERROR_FORUM_NOT_FOUND');

$error->addTranslatedMessagePayload('AT_ERROR_FORUM_NOT_FOUND', getTranslatedCodeStr('AT_ERROR_FORUM_NOT_FOUND'));
$warn->addTranslatedMessagePayload('AT_WARNING_SAVE_YOUR_WORK', getTranslatedCodeStr('AT_WARNING_SAVE_YOUR_WORK'));
$info->addTranslatedMessagePayload('AT_INFOS_NO_SEARCH_RESULTS', getTranslatedCodeStr('AT_INFOS_NO_SEARCH_RESULTS'));
$feed->addTranslatedMessagePayload('AT_FEEDBACK_FORUM_ADDED', getTranslatedCodeStr('AT_FEEDBACK_FORUM_ADDED'));

debug($_SESSION);

echo $error->getMessageToPrint(); ?><br /><?php
echo $warn->getMessageToPrint(); ?><br /><?php
echo $info->getMessageToPrint();?><br /><?php
echo $feed->getMessageToPrint();?><br /><?php

debug($_SESSION);

$feedback[]=array(AT_FEEDBACK_NOW_ENROLLED, 'Welcome Course');
$feed->addTranslatedMessagePayload('AT_FEEDBACK_FORUM_ADDED', getTranslatedCodeStr($feedback));

debug($_SESSION);

echo $feed->getMessageToPrint();

debug($_SESSION);

echo '<body></html>';
?>