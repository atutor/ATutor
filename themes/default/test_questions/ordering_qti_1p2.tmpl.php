<!-- ordering question -->
		<item title="Ordering question" ident="ITEM_<?php echo $this->row['question_id']; ?>">
			<itemmetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_itemtype</fieldlabel>
						<fieldentry>Logical Identifier</fieldentry>
					</qtimetadatafield>
				</qtimetadata>
			</itemmetadata>
			<presentation>
				<flow>
					<material>
						<mattext texttype="text/html"><?php echo $this->row['question']; ?></mattext>
					</material>
					<response_lid ident="RESPONSE<?php echo $this->row['question_id']; ?>" rcardinality="Ordered">
						<render_choice shuffle="Yes" minnumber="<?php echo $this->num_choices;?>" maxnumber="<?php echo $this->num_choices;?>">
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
				<respcondition title="CorrectResponse">
					<conditionvar>					
						<varequal respident="RESPONSE<?php echo $this->row['question_id']; ?>">Choice<?php echo $i; ?></varequal>
					</conditionvar>
					<setvar varname="Respondus_Correct" action="Add"><?php echo (isset($this->row['weight']))?$this->row['weight']/$this->num_choices:1; ?></setvar>
				</respcondition>
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
