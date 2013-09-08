<?php
define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_BASICLTI);

if ( !is_int($_SESSION['course_id']) || $_SESSION['course_id'] < 1 ) {
    $msg->addFeedback('NEED_COURSE_ID');
    exit;
}

require_once('forms.php');

$tool = intval($_REQUEST['id']);

if (isset($_POST['done'])) {
        header('Location: '.AT_BASE_HREF.'mods/_standard/basiclti/index_instructor.php');
        exit;
} 

$sql = "SELECT * FROM %sbasiclti_tools WHERE id = %d AND course_id = %d";
$toolrow = queryDB($sql, array(TABLE_PREFIX, $tool, $_SESSION['course_id']), TRUE);

if ( $toolrow['id'] != $tool ) {
    $msg->addError('UNABLE_TO_FIND_TOOL');
    header('Location: '.AT_BASE_HREF.'mods/_standard/basiclti/index_instructor.php');
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
<?php at_form_view($toolrow, $blti_instructor_form); ?>
        <div class="buttons">
                <input type="submit" name="done" value="<?php echo _AT('done');?>" />
        </div>
    </fieldset>
  </div>
</form>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php');
