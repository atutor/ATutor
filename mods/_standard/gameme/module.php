<?php
//namespace gameme\PHPGamification;

/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_GAMEME', $this->getPrivilege());
define('AT_ADMIN_PRIV_GAMEME', $this->getAdminPrivilege());

/*******
 * create a side menu box/stack.
 */
$this->_stacks['gameme'] = array('title_var'=>'gameme', 'file'=>AT_INCLUDE_PATH.'../mods/_standard/gameme/side_menu.inc.php');

// Uncomment for big icon for module sublinks "detail view" on course home page
$this->_pages['mods/_standard/gameme/index.php']['img']      = 'mods/_standard/gameme/images/gamify.png';

// ** possible alternative: **
// the text to display on module "detail view" when sublinks are not available
$this->_pages['mods/_standard/gameme/index.php']['text']      = _AT('gm_gameme_text');
$_student_tool = 'mods/_standard/gameme/index.php';
/*******
 * add the admin pages when needed.
 */
if (admin_authenticate(AT_ADMIN_PRIV_GAMEME, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
	$this->_pages[AT_NAV_ADMIN] = array('mods/_standard/gameme/index_admin.php');
	$this->_pages['mods/_standard/gameme/index_admin.php']['title_var'] = 'gm_gameme';
	$this->_pages['mods/_standard/gameme/index_admin.php']['parent']    = AT_NAV_ADMIN;
	$this->_pages['mods/_standard/gameme/index_admin.php']['guide'] = '../mods/_standard/gameme/admin_handbook.php';
	$this->_pages['mods/_standard/gameme/edit_event.php']['title_var'] = 'gm_edit_event';
	$this->_pages['mods/_standard/gameme/edit_event.php']['parent']    = 'mods/_standard/gameme/index_admin.php';
	$this->_pages['mods/_standard/gameme/edit_event.php']['guide'] = 'mods/_standard/gameme/admin_handbook.php';
	$this->_pages['mods/_standard/gameme/edit_level.php']['title_var'] = 'gm_edit_level';
    $this->_pages['mods/_standard/gameme/edit_level.php']['parent']   = 'mods/_standard/gameme/index_admin.php';
    $this->_pages['mods/_standard/gameme/edit_badge.php']['title_var'] = 'gm_edit_badge';
    $this->_pages['mods/_standard/gameme/edit_badge.php']['parent']   = 'mods/_standard/gameme/index_admin.php';
	$this->_pages['mods/_standard/gameme/delete_event.php']['title_var'] = 'gm_delete_event';
	$this->_pages['mods/_standard/gameme/delete_event.php']['parent']    = 'mods/_standard/gameme/index_admin.php';
    $this->_pages['mods/_standard/gameme/delete_badge.php']['title_var'] = 'gm_delete_badge';
    $this->_pages['mods/_standard/gameme/delete_badget.php']['parent']   = 'mods/_standard/gameme/index_admin.php';    
	$this->_pages['mods/_standard/gameme/delete_level.php']['title_var'] = 'gm_delete_level';
    $this->_pages['mods/_standard/gameme/delete_level.php']['parent']   = 'mods/_standard/gameme/index_admin.php';
}

/*******
 * instructor Manage section:
 */
 if (authenticate(AT_PRIV_GAMEME, TRUE)) {
    $this->_pages['mods/_standard/gameme/index_instructor.php']['title_var'] = 'gm_gameme';
    $this->_pages['mods/_standard/gameme/index_instructor.php']['parent']   = 'tools/index.php';
    $this->_pages['mods/_standard/gameme/index_instructor.php']['guide'] = '../mods/_standard/gameme/instructor_handbook.php';
    $this->_pages['mods/_standard/gameme/delete_event.php']['title_var'] = 'gm_delete_event';
    $this->_pages['mods/_standard/gameme/delete_event.php']['parent']   = 'mods/_standard/gameme/index_instructor.php';
    $this->_pages['mods/_standard/gameme/edit_event.php']['title_var'] = 'gm_edit_event';
    $this->_pages['mods/_standard/gameme/edit_event.php']['parent']   = 'mods/_standard/gameme/index_instructor.php';
    $this->_pages['mods/_standard/gameme/edit_level.php']['title_var'] = 'gm_edit_level';
    $this->_pages['mods/_standard/gameme/edit_level.php']['parent']   = 'mods/_standard/gameme/index_instructor.php';
    $this->_pages['mods/_standard/gameme/copy_event.php']['title_var'] = 'gm_edit_event';
    $this->_pages['mods/_standard/gameme/copy_event.php']['parent']   = 'mods/_standard/gameme/index_instructor.php';
    $this->_pages['mods/_standard/gameme/copy_badge.php']['title_var'] = 'gm_copy_badge';
    $this->_pages['mods/_standard/gameme/copy_badge.php']['parent']   = 'mods/_standard/gameme/index_instructor.php';
    $this->_pages['mods/_standard/gameme/delete_badge.php']['title_var'] = 'gm_delete_badge';
    $this->_pages['mods/_standard/gameme/delete_badget.php']['parent']   = 'mods/_standard/gameme/index_instructor.php';
    $this->_pages['mods/_standard/gameme/delete_level.php']['title_var'] = 'gm_delete_level';
    $this->_pages['mods/_standard/gameme/delete_level.php']['parent']   = 'mods/_standard/gameme/index_instructor.php';
    $this->_pages['mods/_standard/gameme/game_options.php']['title_var'] = 'gm_game_options';
    $this->_pages['mods/_standard/gameme/game_options.php']['parent']   = 'mods/_standard/gameme/index_instructor.php';
    $this->_pages['mods/_standard/gameme/index_instructor.php']['img']    = 'mods/_standard/gameme/images/gamify.png';
    $this->_pages_i['mods/_standard/gameme/index_instructor.php']['title_var']  = 'manage';
    $this->_pages_i['mods/_standard/gameme/index.php']['children'] = array('mods/_standard/gameme/index_instructor.php');
}

/*******
 * student page.
 */
$this->_pages['mods/_standard/gameme/index.php']['title_var'] = 'gm_gameme';
$this->_pages['mods/_standard/gameme/index.php']['img']       = 'mods/_standard/gameme/images/gamify.png';

/* Add GameMe tab to Course Networking */
//$this->_pages['mods/_standard/gameme/my_progress.php']['title_var']   = 'gm_gameme';
//$this->_pages['mods/_standard/gameme/my_progress.php']['parent']   = 'mods/_standard/social/index.php';
//$this->_pages['mods/_standard/social/index.php']['children'] = array_merge(
//array('mods/_standard/gameme/my_progress.php'));

/* my start page pages */
//$this->_pages[AT_NAV_START]  = array('mods/_standard/gameme/index_mystart.php');
//$this->_pages['mods/_standard/gameme/index_mystart.php']['title_var'] = 'gm_gameme';
//$this->_pages['mods/_standard/gameme/index_mystart.php']['parent'] = AT_NAV_START;


function gamemeEnabled(){
    $sql = "SELECT home_links, main_links, side_menu FROM %scourses WHERE course_id = %d";
    $gameme_elements = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);  
    foreach($gameme_elements as $gameme_element){
        if(preg_match('/gameme/',$gameme_element)){
            return TRUE;   
        }
    }
}
// Run gameme events if a valid user is logged in, and a course_id is set
//if($_SESSION['valid_user'] || $_SESSION['valid_user'] == 1 && ($_SESSION['course_id'] > 0 || $_REQUEST['course'] >0) && gamemeEnabled()){
if($_SESSION['valid_user'] && gamemeEnabled()){
    global $_base_path;
    include(AT_INCLUDE_PATH.'../mods/_standard/gameme/events.php');
    // limit gameme to within courses, comment out the include above, 
    // and uncomment the if statement below (requires less CPU than the above include)
    //if(($_SESSION['course_id']>0 || $_REQUEST['course'] >0) && gamemeEnabled() === TRUE){
        //include($_SERVER['DOCUMENT_ROOT'].$root_path.'/mods/_standard/gameme/events.php');
    //}
}
// Check course gameme options, and warn if not set
if($_SESSION['is_admin'] == true){
    //check if gameme is enabled
    if($_SESSION['course_id']>0 && gamemeEnabled() === true){
        $sql = "SELECT * from %sgm_options WHERE course_id=%d";
        $has_options = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
        if(empty($has_options[0])){
            global $msg;
            $msg->addWarning('GM_SET_GAMEME_OPTIONS');
        }
    }
}
// Insert some basic data to populate GameMe
if (isset($_POST['form_course'])) {
    $sql = "SELECT course_id FROM %scourses ORDER BY course_id DESC LIMIT 1";
    $this_course = queryDB($sql, array(TABLE_PREFIX), TRUE);

     $gm_enable = array("showlog", "showlevels", "showprogress", "showpoints", "showbadges", "showposition", "showleaders", "showalerts");   
    $gm_enable2 = array("showleader_count"	, "level_count	");
    $newcourseid = ($this_course['course_id']+1);
    foreach ($gm_enable as $gm_option){
        $sql = "INSERT IGNORE into %sgm_options (id,course_id, gm_option, value) 
        VALUES(0, %d, '%s', 1)";
        queryDB($sql, array(TABLE_PREFIX,  $newcourseid, $gm_option));
    }
    foreach ($gm_enable2 as $gm_option){
        $sql = "INSERT IGNORE into %sgm_options (id,course_id, gm_option, value) 
        VALUES(0, %d, '%s', 10)";
        queryDB($sql, array(TABLE_PREFIX, $newcourseid, $gm_option));
    }

}

?>