<?php

if (!defined('AT_INCLUDE_PATH')) { exit; } 

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
<form action="<?php echo $_base_path; ?>mods/_standard/google_search/index.php" method="get" name="gsearchform">
<input type="hidden" name="search" value="1" />
<input type="text" name="q" class="formfield" id="query_str" title="<?php echo _AT('search_words'); ?>" size="20" value="<?php echo stripslashes(htmlspecialchars($_GET['q'])); ?>" />
<input type="hidden" name="submit" value="<?php echo _AT('search'); ?>" />
<input type="submit" class="button" />
</form>
</div>

<?php return array(); ?>