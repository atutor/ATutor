<accessForAllResource>  
	<?php 
	if (!empty($this->orig_access_mode)):
	foreach($this->orig_access_mode as $orig_access_mode):
	?>
	<accessModeStatement>		
		<originalAccessMode><?php echo $orig_access_mode; ?></originalAccessMode>
		<accessModeUsage>informative</accessModeUsage>
		<language><?php echo $this->language_code; ?></language>
	</accessModeStatement>
	<?php 
	endforeach;
	endif; 
	?>
	
	<?php if (!empty($this->secondary_resources)): ?>
	<?php foreach ($this->secondary_resources as $uri): ?>
	<hasAdaptation><?php echo $this->relative_path.$uri; ?></hasAdaptation>
	<?php endforeach; ?>
	<?php endif; ?>

	<?php if (isset($this->primary_resources) && $this->primary_resources!=''): ?>
	<isAdaptation>
		<isAdaptationOf><?php echo $this->relative_path.$this->primary_resource_uri; ?></isAdaptationOf>
		<extent>full</extent>
	</isAdaptation>
	<?php foreach ($this->primary_resources as $type_id): ?>
	<adaptationStatement>
			<?php
			switch($type_id){
				case 1:
					$orig_access_mode = 'auditory';
					break;
				case 3:
					$orig_access_mode = 'textual';
					break;
				case 2:
				case 4:
					//sign language, and visual
					$orig_access_mode = 'visual';
					break;
				default:
					$orig_access_mode = '';
			}
		?>
		<originalAccessMode><?php echo $orig_access_mode; ?></originalAccessMode>
	</adaptationStatement>
	<?php endforeach; ?>
	<?php endif; ?>
</accessForAllResource>