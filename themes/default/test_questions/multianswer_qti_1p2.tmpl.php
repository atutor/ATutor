<!-- multi answer multiple choice question with partial marks -->
		<item title="Multiple answer question" ident="ITEM_<?php echo $this->row['question_id']; ?>">
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
						<fieldentry>Multiple-response</fieldentry>
					</qtimetadatafield>
				</qtimetadata>
			</itemmetadata>
			<presentation>
				<flow>
					<material>
						<mattext texttype="text/html"><?php echo $this->row['question']; ?></mattext>
					</material>
					<response_lid ident="RESPONSE<?php echo $this->row['question_id']; ?>" rcardinality="Multiple">
						<render_choice shuffle="No">
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
					<setvar varname="Respondus_Correct" action="Add"><?php echo (isset($this->row['weight']))?$this->row['weight']:1; ?></setvar>
				</respcondition>
				<?php endif; ?>
			<?php endfor; ?>
			</resprocessing>
		<?php if ($this->row['feedback']): ?>
			<itemfeedback ident="FEEDBACK" view="All">
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
