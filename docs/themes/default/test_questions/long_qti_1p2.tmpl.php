<!-- open ended (free text) question -->
		<item title="Open ended question">
			<itemmetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_itemtype</fieldlabel>
						<fieldentry>String</fieldentry>
					</qtimetadatafield>
				</qtimetadata>
				<qtimetadata>
					<qtimetadatafield>
						<fieldlabel>qmd_questiontype</fieldlabel>
						<fieldentry>FIB-string</fieldentry>
					</qtimetadatafield>
				</qtimetadata>
			</itemmetadata>
			<presentation>
				<flow>
					<material>
						<mattext texttype="text/html"><?php echo $this->row['question']; ?></mattext>
					</material>
					<response_str ident="RESPONSE<?php echo $this->row['question_id']; ?>" rcardinality="Single">
						<render_fib>
							<response_label ident="Choice<?php echo $i; ?>" />
						</render_fib>
					</response_str>
				</flow>
			</presentation>
		</item>
