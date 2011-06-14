<?php
/**
 * Auth_viaMySQL MediaWiki extension MediaWiki version 1.14.0rc1
 *
 * @file
 * @ingroup Extensions
 * @version 1.0
 * @author John Derby Russell
 * @link http://www.mediawiki.org/w/index.php/Extension:Auth_viaMySQL
 */
 
# Not a valid entry point, skip unless MEDIAWIKI is defined
if( !defined( 'MEDIAWIKI' ) )
{
        echo "Auth_viaMySQL extension";
        die();
}
 
// Extension credits that will show up on Special:Version
$wgExtensionCredits['other'][] = array(
      'name' => 'MySQL Auto Authentication -> Auth_viaMySQL',
      'version' => '1.0',
      'author' => 'John Derby Russell',
      'url' => 'http://www.mediawiki.org/w/index.php/Extension:Auth_viaMySQL',
      'description' => 'Auto-authenticates users using MySQL database',
);
 
/**
 *
 * MySQL Login Database Integration
 *
 */
 
require_once(MW_INSTALL_PATH.'/MySQLActiveUser.php') ;
 
$wgHooks['UserLoadFromSession'][] = 'Auth_viaMySQL';
 
// Kill logout url
$wgHooks['PersonalUrls'][] = 'PersonalUrls_killLogout'; /* Disallow logout link */
 
function Auth_viaMySQL( $user, $result ) {
    global $MySQLActiveUserData;
    $MySQLActiveUserData->distribute_cookie_data() ;
 
    wfSetupSession();
 
    /**
     * A lot of this is from User::newFromName
     */
    // Force usernames to capital
    global $wgContLang;
 
    $name = $wgContLang->ucfirst( $MySQLActiveUserData->active_user_name );
 
    // Clean up name according to title rules
    $t = Title::newFromText( $name );
    if( is_null( $t ) ) {
        return(true) ;
    }
 
    $canonicalName = $t->getText();
 
    if( !User::isValidUserName( $canonicalName ) ) {
        return(true) ;
    }
 
    $user->setName( $canonicalName );
 
    $user_id_fromMW_DB = $user->idFromName( $MySQLActiveUserData->active_user_name ) ;
 
    $user->setId( $user_id_fromMW_DB );
    if ( $user->getID() == 0 ) {
        /**
        * A lot of this is from LoginForm::initUser
        * LoginForm in in the file called SpecialUserLogin.php line 342 (version 1.14.0rc1)
        */
        $canonicalName = $t->getText();
        $user->setName( $canonicalName );
        $user->addToDatabase();
 
        $user->setEmail( $MySQLActiveUserData->active_user_email );
        $user->setRealName( '' );
        $user->setToken();
 
        $user->saveSettings();
    } else {
        if ( !$user->loadFromDatabase() ) {
            // Can't load from ID, user is anonymous
            return(true) ;
        }
        $user->saveToCache();
    }
 
    $result = 1; // This causes the rest of the authentication process to be skipped.
    return(false);   // As should this, according to the internal error report:
}
 
// Kill logout url
function PersonalUrls_killLogout($personal_urls, $title) {
    $personal_urls['logout'] = null ;
    $personal_urls['login'] = null ;
    $personal_urls['anonlogin'] = null ;
    return true ;
}
?>
