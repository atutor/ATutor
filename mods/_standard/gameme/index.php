<?php
namespace gameme;
use gameme\PHPGamification\DAO;

global $_base_path;
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
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

$sql = "SELECT `gm_option`, `value` FROM %sgm_options WHERE course_id=%d";
$gm_options = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
$this_options = array();
foreach($gm_options as $gm_option){
    if($gm_option['value'] ==1){
    $this_options[] = $gm_option['gm_option'];
    }
}
?>

<div id="gamify">

<!-- <ul class="tablist" role="tablist" id="game_panel"> -->
<ul id="subnavlist" class="tablist " role="tablist">
<?php if(in_array('showbadges', $this_options)){ ?>
<li id="tab1" class="tab" aria-controls="panel1" aria-selected="true" tabindex="0" role="tab"   onclick="javascript:Cookies.set('activetab', 'tab1');">
<?php echo _AT('gm_badges'); ?></li>
<?php } ?>
<?php if(in_array('showlevels', $this_options)){ ?>
<li id="tab2" class="tab" aria-controls="panel2" role="tab"  tabindex="0" aria-selected="false"   onclick="javascript:Cookies.set('activetab', 'tab2');">
<?php echo _AT('gm_levels'); ?></li>
<?php } ?>
<?php if(in_array('showalerts', $this_options)){ ?>
<li id="tab3" class="tab" aria-controls="panel3" role="tab"  tabindex="0" aria-selected="false"   onclick="javascript:Cookies.set('activetab', 'tab3');">
<?php echo _AT('gm_alerts'); ?></li>
<?php } ?>
<?php if(in_array('showlog', $this_options)){ ?>
<li id="tab4" class="tab" aria-controls="panel4" role="tab"  tabindex="0" aria-selected="false"   onclick="javascript:Cookies.set('activetab', 'tab4');">
<?php echo _AT('gm_log'); ?></li>
<?php } ?>
</ul>
<?php if(in_array('showbadges', $this_options)){ ?>
<div id="panel1" class="panel" aria-labelledby="tab1" role="tabpanel" aria-hidden="false">
<?php
showUserBadgesStudents($gamification);
?>
</div>
<?php } ?>
<?php if(in_array('showlevels', $this_options)){ ?>
<div id="panel2" class="panel" aria-labelledby="tab2" role="tabpanel" aria-hidden="true">
<?php
showUserLevels($gamification, $_SESSION['course_id']);
?>
</div>
<?php } ?>
<?php if(in_array('showalerts', $this_options)){ ?>
<div id="panel3" class="panel" aria-labelledby="tab3" role="tabpanel" aria-hidden="true">
<?php
showUserAlerts($gamification);
showUserEvents($gamification);

?>
</div>
<?php } ?>
<?php if(in_array('showlog', $this_options)){ ?>
<div id="panel4" class="panel" aria-labelledby="tab4" role="tabpanel" aria-hidden="true">
<?php
showUserLog($gamification);
?>
</div>
<?php } ?>

</div>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>