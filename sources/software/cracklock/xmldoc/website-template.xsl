<?xml version='1.0'?> 
<xsl:stylesheet  
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"> 

<xsl:import href="docbook-xsl-1.73.2/xhtml/docbook.xsl"/>

<xsl:param name="html.stylesheet" select="'/perso.css'"/> 

<xsl:template match="*" mode="process.root">
  <xsl:variable name="doc" select="self::*"/>

  <xsl:call-template name="user.preroot"/>
  <xsl:call-template name="root.messages"/>

  <html>
    <head>
      <xsl:call-template name="system.head.content">
        <xsl:with-param name="node" select="$doc"/>
      </xsl:call-template>
      <xsl:call-template name="head.content">
        <xsl:with-param name="node" select="$doc"/>
      </xsl:call-template>
      <xsl:call-template name="user.head.content">
        <xsl:with-param name="node" select="$doc"/>
      </xsl:call-template>
  </script>
  
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','http://www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-77730-1', 'auto');
    ga('send', 'pageview');
    </script>

  
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
  <link rel="icon" type="image/png" href="favicon.png" />
  <meta name="keywords" content="cracklock, latexdaemon, latex, truetype, william blum, lambda calculus, oxford" />
    </head>
    <body id="threecolumn" background="images/fond.jpg">
    <div id="container">

      <xsl:call-template name="body.attributes"/>
      
      <xsl:call-template name="user.header.content">
        <xsl:with-param name="node" select="$doc"/>
      </xsl:call-template>
      
<xsl:text disable-output-escaping="yes">&lt;?php </xsl:text>
	<xsl:text disable-output-escaping="yes">?></xsl:text>
    <div id="main-content">
      <xsl:apply-templates select="."/>
      </div>
      
      <xsl:text disable-output-escaping="yes">&lt;?php </xsl:text>
      include("../../../common/footer.html");
      <xsl:text disable-output-escaping="yes">?></xsl:text>

      <xsl:call-template name="user.footer.content">
        <xsl:with-param name="node" select="$doc"/>
      </xsl:call-template>   
    </div>      
    </body>
  </html>
  <xsl:value-of select="$html.append"/>
</xsl:template>

</xsl:stylesheet> 
