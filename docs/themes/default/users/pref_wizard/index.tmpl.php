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
        <link rel="stylesheet" href="<?php echo $_base_path.'themes'.$this->theme; ?>/print.css" type="text/css" media="print" />
        <link rel="stylesheet" href="<?php echo $_base_path.'themes'.$this->theme; ?>/styles.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $_base_path.'themes'.$this->theme; ?>/forms.css" type="text/css" />
        <?php echo get_user_style(); ?>
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
        echo '<input type="checkbox" name="save" id="save" />';
        echo '<label for="save">Save when I click next.</label>';
        echo '<input type="submit" name="set_default" value="'._AT("factory_default").'" accesskey="d" />';
        echo '<input type="submit" value="Previous" name="previous" id="previous" />';
        echo '<input type="hidden" value="'.$this->pref_index.'" name="pref_index" id="pref_index" />';    
        if ($this->pref_index < count($this->pref_wiz) - 1) echo '<input type="submit" value="Next" name="next" id="next" />';
        else echo '<input type="submit" value="Done" name="done" id="done" />';
    }
?>
</form>
</body>
</html>