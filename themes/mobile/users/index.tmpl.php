<?php require(AT_INCLUDE_PATH.'header.inc.php'); ?>

<div id="my_courses_container">
<ul class="my-courses-list-ul" style="padding:0">

<?php foreach ($this->courses as $row):
	static $counter;
	$counter++;
?>

<li class="my-courses-list">
  <?php echo '<a href="'.url_rewrite('bounce.php?course=' . $row['course_id']) . '"> '.htmlentities($row['title']).'</a>' ?>
  <?php if ($row['last_cid']): ?>
	 	  <a class="my-courses-resume" href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('content.php?cid='.$row['last_cid']); ?>"><img src="<?php echo $_base_href;  ?>themes/default/images/resume.png" border="" alt="<?php echo _AT('resume'); ?>" title="<?php echo _AT('resume'); ?>" /></a>
    <?php endif; ?>  

 	<div class="my-courses-links">
    <?php if ($row['member_id'] != $_SESSION['member_id']  && $_config['allow_unenroll'] == 1): ?>
	 <a href="users/remove_course.php?course=<?php echo $row['course_id']; ?>"><?php echo _AT('unenroll_me'); ?></a>
    <?php endif; ?>
    <?php if ($row['tests']): ?>
	    <?php foreach ($row['tests'] as $test): ?>
		   <a href="bounce.php?course=<?php echo $row['course_id'].SEP.'p='.urlencode('mods/_standard/tests/test_intro.php?tid='.$test['test_id']); ?>"><span title="<?php echo _AT('tests'); ?>:<?php echo $test['title']; ?>"><?php echo $test['title']; ?></span></a> 
	    <?php endforeach ;?>
    <?php endif; ?>  
    </div>
   
</li>

<?php endforeach; ?>

</ul>
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
		    echo '<li><img src="'.$this->all_news[$i]['thumb'].'" alt="'.$this->all_news[$i]['alt'].'" title="'.$this->all_news[$i]['alt'].'"/> ' . $this->all_news[$i]['link'] .' <br />';
		    if($this->all_news[$i]['object']['course_id']){
		    echo '<small>(<a href="bounce.php?course='.$this->all_news[$i]['object']['course_id'].'">'.$this->all_news[$i]['course'].'</a>)|';
		    }
		    echo '('.AT_DATE('%F %j, %g:%i',$this->all_news[$i]['time']).')</small><hr></hr></li>';
		}
	    echo '</ul>';
	}
    }
    if($perpage == count($this->all_news)){ ?>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=1"><?php echo _AT('show_pages'); ?></a>
    <?php }else{ ?>
	<div id="show-all"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=all"><?php echo _AT('show_all'); ?></a></div>
    <?php } ?>
<br /><br />
</div>  

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>