<?php 
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
?>
<div class="row">
	<label for="remedial_content"><?php echo _AT('remedial_content'); ?></label>
	<?php print_VE('remedial_content'); ?>
	<textarea id="remedial_content" cols="50" rows="3" name="remedial_content" placeholder="<?php echo _AT('remedial_content_placeholder'); ?>"><?php echo htmlspecialchars($stripslashes($_POST['remedial_content'])); ?></textarea>
</div>