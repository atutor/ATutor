<?php
namespace gameme;
use gameme\PHPGamification\DAO;

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GAMEME);
require_once(AT_INCLUDE_PATH.'../mods/_standard/gameme/gamify.lib.php');

global $_base_path;
$_custom_css = $_base_path . 'mods/_standard/gameme/module.css'; // use a custom stylesheet
$_custom_head.= '<script type="text/javascript" src="'.$_base_path .'mods/_standard/gameme/gamify.js"></script>'."\n";
$_custom_head .= '<script type="text/javascript" src="'.$_base_path.'mods/_standard/gameme/inline_edit/jquery-quickedit.js"></script>'."\n";
$_custom_head .= '<script type="text/javascript" src="'.$_base_path.'mods/_standard/gameme/jquery/js.cookie-min.js"></script>'."\n";
$_custom_head .= '<script type="text/javascript" src="'.$_base_path.'mods/_standard/gameme/dropzone.js"></script>'."\n";
$_custom_head .= '<script type="text/javascript" src="'. $_base_path.'mods/_standard/gameme/inline_edit/jquery-quickedit.js"></script>'."\n";

 $_custom_head.='  
	<script type="text/javascript">
		function showEdit(editableObj) {
			//$(editableObj).css("background","#eee");
		} 
		
		function saveEvent(editableObj,column,id) {
		cellvalue = editableObj.innerText;
		cellvalue = cellvalue.replace(/<br>/g,"");
			//$(editableObj).css("background","#FFF url('.$_base_path.'mods/_standard/gameme/images/loaderIcon.gif) no-repeat right");
			$.ajax({
				url: "'.$_base_path.'mods/_standard/gameme/save_event.php",
				type: "POST",
				data:"column="+column+"&editval="+cellvalue+"&id="+id,
				success: function(data){
					$(editableObj).css("background","#FDFDFD");
				},
				error: function(data){
				    //console.log(data);
				}        
		   });
		}
		function saveBadge(editableObj,column,id) {
		cellvalue = editableObj.innerText;
		cellvalue = cellvalue.replace(/<br>/g,"");
			$(editableObj).css("background","#FFF url('.$_base_path.'mods/_standard/gameme/images/loaderIcon.gif) no-repeat right");
			$.ajax({
				url: "'.$_base_path.'mods/_standard/gameme/save_badge.php",
				type: "POST",
				data:"column="+column+"&editval="+cellvalue+"&id="+id,
				success: function(data){
					$(editableObj).css("background","#FDFDFD");
				},
				error: function(data){
				    //console.log(data);
				}        
		   });
		   }
        function saveLevel(editableObj,column,id) {
		cellvalue = editableObj.innerText;
		cellvalue = cellvalue.replace(/<br>/g,"");
			$(editableObj).css("background","#FFF url('.$_base_path.'mods/_standard/gameme/images/loaderIcon.gif) no-repeat right");
			$.ajax({
				url: "'.$_base_path.'mods/_standard/gameme/save_level.php",
				type: "POST",
				data:"column="+column+"&editval="+cellvalue+"&id="+id,
				success: function(data){
					$(editableObj).css("background","#FDFDFD");
				},
				error: function(data){
				    //console.log(data);
				}        
		   });
		}
        function performKeyPress(elemId) {
           var elem = document.getElementById(elemId);
           if(elem && document.createEvent) {
              var evt = document.createEvent("KeyboardEvent");
              evt.initEvent("keypress", true, false);
              elem.dispatchEvent(evt);
           }
           
        }
		</script>';
require (AT_INCLUDE_PATH.'header.inc.php');
global $_base_path;
$this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$_base_path);

