<?php
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

tool_origin();

$sql = "SELECT forum_id, title FROM %sforums";
$rows_forums = queryDB($sql, array(TABLE_PREFIX));

if(count($rows_forums) == 0){
    $msg->addInfo('NO_FORUMS');
    require (AT_INCLUDE_PATH.'header.inc.php');
    exit;
}

require (AT_INCLUDE_PATH.'header.inc.php');
?>

<form name='forumExp' action='send_zip_archive.php' method='post'>
    <div class='input-form'>
        <div class='row'>
            <h3><?php echo _AT('farchive_select_forum'); ?></h3>
        </div>
        <div class='row'>
            <select name='selForum' id='selForum'> 
        

                <?php

                $course_id = $_SESSION['course_id'];

                // ---Get course forums for the specific course id

                $courseOptions = "";

                $sql = "SELECT forum_id FROM %sforums_courses WHERE course_id=%d";
                $rows_forums_id = queryDB($sql, array(TABLE_PREFIX, $course_id)); 
                
                foreach($rows_forums_id as $fid){  

                    $sql = "SELECT title FROM %sforums WHERE forum_id=%d";
                    $rows_ftitles = queryDB($sql, array(TABLE_PREFIX, $fid['forum_id']));
                    
                    foreach($rows_ftitles as $row){
                        $courseOptions .= "<option value='".$fid['forum_id']."'>".$row['title']."</option>";
                    }
                }

                if ($courseOptions != "") {
                    print "<optgroup label='Course Forums'>";
                    print $courseOptions;
                    print "</optgroup>";
                }


                // ---Get group forums for the specific course id

                $groupOptions = "";

                // get type_id(s) for this course
                $sql = "SELECT type_id FROM %sgroups_types WHERE course_id=%d";
                $rows_gtypes = queryDB($sql, array(TABLE_PREFIX, $course_id));
                global $sqlout;
    
       
                foreach($rows_gtypes as $type_id){
                    // get group_id(s) for each type
                    $sql = "SELECT group_id, title FROM %sgroups WHERE type_id=%d";
                    $rows_gids = queryDB($sql, array(TABLE_PREFIX, $type_id['type_id']));

                    foreach($rows_gids as $group_id){                    
                        // get forum_id for each group
                        $sql = "SELECT forum_id FROM %sforums_groups WHERE group_id=%d";
                        $rows_fgids = queryDB($sql, array(TABLE_PREFIX, $group_id['group_id']));

                        foreach($rows_fgids as $fid){
                            // get forum title for this fid
                            $sql = "SELECT title FROM %sforums WHERE forum_id=%d";
                            $row = queryDB($sql, array(TABLE_PREFIX, $fid['forum_id']), TRUE);

                            $groupOptions .= "<option value='".$fid['forum_id']."'>".$group_id['title']."</option>";
                        }
                    }
                }

                if ($groupOptions != "") {
                    print "<optgroup label='Group Forums'>";
                    print $groupOptions;
                    print "</optgroup>";
                }

                ?>

            </select>
        </div>
        <div class='row buttons'>
            <input type='hidden' name='forumID' id='forumID' />
            <input type='button' class='button input' id='btnExportFrm' name='btnExportFrm' value='<?php echo _AT('farchive_export'); ?>' onclick='submForm()' />
        </div>
    </div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>

<script language='javascript' type='text/javascript'>

var selSize = document.getElementById('selForum'); 
if (selSize.length+2 < 10) {
    selSize.size = selSize.length+2;
} else {
    selSize.size = 10;
}

function submForm() {
    var frm = document.getElementById('selForum'); 
    var id = frm.options[frm.selectedIndex].value;
    var title = frm.options[frm.selectedIndex].text;
    document.forms[0].action = 'mods/_standard/farchive/send_zip_archive.php?fid='+id+'&title='+title;
    document.forms[0].submit();
}
</script>


