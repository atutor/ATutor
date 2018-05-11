<?php
//namespace gameme;

/* Get a list of gameme leaders in a course
* @$gamificaiton - a PHPGamifcations object
* @$leader_depth - the number of leaders to display
*/
function getLeaders($gamification, $leader_depth){
    echo "<h3>"._AT('gm_leaders_top', $leader_depth)."</h3>";
    $leaders = $gamification->getUsersPointsRanking($leader_depth);
    $sql = "SELECT member_id FROM %scourses WHERE course_id = %d";
    $instructor = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);
    $count = 0;
    $leader_board = '<table  class="data"><tr><th>#</th><th>'._AT('gm_id').'</th><th>'._AT('gm_points').'</th><th>'._AT('gm_level').'</th></tr>';
   if(!empty($leaders)){
    foreach($leaders as $key=>$leader){
        // don't display instructor on leader board
            if($instructor['member_id'] == $leader->id_user){
                if(show_instructor() == 1){
                    $login_name = getLoginName($leader->id_user);
                    $count++;
                    $leader_board .= '<tr>
                    <td>'.$count.'</td>
                    <td>'.$login_name.'</td>
                    <td>'.$leader->points.'</td>
                    <td>'.$leader->id_level.'</td>
                    </tr>'."\n"; 
                }
            } else if($instructor['member_id'] != $leader->id_user){
                $login_name = getLoginName($leader->id_user);
                $count++;
                $leader_board .= '<tr>
                <td>'.$count.'</td>
                <td>'.$login_name.'</td>
                <td>'.$leader->points.'</td>
                <td>'.$leader->id_level.'</td>
                </tr>'."\n"; 
                }
            }  
    }
    $leader_board .='</table>';
    echo $leader_board;
}  
/*


*/
function getLoginName($mid){
    $sql = "SELECT login FROM %smembers WHERE member_id=%d";
    $member_login = queryDB($sql, array(TABLE_PREFIX, $mid), TRUE);
    return $member_login['login'];
}
/*


*/
function yourPosition($mid){
    $sql = "SELECT * FROM %sgm_user_scores WHERE course_id = %d ORDER BY points DESC";
    $leaders_desc = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));
    
    $sql = "SELECT member_id FROM %scourses WHERE course_id = %d";
    $instructor = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);
    
    $count = 0;
    foreach($leaders_desc as $leader){
            // remove instructor from leader board
            if($leader['id_user'] != $instructor['member_id']){
                $count++;
                if($leader['id_user'] == $_SESSION['member_id'] && !$_SESSION['is_admin']){
                    echo "<p>"._AT('gm_in_position' , $count)."</p>"."\n";
                }
            } else{
                if(show_instructor() == 1){
                $count++;
                echo "<p>"._AT('gm_in_position' , $count)."</p>"."\n";
                } else{
                 echo "<p></p>"."\n";
                }
            }
    }
    return $member_login['login'];
}
/*


*/
function showUserScores($gamification){
    echo "<h3>"._AT('gm_levels_awarded')."</h3>";
    $score = $gamification->getUserScores();
    echo "<table class='data'>";
    echo "<tr>
    <th>&nbsp;</th>
    <th>"._AT('gm_level')."</th>
    <th>"._AT('gm_description')."</th>
    </tr>"."\n";
    echo showstars($score->getPoints());
    echo "</table>";
}
/*


*/  
function showUserScore($gamification){
    $score = $gamification->getUserScores();
    if($score->getPoints() == 0){
        echo '<div style="text-align:center;">'._AT('gm_login_for_points').'</div>';
    }
    echo "<div id='gameme_points'
    >"._AT('gm_points').": " . $score->getPoints() . " </div><br /><hr />";
}
/*


*/ 
function showUserLevels($gamification, $course_id)
{
    $score = $gamification->getUserScores();
    echo "<h3>"._AT('gm_your_levels_reached')."</h3>";
    if ($score){
        $sql = "SELECT `value` from %sgm_options WHERE `course_id`=%d AND `gm_option`='%s'";
        if($level_max = queryDB($sql, array(TABLE_PREFIX, $course_id, "level_count"),TRUE)){
            if($level_max['value']  >0){
                $limit = " LIMIT ".$level_max['value'];
            }
        }

         // Get default levels   
        $sql = "SELECT * FROM %sgm_levels WHERE course_id = %d AND points <= %d ORDER BY id asc $limit";
        $levels = queryDB($sql, array(TABLE_PREFIX, 0, $score->getPoints()));
        $level_ids = array();
        foreach($levels as $level){
            array_push($level_ids, $level['id']);
        }
        // Get customized course levels
        $sql = "SELECT * FROM %sgm_levels WHERE course_id = %d AND points <= %d ORDER BY id asc $limit";
        $course_levels = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $score->getPoints()));
        $course_level_ids = array();
    
        foreach($course_levels as $course_level){
            array_push($course_level_ids, $course_level['id']);
        }
        $this_levels = array();
        foreach($course_levels as $course_level){
            if(!in_array($course_level['id'], $this_levels)){
                array_push($this_levels, $course_level);
            }
        }

        foreach($levels as $level){
            if(!in_array($level['id'], $course_level_ids)){
                array_push($this_levels, $level);
            }
        }
        //usort($this_levels, 'sortByOrder');
        krsort($this_levels);
        echo "<table class='data'>
        <tr>
        <th>"._AT('gm_level')."</th>
        <th>"._AT('gm_title')."</th>
        <th>"._AT('gm_description')."</th>
        <th>"._AT('gm_points')."</th>
        </tr>";

        foreach($this_levels as $level){
                echo "<tr>";
                echo "<td>".showstar($level['points'])."</td>";
                echo "<td>".$level['title']."</td>";
                echo "<td>".$level['description']."</td>";
                echo "<td>".$level['points']."</td>";            
                echo "</tr>"."\n";
        }
        echo "</table>";
    }
}