?>
<ul class="tablist " role="tablist" id="subnavlist">
<li id="tab1" class="tab" aria-controls="panel1" aria-selected="true" tabindex="0" role="tab"  onclick="javascript:Cookies.set('activetab', 'tab1');">
<?php echo _AT('gm_events'); ?></li>
<li id="tab2" class="tab" aria-controls="panel2" role="tab"  tabindex="0" aria-selected="false" onclick="javascript:Cookies.set('activetab', 'tab2');">
<?php echo _AT('gm_badges'); ?></li>
<li id="tab3" class="tab" aria-controls="panel3" role="tab"  tabindex="0" aria-selected="false"  onclick="javascript:Cookies.set('activetab', 'tab3');">
<?php echo _AT('gm_levels'); ?></li>
<li id="tab4" class="tab" aria-controls="panel4" role="tab"  tabindex="0" aria-selected="false"  onclick="javascript:Cookies.set('activetab', 'tab4');">
<?php echo _AT('gm_options'); ?></li>
<li id="tab5" class="tab" aria-controls="panel5" role="tab"  tabindex="0" aria-selected="false"  onclick="javascript:Cookies.set('activetab', 'tab5');">
<?php echo _AT('gm_progress'); ?></li>
</ul>


<div id="panel1" class="panel" aria-labelledby="tab1" role="tabpanel" aria-hidden="false">
<?php  if(!$_config['instructor_edit']){ ?>
<?php $msg->printInfos('GM_COPY_EVENT'); ?>
<h3><?php echo _AT('gm_course_events'); ?></h3>
<table class="table table-hover table-bordered col-sm-12 data">
<tr>
<th><?php echo _AT('gm_alias'); ?></th>
<th><?php echo _AT('gm_description'); ?></th>
<th><?php echo _AT('gm_repetition'); ?></th>
<th><?php echo _AT('gm_reach_reps'); ?></th>
<th><?php echo _AT('gm_max_points'); ?></th>
<th><?php echo _AT('gm_each_badge'); ?></th>
<th><?php echo _AT('gm_reach_badge'); ?></th>
<th><?php echo _AT('gm_each_points'); ?></th>
<th><?php echo _AT('gm_reach_points'); ?></th>
<th><?php echo _AT('gm_reach_message'); ?></th>
<th></th>
</tr>
<?php
$sql = "SELECT * from %sgm_events WHERE course_id = %d";
$all_crs_events = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
    if(!empty($all_crs_events)){
        foreach($all_crs_events as $key=>$event){
            echo '<tr>
            <td>'.$event['alias'].'</td>
            <td contenteditable="true" onBlur="saveEvent(this,\'description\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['description'].'</td>
    
            <td style="text-align:center;" contenteditable="true" onBlur="saveEvent(this,\'allow_repetitions\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['allow_repetitions'].'</td>
            <td style="text-align:center;" contenteditable="true" onBlur="saveEvent(this,\'reach_required_repetitions\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['reach_required_repetitions'].'</td>
            <td style="text-align:center;" contenteditable="true" onBlur="saveEvent(this,\'max_points\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['max_points'].'</td>
            <td style="text-align:center;" contenteditable="true" onBlur="saveEvent(this,\'id_each_badge\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['id_each_badge'].'</td>
            <td style="text-align:center;" contenteditable="true" onBlur="saveEvent(this,\'id_reach_badge\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['id_reach_badge'].'</td>
            <td style="text-align:center;" contenteditable="true" onBlur="saveEvent(this,\'each_points\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['each_points'].'</td>
            <td style="text-align:center;" contenteditable="true" onBlur="saveEvent(this,\'reach_points\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['reach_points'].'</td>
             <td contenteditable="true" onBlur="saveEvent(this,\'reach_message\',\''.$event['id'].' \')" onClick="showEdit(this);">'.get_reach_message($event['alias']).'</td>
            <!--<td  contenteditable="true" onBlur="saveEvent(this,\'each_callback\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['each_callback'].'</td>
            <td contenteditable="true" onBlur="saveEvent(this,\'reach_callback\',\''.$event['id'].' \')" onClick="showEdit(this);">'.$event['reach_callback'].'</td>-->
            <td><!--<a href="mods/_standard/gameme/edit_event.php?id='.$event['id'].SEP.'course_id = '.$_SESSION['course_id'].'">'._AT('gm_edit').'</a>--> 
            <a href="mods/_standard/gameme/delete_event.php?id='.$event['id'].SEP.'course_id='.$_SESSION['course_id'].'">'._AT('gm_remove').'</a></td>
            </tr>'."\n";
            }
    } else {
        echo '<tr><td colspan="12">'._AT('gm_no_course_events_yet').'</td></tr>';
    }
?>
</table>
<?php } else {

    $msg->printInfos('GM_EDITING_DISABLED');
    
}?>
<h3><?php echo _AT('gm_default_events'); ?></h3>
<table class="table table-hover table-bordered col-sm-12 data">
<tr>
<th><?php echo _AT('gm_alias'); ?></th>
<th><?php echo _AT('gm_description'); ?></th>
<th><?php echo _AT('gm_repetition'); ?></th>
<th><?php echo _AT('gm_reach_reps'); ?></th>
<th><?php echo _AT('gm_max_points'); ?></th>
<th><?php echo _AT('gm_each_badge'); ?></th>
<th><?php echo _AT('gm_reach_badge'); ?></th>
<th><?php echo _AT('gm_each_points'); ?></th>
<th><?php echo _AT('gm_reach_points'); ?></th>
<th><?php echo _AT('gm_reach_message'); ?></th>
<?php  if(!$_config['instructor_edit']){ ?>
<th></th>
<?php } ?>
</tr>

