<?xml version="1.0" encoding="<?php echo $this->encoding; ?>"?>
<!-- matching question with partial marks -->
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
	<responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="directedPair">
		<correctResponse>
			<?php for ($i=0; $i < $this->num_choices; $i++): ?>
				<value>Choice<?php echo $i; ?> Option<?php echo $this->row['answer_'.$i]; ?></value>
			<?php endfor; ?>
		</correctResponse>
		<mapping lowerBound="0" defaultValue="0">
			<?php for ($i=0; $i < $this->num_choices; $i++): ?>
				<mapEntry mapKey="Choice<?php echo $i; ?> Option<?php echo $this->row['answer_'.$i]; ?>" mappedValue="1"/>
			<?php endfor; ?>
		</mapping>
	  <?php if ($this->row['feedback']): ?>
		  <outcomeDeclaration identifier="FEEDBACK" cardinality="multiple" baseType="identifier"/>
	  <?php endif; ?>
	</responseDeclaration>

	<itemBody>
		<associateInteraction responseIdentifier="RESPONSE" shuffle="true" maxAssociations="<?php echo $this->num_choices; ?>">
			<prompt><?php echo $this->row['question']; ?></prompt>
			<simpleMatchSet>
				<?php for ($i=0; $i < $this->num_choices; $i++): ?>
					<simpleAssociableChoice identifier="Choice<?php echo $i; ?>" matchMax="1"><?php echo $this->row['choice_'.$i]; ?></simpleAssociableChoice>
				<?php endfor; ?>
			</simpleMatchSet>

			<simpleMatchSet>
				<?php for ($i=0; $i < $this->num_options; $i++): ?>
					<simpleAssociableChoice identifier="Option<?php echo $i; ?>" matchMax="<?php echo $this->num_choices; ?>"><?php echo $this->row['option_'.$i]; ?></simpleAssociableChoice>
				<?php endfor; ?>
			</simpleMatchSet>
		</associateInteraction>
	</itemBody>

	<?php if ($this->row['feedback']): ?>
		<modalFeedback outcomeIdentifier="FEEDBACK" identifier="FEEDBACK” showHide="hide"><?php echo $this->row['feedback']; ?></modalFeedback> 
	<?php endif; ?>
</assessmentItem>