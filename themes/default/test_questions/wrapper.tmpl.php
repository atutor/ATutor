<<?php echo '?'; ?>xml version="1.0" encoding="<?php echo $this->encoding; ?>"<?php echo '?'; ?>>

<questestinterop  
  xmlns="http://www.imsglobal.org/xsd/ims_qtiasiv1p2"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
  xsi:schemaLocation="http://www.imsglobal.org/xsd/ims_qtiasiv1p2 http://www.imsglobal.org/profile/cc/ccv1p0/derived_schema/domainProfile_4/ims_qtiasiv1p2_localised.xsd"
>
	<assessment title="<?php echo $this->title; ?>" ident="<?php echo md5($this->tid); ?>">
        <?php 
            /* If this is Common Cartridge export, add these metadatas
             * Based on the IMSCC 1.0 specification
             * http://www.imsglobal.org/cc/ccv1p0/imscc_profilev1p0.html
             * Check Section 4.9 QTI
             */
        if ($this->use_cc):
            $this->num_takes = intval($this->num_takes);
            $this->num_takes = ($this->num_takes==0)?'unlimited':$this->num_takes;
        ?>
        <qtimetadata>
                <qtimetadatafield>
                    <fieldlabel>cc_profile</fieldlabel>
                    <fieldentry>cc.exam.v0p1</fieldentry>
                </qtimetadatafield>
                <qtimetadatafield>
                    <fieldlabel>qmd_assessmenttype</fieldlabel>
                    <fieldentry>Examination</fieldentry>
                </qtimetadatafield>
                <qtimetadatafield>
                    <fieldlabel>qmd_scoretype</fieldlabel>
                    <fieldentry>Percentage</fieldentry>
                </qtimetadatafield>
                <qtimetadatafield>
                    <fieldlabel>qmd_feedbackpermitted</fieldlabel>
                    <fieldentry>No</fieldentry>
                </qtimetadatafield>
                <qtimetadatafield>
                    <fieldlabel>qmd_hintspermitted</fieldlabel>
                    <fieldentry>Yes</fieldentry>
                </qtimetadatafield>
                <qtimetadatafield>
                    <fieldlabel>qmd_solutionspermitted</fieldlabel>
                    <fieldentry>Yes</fieldentry>
                </qtimetadatafield>
                <qtimetadatafield>
                    <fieldlabel>qmd_timelimit</fieldlabel>
                    <fieldentry>120</fieldentry>
                </qtimetadatafield>
                <qtimetadatafield>
                    <fieldlabel>cc_allow_late_submission</fieldlabel>
                    <fieldentry>No</fieldentry>
                </qtimetadatafield>
                <qtimetadatafield>
                    <fieldlabel>cc_maxattempts</fieldlabel>
                    <fieldentry><?php echo $this->num_takes; ?></fieldentry>
                </qtimetadatafield>
        </qtimetadata>
        <?php endif; ?>
        
		<section ident="ATUTOR-<?php echo $this->row['question_id']; ?>" title="<?php echo $this->title; ?>">
		<?php echo $this->xml_content; ?>
		</section>
	</assessment>
</questestinterop>
