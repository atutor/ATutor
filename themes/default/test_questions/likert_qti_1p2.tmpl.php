<!-- likert question (aka multiple choice with no correct answer) -->
		<item title="Multiple choice question" ident="ITEM_<?php echo $this->row['question_id']; ?>">
			<itemmetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_itemtype</fieldlabel>
						<fieldentry>Logical Identifier</fieldentry>
					</qtimetadatafield>
					<qtimetadatafield>
						<fieldlabel>qmd_questiontype</fieldlabel>
						<fieldentry>Multiple-choice</fieldentry>
					</qtimetadatafield>
                    <qtimetadatafield>
                        <fieldlabel>cc_profile</fieldlabel>
                        <fieldentry>cc.mutliple_choice.v0p1</fieldentry>
                    </qtimetadatafield>
				</qtimetadata>
			</itemmetadata>
			<presentation>
				<flow>
					<material>
						<mattext texttype="text/html"><?php echo $this->row['question']; ?></mattext>
					</material>
					<response_lid ident="RESPONSE<?php echo $this->row['question_id']; ?>" rcardinality="Single">
						<render_choice shuffle="No" minnumber="1" maxnumber="1">
							<flow_label>
							<?php for ($i=0; $i < $this->num_choices; $i++): ?>
								<response_label ident="Choice<?php echo $i; ?>">	
									<material>
										<mattext texttype="text/html"><?php echo $this->row['choice_'.$i]; ?></mattext>
									</material>
								</response_label>
							<?php endfor; ?>
							</flow_label>
						</render_choice>
					</response_lid>
				</flow>
			</presentation>

			<resprocessing>
				<?php for ($i=0; $i < $this->num_choices; $i++): ?>
				<respcondition title="CorrectResponse">
					<conditionvar>					
						<varequal respident="RESPONSE<?php echo $this->row['question_id']; ?>">Choice<?php echo $i; ?></varequal>
					</conditionvar>
					<setvar varname="que_score" action="Set"><?php echo (isset($this->row['weight']))?$this->row['weight']:1; ?></setvar>
				</respcondition>
				<?php endfor; ?>
			</resprocessing>
		</item>
