<<?php echo '?'; ?>xml version="1.0" encoding="utf-8" <?php echo '?'; ?>>
<accessForAll>  
	<resources>
		<?php
		foreach ($this->resources as $index=>$items) :
			foreach($items['primary_resource_type'] as $type_id){
				$pri_auditory = 'false';
				$pri_text = 'false';
				$pri_visual = 'false';

				switch($type_id){
					case 1:
						$pri_auditory = 'true';
						break;
					case 3:
						$pri_text = 'true';
						break;
					case 4:
						$pri_visual = 'true';
						break;
				}
			} ?>
		<resource>
			<primaryResource is_auditory="<?php echo $pri_auditory; ?>" is_visual="<?php echo $pri_visual; ?>" is_textual="<?php echo $pri_text; ?>" lang="<?php echo $items['language_code']; ?>"><?php echo $items['resource']; ?></primaryResource>
			<?php foreach ($items['secondary_resources'] as $id=>$sec_items): 
					$sec_auditory	= 'false';
					$sec_text		= 'false';
					$sec_visual		= 'false';					
					if (in_array("1", $sec_items['resource_type'])){
						$sec_auditory = 'true';
					}
					if (in_array("3", $sec_items['resource_type'])){
						$sec_text = 'true';
					}
					if (in_array("4", $sec_items['resource_type'])){
						$sec_visual = 'true';
					}
			?>
			<secondaryResource is_auditory="<?php echo $sec_auditory; ?>" is_visual="<?php echo $sec_visual; ?>" is_textual="<?php echo $sec_text; ?>" lang="<?php echo $sec_items['language_code']; ?>"><?php echo $sec_items['resource']; ?></secondaryResource>
			<?php endforeach; ?>
		</resource>
		<?php endforeach; ?>
	</resources>
</accessForAll>