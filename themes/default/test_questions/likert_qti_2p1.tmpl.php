<<?php echo '?'; ?>xml version="1.0" encoding="<?php echo $this->encoding; ?>"<?php echo '?'; ?>>
<!-- likert question (aka multiple choice with no correct answer) -->
<assessmentItem 
	xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 imsqti_v2p1.xsd" 
	identifier="ATUTOR-<?php echo $this->row['question_id']; ?>" 
	title="<?php echo $this->row['question']; ?>" 
	adaptive="false" 
	timeDependent="false"
	toolname="ATutor - atutor.ca"
	toolversion="<?php echo VERSION; ?>"
>

	<itemBody>
		<choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="1">
			<prompt><?php echo $this->row['question']; ?></prompt>
			<?php for ($i=0; $i < $this->num_choices; $i++): ?>
				<simpleChoice identifier="Choice<?php echo $i; ?>" fixed="true"><?php echo $this->row['choice_'.$i]; ?></simpleChoice> 
			<?php endfor; ?>
	  </choiceInteraction>
	</itemBody>

</assessmentItem>