<?php 
global $savant;
global $_base_path;
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>Preferences wizard</title>
        <script src="<?php echo $_base_path; ?>jscripts/infusion/InfusionAll.js" type="text/javascript"></script>
		<script src="<?php echo $_base_path; ?>jscripts/TILE.js" type="text/javascript"></script>
    </head>
    <body>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
<?php
    $pref_next = $this->pref_next;
    $pref = $this->pref_wiz[$pref_next];
    $submitVal = "Next";
    if ($this->start_template != null) {
        $savant->display($this->start_template);
    }
    else if ($this->pref_template != null) {
        include_once($this->pref_template);

        $pref_next++;
        $max_pref_key = count($this->pref_wiz) - 1;       
        if ($pref_next <= $max_pref_key) {
            foreach ($this->pref_wiz as $pref => $template) { 
                echo '<input type="hidden" name="pref_wiz[]" value="'.$template.'" />';
            }
            echo '<input type="hidden" value="'.$pref_next.'" name="pref_next" id="pref_next" />';
        } else {
            $submitVal = "Done";
        }
    }
    echo '<input type="submit" value="'.$submitVal.'" name="submit" id="submit" />';
?>
</form>
</body>
<div id="footer">
    <?php require(AT_INCLUDE_PATH.'html/languages.inc.php'); ?>
    <?php require(AT_INCLUDE_PATH.'html/copyright.inc.php'); ?>
</div>
</body>
</html>