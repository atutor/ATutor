<<?php echo '?'; ?>xml version="1.0" encoding="<?php echo $this->encoding; ?>"<?php echo '?'; ?>>

<questestinterop 
	xmlns="http://www.imsproject.org/question"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
>
	<assessment title="<?php echo $this->title; ?>" ident="<?php echo md5($this->tid); ?>">
		<section ident="ATUTOR-<?php echo $this->row['question_id']; ?>" title="<?php echo $this->title; ?>">

		<?php echo $this->xml_content; ?>

		</section>
	</assessment>

</questestinterop>
