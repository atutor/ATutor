<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

require('ErrorMessage.class.php');
require('WarningMessage.class.php');
require('InfoMessage.class.php');
require('FeedbackMessage.class.php');

/*
wouldn't it be nicer to do something like:

MessageHandler has names of the savant templates to use for each type.

$messageHandler =& new MessageHandler($savant);

$messageHandler->addError('FORUM_NOT_FOUND', 'forum name', 'link name' [, 'additional dynamic text']);

and have it stored in

$_SESSION['messages']['errors'][] ?

then simply do something like:

$messageHandler->printAllMessages(); which will print everything or
$messageHandler->printErrors() | printFeedback() etc.  to print only that type

instead of having the HTML hard coded in the code, just pass it the name of the template file. and let savant handle the output.


add methods such as: $messageHandler->hasError() returns true|false if there is a saved error.

*/

echo '<br />';

$error = new ErrorMessage('Error', $base_href);
$warn = new WarningMessage('Warning', $base_href);
//$info = new InfoMessage('Info', $base_href);
//$feed = new FeedbackMessage('Feedback', $base_href);

//echo getTranslatedCodeStr('AT_ERROR_FORUM_NOT_FOUND');

$error->addMessageTranslatedPayload('AT_ERROR_FORUM_NOT_FOUND', 'optional value');
$warn->addMessageTranslatedPayload('AT_WARNING_SAVE_YOUR_WORK', getTranslatedCodeStr('AT_WARNING_SAVE_YOUR_WORK'));
//$info->addMessageTranslatedPayload('AT_INFOS_NO_SEARCH_RESULTS', getTranslatedCodeStr('AT_INFOS_NO_SEARCH_RESULTS'));
//$feed->addMessageTranslatedPayload('AT_FEEDBACK_FORUM_ADDED', getTranslatedCodeStr('AT_FEEDBACK_FORUM_ADDED'));

debug($_SESSION);
exit;
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