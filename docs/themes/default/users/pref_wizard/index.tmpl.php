<?php 
global $savant;
global $_base_path;
global $msg;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="<?php echo $this->lang_code; ?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->lang_charset; ?>" />
        <title><?php echo SITE_NAME; ?> : Preferences wizard</title>
        <script src="<?php echo $_base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
		<script src="<?php echo $_base_path; ?>jscripts/TILE.js" type="text/javascript"></script>
        <link rel="shortcut icon" href="<?php echo $_base_path; ?>favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="<?php echo $_base_path ?>jscripts/infusion/framework/fss/css/fss-layout.css" type="text/css" />       
        <script src="<?php echo $_base_path; ?>jscripts/ATutor.js" type="text/javascript"></script>   
        <style id="pref_style" type="text/css"></style>       
    </head>
    <body onload="<?php echo $this->onload; ?>">
        <div align="right"><a href="javascript:window.close()">Close</a></div>
        <a name="content"></a>

        <h1><?php echo _AT('preferences') ?></h1>
        <?php $msg->printAll(); ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
<?php
    if ($this->start_template != null) {
        $savant->display($this->start_template);
    }
    else if ($this->pref_template != null) {
        include_once($this->pref_template);
        
        foreach ($this->pref_wiz as $pref => $template) { 
            echo '<input type="hidden" name="pref_wiz[]" value="'.$template.'" />';
        }
        echo '<input type="hidden" value="'.$this->pref_index.'" name="pref_index" id="pref_index" />';    
        
        echo '<div class="fl-container-flex"><input class="fl-force-left" type="submit" name="set_default" value="'._AT("factory_default").'" accesskey="d" />';
        echo '<span class="fl-force-right"><input type="submit" value="Previous" name="previous" id="previous" />';
        if ($this->pref_index < count($this->pref_wiz) - 1) echo '<input type="submit" value="Next" name="next" id="next" />';
        else echo '<input type="submit" value="Done" name="done" id="done" />';
        echo '</span></div>';
    }
?>
</form>
<script type="text/javascript">
//<!--
    <?php 
    if (isset($_POST['done'])) {
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