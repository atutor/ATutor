<?php

if (!defined('AT_INCLUDE_PATH')) { exit; } 

$array = array("test");
global $_config;

?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".search_div").click(function(){
            $(this).children(".children").toggle();
        });
       $(".search_div input").click(function(e) {
            e.stopPropagation();
       });
    });
</script>
<div style="padding:1em;" class="search_div">
 <?php
 if (isset($_config['gsearch'])): ?>
	<form action="<?php echo $_base_path; ?>mods/_standard/google_search/index.php" method="get" name="gsearchform" class="children">
<?php else: ?>
	<form action="http://www.google.com/search" method="get" target="_new">
	<input type="hidden" name="l" value="<?php echo $_SESSION['lang']; ?>" class="children"/>
<?php endif; ?>

<?php if (!$_config['gsearch']): ?>
	<?php echo _AT('google_new_window'); ?><br />
<?php endif; ?>
<input type="hidden" name="search" value="1" />
<input type="text" name="q" class="formfield" id="query_str" title="<?php echo _AT('search_words'); ?>" size="20" value="<?php echo stripslashes(htmlspecialchars($_GET['q'])); ?>" />
<input type="hidden" name="submit" value="<?php echo _AT('search'); ?>" />
<input type="submit" class="button" />
</form>
</div>

<?php return array(''); ?>