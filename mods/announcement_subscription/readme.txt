This module installes the following into the file /editor/add_news.php:

	/***** 
	* Added by announcement_subscription: Send mail to announcement subscribers 
	*/

	 $subscriberMod =& $moduleFactory->getModule('announcement_subscription'); 
	 if ($subscriberMod->isEnabled() && !$subscriberMod->isMissing()) { 
		 include_once(AT_MODULE_PATH . 'announcement_subscription/sendmail.php'); 
	 } 

	/***** 
	* End announcement_subscription 
	*/ 
	
These lines are added immediately after the line 

  if (!$msg->containsErrors() && (!isset($_POST['setvisual']) || isset($_POST['submit']))) {

which should be around line 70 of the file. add_news.php needs to be writable for this module
to install properly - if the file is unwritable you will need to change the file permissions
before installing the mod. There are different ways to do this depending on the web server software,
please see the web server documentation for details. 

To learn more about file permissions, see http://en.wikipedia.org/wiki/Permissions