<?php
$sql = "SELECT * from %sgm_events WHERE course_id=0";
$all_events = queryDB($sql, array(TABLE_PREFIX));
$count = 0;
foreach($all_events as $key=>$event){
    $sql = "SELECT * from %sgm_events WHERE id = %d AND course_id=%d";
    $events_crs_exists = queryDB($sql, array(TABLE_PREFIX, $event['id'], $_SESSION['course_id']));
    if(empty($events_crs_exists)){
        echo '<tr>
        <td>'.$event['alias'].'</td>
        <td>'.$event['description'].'</td>
        <td style="text-align:center;">'.$event['allow_repetitions'].'</td>
        <td style="text-align:center;">'.$event['reach_required_repetitions'].'</td>
        <td style="text-align:center;">'.$event['max_points'].'</td>
        <td style="text-align:center;">'.$event['id_each_badge'].'</td>
        <td style="text-align:center;">'.$event['id_reach_badge'].'</td>
        <td style="text-align:center;">'.$event['each_points'].'</td>
        <td style="text-align:center;">'.$event['reach_points'].'</td>
        <td>'.get_reach_message($event['alias']).'</td>';
         if(!$_config['instructor_edit']){ 
        echo '<td><a href="mods/_standard/gameme/copy_event.php?id='.$event['id'].SEP.'course_id = '.$_SESSION['course_id'].'">'._AT('gm_copy').'</a></td>'."\n";
        }
    }
    echo'    </tr>';    
}
?>
</table>
</div>

