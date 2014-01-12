<?php

define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ((isset($_REQUEST['popup']) && $_REQUEST['popup']) && 
    (!isset($_REQUEST['framed']) || !$_REQUEST['framed'])) {
    $popup = TRUE;
    $framed = FALSE;
} elseif (isset($_REQUEST['framed']) && $_REQUEST['framed'] && isset($_REQUEST['popup']) && $_REQUEST['popup']) {
    $popup = TRUE;
    $framed = TRUE;
    $tool_flag = TRUE;
} else {
    $popup = FALSE;
    $framed = FALSE;
}

$_REQUEST['cid'] = intval($_REQUEST['cid']);    //uses request 'cause after 'saved', the cid will become $_GET.

$cid = intval($_REQUEST['cid']);

require(AT_INCLUDE_PATH.'header.inc.php');

$main_links = get_main_navigation($current_page);  // get_main_navigation() is defined in menu_pages.php which is included in header.inc.php

foreach ($main_links as $main) {
    if ($main['title'] == $_REQUEST['tool_for'] && $main['tool_file'] != '') {
        $tool_file = AT_INCLUDE_PATH . '../' . $main['tool_file'];
        break;
    }
}

if ($tool_file) {
    $tool_list = require($tool_file);
}

?>
<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('tools_manager'); ?></legend>
<br/>
<?php echo _AT('tool_man_comment');?>
<br/><br/><br/>
<?php echo $msg->printFeedbacks();

$sql = "SELECT forum_id FROM %scontent_forums_assoc WHERE content_id='%d'";
if(isset($tool_list)) {?>
<form name="datagrid" action="<?php AT_INCLUDE_PATH.'../'.$_REQUEST['tool_file'];?>" method="POST">
    <table class="data" summary="" style="width: 90%">
        <thead>
            <tr>
                <th scope="col" style="width:5%">&nbsp;</th>
                <th scope="col"><?php echo _AT('Title');  ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tool_list as $tool) {
                    $i = $i+1;
                    $rows_forums = queryDB($sql, array(TABLE_PREFIX, $cid));
                   foreach($rows_forums as $row){
                        if($tool['id'] == $row['forum_id']){
                            $checked='checked';
                            break;
                        } else {
                            $checked='';
                        }
                    }
                ?>
            <tr>
                <td valign="top">
                    <input name="check[]" value="<?php echo $tool['id'];?>" id="<?php echo $i; ?>" type="checkbox" <?php echo $checked;?> />
                    &nbsp;<?php echo $files;?>
                </td>
                <td valign="top"><label for="<?php echo $i; ?>"><?php echo $tool['title']; ?></label></td>
            </tr>
                <?php }
        $i=0;?>
        </tbody>
    </table>
    <br /><br /><br />
    <input type="hidden" name="cid" value="<?php echo $cid;?>" />
    <input type="submit" name="save" value="<?php echo _AT('save');?>" class="button" />
    
</form>

<?php } ?>
</fieldset>
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
