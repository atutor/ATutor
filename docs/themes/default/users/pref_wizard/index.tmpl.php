<?php 
    require(AT_INCLUDE_PATH.'header.inc.php'); 
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form" enctype="multipart/form-data">
    <div>
        <input type="checkbox" name="pref_wiz[]" value="0" id="display" />
        <label for="display">I would like to make the text on the screen easier to see.</label>
    </div>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="1" id="structure" />
        <label for="structure">I would like to enhance the structure of the content.</label>
    </div>

    <div>
        <input type="checkbox" name="pref_wiz[]" value="2" id="navigation" />
        <label for="navigation">I would like to enhance the navigation of the content.</label>
    </div>

<input type="submit" value="Next" name="next" id="next" />
</form>

<?php 
    require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>