<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output
            method="xml"
            version="1.0"
            encoding="UTF-8"
            omit-xml-declaration="no"
            indent="yes"
            media-type="string"/>

    <xsl:template match="/content">
        <root><xsl:value-of select="current()"></xsl:value-of></root>
    </xsl:template>

</xsl:stylesheet>
