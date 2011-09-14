<<?php echo '?'; ?>xml version="1.0" encoding="<?php echo $this->encoding; ?>"<?php echo '?'; ?>>
<!-- true or false question (aka multiple choice with two choices) -->
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
	<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
	  <correctResponse>
		<?php if ($this->row['answer_0'] == 1): ?>
			<value>ChoiceT</value>
		<?php else: ?>
			<value>ChoiceF</value>
		<?php endif; ?>
	  </correctResponse>
	  <?php if ($this->row['feedback']): ?>
		  <outcomeDeclaration identifier="FEEDBACK" cardinality="single" baseType="identifier"/>
	  <?php endif; ?>
	</responseDeclaration>

	<itemBody>
		<choiceInteraction responseIdentifier="RESPONSE<?php echo $this->row['question_id']; ?>" shuffle="false" maxChoices="1">
			<prompt><?php echo $this->row['question']; ?></prompt>
			<simpleChoice identifier="ChoiceT" fixed="true"><?php echo _AT('true'); ?></simpleChoice> 
			<simpleChoice identifier="ChoiceF" fixed="true"><?php echo _AT('false'); ?></simpleChoice> 
	  </choiceInteraction>
	</itemBody>

	<?php if ($this->row['feedback']): ?>
		<modalFeedback outcomeIdentifier="FEEDBACK" identifier="FEEDBACK" showHide="hide"><?php echo $this->row['feedback']; ?></modalFeedback> 
	<?php endif; ?>
</assessmentItem>