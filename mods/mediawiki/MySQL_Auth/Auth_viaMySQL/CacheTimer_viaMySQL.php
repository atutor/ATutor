<?php
/**
 * CacheTimer_viaMySQL MediaWiki extension
 *
 * @file
 * @ingroup Extensions
 * @version 1.0
 * @author John Derby Russell
 * @link http://www.mediawiki.org/w/index.php/Extension:Auth_viaMySQL
 */
 
// Extension credits that will show up on Special:Version
$wgExtensionCredits['other'][] = array(
      'name' => 'MySQL Cache Timer',
      'version' => '1.0',
      'author' => 'John Derby Russell',
      'url' => 'http://www.mediawiki.org/w/index.php/Extension:Auth_viaMySQL',
      'description' => 'Tells Wiki when to regenerate client cache for users',
);
 
require_once(MW_INSTALL_PATH.'/MySQLActiveUser.php') ;
 
/**
 *
 * The MySQL cache epoche timer is for when to rebuild the cache stored on the client side.
 * This is ussually done at login.
 *
 */
 
function CacheTimer_viaMySQL( ) {
    global $MySQLActiveUserData ;
    $MySQLActiveUserData->distribute_cookie_data() ;
 
    return $MySQLActiveUserData->active_user_login_time ;
}
?>
