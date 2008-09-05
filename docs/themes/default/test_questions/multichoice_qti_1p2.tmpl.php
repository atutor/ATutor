<!-- single answer multiple choice question -->
		<item title="Multiple choice question">
			<itemmetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_itemtype</fieldlabel>
						<fieldentry>Logical Identifier</fieldentry>
					</qtimetadatafield>
				</qtimetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_questiontype</fieldlabel>
						<fieldentry>Multiple-choice</fieldentry>
					</qtimetadatafield>
				</qtimetadata>
			</itemmetadata>
			<presentation>
				<material>
					<mattext texttype="text/html"><?php echo $this->row['question']; ?></mattext>
				</material>
				<flow>
					<response_lid ident="RESPONSE<?php echo $this->row['question_id']; ?>" rcardinality="Single">
						<render_choice shuffle="No" minnumber="1" maxnumber="1">
						<?php for ($i=0; $i < $this->num_choices; $i++): ?>
							<response_label ident="Choice<?php echo $i; ?>">
								<flow_mat>
									<material>
										<mattext texttype="text/html"><?php echo $this->row['choice_'.$i]; ?></mattext>
									</material>
								</flow_mat>
							</response_label>
						<?php endfor; ?>
						</render_choice>
					</response_lid>
				</flow>
			</presentation>
			<resprocessing>
				<outcomes>
					<decvar/>
				</outcomes>
				<?php for ($i=0; $i < $this->num_choices; $i++): ?>
					<?php if ($this->row['answer_'.$i]): ?>
				<respcondition title="CorrectResponse">
					<conditionvar>						
						<varequal respident="RESPONSE<?php echo $this->row['question_id']; ?>">Choice<?php echo $i; ?></varequal>
					</conditionvar>
					<setvar varname="que_score" action="Set"><?php echo (isset($this->row['weight']))?$this->row['weight']:1; ?></setvar>
				</respcondition>
					<?php endif; ?>
				<?php endfor; ?>						
			</resprocessing>
		<?php if ($this->row['feedback']): ?>
			<itemfeedback ident="FEEDBACK" view="All">
				<material>
					<mattext texttype="text/html"><?php echo $this->row['feedback']; ?></mattext>
				</material>
			</itemfeedback>
		<?php endif; ?>
		</item>