<div id="panel2" class="panel" aria-labelledby="tab2" role="tabpanel" aria-hidden="true">
<?php
    if(!$_config['instructor_edit']){
    $msg->printInfos('GM_COPY_BADGE');
    ?>
    <h3><?php echo _AT('gm_course_badges'); ?></h3>
    <table class="table table-hover table-bordered col-sm-12 data" style="max-width:100%;">
    <tr>
    <th><?php echo _AT('gm_badge'); ?></th>
    <th><?php echo _AT('gm_id'); ?></th>
    <th><?php echo _AT('gm_alias'); ?></th>
    <th><?php echo _AT('gm_title'); ?></th>
    <th><?php echo _AT('gm_description'); ?></th>
    <th></th>

    </tr>
    <?php

    $sql = "SELECT * from %sgm_badges WHERE course_id=%d";
    $all_badges = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

    if(!empty($all_badges)){
        foreach($all_badges as $badge){
            $badge_file_name = explode("/",$badge['image_url']);
            array_shift($badge_file_name );
            $badge_path = implode("/",$badge_file_name);
            if(strstr($badge['image_url'], "content")){
                $badge_file_array = explode('/',$badge['image_url']);
                if($badge_file_array[1] == 0){
                    $custom_default = TRUE;
                } elseif($badge_file_array[1] >= 1){
                    $custom_course = TRUE;
                }
                array_shift($badge_file_array);
                $badge_file_stem = implode('/',$badge_file_array);

                if(is_file(AT_CONTENT_DIR.$badge_file_stem)){
                    if($custom_course){
                        // Course Badge
                        if(is_file($_base_href.'get.php/gameme/badges/'.end($badge_file_array))){
                            $badge_file = $_base_href.'get.php/gameme/badges/'.end($badge_file_array);
                        }else{
                            // get.php breaks above for php7.2, fallback here when get.php fails
                            $badge_file = $_base_href.'content/'.$_SESSION['course_id'].'/gameme/badges/'.end($badge_file_array);
                        }
                    } else  if($custom_default){
                        //Custom Default
                        $badge_file = $_base_href.'content/0/gameme/badges/'.end($badge_file_array);
                    } else{
                        // Default Badge
                        $badge_file = $_base_href.'mods/_standard/gameme/get_badge_icon.php?badge_id='.$badge['id'];
                    }
                }
            } else{
                $badge_file = $_base_href.$badge['image_url'];
                // Not a course badge, so check for custom system badge
                $sql = "SELECT image_url, description FROM %sgm_badges WHERE id=%d AND course_id=%d";
                $badge_image = queryDB($sql, array(TABLE_PREFIX, $badge['id'], 0), TRUE);
                $badge_file_array = explode('/',$badge_image['image_url']);
                array_shift($badge_file_array);

                // get the custom admin created icon
                if(is_file(AT_CONTENT_DIR.'0/gameme/badges/'.end($badge_file_array))){
                    // Custom Default Badge
                    $badge_file = $_base_href.'mods/_standard/gameme/get_badge_icon.php?badge_id='.$badge['id'];
                } else{
                    // Default Badge
                    $badge_file = $_base_href.$badge['image_url'];
                }
            
            }        
        
            echo '<tr>
            <td contenteditable="true" onClick="showEdit(this);"><form action="'.$_base_href.'mods/_standard/gameme/upload_badge.php"
              class="dropzone"
              id="badge'.$badge['id'].'"  style="background-image:url('.$badge_file.');background-repeat:no-repeat;" method="post" tabindex="0">
              <input type="hidden" name="course_id" value="'.$_SESSION['course_id'].'" /> 
              <input type="hidden" name="badge_id" value="'.$badge['id'].' " />
              <div class="fallback">
                <input name="file" type="file" />
                </div>
                </form></td>
            <td>'.$badge['id'].'</td>
            <td>'.$badge['alias'].'</td>
            <td contenteditable="true" onBlur="saveBadge(this,\'title\',\''.$badge['id'].' \')" onClick="showEdit(this);">'.$badge['title'].'</td>
            <td contenteditable="true" onBlur="saveBadge(this,\'description\',\''.$badge['id'].' \')" onClick="showEdit(this);">'.$badge['description'].'</td>

            <td> <a href="mods/_standard/gameme/delete_badge.php?id='.$badge['id'].SEP.'course_id='.$_SESSION['course_id'].'">'._AT('gm_remove').'</a></td></td>
            </tr>'."\n";
        }
    } else{
        echo '<tr><td colspan="6">'._AT('gm_no_course_badges_yet').'</td></tr>';
    }
    ?>
    </table>
    <?php } else {
    $msg->printInfos('GM_EDITING_DISABLED');    
}?>

<h3><?php echo _AT('gm_default_badges'); ?></h3>
<table class="table table-hover table-bordered col-sm-12 data" style="max-width:100%;">
<tr>
<th><?php echo _AT('gm_badge'); ?></th>
<th><?php echo _AT('gm_id'); ?></th>
<th><?php echo _AT('gm_alias'); ?></th>
<th><?php echo _AT('gm_title'); ?></th>
<th><?php echo _AT('gm_description'); ?></th>
<?php
         if(!$_config['instructor_edit']){ ?>
<th></th>
<?php } ?>
</tr>
<?php
$sql = "SELECT * from %sgm_badges WHERE course_id=0";
$all_badges = queryDB($sql, array(TABLE_PREFIX));

