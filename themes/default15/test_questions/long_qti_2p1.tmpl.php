<<?php echo '?'; ?>xml version="1.0" encoding="<?php echo $this->encoding; ?>"<?php echo '?'; ?>>
<!-- open ended (free text) question -->
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

	<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="string">
	  <?php if ($this->row['feedback']): ?>
		  <outcomeDeclaration identifier="FEEDBACK" cardinality="single" baseType="identifier"/>
	  <?php endif; ?>
	</responseDeclaration>


	<itemBody>
		<extendedTextInteraction responseIdentifier="RESPONSE">
			<prompt><?php echo $this->row['question']; ?></prompt>
		</extendedTextInteraction>
	</itemBody>

	<?php if ($this->row['feedback']): ?>
		<modalFeedback outcomeIdentifier="FEEDBACK" identifier="FEEDBACK” showHide="hide"><?php echo $this->row['feedback']; ?></modalFeedback> 
	<?php endif; ?>
</assessmentItem>