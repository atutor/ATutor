<?php
namespace gameme;
use gameme\PHPGamification\DAO;

global $_base_path;
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if($_REQUEST['member_id'] > 0){
    $this_member =$_REQUEST['member_id'];
    $_SESSION['tmp_member'] = $this_member =$_REQUEST['member_id'];
}else{  
    $this_member =$_SESSION['member_id'];
    unset($_SESSION['tmp_member'] );
}
$sql="SELECT course_id FROM %scourse_enrollment WHERE member_id =%d";
$enrolled_courses = queryDB($sql, array(TABLE_PREFIX, $this_member));


$_custom_css = $_base_path . 'mods/_standard/gameme/module.css'; // use a custom stylesheet
//$_custom_head ='<script type="text/javascript" src="'.$_base_path .'jscripts/lib/jquery.1.10.1.min.js"></script>'."\n";
$_custom_head.='<script type="text/javascript" src="'.$_base_path .'mods/_standard/gameme/gamify.js"></script>'."\n";
$_custom_head .= '<script type="text/javascript" src="'.$_base_path.'mods/_standard/gameme/jquery/js.cookie-min.js"></script>'."\n";
require (AT_INCLUDE_PATH.'header.inc.php');

$this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$_base_path);
require_once($this_path.'mods/_standard/gameme/gamify.lib.php');
require_once($this_path.'mods/_standard/gameme/PHPGamification/PHPGamification.class.php');
$gamification = new PHPGamification();
$gamification->setDAO(new DAO(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD));
$gamification->setUserId($_SESSION['member_id']);
?>

<div id="gamify">

<!-- <ul class="tablist" role="tablist" id="game_panel"> -->
<ul id="subnavlist" class="tablist " role="tablist">
<li id="tab1" class="tab" aria-controls="panel1" aria-selected="true" tabindex="0" role="tab"   onclick="javascript:Cookies.set('activetab', 'tab1');">
<?php echo _AT('gm_badges'); ?></li>
<li id="tab2" class="tab" aria-controls="panel2" role="tab"  tabindex="0" aria-selected="false"   onclick="javascript:Cookies.set('activetab', 'tab2');">
<?php echo _AT('gm_levels'); ?></li>
</ul>
<div id="panel1" class="panel" aria-labelledby="tab1" role="tabpanel" aria-hidden="false">
<?php
showUserBadgesStudents($gamification);
?>
</div>

<div id="panel2" class="panel" aria-labelledby="tab2" role="tabpanel" aria-hidden="true">
<?php
showUserLevels($gamification, $_SESSION['course_id']);
?>
</div>
</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>