foreach($all_badges as $badge){

    $sql = "SELECT * from %sgm_badges WHERE id = %d AND course_id=%d";
    $badges_crs_exists = queryDB($sql, array(TABLE_PREFIX, $badge['id'], $_SESSION['course_id']));
    if(empty($badges_crs_exists)){
    echo '<tr>
        <td>'.getBadgeImage($badge['id']).'</td>
        <td>'.$badge['id'].'</td>
        <td>'.$badge['alias'].'</td>
        <td>'.$badge['title'].'</td>
        <td>'.$badge['description'].'</td>';
         if(!$_config['instructor_edit']){ 
        echo '<td><a href="mods/_standard/gameme/copy_badge.php?id='.$badge['id'].SEP.'course_id = '.$_SESSION['course_id'].'">'._AT('gm_copy').'</a></td>';
        }
        echo '</tr>'."\n";
    }
}
?>
</table>
</div>

<div id="panel3" class="panel" aria-labelledby="tab3" role="tabpanel" aria-hidden="true">
<?php
if(!$_config['instructor_edit']){ 
$msg->printInfos('GM_COPY_DEFAULT_LEVELS');
?>
<h3><?php echo _AT('gm_course_levels'); ?></h3>
<table class="table table-hover table-bordered col-sm-12 data">
<tr>
<th><?php echo _AT('gm_icon'); ?></th>
<th><?php echo _AT('gm_level_name'); ?></th>
<th><?php echo _AT('gm_description'); ?></th>
<th><?php echo _AT('gm_points_threshold'); ?></th>
<th></th>

</tr>
<?php
$sql = "SELECT * from %sgm_levels WHERE course_id=%d";
$all_levels = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
if(!empty($all_levels)){
    foreach($all_levels as $level){
        echo '<tr>
        <td><form action="'.$_base_href.'mods/_standard/gameme/upload_level_icon.php"
              class="dropzone"
              id="level'.$level['id'].'"  style="background-image:url('.star_file($level['id']).');background-repeat:no-repeat;background-position: center; " method="post"  tabindex="0">
            <input type="hidden" name="course_id" value="'.$_SESSION['course_id'].'" /> 
            <input type="hidden" name="level_id" value="'.$level['id'].' " />
            <div class="fallback">
                <input name="file" type="file" id="theFile" onclick="performKeyPress(\'theFile\')";/>
            
            </div>
            </form></td>
        <td contenteditable="true" onBlur="saveLevel(this,\'title\',\''.$level['id'].' \')" onClick="showEdit(this);">'.$level['title'].'</td>
        <td contenteditable="true" onBlur="saveLevel(this,\'description\',\''.$level['id'].' \')" onClick="showEdit(this);">'.$level['description'].'</td>
        <td contenteditable="true" onBlur="saveLevel(this,\'points\',\''.$level['id'].' \')" onClick="showEdit(this);">'.$level['points'].'</td>
        <td><a href="mods/_standard/gameme/delete_level.php?id='.$level['id'].SEP.'course_id = '.$_SESSION['course_id'].'">'._AT('gm_remove').'</a></td>
        </tr>'."\n";
    }
} else {
        echo '<tr><td colspan="5">'._AT('gm_no_course_levels_yet').'</td></tr>';
}
?>
</table>
<?php } else {
    $msg->printInfos('GM_EDITING_DISABLED');  
}?>
<h3><?php echo _AT('gm_default_levels'); ?></h3>

<table class="table table-hover table-bordered col-sm-12 data">
<tr>
<th><?php echo _AT('gm_icon'); ?></th>
<th><?php echo _AT('gm_level_name'); ?></th>
<th><?php echo _AT('gm_description'); ?></th>
<th><?php echo _AT('gm_points_threshold'); ?></th>
<th></th>

</tr>
<?php
$sql = "SELECT `value` from %sgm_options WHERE `course_id`=%d AND `gm_option`='%s'";
if($level_max = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], "level_count"),TRUE)){
    if($level_max['value']  >0){
        $limit = " LIMIT ".$level_max['value'];
    }
}

