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
    
    /*******
     * doesn't allow this file to be loaded with a browser.
     */
    if (!defined('AT_INCLUDE_PATH')) { exit; }

    /******
     * this file must only be included within a Module obj
     */
    if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { 
        exit(__FILE__ . ' is not a Module'); 
    }
    /******
    * modules sub-content to display on course home detailed view
    */
    $this->_list['calendar'] = array('title_var'=>'calendar_header','file'=>'mods/_standard/calendar/sublinks.php');

    /*******
     * assign the instructor and admin privileges to the constants.
     */
    define('AT_PRIV_CALENDAR',       $this->getPrivilege());
    define('AT_ADMIN_PRIV_CALENDAR', $this->getAdminPrivilege());
    global $_custom_head;
    $_custom_head .='
    <script type="text/javascript" src="'.AT_BASE_HREF.
    'jscripts/infusion/InfusionAll.js"></script>
    <script type="text/javascript" src="'.AT_BASE_HREF.
    'jscripts/lib/calendar.js"></script>
    <link href="'.AT_BASE_HREF.'mods/_standard/calendar/lib/jquery/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css" />
    <link href="'.AT_BASE_HREF.'jscripts/infusion/lib/jquery/plugins/tooltip/css/jquery.tooltip.css" rel="stylesheet" type="text/css" />';

    /*******
     * create a side menu box/stack.
     */
    if( !stristr( $_SERVER["REQUEST_URI"], "calendar") )
    $this->_stacks['calendar_header'] = array('title_var' => 'calendar_header', 'file' => AT_INCLUDE_PATH.'../mods/_standard/calendar/side_menu.inc.php');
    // ** possible alternative: **
    // $this->addStack('calendar', array('title_var' => 'calendar', 'file' => './side_menu.inc.php');

    /*******
     * if this module is to be made available to students on the Home or Main Navigation.
     */
    $_student_tool = 'mods/_standard/calendar/index.php';
    // ** possible alternative: **
    // $this->addTool('./index.php');


    /*******
     * instructor Manage section:
     
    $this->_pages['mods/_standard/calendar/index_instructor.php']['title_var'] = 'ATutor Calendar';
    $this->_pages['mods/_standard/calendar/index_instructor.php']['parent']   = 'tools/index.php';
    */


    /*******
     * student page.
     */
    $this->_pages['mods/_standard/calendar/index.php']['title_var'] = 'calendar_header';
    $this->_pages['mods/_standard/calendar/index.php']['img']       = 'mods/_standard/calendar/img/calendar.png';

    /*******
     * import page
     */
    $this->_pages['mods/_standard/calendar/file_import.php']['title_var']= 'calendar_import_file';
    $this->_pages['mods/_standard/calendar/file_import.php']['parent']   = 'mods/_standard/calendar/index.php';
    
    /*******
     * Email calendar link
     */
    $this->_pages['mods/_standard/calendar/send_mail.php']['title_var'] = 'calendar_share';
    $this->_pages['mods/_standard/calendar/send_mail.php']['parent']    = 'mods/_standard/calendar/index.php';
    
    /*******
     * Public page to display shared calendar
     */
    $this->_pages[AT_NAV_START] = array('mods/_standard/calendar/index_mystart.php');
    $this->_pages['mods/_standard/calendar/index_mystart.php']['title_var'] = 'calendar';    
    $this->_pages['mods/_standard/calendar/index_mystart.php']['parent'] = AT_NAV_START;
    /*******
     * Public page to display shared calendar
     */
   // $this->_pages[AT_NAV_PUBLIC] = array('mods/_standard/calendar/index_public.php');
    $this->_pages['mods/_standard/calendar/index_public.php']['title_var'] = 'calendar';
    //$this->_pages['mods/_standard/calendar/google_connect_disconnect.php.php']['title_var'] = 'calendar';
?>