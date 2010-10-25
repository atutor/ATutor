<?php
/*******
 * the line below safe-guards this file from being accessed directly from
 * a web browser. It will only execute if required from within an ATutor script,
 * in our case the Module::install() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }
require(AT_INCLUDE_PATH.'lib/constants.inc.php');

$_course_privilege = TRUE; // possible values: FALSE | AT_PRIV_ADMIN | TRUE
$_admin_privilege  = TRUE; // possible values: FALSE | TRUE
$_cron_interval    = 0; // disable


/******
 * the following code checks if there are any errors (generated previously)
 * then uses the SqlUtility to run any database queries it needs, ie. to create
 * its own tables.
 */
 
if (!$msg->containsErrors() && file_exists(dirname(__FILE__) . '/module.sql')) {
	// deal with the SQL file:
	require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
	$sqlUtility =& new SqlUtility();

	/*
	 * the SQL file could be stored anywhere, and named anything, "module.sql" is simply
	 * a convention we're using.
	 */
	$sqlUtility->queryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

/******
 * The following core files are changed: 
 * 
 *  editor/add_news.php:   
 *
 */  

/******
 * Open editor/add_news.php, check if changes need to be made, make them if neccessary
 */ 

$installed=FALSE;
$needle = "if (!\$msg->containsErrors() && (!isset(\$_POST['setvisual']) || isset(\$_POST['submit']))) {";
$changes = "\n\t/***** 
\t* Added by announcement_subscription: Send mail to announcement subscribers 
\t*/\n
\t \$subscriberMod =& \$moduleFactory->getModule('announcement_subscription'); 
\t if (\$subscriberMod->isEnabled() && !\$subscriberMod->isMissing()) { 
\t\t include_once(AT_MODULE_PATH . 'announcement_subscription/sendmail.php'); 
\t } \n
\t/***** 
\t* End announcement_subscription 
\t*/ \n ";


$filename=('../../editor/add_news.php');
if(!is_writable($filename)){
$msg->addError('ANOUNCEMENTSUB_INSTALL_UNWRITE');
}else{
  $data = file($filename);
  foreach($data as $line){
    $newfile .= (strpos($line,$needle))? $line . "\n" . $changes . "\n": $line;
    if(strpos($line,'announcement_subscription/sendmail.php')){
      $msg->addInfo('ANNOUNCEMENTSUB_ALREADYINSTALLED_ADDNEWS');
      $installed=TRUE;
    }
  }
  
  if (!$installed) {
    $file = fopen('$filename','w');
    if(fwrite($file,$newfile)){
      $msg->addFeedback('ANNOUNCEMENTSUB_INSTALL_ADDNEWS');
    }else{
      $msg->addError('ANNOUNCEMENTSUB_INSTALL_ADDNEWS');
    }
    fclose($file);
  }
}

?>
