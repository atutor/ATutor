<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg, Heidi Hazelton	*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

/*
if(isset($_POST['submit'])) {
	unset($errors);
	unset($_POST['submit']);
	$step++;
	return;
}
*/

print_progress($step);

	unset($errors);

    $writeable = array('content/', 'content/1/', 'content/import/', 'content/chat/', 'include/config.inc.php');

    foreach ($writeable as $file) {
        if (!is_dir('../'.$file)) {
            if ( file_exists('../'.$file) ) {
                @chmod('../'.$file, 0666);
                if (!is_writeable('../'.$file)) {
					$errors[] = '<b>'.$file . '</b> is not writeable.';
                }else{
                    $progress[] = '<b>'.$file.'</b> is writeable.';
                }
            }
        } else {
            @chmod('../'.$file, 0777);
            if (!is_writeable('../'.$file)) {
				$errors[] = '<b>'.$file . '</b> is not writeable.';
            }else{
                $progress[] = '<b>'.$file.'</b> is writeable.';
            }
        }
    }

echo '<form action="'.$_SERVER['PHP_SELF'].'" method="post" name="form">';

if (isset($errors)) {
	if (isset($progress)) {
		print_feedback($progress);
	}
	print_errors($errors);

	echo'<input type="hidden" name="step" value="'.$step.'" />';

	unset($_POST['step']);
	unset($_POST['action']);
	print_hidden($step);

	echo '<p align="center"><input type="submit" class="button" value=" Try Again " name="retry" />';

} else {
	echo '<input type="hidden" name="step" value="'.($step+1).'" />';

	unset($_POST['step']);
	unset($_POST['action']);
	print_hidden($step);

	if (!$_POST['step1']['old_version']) {
		require('include/config_template.php');
		write_config_file('../include/config.inc.php');
		$progress[] =  'Data has been saved successfully.';

		@chmod('../include/config.inc.php', 1444);
	}
	print_feedback($progress);
	


	echo '<p align="center"><input type="submit" class="button" value=" Next » " name="submit" />';

}


?>

</form>