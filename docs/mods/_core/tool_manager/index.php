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

$_REQUEST['cid'] = intval($_REQUEST['cid']);	//uses request 'cause after 'saved', the cid will become $_GET.

$cid = intval($_REQUEST['cid']);

require(AT_INCLUDE_PATH.'header.inc.php');

$tool_file= AT_INCLUDE_PATH.'../'.$_REQUEST['tool_file'];	// viene prelevato il path del file necessario per prelevare le informazioni relative ai sottocontenuti
$tool_list = require($tool_file);                            //si richiede la lista ei contenuti per lo strumento. i contenuti trovati potranno essere inseriti all'interno del materiale didattico come collegamento.
?>
<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('tools_manager'); ?></legend>
<br/>
<?php echo _AT('tool_man_comment');?>
<br/><br/><br/>
<?php echo $msg->printFeedbacks();

$sql = "SELECT forum_id FROM ".TABLE_PREFIX."content_forums_assoc WHERE content_id='$cid'";
if(isset($tool_list)) {?>
<form name="datagrid" action="<?php AT_INCLUDE_PATH.'../'.$_REQUEST['tool_file'];?>" method="POST">
    <table class="data" summary="" style="width: 90%" rules="cols">
        <thead>
            <tr>
                <th scope="col" style="width:5%">&nbsp;</th>
                <th scope="col"><?php echo _AT('Title');  ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($tool_list as $tool) {
		    $i = $i+1;
                    $result = mysql_query($sql, $db);
                    while($row = mysql_fetch_assoc($result)){
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
                    <!--<input name='checkAll' type='checkbox' onClick="checkAll();" />-->
                    <input name="check[]" value="<?php echo $tool['id'];?>" id="<?php echo $i; ?>" type="checkbox" <?php echo $checked;?> onClick="chkBoxes();" />
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

<?php }


/*###############################*/
/* added*/
/*##############################*/?>
</fieldset>
</div>
<script language="javascript" type="text/javascript">
    function checkAll() {
        for (var i = 0; i < document.datagrid.check.length; i++) {
            if (document.datagrid.checkall.checked === true) {
                document.datagrid.check[i].checked = true;
            } else {
                document.datagrid.check[i].checked = false;
            }
        }
    }
    
    function chkBoxes() {
        document.datagrid.del_selected.disabled = false;
        var myCheckBoxes = document.datagrid.elements['check[]']; //array di checkboxes
        for (var i = 0; i < myCheckBoxes.length; i++) {
            if (myCheckBoxes[i].checked === true) {
                break;
            }
        }
        if (i === myCheckBoxes.length) {
            document.datagrid.del_selected.disabled = true;
        }
    }
</script>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
