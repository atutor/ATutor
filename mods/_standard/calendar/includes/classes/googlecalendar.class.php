<?php
    /****************************************************************/
    /* ATutor Calendar Module                                       */
    /* https://atutorcalendar.wordpress.com/                        */
    /*                                                              */
    /* This module provides standard calendar features in ATutor.   */
    /*                                                              */
    /* Author: Anurup Raveendran, Herat Gandhi                      */
    /* This program is free software. You can redistribute it and/or*/
    /* modify it under the terms of the GNU General Public License  */
    /* as published by the Free Software Foundation.                */
    /****************************************************************/
    
    /**
     * This file is used to display all the available
     * calendars in Google Account of a user.
     */
    $includePath   = array();
    $includePath[] = '.';
    $includePath[] = 'lib';
    $includePath[] = get_include_path();
    $includePath   = implode(PATH_SEPARATOR,$includePath);
    set_include_path($includePath); 
    
    require_once 'Zend/Loader.php';

    Zend_Loader::loadClass('Zend_Gdata');
    Zend_Loader::loadClass('Zend_Gdata_AuthSub');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_HttpClient');
    Zend_Loader::loadClass('Zend_Gdata_Calendar');

    $_authSubKeyFile           = null; // Example value for secure use: 'mykey.pem'
    $_authSubKeyFilePassphrase = null;

    class GoogleCalendar {
        /**
         * Returns the full URL of the current page, based upon env variables
         *
         * Env variables used:
         * $_SERVER['HTTPS'] = (on|off|)
         * $_SERVER['HTTP_HOST'] = value of the Host: header
         * $_SERVER['SERVER_PORT'] = port number (only used if not http/80,https/443)
         * $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
         *
         * @return string Current URL
         */
        public function getCurrentUrl() {
            global $_SERVER;
    
            /**
             * Filter php_self to avoid a security vulnerability.
             */
            $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0, 
                               strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);
    
            if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
                $protocol = 'https://';
            } else {
                $protocol = 'http://';
            }
            $host = $_SERVER['HTTP_HOST'];
            if ($_SERVER['SERVER_PORT'] != '' &&
                (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
                    ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
                $port = ':' . $_SERVER['SERVER_PORT'];
            } else {
                $port = '';
            }
            return $protocol . $host . $port . $php_request_uri;
        }
    
        /**
         * Returns the AuthSub URL which the user must visit to authenticate requests
         * from this application.
         *
         * Uses getCurrentUrl() to get the next URL which the user will be redirected
         * to after successfully authenticating with the Google service.
         *
         * @return string AuthSub URL
         */
        public function getAuthSubUrl() {
            global $_authSubKeyFile;
            $next = $this->getCurrentUrl();
            $scope = 'http://www.google.com/calendar/feeds/';
            $session = true;
            if ($_authSubKeyFile != null) {
                $secure = true;
            } else {
                $secure = false;
            }
            return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure,
                $session);
        }
    
        /**
         * Outputs a request to the user to login to their Google account, including
         * a link to the AuthSub URL.
         *
         * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
         *
         * @param string to be displayed for user interaction
         *
         * @return void
         */
        public function requestUserLogin($linkText) {
            $authSubUrl = $this->getAuthSubUrl();
            echo "<a href='javascript:void(0)' onclick=\"window.open('{$authSubUrl}','Authentication','height=500,width=600');\">{$linkText}</a>";
        }
        /**
         * Returns a HTTP client object with the appropriate headers for communicating
         * with Google using AuthSub authentication.
         *
         * Uses the $_SESSION['sessionToken'] to store the AuthSub session token after
         * it is obtained.  The single use token supplied in the URL when redirected
         * after the user succesfully authenticated to Google is retrieved from the
         * $_GET['token'] variable.
         *
         * @return Zend_Http_Client
         */
        public function getAuthSubHttpClient() {
            global $_SESSION, $_GET, $_authSubKeyFile, $_authSubKeyFilePassphrase;
            $client = new Zend_Gdata_HttpClient();
            if ($_authSubKeyFile != null) {
                // set the AuthSub key
                $client->setAuthSubPrivateKeyFile($_authSubKeyFile, $_authSubKeyFilePassphrase, true);
            }
            if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
                $_SESSION['sessionToken'] =
                    Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token'], $client);
            }
            $client->setAuthSubToken($_SESSION['sessionToken']);
            return $client;
        }
    
        /**
         * Checks validity of a token. If token is valid then proceed ahead
         * otherwise the user will be logged out. To check token a dummy
         * call to function getCalendarListFeed is made. If there are some 
         * problems then the token is not valid.
         *
         * @param Zend client object
         *
         * @return void
         */
        public function outputCalendarListCheck($client) {
            $gdataCal = new Zend_Gdata_Calendar($client);
            $calFeed = $gdataCal->getCalendarListFeed();
        }
        
        public function isvalidtoken($tokent) {
            try {
                $client = $this->getAuthSubHttpClient();
                $this->outputCalendarListCheck($client);
                return true;
            }
            catch( Zend_Gdata_App_HttpException $e ) {

                $qry = "DELETE FROM %scalendar_google_sync WHERE userid=%d";
                queryDB($qry, array(TABLE_PREFIX, $_SESSION['member_id']));
                
                $this->logout();
            }
        }
        
        /**
         * Display list of calendars in the sidemenu with 
         * checkbox ahead of each calendar's title.
         *
         * @param Zend client object
         *
         * @return void
         */
        public function outputCalendarList($client) {
            $gdataCal = new Zend_Gdata_Calendar($client);
            $calFeed = $gdataCal->getCalendarListFeed();
            
            //Get calendar list from database

            $query = "SELECT * FROM %scalendar_google_sync WHERE userid=%d";
            $rowval = queryDB($query, array(TABLE_PREFIX, $_SESSION['member_id']), TRUE);
            
            $prevval = $rowval['calids'];
            $selectd = ''; 
            $i = 1;
            
            //Iterate through each calendar id
            foreach ($calFeed as $calendar) {
                //set state according to database and if changed then update database
                if( strpos($prevval,$calendar->id->text.',') === false )
                    $selectd = '';
                else
                    $selectd = "checked='checked'";
                echo "\t <div class='fc-square fc-inline-block'
                    style='background-color:".$calendar->color->value."' ></div>
                    <input id='gcal".$i."' type='checkbox' name ='calid' value='".
                    $calendar->id->text."' ".$selectd.
                    " onclick='if(this.checked) $.get(\"mods/_standard/calendar/google_calendar_db_sync.php\",
                    { calid: this.value, mode: \"add\" },function (data){ refreshevents(); } );
                    else $.get(\"mods/_standard/calendar/google_calendar_db_sync.php\",
                    { calid: this.value, mode: \"remove\" },function (data){ refreshevents(); } );'
                    />
                    <label for='gcal".$i."'>".$calendar->title->text."</label><br/>";
                $i++;
            }
        }
    
        /**
         * If there are some discrepancies in the session or user
         * wants not to connect his/her Google Calendars with ATutor
         * then this function will securely log out the user.
         *
         * @return void
         */
        public function logout() {
            // Carefully construct this value to avoid application security problems.
            $php_self = htmlentities(substr($_SERVER['PHP_SELF'], 0 ,
                        strcspn($_SERVER['PHP_SELF'], "\n\r")), ENT_QUOTES);
            //Revoke access for the stored token
            Zend_Gdata_AuthSub::AuthSubRevokeToken($_SESSION['sessionToken']);
            unset($_SESSION['sessionToken']);
            //Close this popup window
            echo "<script>window.opener.location.reload(true);window.close();</script>";
            exit();
        }
    }
?>