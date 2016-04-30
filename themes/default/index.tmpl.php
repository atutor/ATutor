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

if ($this->banner): ?><?php echo strip_returns($this->banner); ?><br /><?php endif;

if(count($this->home_links) > 0){
    echo '<div class="detail_switch" id="detail_switch"><a href ="javascript:void(0)"  title="'._AT('switch_icon_view').'">&nbsp;</a></div>';
}
?>

<div id="icon_view" style="width: 98%; margin-top: -5px; float:left;">
    <span id="detailed_to_icon" title="<?php echo _AT('icon_on'); ?>" aria-live="polite"></span>
    <ul id="home-links">
    <?php if (is_array($this->home_links)): ?>
    <?php foreach ($this->home_links as $link): ?>
        <li><a href="<?php echo $link['url']; ?>"><img src="<?php echo $link['img']; ?>" alt="" class="img-size-home"  /><?php echo $link['title']; ?></a></li>
    <?php endforeach; ?>
    <?php endif; ?>
    </ul>
</div>
	
<div id="details_view" class="fluid-horizontal-order" style="width: 98%; margin-top: -5px; float: left; ">
<span id="icon_to_detailed" title="<?php echo _AT('detailed_on'); ?>" aria-live="polite"></span>

<?php 				
    // create table container divided into two columns for the placement of modules
	if(authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN) && is_array($this->home_links)){		
	    // display enabled course tool
		foreach ($this->home_links as $link){ 
?>
		<div class="home_box" id="<?php echo str_replace('/', '-', substr($link['url'], strlen($_base_path))); ?>"> 
        <?php 
            // display each module
            print_sublinks($link); 						 
        ?>
		</div>
<?php
		}  
	} else { 
		if (is_array($this->home_links)) {
			foreach ($this->home_links as $link){?>
		        <div class="home_box">
                <?php print_sublinks($link); ?>
		        </div>
<?php		}  
		}
	} ?>
	</div> 
	<br style="clear:both;" />&nbsp;
	
<?php
if ($this->announcements): 
	global $system_courses; 
?>

<?php if ($system_courses[$this->course_id]['rss']): ?>
<div style="float:right;">
<a title="<?php echo SITE_NAME; ?> - RSS 2.0" href="<?php echo $this->base_href; ?>get_rss.php?<?php echo $this->course_id; ?>-2"><img src="<?php echo $this->base_href;?>images/rss-icon.jpg" alt ="Announcements RSS Feed" style="height:2em;width:2em;"></a>
</div>
<?php endif;  ?>
	<br style="clear:both;" />
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
<!--  <div class="details_ol">-->
	<div class="details_or">
		<div class="outside_box">
<?php if (authenticate(AT_PRIV_ADMIN,AT_PRIV_RETURN)) {?>
			<div class="buttonbox">
			<input type="image" onclick="javascript: remove_module('<?php echo htmlentities(substr($link['url'], strlen($_base_path))); ?>');" src="<?php echo AT_BASE_HREF; ?>images/x.gif" alt="<?php echo _AT('close'); ?>" class="img1616"/>
			</div>
<?php }?>
			<img src="<?php echo $link['img']; ?>" alt="" style="vertical-align:middle;" class="img-size-home"/>
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
					<img src="<?php echo $link['icon']; ?>" alt="" style="vertical-align:middle;"/> 
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

<!-- </div> -->
<br style="clear:both;" />

<?php } ?>
