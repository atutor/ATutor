<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002 - 2009                                            */
/* Inclusive Design Institute                                           */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path;

if ($this->banner): ?><?php echo $this->banner; ?><br /><?php endif;

// positioning switch of home ONLY FOR INSTRUCTORS. two icons will be used for identification to distinguish the two different views of the home.
if(authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN) && count($this->home_links) > 0){
	if($this->view_mode==0)
		echo '<a href ="'.AT_BASE_HREF.'switch_view.php?swid='.$this->view_mode.'" style="background-color:#FFFFFF;"><img src="'.AT_BASE_HREF.'images/detail_view.png" title ="'._AT('detail_view').'"  alt ="'._AT('detail_view').'" border="0" class="img1616"/></a><br />';
	else
		echo '<a href ="'.AT_BASE_HREF.'switch_view.php?swid='.$this->view_mode.'" style="background-color:#FFFFFF;"><img src="'.AT_BASE_HREF.'images/icon_view.png"  title ="'._AT('icon_view').'" alt ="'._AT('icon_view').'" border="0" class="img1616"/></a><br />';
}	

// Icon View, $this->view_mode = 0. course will be made changes to the icons to restore the classic icons.
if($this->view_mode==0){
?>
	<div style="width: 98%; margin-top: -5px; float:left;">
		<ul id="home-links">
		<?php if (is_array($this->home_links)): ?>
		<?php foreach ($this->home_links as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><img src="<?php echo $link['img']; ?>" alt="" class="img-size-home" border="0" /><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
		<?php endif; ?>
		</ul>
	</div> <?php
} else { ?>
	
	<div id="details_view" class="fluid-horizontal-order" style="width: 98%; margin-top: -5px; float: left; ">
<?php 				// create table container divided into two columns for the placement of modules
	if(authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN) && is_array($this->home_links)){		// display enabled course tool
		foreach ($this->home_links as $link){ 
?>
		<div class="home_box" id="<?php echo str_replace('/', '-', substr($link['url'], strlen($_base_path))); ?>"> 
<?php print_sublinks($link); 						// display each module ?>
		</div>
<?php
		} // end of foreach 
	}  // end of inner if 
	else {
		if (is_array($this->home_links)) {
			foreach ($this->home_links as $link){?>
		<div class="home_box">
<?php print_sublinks($link); ?>
		</div>
<?php			}  // end of foreach
		}// end of inner inner if
	} ?>
	</div> 
<?php
} // end of if

if ($this->announcements): ?>
<h2 class="page-title"><?php echo _AT('announcements'); ?></h2>
	<?php foreach ($this->announcements as $item): ?>
		<div class="news">
			<h3><?php echo $item['title']; ?></h3>
			<p><span class="date"><?php echo $item['date'] .' '. _AT('by').' ' . $item['author']; ?></span></p> <?php echo $item['body']; ?>
		</div>
	<?php endforeach; ?>

	<?php if ($this->num_pages > 1): ?>
		<?php echo _AT('page'); ?>: | 
		<?php for ($i=1; $i<=$this->num_pages; $i++): ?>
			<?php if ($i == $this->current_page): ?>
				<strong><?php echo $i; ?></strong>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
			 | 
		<?php endfor; ?>
	<?php endif; ?>
<?php endif;

// Generate HTML for modules at "detail view"
function print_sublinks($link){
	global $_base_path;
?>
<div class="details_ol">
	<div class="details_or">
		<div class="outside_box">
<?php if (authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN)) {?>
			<div class="buttonbox">
			<a href="#" onclick="javascript: remove_module('<?php echo htmlentities(substr($link['url'], strlen($_base_path))); ?>'); return false;"><img src="<?php echo AT_BASE_HREF; ?>images/x.gif" border="0" alt="<?php echo _AT('close'); ?>" class="img1616"/></a>
			</div>
<?php }?>
			<img src="<?php echo $link['img']; ?>" alt="" border="0" style="vertical-align:middle;" class="img-size-home"/>
			<span class="home-title"><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></span>
			<div class="inside_box">

<?php
	// if $link['sub_file'] is defined, print the text array returned from sub_file, otherwise, print the text defined in $link['text']
	if($link['sub_file']!=""){
		$array = require(AT_INCLUDE_PATH.'../'.$link['sub_file']);
		if(!is_array($array)){ 
?>
				<div class="details-text">
				<?php echo _AT('none_found'); ?>
				</div>
<?php } else { ?>
				<div class="details-text">
<?php 	foreach($array as $sublink){ ?>
					<img src="<?php echo $link['icon']; ?>" border="0" alt="" style="vertical-align:middle;"/> 
<?php		if ($sublink <> '') echo $sublink."<br />"; } ?>
				</div> 
<?php 
		} // end of else						
	} else { ?>
				<div class="details_text"><?php echo $link['text']; ?></div>
<?php } ?>
			</div>
		</div>
	</div>

</div>


<?php } ?>