/*


*/
function showUserLevel($gamification, $courseId){

$score = $gamification->getUserScores();
echo "<h3>"._AT('gm_levels_awarded')."</h3>
    <div style='background-color:#f6f6f6; width:100%; padding:.2em;margin-left:auto;margin-right:auto;' >".showstars($score->getPoints())."</div>";
    
}

/*


*/
function showUserProgress($gamification, $courseId){
$score = $gamification->getUserScores();
 return "<br >"._AT('gm_progress_to_next', $score->getProgress())."<hr />";
}

function sortByOrder($a, $b) {
     return $a['points'] - $b['points'];
}
/*
* Strings stars together, laid out in Levels in the sidemenu block
* @param $points - the user's current points scored
*/
function showstars($points){
    global $_base_href;
    
    $sql = "SELECT `value` from %sgm_options WHERE `course_id`=%d AND `gm_option`='%s'";
        if($level_max = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], "level_count"),TRUE)){
            if($level_max['value']  >0){
                $limit = " LIMIT ".$level_max['value'];
            }
        }
     // Get default levels   
    $sql = "SELECT * FROM %sgm_levels WHERE course_id = %d AND points <= %d ORDER BY id asc $limit";
    $levels = queryDB($sql, array(TABLE_PREFIX, 0, $points));
    $level_ids = array();
    foreach($levels as $level){
        array_push($level_ids, $level['id']);
    }
    // Get customized course levels
    $sql = "SELECT * FROM %sgm_levels WHERE course_id = %d AND points <= %d ORDER BY id asc $limit";
    $course_levels = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $points));
    $course_level_ids = array();
    
    foreach($course_levels as $course_level){
        array_push($course_level_ids, $course_level['id']);
    }
    
    $this_levels = array();
    foreach($course_levels as $course_level){
        if(!in_array($course_level['id'], $this_levels)){
            array_push($this_levels, $course_level);
        }
    }

    foreach($levels as $level){
        if(!in_array($level['id'], $course_level_ids)){
            array_push($this_levels, $level);
        }
    }
    krsort($this_levels);
    //usort($this_levels, 'sortByOrder');

    $content_dir = explode('/',AT_CONTENT_DIR);
    array_pop($content_dir);
    
    foreach($this_levels as $level){
        if(in_array($level, $this_levels)){
            $course_id = $_SESSION['course_id'];
        } else{
            $course_id = 0;
        }
        $sql = "SELECT id, icon, title, description FROM %sgm_levels WHERE id=%d AND course_id=%d";
        $level_image = queryDB($sql, array(TABLE_PREFIX, $level['id'],$_SESSION['course_id']), TRUE);

        if(!empty($level_image['icon'])){
            if(is_file(AT_CONTENT_DIR.$_SESSION['course_id'].'/gameme/levels/'.$level_image['icon'])){
                 //$level_file = $_base_href.'get.php/gameme/levels/'.$level_image['icon'];
                 $level_file = $_base_href.'content/'.$_SESSION['course_id'].'/gameme/levels/'.$level_image['icon'];
            }else if(is_file(AT_CONTENT_DIR.'0/gameme/levels/'.$level_image['icon'])){
                $level_file = $_base_href.'mods/_standard/gameme/get_level_icon.php?level_id='.$level_image['id'];
            }else {
                $level_file = $_base_href.'mods/_standard/gameme/images/'.$level_image['icon'];
            }
        } else {
            if(!in_array($level['id'] , $course_levels)){
                $sql = "SELECT id, icon, title, description FROM %sgm_levels WHERE id=%d AND course_id=%d";
                $level_image = queryDB($sql, array(TABLE_PREFIX, $level['id'],0), TRUE);
                if(!is_file(AT_CONTENT_DIR.'0/gameme/levels/'.$level_image['icon'])){
                    $level_file = $_base_href.'mods/_standard/gameme/images/'.$level_image['icon'];
                } else {
                    $content_dir = explode('/',AT_CONTENT_DIR);
                    array_pop($content_dir);
                    $level_file = $_base_href.'mods/_standard/gameme/get_level_icon.php?level_id='.$level_image['id'];
                }
            }
        }
        $stars .= '<img src="'.$level_file.'" alt="'.$level_image['title'].'" title="'.$level_image['title'].' '.$level_image['description'].'" style="margin:.2em;"/>'."\n";
    }
    
    return $stars;
}
/*
* Show the star associated with a particular point value
* @param $points
* @return html img tag for the star/level
*/
function showstar($points){
    global $_base_href; 
    $sql = "SELECT id, icon, description FROM %sgm_levels WHERE points=%d AND course_id=%d";
    $level_image = queryDB($sql, array(TABLE_PREFIX, $points,$_SESSION['course_id']), TRUE);
    //$level_file = star_file($level_image['id']);
        if(!empty($level_image['icon'])){
             $level_file = star_file($level_image['id']);
        } else {
            $sql = "SELECT id, icon, description FROM %sgm_levels WHERE points=%d AND course_id=%d";
            $level_image = queryDB($sql, array(TABLE_PREFIX, $points,0), TRUE);
            $level_file = star_file($level_image['id']);
        }
    return '<img src="'.$level_file.'" alt="'.$level_image['description'].'" title="'.$level_image['description'].'" style="margin:.2em;"/>'."\n"; 
}

