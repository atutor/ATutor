<?php
namespace gameme\PHPGamification;
use Exception;
use gameme\PHPGamification;
use gameme\PHPGamification\Model;
use gameme\PHPGamification\Model\Badge;

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_GAMEME);
$_custom_css = $_base_path . 'mods/_standard/gameme/module.css'; // use a custom stylesheet
//$_custom_head ='<script type="text/javascript" src="'.$_base_path .'jscripts/lib/jquery.1.10.1.min.js"></script>'."\n";
$_custom_head.='<script type="text/javascript" src="'.$_base_path .'mods/_standard/gameme/gamify.js"></script>'."\n";
 $_custom_head.='  
	<script type="text/javascript">
	//<!--
	jQuery.noConflict();
	//-->
	</script>';

if($_POST['cancel']){
$msg->addFeedback('cancelled');
header('Location:'.$_base_href.'mods/_standard/gameme/index_admin.php?tab=3');
exit;
}
if($_POST['submit']){
    global $_base_path;
    // this line is a hack
    $this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$_base_path);
    require_once($this_path.'mods/_standard/gameme/gamify.lib.php');
    require_once($this_path.'mods/_standard/gameme/PHPGamification/PHPGamification.class.php');
    $gamification = new PHPGamification();
    $gamification->setDAO(new DAO(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD));
    
    if(!empty($_POST['title']) && !empty($_POST['alias'])){
        $gamification->addBadge($_POST['alias'], $_POST['title'], $_POST['description']);
        $msg->addFeedback('GM_BADGED_ADDED');
        header('Location:'.$_base_href.'mods/_standard/gameme/index_admin.php?tab=3');
        exit;
    } else {
        $msg->addError("GM_BADGE_REQUIREMENTS");
        //$gamification->addLevel(0, 'No Star');
    }


}
require (AT_INCLUDE_PATH.'header.inc.php');
$sql = "SELECT * FROM %sgm_badges WHERE id = %d";
$this_badge = queryDB($sql, array(TABLE_PREFIX, $_GET['id']), TRUE);
$msg->printInfos('GM_BADGE_PROPERTIES');
?>
<form name="form" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<input type="hidden" id="id" name="id" value="<?php echo $this_badge['id']; ?>" />
<div class="input-form">
<fieldset class="group_form">
<legend class="group_form"><?php echo _AT('gm_edit_badge'); ?></legend>
<span title="required">*</span><label for="alias"><?php echo _AT('gm_alias'); ?></label><br />
<?php
if(!isset($_GET['id'])){?>
    <input type="text" id="alias" name="alias" value="<?php echo $this_badge['alias']; ?>" aria-required="true" />
<?php } else { ?>
    <strong><?php echo $this_badge['alias']; ?></strong>
    <input type="hidden" id="alias" name="alias" value="<?php echo $this_badge['alias']; ?>" />
<?php } ?>

<br /><br />
<label for="title"><span title="required">*</span><?php echo _AT('gm_title'); ?></label><br />
<input type="text" id="title" name="title" value="<?php echo $this_badge['title']; ?>"  size="48" aria-required="true"/><br />
<label for="description"><?php echo _AT('gm_description'); ?></label><br />
<input type="text" id="description" name="description" value="<?php echo $this_badge['poidescriptionnts']; ?>" size="65" /><br />

<input type="submit" name="submit" value="<?php echo _AT('gm_save_badge'); ?>" /><input type="submit" name="cancel" value="<?php echo _AT('gm_cancel'); ?>"/>
</fieldset>
</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>