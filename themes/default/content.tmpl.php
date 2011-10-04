<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; } 

// print the AccessForAll alternatives tool bar
// see /content.php for details of the alt_infos() array
// images for the toolbar can be customized by adding images of the same name to a theme's images directory
?>
<?php  if (!empty($this->alt_infos)){ ?>
<div id="alternatives_shortcuts">
<?php 
	foreach ($this->alt_infos as $alt_info){
		echo '<a href="'.$_SERVER['PHP_SELF'].'?cid='.$this->cid.(($_GET['alternative'] == $alt_info['0']) ? '' : htmlentities_utf8(SEP).'alternative='.$alt_info[0]).'">
			<img src="'.AT_BASE_HREF.(($_GET['alternative'] == $alt_info[0]) ? $alt_info[3] : $alt_info[4]).'" alt="'.(($_GET['alternative'] == $alt_info[0]) ? $alt_info[2] : $alt_info[1]).'" title="'.(($_GET['alternative'] == $alt_info[0]) ? $alt_info[2] : $alt_info[1]).'" border="0" class="img1616"/></a>';
	} 
?>
</div>
<?php } ?>
<?php 
if ($_SESSION["prefs"]["PREF_SHOW_CONTENTS"] && $this->content_table <> "") {
	echo $this->content_table;
}
?>

<div id="content-text">
	<?php echo $this->body; ?>
</div>

<?php if (!empty($this->test_ids)): ?>
<div id="content-test" class="input-form">
	<ol>
		
		<li><strong><?php echo _AT('tests') . ':' ; ?></strong>
		<ul class="tools">
		<?php 
			foreach ($this->test_ids as $id => $test_obj){
				echo '<li><a href="'.url_rewrite('mods/_standard/tests/test_intro.php?tid='.$test_obj['test_id'], AT_PRETTY_URL_IS_HEADER).'">'.
					AT_print($test_obj['title'], 'tests.title').'</a><br /></li>';
			}
		?>
		</ul>
	</li></ol>
</div>
<?php endif; ?>

<?php

if (!empty($this->forum_ids)): ?>
<div id="content-forum" class="input-form">
    <ol>
        <li><strong><?php echo _AT('forums') . ':' ; ?></strong>
            <ul class="tools">
                <?php
                foreach ($this->forum_ids as $id => $forum_obj) {
                    echo '<li><a href="'.url_rewrite('mods/_standard/forums/forum/index.php?fid='.$forum_obj['forum_id'], AT_PRETTY_URL_IS_HEADER).'">'.
                        AT_print($forum_obj['title'], 'forums.title').'</a><br /></li>';
                }
                ?>
            </ul>
        </li>
    </ol>
</div>
<?php endif; ?>

<?php if (!empty($this->module_contents)) echo $this->module_contents;?>

<div id="content-info">
	<?php echo $this->content_info; ?>

</div>