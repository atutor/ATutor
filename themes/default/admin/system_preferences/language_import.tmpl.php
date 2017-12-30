<?php global $languageManager;?>
<form name="form1" method="post" action="mods/_core/languages/language_import.php" enctype="multipart/form-data" onsubmit="openWindow('<?php echo AT_BASE_HREF; ?>tools/prog.php');">
<div class="input-form">
	<div class="row">
		<p><?php echo _AT('import_lang_howto'); ?></p>
	</div>
	
	<div class="row">
		<label for="file"><?php echo _AT('import_a_new_lang'); ?></label><br />
		<input type="file" name="file" id="file" />
	</div>
	
	<div class="row buttons">
		<input type="submit" name="submit" value="<?php echo _AT('import'); ?>" />
	</div>
</div>
</form>
<form name="form1" method="post" action="mods/_core/languages/language_import.php">
<div class="input-form">
	<div class="row">
		<?php echo _AT('import_remote_language'); ?>
	</div>

	<div class="row">
	<?php
        echo '<form action="'.$_SERVER['PHP_SELF'].'" enctype="multipart/form-data"  onsubmit="openWindow(\'http://localhost/atutorgit/tools/prog.php\');" method="post">';
        echo '<input type="hidden" name="submit_import" value="1"/>';
        echo '<select name="language">';
        foreach($this->response as $languages=>$language){
            if(strstr($language['name'], "_")){
            $language_code = explode("_",$language['name']);
            echo '<option value="'.$language['name'].'">'.$language['name'].'</option>';
            }
        }
        echo "</select>";
        echo '<input type="submit" value="Import"/>';
        echo "</form>";
        ?>
    </div>
    </div>
</form>