function star_file($id){
    global $_base_href;
    $sql = "SELECT icon, description FROM %sgm_levels WHERE id=%d AND course_id=%d";
    $level_image = queryDB($sql, array(TABLE_PREFIX,$id,$_SESSION['course_id']), TRUE);

        if(!empty($level_image['icon'])){
            if(is_file(AT_CONTENT_DIR.$_SESSION['course_id'].'/gameme/levels/'.$level_image['icon'])){
                // Course level
                //$level_file = $_base_href.'get.php/gameme/levels/'.$level_image['icon'];
                $level_file = $_base_href.'content/'.$_SESSION['course_id'].'/gameme/levels/'.$level_image['icon'];
            }else {
                $level_file = $_base_href.'mods/_standard/gameme/images/'.$level_image['icon'];
            }
        } else {
            $sql = "SELECT icon, description FROM %sgm_levels WHERE id=%d AND course_id=%d";
            $level_image = queryDB($sql, array(TABLE_PREFIX, $id,0), TRUE);

            if(is_file(AT_CONTENT_DIR.'0/gameme/levels/'.$level_image['icon'])){
                // Custom default level
                $level_file = $_base_href.'mods/_standard/gameme/get_level_icon.php?level_id='.$id;
            } else{
                // Default Level
                $level_file = $_base_href.'mods/_standard/gameme/images/'.$level_image['icon'];
            }
        }
    return $level_file;
}

