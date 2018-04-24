<?php
namespace gameme\PHPGamification;

/*
use Exception;
use gameme\PHPGamification;
use gameme\PHPGamification\Model;
use gameme\PHPGamification\Model\Event;
*/
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$_POST["editval"] = str_replace("\n", "", $_POST["editval"]);
$_POST["editval"] = str_replace("\r", "", $_POST["editval"]);

if($_POST["editval"] != ''){
    $sql = "UPDATE %sgm_badges set %s = '%s' WHERE  id=%d";
    if($_SESSION['course_id'] > 0){
        $result = queryDB($sql, array(TABLE_PREFIX, $_POST["column"], $_POST["editval"], $_POST["id"], $_SESSION['course_id']));
    }else{
        $result = queryDB($sql, array(TABLE_PREFIX, $_POST["column"], $_POST["editval"], $_POST["id"], 0));
    }
}
if(!empty($result)){
    return true;
    }
?>