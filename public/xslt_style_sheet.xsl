<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:aws="http://webservices.amazon.com/AWSECommerceService/2009-07-01"
version="1.0">
<xsl:output method="html" encoding="UTF-8"/>

<xsl:template match="/">
<html lang="ja">
<head>
<title>XSLサンプル</title>
</head>
<body>

<xsl:apply-templates select="aws:">

<p>サンプル</p>

</body>
</html>
</xsl:template>


</xsl:stylesheet>
