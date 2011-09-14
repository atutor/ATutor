<!-- open ended (free text) question -->
		<item title="Open ended question" ident="ITEM_<?php echo $this->row['question_id']; ?>">
			<itemmetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_itemtype</fieldlabel>
						<fieldentry>String</fieldentry>
					</qtimetadatafield>
					<qtimetadatafield>
						<fieldlabel>qmd_questiontype</fieldlabel>
						<fieldentry>FIB-string</fieldentry>
					</qtimetadatafield>
                    <qtimetadatafield>
                        <fieldlabel>cc_profile</fieldlabel>
                        <fieldentry>cc.fib.v0p1</fieldentry>
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
					<response_str ident="RESPONSE<?php echo $this->row['question_id']; ?>" rcardinality="Single">
						<?php
							$fib_columns = 20;
							$fib_rows = 1;

							if ($this->row['properties'] == 1){
								$fib_columns = 5;
							} elseif ($this->row['properties'] == 2){
								$fib_rows = 1;
							} elseif ($this->row['properties'] == 3){
								$fib_rows = 3;
							} elseif ($this->row['properties'] == 4){
								$fib_rows = 8;
							} 
						?>
						<render_fib rows="<?php echo $fib_rows; ?>" columns="<?php echo $fib_columns; ?>">
							<response_label ident="Choice<?php echo $i; ?>" />
						</render_fib>
					</response_str>
				</flow>
			</presentation>
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