/*
*
*
*/
function showUserBadges($gamification)
{
    echo "<h3>"._AT('gm_manage_badges')."</h3>";
    $badges = $gamification->getUserBadges();
    if (!empty($badges)){
        echo "<table class='data'>
        <tr>
        <th>&nbsp;</th>
        <th>"._AT('gm_badge_id')."</th>
        <th>"._AT('gm_counter')."</th>
        <th>"._AT('gm_alias')."</th>
        <th>"._AT('gm_description')."</th>
        </tr>";
        foreach ($badges as $badge) {
            echo "<tr>";
            echo "<td>".getBadgeImage($badge->getIdBadge())."</td>";
            echo "<td>".$badge->getIdBadge()."</td>";
            echo "<td>".$badge->getBadgeCounter()."</td>";
            echo "<td>".$badge->getBadge()->getAlias()."</td>";
            echo "<td>".$badge->getBadge()->getDescription()."</td>";            
            echo "</tr>"."\n";
        }
        echo "</table>";
    } else{
        echo "No badges yet.";
    }
}
function showUserBadgesStudents($gamification)
{
    echo "<h3>"._AT('gm_your_badges')."</h3>";
    $badges = $gamification->getUserBadges();
    if ($badges){
        echo "<table class='data'>
        <tr>
        <th>&nbsp;</th>
        <!--<th>"._AT('gm_counter')."</th>-->
        <th>"._AT('gm_title')."</th>
        <th>"._AT('gm_description')."</th>
        </tr>";
        foreach ($badges as $badge) {
            echo "<tr>";
            echo "<td>".getBadgeImage($badge->getIdBadge())."</td>";;
            //echo "<td>".$badge->getBadgeCounter()."</td>";
            echo "<td>".$badge->getBadge()->getTitle()."</td>";
            echo "<td>".$badge->getBadge()->getDescription()."</td>";            
            echo "</tr>"."\n";
        }
        echo "</table>";
    }
}
/*
*
*
*/
function showUserBadge($gamification)
{
    echo "<h3>"._AT('gm_your_badges')."</h3>";
    $badges = $gamification->getUserBadges();
    if (!empty($badges)){
        foreach ($badges as $badge) {
            echo getBadgeImage($badge->getIdBadge());

        }
    } else {
        echo '<p>'._AT('gm_no_badges_earned').'</p>';
    }
    echo "<hr />";
}
/*
*
*
*/
function getBadgeImage($badge_id){
    global $_base_href; 
    $sql = "SELECT image_url, description FROM %sgm_badges WHERE id=%d AND course_id=%d";
    $badge_image = queryDB($sql, array(TABLE_PREFIX, $badge_id,$_SESSION['course_id']), TRUE);
    
    if(strstr($badge_image['image_url'], "content")){
        $badge_file_array = explode('/',$badge_image['image_url']);
        if($badge_file_array[1] == 0){
            $custom_default = TRUE;
        }
        array_shift($badge_file_array);
        $badge_file_stem = implode('/',$badge_file_array);
        
        if(is_file(AT_CONTENT_DIR.$badge_file_stem)){
            if(!$custom_default){
                //$badge_file = $_base_href.'get.php/gameme/badges/'.end($badge_file_array);
                $badge_file = $_base_href.'content/'.$_SESSION['course_id'].'/gameme/badges/'.end($badge_file_array);
            } else{
                $badge_file = $_base_href.'mods/_standard/gameme/get_badge_icon.php?badge_id='.$badge_id;
            }
        }
    } else{
        $badge_file = $_base_href.$badge_image['image_url'];
        // Not a course badge, so check for custom system badge
        $sql = "SELECT image_url, description FROM %sgm_badges WHERE id=%d AND course_id=%d";
        $badge_image = queryDB($sql, array(TABLE_PREFIX, $badge_id, 0), TRUE);
        $badge_file_array = explode('/',$badge_image['image_url']);

        array_shift($badge_file_array);

        // get the custom admin created icon
        if(is_file(AT_CONTENT_DIR.'0/gameme/badges/'.end($badge_file_array))){
            $badge_file = $_base_href.'mods/_standard/gameme/get_badge_icon.php?badge_id='.$badge_id;
        } else{
            // Default Badge
            $badge_file = $_base_href.$badge_image['image_url'];
        }
    }
    return '<img src="'.$badge_file.'" alt="'.$badge_image['description'].'" title="'.$badge_image['description'].'" style="margin:.2em;"/>'."\n"; 
}
/*
*
*
*/
function showUserEvents($gamification){
    echo "<h2>"._AT('gm_events')."</h2>";
    $events = $gamification->getUserEvents();
    echo "<ul style='margin-left:1em;line-height:1.8em;'>"."\n";
    foreach ($events as $event) {
        echo '<li><strong>'.$event['event']->getDescription(). '</strong> - '._AT('gm_count').'('.$event[counter].')<br />';
        echo $event['event']->getReachMessage();      
    }
    echo "</ul>"."\n";
}
/*
*
*
*/
function showUserAlerts($gamification){
 echo "<h3>"._AT('gm_your_alert')."</h3>"."\n";
    $alerts = $gamification->getUserAlerts();
    if ($alerts == null) {
        echo _AT('gm_no_alerts')."<br/>";
    } else {
        foreach ($alerts as $alert) {
            if ($alert->getIdBadge())
                echo _AT('gm_badge').": " . $alert->getBadge()->getTitle();
            if ($alert->getIdLevel())
            echo _AT('gm_level').": " . $alert->getIdLevel();
            echo "<br>"."\n";
        }
    }
}
/*
*
*
*/
function showUserLog($gamification){
 echo "<h3>"._AT('gm_your_activity_log')."</h3>";
    $logs = $gamification->getUserLog();
    if ($logs)
        echo "<table class='data'>
        <tr>
        <th>"._AT('gm_event_date')."</th>
        <th>"._AT('gm_event')."</th>
        <th>"._AT('gm_points')."</th>
        <th>"._AT('gm_badge')."</th>
        <th>"._AT('gm_level')."</th>
        </tr>";
        foreach ($logs as $log) {
            $level = $gamification->getLevel($log->getIdLevel());
            echo "<tr>";
            echo "<td>".$log->getEventDate()."</td><td>".$log->getEvent()->getAlias()."</td>";
            if ($log->getPoints()){
                echo "<td>".$log->getPoints()."</td>";
            }else{
                echo "<td>&nbsp</td>";
            }
            if ($log->getIdBadge()){
                echo "<td>".$log->getBadge()->getTitle()."</td>";
            }else{
                echo "<td>&nbsp</td>";
            }
            if ($log->getIdLevel() > 0){
                echo "<td>".$level->getTitle()."</td>";
            }else{
                echo "<td>&nbsp</td>";
            }
            echo "</tr>"."\n";
        }
        echo "</table>";
}
function get_leader_count(){
        $sql = "SELECT * FROM %sgm_options WHERE `course_id`=%d and `gm_option` = 'showleader_count'";
        $this_count = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']), TRUE);
        return $this_count['value'];
}        
function show_instructor(){
    $sql = 'SELECT value FROM %sgm_options WHERE `gm_option` = "%s" AND `course_id` = %d';
    $show_instructor = queryDB($sql, array(TABLE_PREFIX, 'showinstructor', $_SESSION['course_id']), TRUE);
    return $show_instructor['value'];
}  

/* Gets the reach message from the database, either
* 1. a message created by the instructor for a particular course
* 2. or the default message that come with the module
* -in that order, whichever come first-
* @$alias the alias for the event defined in the gm_events table, 
* and passed from the events.php file 
*/
function get_reach_message($alias){
         if($_SESSION['course_id'] > 0){
            $is_course = " AND course_id=".$_SESSION['course_id'];
        } else{
            $is_course = " AND course_id=0";
        }
        
        //$is_course = " AND course_id=".$_SESSION['course_id'];
        $sql = "SELECT reach_message from %sgm_events WHERE alias = '%s' $is_course";
        
        if($reach_message = queryDB($sql, array(TABLE_PREFIX, $alias), TRUE)){
            // all good
        }else{
            // reach message does not exist so get the system default
            $sql = "SELECT reach_message from %sgm_events WHERE alias = '%s' AND course_id=0";
            $reach_message = queryDB($sql, array(TABLE_PREFIX, $alias), TRUE);
        }
        return $reach_message['reach_message'];
    }
    
?>