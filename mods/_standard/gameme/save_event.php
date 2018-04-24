<?php
namespace gameme\PHPGamification;
use Exception;
use gameme\PHPGamification;
use gameme\PHPGamification\Model;
use gameme\PHPGamification\Model\Event;

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


$_POST["editval"] = str_replace("\n", "", $_POST["editval"]);
$_POST["editval"] = str_replace("\r", "", $_POST["editval"]);

if($_SESSION['course_id'] >0){
    $course_id = $_SESSION['course_id'];
}else{
    $course_id = 0;
}

if($_SESSION['course_id'] > 0){
    // instructor editing events
    global $_base_path;
    $this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$_base_path);
    if($_POST["editval"] != ''){
        
        $sql = "SELECT * from %sgm_events WHERE  id=%d AND course_id = %d";
        $is_course_event = queryDB($sql, array(TABLE_PREFIX,$_POST["id"], $_SESSION['course_id']), TRUE);
        
        if (!empty($is_course_event)){
             $sql = "UPDATE %sgm_events set %s = '%s' WHERE  id=%d AND  course_id = %d";
            queryDB($sql, array(TABLE_PREFIX, $_POST["column"], $_POST["editval"],$_POST["id"], $_SESSION['course_id']));
        } else {
            $sql = "SELECT * from %sgm_events WHERE  id=%d AND course_id = 0";
            $default_event = queryDB($sql, array(TABLE_PREFIX, $_POST["id"]), TRUE);
            require_once($this_path.'mods/_standard/gameme/gamify.lib.php');
            require_once($this_path.'mods/_standard/gameme/PHPGamification/PHPGamification.class.php');
            $gamification = new PHPGamification();
            $gamification->setDAO(new DAO(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD));
            $event = new Event();
            
            if($default_event['id']){
                $event->setId($default_event['id'], $course_id);
            }
            if(isset($_SESSION['course_id'])){
                $event->setCourseId($_SESSION['course_id']);
            } else{
                $event->setCourseId(0);
            }
            $event->setAlias($default_event['alias']);

            if($_POST['column'] == "description"){
                $event->setDescription($_POST["editval"]);
            } else {
                $event->setDescription($default_event['description']);
            }
            if($_POST['column'] == "allow_repetitions"){
            
                if($_POST["editval"] > 0){
                    $event->setAllowRepetitions(1);
                }else{
                    $event->setAllowRepetitions(0);
                }
                
            } else {
                $event->setAllowRepetitions($default_event['allow_repetitions']);
            }
            if($_POST['column'] == "reach_required_repetitions"){
                $event->setReachRequiredRepetitions($_POST["editval"]);
            } else {
                $event->setReachRequiredRepetitions($default_event['reach_required_repetitions']);
            }
             if($_POST['column'] == "each_points"){
                $event->setEachPointsGranted($_POST["editval"]);
            } else {
                $event->setEachPointsGranted($default_event['each_points']);
             }
            if($_POST['column'] == "reach_points"){
                $event->setReachPointsGranted($_POST["editval"]);
            } else {
                $event->setReachPointsGranted($default_event['reach_points']);
            }    
            if($_POST['column'] == "id_each_badge"){
                $sql = "SELECT alias FROM %sgm_badges WHERE id = %d AND course_id = %d";
                $this_alias = queryDB($sql, array(TABLE_PREFIX, $default_event['id_each_badge'], $_SESSION['course_id']), TRUE);
                $event->setEachBadgeGranted($gamification->getBadgeByAlias($this_alias['alias']));
            } else {
                $sql = "SELECT alias FROM %sgm_badges WHERE id = %d AND course_id = %d";
                $this_alias = queryDB($sql, array(TABLE_PREFIX, $default_event['id_each_badge'], $_SESSION['course_id']), TRUE);
                $event->setEachBadgeGranted($gamification->getBadgeByAlias($this_alias['alias']));
            }
            if($_POST['column'] == "id_reach_badge"){
                $sql = "SELECT alias FROM %sgm_badges WHERE id = %d AND course_id = %d";
                $this_alias = queryDB($sql, array(TABLE_PREFIX, $default_event['id_reach_badge'], $_SESSION['course_id']), TRUE);
                $event->setReachBadgeGranted($gamification->getBadgeByAlias($this_alias['alias']));
            } else {
                $sql = "SELECT alias FROM %sgm_badges WHERE id = %d AND course_id = %d";
                $this_alias = queryDB($sql, array(TABLE_PREFIX, $default_event['id_reach_badge'], $_SESSION['course_id']), TRUE);
                $event->setReachBadgeGranted($gamification->getBadgeByAlias($this_alias['alias']));
             }
            if($_POST['column'] == "each_callback"){
                $event->setEachCallback($_POST["editval"]);
            } else if($default_event['each_callback'] != '') {
                $event->setEachCallback($default_event['each_callback']);
            }
            if($_POST['column'] == "reach_callback"){
                $event->setReachCallback($_POST["editval"]);
            } else if($default_event['reach_callback'] != ''){
                $event->setReachCallback($default_event['reach_callback']);
            }     
            $gamification->addEvent($event);
        }
    }else{
        // Admin editing system events
        if(is_int($_POST["editval"]) ){
            $sql = "UPDATE %sgm_events set %s = %d WHERE  id=%d AND course_id = %d";
        }else{
            $sql = "UPDATE %sgm_events set %s = '%s' WHERE  id=%d AND course_id = %d"; // editval is a string
        }
        $result = queryDB($sql, array(TABLE_PREFIX, $_POST["column"], $_POST["editval"], $_POST["id"], $course_id));
    }

} else {
    // admin editing events
    if($_POST["editval"] == ''){
        $sql = 'UPDATE %sgm_events set %s = NULL WHERE  id=%d AND course_id=%d';
        queryDB($sql, array(TABLE_PREFIX, $_POST["column"], $_POST["id"], $course_id ));
    }else{
        if(is_int($_POST["editval"]) ){
            $sql = "UPDATE %sgm_events set %s = %d WHERE  id=%d AND course_id=%d";
        }else{
            $sql = "UPDATE %sgm_events set %s = '%s' WHERE  id=%d AND course_id=%d";
        }
        $result = queryDB($sql, array(TABLE_PREFIX, $_POST["column"], $_POST["editval"], $_POST["id"], $course_id));
    }
}
if(!empty($result)){
    return true;
    }
?>