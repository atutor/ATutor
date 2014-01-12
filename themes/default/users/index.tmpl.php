<?php 

require(AT_INCLUDE_PATH.'header.inc.php'); ?>
<div id="my_courses_container" <?php if($_config['show_current'] != 1){ echo ' class="wide"';} else {echo ' class="narrow"';} ?>>
<table  class="data" summary="">
<tr><th  class="hidecol480"></th>
<th><?php echo _AT('course'); ?></th>
<th class="hidecol480"><?php echo _AT('instructor'); ?></th>
<th class="hidecol480"><?php echo _AT('status'); ?></th>
<th><?php echo _AT('shortcuts'); ?></th>
</tr>
<?php foreach ($this->courses as $row):
    static $counter;
    $counter++;
?>
    <tr class="<?php if ($counter %2) { echo 'odd'; } else { echo 'even'; } ?>">
    <td  class="hidecol480">
      <?php if ($row['icon'] == ''): ?>
                  <img src="images/clr.gif" class="icon" border="1" width="79" height="79" alt="<?php echo htmlentities_utf8($row['title']); ?>" />
          <?php else: 
                  echo $link;  
            $path = AT_CONTENT_DIR .$row['course_id'].'/custom_icons/'.$this->icon[$row['course_id']];
                    if (file_exists($path)) {
                        if (defined('AT_FORCE_GET_FILE')) {
                            $dir = 'get_course_icon.php?id='.$row['course_id'];
                        } else {
                            $dir = 'content/' . $_SESSION['course_id'] . '/'.$row['icon'];
                        }
                    } else {
                        $dir = "images/courses/".$row['icon'];
                    }
                    ?>
                    <img src="<?php echo $dir; ?>" class="icon" border="0" alt="<?php echo htmlentities_utf8($row['title']); ?>" />
                        <?php echo $link2; ?>
            <?php endif; ?>


    </td>
    <td><?php echo '<a href="'.url_rewrite('bounce.php?course=' . $row['course_id']) . '"> '.htmlentities_utf8($row['title']).'</a>' ?>
    <br /><small><?php echo _AT('category'); ?>: <?php echo get_category_name($row['cat_id']); ?></small>
    </td>
    <td class="hidecol480"><small><?php echo '<a href="'.AT_BASE_HREF.'inbox/send_message.php?id='.$row['member_id'].'">'. get_display_name($row['member_id']).'</a>'; ?></small></td>
    <td class="hidecol480"><small>
    <?php    

            if ($_SESSION['member_id'] == $row['member_id']) {
                //if instructor
                echo _AT('instructor');
            } else if ($row['approved'] == 'a') {
                //if alumni
                echo _AT('alumni');
            } else if ($row['approved'] == 'n') {
                //if notenrolled
                echo _AT('pending_approval');
                $link  = $link2 = "";
            } else {
                //if no role and enrolled
                echo _AT('student1');
            } ?>


    </small></td>
    <td>
<?php if($_config['allow_unenroll'] || $row['tests']){  ?>

    <?php if ($row['member_id'] != $_SESSION['member_id']  && $_config['allow_unenroll'] == 1): ?>
          <ul><li><a href="users/remove_course.php?course=<?php echo $row['course_id']; ?>"><?php echo _AT('unenroll_me'); ?></a></li>   </ul>  
    <?php endif; ?>
    <?php if ($row['tests']): ?>
        <?php foreach ($row['tests'] as $test): ?>
             <ul> <li><a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('mods/_standard/tests/test_intro.php?tid='.$test['test_id']); ?>"><span title="<?php echo _AT('tests'); ?>:<?php echo $test['title']; ?>"><?php echo $test['title']; ?></span></a> </li>   </ul>  
        <?php endforeach ;?>
    <?php endif; ?>
 
<?php }  ?>

    <?php if ($row['last_cid']): ?>
      <div class="shortcuts" style="float:right;">
          <a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><img src="<?php echo AT_BASE_HREF;  ?>themes/default/images/resume.png" border="" alt="<?php echo _AT('resume'); ?>" title="<?php echo _AT('resume'); ?>" class="img1616"/></a>
      </div>
    <?php endif; ?>


    </td>
    </tr>
<?php endforeach; ?>
</table>
</div>
<?php if($_config['show_current'] == 1){ ?>
<div class="current_box">
<div class="current_head"> <h3><?php echo _AT('things_current'); ?></h3></div>
    <?php
        
    //display current news

    if(!isset($_GET['p']) && $_GET['p'] == 0){
      $p = 1;
    }else{
      $p = intval($_GET['p']);
    }
    if(isset($_GET['p']) && $_GET['p'] == "all"){
      $perpage = count($this->all_news);
      $p = 1;
    }else{
      $perpage = 10;
    }

    $newscount = count($this->all_news);
    $num_pages = ($perpage==0)?0:(ceil($newscount/$perpage));
    $start = ($p-1)*$perpage;
    $end = ($p*$perpage);
    $page = isset($$page) ? $page : $p;

    print_paginator($page, $num_pages, '', 1); 

    for($i=$start;$i<=$end; $i++){
    $count = $i;
    if (isset($this->all_news)) {
        echo '<ul class="current_list">'."\n";
          if(isset($this->all_news[$i]['thumb'])){
              $alt = isset($this->all_news[$i]['alt']) ? $this->all_news[$i]['alt'] : '';
            $link =  isset($this->all_news[$i]['link']) ? $this->all_news[$i]['link'] : '';
            
            echo '<li><img src="'.$this->all_news[$i]['thumb'].'" alt="'.$alt.'" title="'.$alt.'" class="img1616"/> ' .$link.' <br />'."\n";
            if(isset($this->all_news[$i]['object']['course_id'])){
            echo '<small>(<a href="bounce.php?course='.$this->all_news[$i]['object']['course_id'].'">'.$this->all_news[$i]['course'].'</a>)|'."\n";
            }
            echo '('.AT_DATE('%F %j, %g:%i',$this->all_news[$i]['time']).')</small><hr style=""/></li>'."\n";
        }
        echo '</ul>';
    }
    }
    if($perpage == count($this->all_news)){ ?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=1"><?php echo _AT('show_pages'); ?></a>
    <?php }else if($newscount > 0){ ?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=all"><?php echo _AT('show_all'); ?></a>
    <?php } else {
        echo _AT('none_found');
      }?>
<br /><br />
</div>  
<?php } ?>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>