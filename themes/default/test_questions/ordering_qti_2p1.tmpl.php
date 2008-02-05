<<?php echo '?'; ?>xml version="1.0" encoding="<?php echo $this->encoding; ?>"<?php echo '?'; ?>>
<!-- ordering question -->
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

	<responseDeclaration identifier="RESPONSE" cardinality="ordered" baseType="identifier">
		<correctResponse>
		<?php for ($i=0; $i < $this->num_choices; $i++): ?>
			<value>Choice<?php echo $i; ?></value>
		<?php endfor; ?>
		</correctResponse>
	  <?php if ($this->row['feedback']): ?>
		  <outcomeDeclaration identifier="FEEDBACK" cardinality="multiple" baseType="identifier"/>
	  <?php endif; ?>
	</responseDeclaration>

	<itemBody>
		<orderInteraction responseIdentifier="RESPONSE" shuffle="true">
			<prompt><?php echo $this->row['question']; ?></prompt>
			<?php for ($i=0; $i < $this->num_choices; $i++): ?>
				<simpleChoice identifier="Choice<?php echo $i; ?>" fixed="false"><?php echo $this->row['choice_'.$i]; ?></simpleChoice> 
			<?php endfor; ?>
		</orderInteraction>
	</itemBody>
	<?php if ($this->row['feedback']): ?>
		<modalFeedback outcomeIdentifier="FEEDBACK" identifier="FEEDBACK<?php echo $this->row['question_id'];?>” showHide="hide"><?php echo $this->row['feedback']; ?></modalFeedback> 
	<?php endif; ?>
</assessmentItem>