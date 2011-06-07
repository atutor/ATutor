<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form" style="max-width: 95%">
		<div class="row">
			<?php echo _AT("google_search_type_txt"); ?><br/>
			<?php
				if ($this->googleType==GOOGLE_TYPE_SOAP){
					$type1=' checked="checked"'; 
				} elseif ($this->googleType==GOOGLE_TYPE_AJAX){
					$type2=' checked="checked"'; 
				}
			?>
			<input type="radio" name="gtype" id="googleTypeSoap" value="<?php echo GOOGLE_TYPE_SOAP?>" <?php echo $type1 ?>/>
			<label for="googleTypeSoap"><?php echo _AT("google_search_soap"); ?></label><br/>

			<input type="radio" name="gtype" id="googleTypeAjax" value="<?php echo GOOGLE_TYPE_AJAX?>" <?php echo $type2 ?>/>
			<label for="googleTypeAjax"><?php echo _AT("google_search_ajax"); ?></label><br/>
		</div>
		
		<div class="row">
			<?php echo _AT('google_search_attn'); ?><br/><br/>
			<?php echo _AT('google_key_txt'); ?>
		</div>
		<div class="row">
			<input type="text" name="key" size="80" value="<?php echo $key; ?>" style="min-width: 90%;" />
		</div>

		<div class="row buttons">
			<input type="submit" name="submit" value="<?php echo _AT('save'); ?>" accesskey="s" />
		</div>
	</div>

</form>
