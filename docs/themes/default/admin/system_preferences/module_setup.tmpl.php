<?php global $_config; ?>
<form action="<?php  $_SERVER['PHP_SELF']; ?>" method="post" name="form">
    <div class="input-form">
        <div class="row">
         	<p><label for="uri"><?php echo _AT('transformable_uri'); ?></label></p>
            	<input type="text" name="transformable_uri" value="<?php echo $_config['transformable_uri']; ?>" id="uri" size="80" style="min-width: 95%;" />
    	     
		    <p><label for="key"><?php echo _AT('web_service_id'); ?></label></p>
           	    <input type="text" name="transformable_web_service_id" value="<?php echo $_config['transformable_web_service_id']; ?>" id="key" size="80" style="min-width: 95%;" />

		    <p><label for="key"><?php echo _AT('oauth_expire'); ?></label></p>
           	    <input type="text" name="transformable_oauth_expire" value="<?php echo $_config['transformable_oauth_expire']; ?>" id="key" size="20" />&nbsp;<?php echo _AT('seconds'); ?><br />
		       <small>&middot; <?php echo _AT('oauth_expire_note'); ?><br />
        </div>

        <div class="row buttons">
            <input type="submit" name="submit" value="<?php echo _AT('save'); ?>"  />
        </div>
    </div>
</form> 