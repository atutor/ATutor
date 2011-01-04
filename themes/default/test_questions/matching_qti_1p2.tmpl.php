<!-- matching question with partial marks -->
		<item title="Mathcing question" ident="ITEM_<?php echo $this->row['question_id']; ?>">
			<itemmetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_itemtype</fieldlabel>
						<fieldentry>Logical Groups</fieldentry>
					</qtimetadatafield>
					<qtimetadatafield>
						<fieldlabel>qmd_questiontype</fieldlabel>
						<fieldentry>Drag-and-drop</fieldentry>
					</qtimetadatafield>
                    <qtimetadatafield>
                        <fieldlabel>cc_profile</fieldlabel>
                        <fieldentry>cc.pattern_match.v0p1</fieldentry>
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
					<?php for ($i=0; $i < $this->num_choices; $i++): ?>		
					<response_lid ident="RESPONSE-<?php echo md5($this->row['question_id'].$i); ?>" rcardinality="Multiple">
						<material>
							<mattext texttype="text/html"><?php echo $this->row['choice_'.$i]; ?></mattext>
						</material>
						<render_choice shuffle="No">
						<?php for ($j=0; $j < $this->num_options; $j++): ?>
							<response_label ident="Option<?php echo $j; ?>">	
								<flow_mat>
									<material>
										<mattext texttype="text/html"><?php echo $this->row['option_'.$j]; ?></mattext>
									</material>
								</flow_mat>
							</response_label>
						<?php endfor; ?>
						</render_choice>
					</response_lid>
					<?php endfor; ?>
				</flow>
			</presentation>

			<resprocessing>
				<outcomes>
					<decvar varname="SCORE" />
				</outcomes>
			<?php for ($i=0; $i < $this->num_choices; $i++): ?>
				<?php if ($this->row['answer_'.$i] > -1): ?>
				<respcondition title="CorrectResponse">
					<conditionvar>						
						<varequal respident="RESPONSE-<?php echo md5($this->row['question_id'].$i); ?>">Option<?php echo $this->row['answer_'.$i]; ?></varequal>
					</conditionvar>
					<setvar varname="Respondus_Correct"><?php echo (isset($this->row['weight']))?$this->row['weight']:1; ?></setvar>
				</respcondition>
				<?php endif; ?>
			<?php endfor; ?>
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
