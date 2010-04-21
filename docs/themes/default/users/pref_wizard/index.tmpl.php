<?php 
global $savant;
global $_base_path;
global $msg;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $this->lang_code; ?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
        <title><?php echo SITE_NAME; ?> : <?php echo _AT('prefs_wizard'); ?>Preferences wizard</title>
        <script src="<?php echo $_base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
		<script src="<?php echo $_base_path; ?>jscripts/TILE.js" type="text/javascript"></script>
        <link rel="shortcut icon" href="<?php echo $_base_path; ?>favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="<?php echo $_base_path ?>jscripts/infusion/framework/fss/css/fss-layout.css" type="text/css" />       
        <link rel="stylesheet" href="<?php echo $_base_path; ?>themes/default/styles.css" type="text/css" />
   <script src="<?php echo $_base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
        <style id="pref_style" type="text/css"></style>    
    </head>
    <body onload="<?php echo $this->onload; ?>">
        <div class="fl-force-right"><br /><a href="javascript:window.close()"><?php echo _AT('close'); ?></a></div>
        <a name="content"></a>

        <h1><?php echo _AT('preferences') ?></h1>
        <?php $msg->printAll(); ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
<?php
    if ($this->start_template != null) {
        $savant->display($this->start_template);
    }
    else if ($this->pref_template != null) {
    	echo '<fieldset class="wizscreen">';
        include_once($this->pref_template);

        
        foreach ($this->pref_wiz as $pref => $template) { 
            echo '<input type="hidden" name="pref_wiz[]" value="'.$template.'" />';
        }
        echo '<input type="hidden" value="'.$this->pref_index.'" name="pref_index" id="pref_index" />';    
        echo '<input type="hidden" value="'.$_SESSION['course_id'].'" name="course_id" id="course_id" />';
        
        echo '<span class="fl-force-right"><input type="submit" value="'._AT('previous').'" name="previous" id="previous" class="button"/>';
        if ($this->pref_index < count($this->pref_wiz) - 1) echo '<input type="submit" value="'._AT('next').'" name="next" id="next" class="button"/>';
        else echo '<input type="submit" value="'._AT('done').'" name="done" id="done" class="button"/>';
        echo '</span>';
        echo '</fieldset>';
    }
?>

    <input class="fl-centered button" type="submit" name="set_default" value="<?php echo _AT("reapply_default") ?>" accesskey="d" />

</form>
<script type="text/javascript">
//<!--
    <?php 
    if (isset($_POST['done']) || isset($_POST['set_default'])) {
        echo 'ATutor.users.preferences.setStyles("'.$_SESSION["prefs"]["PREF_BG_COLOUR"].
            '","'.$_SESSION["prefs"]["PREF_FG_COLOUR"].
            '","'.$_SESSION["prefs"]["PREF_HL_COLOUR"].
            '","'.$_SESSION["prefs"]["PREF_FONT_FACE"].
            '","'.$_SESSION["prefs"]["PREF_FONT_TIMES"].'");';
        
        echo "window.close();";
    }   
    require_once(AT_INCLUDE_PATH.'../jscripts/ATutor_js.php'); 

    ?>
//-->


</script>

</body>
</html>