$sql = "SELECT * from %sgm_levels WHERE course_id =%d $limit";
$all_levels = queryDB($sql, array(TABLE_PREFIX, 0));

foreach($all_levels as $level){
    $levels_crs_exists ='';

    $sql = "SELECT * from %sgm_levels WHERE id = %d AND course_id=%d";
    $level_crs_exists = queryDB($sql, array(TABLE_PREFIX, $level['id'], $_SESSION['course_id']), TRUE);
    
    if(empty($level_crs_exists)){
    echo '<tr>
        <td>'.showstar($level['points']).'</td>
        <td>'.$level['title'].'</td>
        <td>'.$level['description'].'</td>
        <td>'.$level['points'].'</td>';
    if(!$_config['instructor_edit']){ ?>
        <td><a href="mods/_standard/gameme/copy_level.php?id=<?php echo $level['id'].SEP;?>course_id =<?php echo $_SESSION['course_id']; ?>"><?php echo _AT('gm_copy'); ?></a></td>
    <?php }
        echo '</tr>'."\n";
    }
}
?>
</table>
</div>

<div id="panel4" class="panel" aria-labelledby="tab4" role="tabpanel" aria-hidden="true">
    <?php  $msg->printInfos('GM_DISPLAY_ELEMENTS');  ?>
    <br style="clear:both;">
    <form action="<?php echo  $_base_href; ?>mods/_standard/gameme/game_options.php" method="post">
        <input type="checkbox" name="showpoints" id="showpoints" <?php if(get_option('showpoints', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showpoints"><?php echo _AT('gm_points'); ?></label><br />
        <input type="checkbox" name="showlog" id="showlog" <?php if(get_option('showlog', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showlog"><?php echo _AT('gm_log'); ?></label><br />
        <input type="checkbox" name="showlevels" id="showlevels" <?php if(get_option('showlevels', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showlevels"><?php echo _AT('gm_levels'); ?></label><br />
        <input type="checkbox" name="showprogress" id="showprogress" <?php if(get_option('showprogress', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showprogress"><?php echo _AT('gm_progress_to_next2'); ?></label><br />
        <input type="checkbox" name="showposition" id="showposition" <?php if(get_option('showposition', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showposition"><?php echo _AT('gm_position'); ?></label><br />
        <input type="checkbox" name="showbadges" id="showbadges" <?php if(get_option('showbadges', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showbadges"><?php echo _AT('gm_badges'); ?></label><br />
        <input type="checkbox" name="showinstructor" id="showinstructor" <?php if(get_option('showinstructor', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showinstructor"><?php echo _AT('gm_show_instructor'); ?></label><br />
        <input type="checkbox" name="showleaders" id="showleaders" <?php if(get_option('showleaders', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showleaders"><?php echo _AT('gm_leader_board'); ?></label><br />
        <input type="checkbox" name="showalerts" id="showalerts" <?php if(get_option('showalerts', $_SESSION['course_id'])){ echo 'checked = "checked"';}?>/>
        <label for="showalerts"><?php echo _AT('gm_alerts'); ?></label><br />
        <select name="showleader_count" id="showleader_count">
        <?php
            // create an array with possible leader board lengths
            $leader_counts = array('1','3','5','10','15','20','25');
            $sql = "SELECT * from %sgm_options WHERE course_id=%d";
            $gm_course_options = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

            foreach($gm_course_options as $option=>$value){
                if($value['gm_option'] == "showleader_count"){
                    $option_selected = $value['value'];
                }
            }
            foreach($leader_counts as $leader_count){
                if($leader_count == $option_selected){
                    echo '<option value="'.$leader_count.'" selected="selected">'.$leader_count.'</option>';
                }else{
                    echo '<option value="'.$leader_count.'">'.$leader_count.'</option>';
                }
            }
        ?>
        </select>
        <label for="show_leaders"><?php echo _AT('gm_leader_length'); ?></label><br />
        <select name="level_count" id="level_count">
        <?php
            $level_counts = array('1','2','3','4','5','6','7','8','9','10', '11');
            $sql = "SELECT * from %sgm_options WHERE course_id=%d";
            $gm_course_options = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
            
            foreach($gm_course_options as $option=>$value){
                if($value['gm_option'] == "level_count"){
                    $option_selected = $value['value'];
                }
            }
            foreach($level_counts as $level_count){
                if($level_count == $option_selected){
                    echo '<option value="'.$level_count.'" selected="selected">'.$level_count.'</option>';
                }else{
                    echo '<option value="'.$level_count.'">'.$level_count.'</option>';
                }
            }
        ?>
        </select>
        
        <label for="level_count"><?php echo _AT('gm_level_number'); ?></label></br />
        <input type="submit" name="submit" value="Update Options">
    </form>
    </div>
<?php
function get_option($option, $course_id){
    $sql = "SELECT * FROM %sgm_options WHERE `course_id` = %d AND `gm_option` = '%s'";
    $option_set = queryDB($sql, array(TABLE_PREFIX, $course_id, $option), TRUE);
    
    if(!empty($option_set) && $option_set['value'] >0){
        return true;
    }else{
        return false;
    }
}
?>
<div id="panel5" class="panel" aria-labelledby="tab5" role="tabpanel" aria-hidden="true">
<h3><?php echo _AT('gm_progress'); ?></h3>
<?php
$sql = "SELECT %scourse_enrollment.member_id, %smembers.login 
            FROM %scourse_enrollment 
            INNER JOIN %smembers on %scourse_enrollment.member_id = %smembers.member_id 
            WHERE %scourse_enrollment.course_id = %d";
$students = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX,TABLE_PREFIX,TABLE_PREFIX,$_SESSION['course_id']));

$msg->printInfos('GM_SEE_PROGRESS'); 
?>
<hr style="clear:both; width:99%;" />
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="input-form">
<select name="member_id">
<?php
foreach($students as $student){
    $selected='';
    if($student['member_id'] == $_POST['member_id']){
        $selected = ' selected="selected"';
    } 
    echo '<option value="'.$student['member_id'].'" '.$selected.'>'.$student['login'].'</option>'."\n";
}
?>
</select>
<input type="submit" name="submit" value="<?php echo _AT('gm_view'); ?>" />
</form>


<?php
if(!empty($_POST['member_id'])){

    echo '<h3>'._AT('gm_progress_for', get_display_name($_POST['member_id'])).'</h3>';
    global $_base_path;
    $sql = "SELECT * FROM %sgm_options WHERE course_id=%d";
    $gm_options = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

    $count = 0;
    foreach($gm_options as $option => $value){
        $enabled_options[$count] = $value['option'];
        $count++;
    }

    require_once($this_path.'mods/_standard/gameme/PHPGamification/PHPGamification.class.php');
    $course_game = new PHPGamification();
    $course_game->setDAO(new DAO(DB_HOST, DB_NAME, DB_USER, DB_PASSWORD));
    $course_game->setUserId($_POST['member_id']);
   
    $this_path =  preg_replace ('#/get.php#','',$_SERVER['DOCUMENT_ROOT'].$_base_path);
    //require_once($this_path.'mods/_standard/gameme/gamify.lib.php');
    if(in_array('showpoints',$enabled_options)){
        showUserScore($course_game, $_SESSION['course_id']);
    }
    if(in_array('showlevels',$enabled_options)){
        showUserLevel($course_game, $_SESSION['course_id']);
    }
    if(in_array('showprogress',$enabled_options)){
        echo showUserProgress($course_game, $_SESSION['course_id']);
    }
    if(in_array('showbadges',$enabled_options)){
        echo showUserBadge($course_game, $_SESSION['course_id']);
    }
    if(in_array('showleaders',$enabled_options)){
        echo getLeaders($course_game, get_leader_count());
    }
    if(in_array('showposition',$enabled_options)){
        echo yourPosition($_POST['member']);
    }
    
    showUserLog($course_game);

}

?>
</div>
<?php
// not sure what this is doing here
//if(!empty($_POST)){
    //$json = json_encode($_POST);
//}

?>


<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>