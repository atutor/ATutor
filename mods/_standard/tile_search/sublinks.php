<?php

if (!defined('AT_INCLUDE_PATH')) { exit; } 

?>

<div style="padding:1em;" class="search_div">
		<?php  echo _AT('tile_howto'); ?>
<form action="<?php echo $_base_path; ?>mods/_standard/tile_search/tile.php" method="get" name="form">
    <input type="hidden" name="search" value="1" />
    <input type="text" name="keywords" size="20" id="words2" value="<?php echo htmlspecialchars($_REQUEST['keywords'], ENT_QUOTES); ?>" title="<?php echo _AT('search_words'); ?>"/>
      <input type="hidden" name="submit" value="<?php echo _AT('search'); ?>" />
    <input type="submit" class="button" />
</form>
</div>

<?php return array(); ?>