<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<div id="my_courses_container">
<table class="data" style="width:100%;">
<tr><th></th>
<th><?php echo _AT('course'); ?></th>
<th><?php echo _AT('instructor'); ?></th>
<th><?php echo _AT('status'); ?></th>
<th><?php echo _AT('shortcuts'); ?></th>
</tr>
<?php foreach ($this->courses as $row):
	static $counter;
	$counter++;
?>

    <tr class="<?php if ($counter %2) { echo 'odd'; } else { echo 'even'; } ?>">
    <td>
      <?php if ($row['icon'] == ''): ?>
			      <img src="images/clr.gif" class="icon" border="1" width="79" height="79" alt="<?php echo htmlentities($row['title'], ENT_QUOTES, 'UTF-8'); ?>" />
	      <?php else: 
			      echo $link;  

		    $sql2="SELECT icon from ".TABLE_PREFIX."courses WHERE course_id='$row[course_id]'";
				    $result2 = mysql_query($sql2, $db);
				    
				    while($row2=mysql_fetch_assoc($result2)){
					    $filename = $row2['icon'];
				    }
		    
		    $path = AT_CONTENT_DIR .$row['course_id'].'/custom_icons/'.$filename;
		    
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
		    <img src="<?php echo $dir; ?>" class="icon" border="0" alt="<?php echo htmlentities($row['title'], ENT_QUOTES, 'UTF-8'); ?>" />
				    <?php echo $link2; ?>
		    <?php endif; ?>


    </td>

    <td><?php echo '<a href="'.url_rewrite('bounce.php?course=' . $row['course_id']) . '"> '.htmlentities($row['title']).'</a>' ?>
    <br /><small><?php echo _AT('category'); ?>: <?php echo get_category_name($row['cat_id']); ?></small>
    </td>
    <td><small><?php echo '<a href="'.AT_BASE_HREF.'inbox/send_message.php?id='.$row['member_id'].'">'. get_display_name($row['member_id']).'<a/>'; ?></td>
    <td><small>
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
    <small>
    <ul>
    <?php if ($row['member_id'] != $_SESSION['member_id']  && $_config['allow_unenroll'] == 1): ?>
	    <li><a href="users/remove_course.php?course=<?php echo $row['course_id']; ?>"><?php echo _AT('unenroll_me'); ?></a></li>
    <?php endif; ?><br>
    <?php if ($row['tests']): ?>
	    <?php foreach ($row['tests'] as $test): ?>
		    <li><a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('mods/_standard/tests/test_intro.php?tid='.$test['test_id']); ?>"><span title="<?php echo _AT('tests'); ?>:<?php echo $test['title']; ?>"><?php echo $test['title']; ?></span></a> </li>
	    <?php endforeach ;?>
    <?php endif; ?>
    </ul>
    <?php if ($row['last_cid']): ?>
	  <div class="shortcuts" style="float:right;">
		  <a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><img src="<?php echo $_base_href;  ?>themes/default/images/resume.png" border="" alt="<?php echo _AT('resume'); ?>" title="<?php echo _AT('resume'); ?>" /></a>
	  </div>
    <?php endif; ?>

    </small>
    </td>
    </tr>
<?php endforeach; ?>
</table>
</div>

<div class="current_box">
<div class="current_head"> <h3><?php echo _AT('things_current'); ?></h3></div>
    <?php
		
    //display current news

    if($_GET['p'] == 0){
      $p = 1;
    }else{
      $p = intval($_GET['p']);
    }
    if($_GET['p'] == "all"){
      $perpage = count($this->all_news);
    }else{
      $perpage = 10;
    }

    $newscount = count($this->all_news);
    $num_pages = (ceil($newscount/$perpage));;
    $start = ($p-1)*$perpage;
    $end = ($p*$perpage);

    print_paginator($page, $num_pages, '', 1); 
    for($i=$start;$i<=$end; $i++){
	$count = $i;
	if (isset($this->all_news)) {
	    echo '<ul class="current_list">';
	      if(isset($this->all_news[$i]['thumb'])){
		    echo '<li"><img src="'.$this->all_news[$i]['thumb'].'" alt="'.$this->all_news[$i]['alt'].'" title="'.$this->all_news[$i]['alt'].'"/> ' . $this->all_news[$i]['link'] .' <br />';
		    if($this->all_news[$i]['object']['course_id']){
		    echo '<small>(<a href="bounce.php?course='.$this->all_news[$i]['object']['course_id'].'">'.$this->all_news[$i]['course'].'</a>)|';
		    }
		    echo '('.AT_DATE('%F %j, %g:%i',$this->all_news[$i]['time']).')</small><hr style=""/></li>';
		}
	    echo '</ul>';
	}
    }
    if($perpage == count($this->all_news)){ ?>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=1"><?php echo _AT('show_pages'); ?></a>
    <?php }else{ ?>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=all"><?php echo _AT('show_all'); ?></a>
    <?php } ?>
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>