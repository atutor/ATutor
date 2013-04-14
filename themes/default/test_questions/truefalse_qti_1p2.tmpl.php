<!-- true or false question (aka multiple choice with two choices) -->
		<item title="True or False question" ident="ITEM_<?php echo $this->row['question_id']; ?>">
			<itemmetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_itemtype</fieldlabel>
						<fieldentry>Logical Identifier</fieldentry>
					</qtimetadatafield>
                    <qtimetadatafield>
						<fieldlabel>qmd_questiontype</fieldlabel>
						<fieldentry>True/false</fieldentry>
					</qtimetadatafield>
                    <qtimetadatafield>
						<fieldlabel>cc_profile</fieldlabel>
						<fieldentry>cc.true_false.v0p1</fieldentry>
					</qtimetadatafield>
                    <qtimetadatafield>
						<fieldlabel>cc_weighting</fieldlabel>
						<fieldentry><?php echo $this->weight; ?></fieldentry>
					</qtimetadatafield>
				</qtimetadata>
			</itemmetadata>
			<presentation>
				<flow>
					<material>
						<mattext texttype="text/html"><?php echo $this->row['question']; ?></mattext>
					</material>
					<response_lid ident="RESPONSE<?php echo $this->row['question_id']; ?>" rcardinality="Single">
						<render_choice shuffle="No">
							<response_label ident="ChoiceT">	
								<flow_mat>
									<material>
										<mattext texttype="text/html"><?php echo _AT('true'); ?></mattext>
									</material>
								</flow_mat>
							</response_label>
							<response_label ident="ChoiceF">	
								<flow_mat>
									<material>
										<mattext texttype="text/html"><?php echo _AT('false'); ?></mattext>
									</material>
								</flow_mat>
							</response_label>
						</render_choice>
					</response_lid>
				</flow>
			</presentation>
			<resprocessing>
				<outcomes>
					<decvar varname="SCORE" />
				</outcomes>
				<respcondition title="CorrectResponse">
					<conditionvar>
						<varequal respident="RESPONSE<?php echo $this->row['question_id']; ?>">
							<?php 
								if ($this->row['answer_0'] == 1) {
									echo 'ChoiceT'; 
								} else {
									echo 'ChoiceF';
								}
							?>
						</varequal>
					</conditionvar>
					<setvar varname="que_score" action="Set"><?php echo (isset($this->row['weight']))?$this->row['weight']:1; ?></setvar>
				</respcondition>
			</resprocessing>
		<?php if ($this->row['feedback']): ?>
			<itemfeedback ident="FEEDBACK">
				<solution>
					<solutionmaterial>
						<flow_mat>
							<material>
								<mattext texttype="text/html"><?php echo $this->row['feedback']; ?></mattext>
							</material>
						</flow_mat>
					</solutionmaterial>
				</solution>
			</itemfeedback>
		<?php endif; ?>
		</item>
