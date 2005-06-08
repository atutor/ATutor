<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; } ?>
<?php if ($this->table_of_contents): ?>
	<div id="toc">
		<h4><?php if ($this->sequence_links['previous']): ?>
				<a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo $this->sequence_links['previous']['title']; ?>" accesskey=",">&lt;</a> | 
			<?php else: ?>
				&lt; | 
			<?php endif; ?>
			<?php if ($this->sequence_links['next']): ?>
				<a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo $this->sequence_links['next']['title']; ?>" accesskey=".">&gt;</a>
			<?php else: ?>
				&gt;
			<?php endif; ?>
		<?php echo _AT('contents'); ?> <script type="text/javascript">
		//<![CDATA[
		var state = getcookie("toccontent");
		if (state && (state == 'none')) {
			showTocToggle("toccontent", "<?php echo _AT('show'); ?>","<?php echo _AT('hide'); ?>", "", "show");
		} else {
			showTocToggle("toccontent", "<?php echo _AT('show'); ?>","<?php echo _AT('hide'); ?>", "", "hide");
		}
		//]]>
		</script></h4>

		<div style="margin-left: -15px;">
		<script type="text/javascript">
		//<![CDATA[
		if (state && (state == 'none')) {
			document.writeln('<div style="display:none;" id="toccontent">');
		} else {
			document.writeln('<div style="" id="toccontent">');
		}
		//]]>
		</script>

		<?php echo $this->table_of_contents; ?>

		<script type="text/javascript">
		//<![CDATA[
			document.writeln('</div>');
		//]]>
		</script>
		</div>
	</div>
<?php else: ?>
	<div id="toc">
		<h4><?php if ($this->sequence_links['previous']): ?>
				<a href="<?php echo $this->sequence_links['previous']['url']; ?>" title="<?php echo $this->sequence_links['previous']['title']; ?>" accesskey=",">&lt;</a> | 
			<?php else: ?>
				&lt; | 
			<?php endif; ?>
			<?php if ($this->sequence_links['next']): ?>
				<a href="<?php echo $this->sequence_links['next']['url']; ?>" title="<?php echo $this->sequence_links['next']['title']; ?>" accesskey=".">&gt;</a>
			<?php else: ?>
				&gt;
			<?php endif; ?>
		</h4>
	</div>
<?php endif; ?>

<?php if ($this->shortcuts): ?>
<fieldset id="shortcuts"><legend>Shortcuts</legend>
	<ul>
		<?php foreach ($this->shortcuts as $link): ?>
			<li><a href="<?php echo $link['url']; ?>"><?php echo $link['title']; ?></a></li>
		<?php endforeach; ?>
	</ul>
</fieldset>
<?php endif; ?>


<div id="content-text">
	<?php echo $this->body; ?>
</div>

<div id="content-info">
	<?php echo $this->content_info; ?>
</div>