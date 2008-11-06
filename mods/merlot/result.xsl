<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" />
<xsl:template match="results">
<html>
<body>
  <xsl:for-each select="material"> 
<div style="border:thin black solid; padding:1em;width:90%;">

		<a><xsl:attribute name="href">
			<xsl:value-of select="URL" />
		</xsl:attribute><xsl:value-of select="title"/> </a> 
		    <br/><br/> 
    		<xsl:value-of select="description" /> 
</div><br />
  </xsl:for-each>

</body>
</html>
</xsl:template>
</xsl:stylesheet>
