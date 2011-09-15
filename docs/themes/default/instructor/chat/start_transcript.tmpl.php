<form name="f1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<div class="row">
		<?php echo _AT('chat_keep_tran'); ?>
	</div>
<?php
   	echo '<input type="hidden" name="adminPass" value="'.$this->adminPass.'" />';

	if ($this->admin['produceTran'] > 0) {
		echo '<input type="hidden" name="function" value="stopTran" />';
		echo '<div class="row">';
			echo _AT('chat_current_tran').' <a href="mods/_standard/chat/view_transcript.php?t='.str_replace('.html', '', $this->admin['tranFile']).'" >'.str_replace('.html', '', $this->admin['tranFile']).'</a>.</p>';
		echo '</div>';

		echo '<div class="row buttons">';
	    	echo '<input type="submit" value="'._AT('chat_stop_tran').'" name="submit2" />';
		echo '</div>';

    } else {
        echo '<input type="hidden" name="function" value="startTran" />';

		echo '<div class="row">';
			echo _AT('chat_tran_file_name').' ';
			echo '<input type="text" name="tranFile" class="formfield" />';
		echo '</div>';		

		echo '<div class="row buttons">';
    		echo '<input type="submit" value="'._AT('chat_start_tran').'" name="submit2" />';
		echo '</div>';
    }
	echo '</div>';
	echo '</form>';
?>