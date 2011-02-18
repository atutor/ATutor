<?php
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_BASICLTI);

require_once('forms.php');
if($_POST['submit']){
	// filter all POST data
	$_POST['form_basiclti'] = $addslashes($_POST['form_basiclti']);
	$_POST['title'] = $addslashes($_POST['title']);
	$_POST['toolid'] = $addslashes($_POST['toolid']);
	$_POST['description'] = $addslashes($_POST['description']);
	$_POST['toolurl'] = $addslashes($_POST['toolurl']);
	$_POST['resourcekey'] = $addslashes($_POST['resourcekey']);
	$_POST['password'] = $addslashes($_POST['password']);
	$_POST['preferheight'] = intval($_POST['preferheight']);
	$_POST['allowpreferheight'] = intval($_POST['allowpreferheight']);
	$_POST['launchinpopup'] = intval($_POST['launchinpopup']);
	$_POST['debuglaunch'] = intval($_POST['debuglaunch']);
	$_POST['sendname'] = intval($_POST['sendname']);
	$_POST['sendemailaddr'] = intval($_POST['sendemailaddr']);
	$_POST['acceptgrades'] = intval($_POST['acceptgrades']);
	$_POST['allowroster'] = intval($_POST['allowroster']);
	$_POST['allowsetting'] = intval($_POST['allowsetting']);
	$_POST['allowcustomparameters'] = intval($_POST['allowcustomparameters']);
	$_POST['customparameters'] = $addslashes($_POST['customparameters']);
	$_POST['organizationid'] = $addslashes($_POST['organizationid']);
	$_POST['organizationurl'] = $addslashes($_POST['organizationurl']);
//	$_POST['organizationdescr'] = $addslashes($_POST['organizationdescr']);
	$_POST['submit'] = $addslashes($_POST['submit']);
}
$tool = intval($_REQUEST['id']);

if (isset($_POST['cancel'])) {
        $msg->addFeedback('CANCELLED');
        header('Location: '.AT_BASE_HREF.'mods/basiclti/index_admin.php');
        exit;
} else if (isset($_POST['form_basiclti'], $tool)) {

    if ( at_form_validate($blti_admin_form, $msg) ) {
        $sql = "SELECT count(*) cnt FROM ".TABLE_PREFIX."basiclti_tools WHERE toolid = '".
                mysql_real_escape_string($_POST['toolid'])."' AND id != $tool;";
        $result = mysql_query($sql, $db) or die(mysql_error());
        $row = mysql_fetch_assoc($result);

        if ($row["cnt"] != 0) {
           $msg->addFeedback('NEED_UNIQUE_TOOLID');
        } else {
            $sql = at_form_update($_POST, $blti_admin_form);
            $sql = 'UPDATE '.TABLE_PREFIX."basiclti_tools SET ".$sql." WHERE id = $tool;";
            $result = mysql_query($sql, $db) or die(mysql_error());
            write_to_log(AT_ADMIN_LOG_INSERT, 'basiclti_create', mysql_affected_rows($db), $sql);
            $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
            header('Location: '.AT_BASE_HREF.'mods/basiclti/index_admin.php');
            exit;
	}
    }
}

$sql = "SELECT * FROM ".TABLE_PREFIX."basiclti_tools WHERE id = ".$tool.";";
$result = mysql_query($sql, $db) or die(mysql_error());
$toolrow = mysql_fetch_assoc($result);
if ( $toolrow['id'] != $tool ) {
    $msg->addFeedback('COULD_NOT_LOAD_TOOL');
    header('Location: '.AT_BASE_HREF.'mods/basiclti/index_admin.php');
    exit;
}

include(AT_INCLUDE_PATH.'header.inc.php');

$msg->printAll();

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];  ?>" name="basiclti_form" enctype="multipart/form-data">
  <input type="hidden" name="form_basiclti" value="true" />
  <input type="hidden" name="id" value="<?php echo $tool; ?>" />
  <div class="input-form">
    <fieldset class="group_form"><legend class="group_form"><?php echo _AT('properties'); ?></legend>
<?php at_form_generate($toolrow, $blti_admin_form); ?>
        <div class="buttons">
                <input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
                <input type="submit" name="cancel" value="<?php echo _AT('cancel');?>" />
        </div>
    </fieldset>
  </div>
</form>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
