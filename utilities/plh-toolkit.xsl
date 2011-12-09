<?xml version="1.0" encoding="UTF-8"?>
	<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:exsl="http://exslt.org/common"
		xmlns:string="http://symphony-cms.com/functions">
	
	
	
	
	<xsl:import href="string-op.xsl" />
	
	
	
	
	<xsl:template match="page" mode="plh-url">
	    <xsl:value-of select="concat(/data/params/root,'/',/data/events/language-redirect/current-language/@handle,'/',item/@handle, '/')"/>
	</xsl:template>
	
	<xsl:template match="page/page" mode="plh-url">
	    <xsl:apply-templates select="parent::page" mode="plh-url"/>
	    <xsl:value-of select="concat(item/@handle, '/')"/>
	</xsl:template>
	
	
	
	
	<!--
		Generates links for current-page in all languages. Languages are add as:
		
			<a title="languageName">
				<img src="path-to-image" alt="languageName" />
			</a>
		
		In my case the images are located in `workspace/public/css/images/flags`.
		I took them from "Backend languages" extension.
	-->
	<xsl:template name="plh-site-languages">
		<xsl:apply-templates select="/data/events/language-redirect/supported-languages/item" mode="plh" />
	</xsl:template>
	
	
	<xsl:template match="language-redirect/supported-languages/item" mode="plh">
		<xsl:variable name="v_images_path" select="concat(/data/params/workspace, '/public/css/images/flags/')" />
		
		<a title="{text()}">
			<xsl:attribute name="href">
				<!-- root and page handle -->
				<xsl:apply-templates select="/data/plh-page//page[ @id=/data/params/current-page-id ]" mode="plh-page">
					<xsl:with-param name="lang" select="@handle" />
				</xsl:apply-templates>
				
				<!-- page params -->
				<xsl:call-template name="plh-page-parameters">
					<xsl:with-param name="lang" select="@handle" />
				</xsl:call-template>
				
				<!-- url params -->
				<xsl:call-template name="plh-url-parameters" />
			</xsl:attribute>
			
			<img src="{$v_images_path}{@handle}.png" alt="{text()}" />
		</a>
	</xsl:template>



	<xsl:template match="page" mode="plh-page">
		<xsl:param name="lang" />
	    <xsl:value-of select="concat(/data/params/root, '/', $lang, '/', item[@lang=$lang]/@handle, '/')"/>
	</xsl:template>
	
	<xsl:template match="page/page" mode="plh-page">
		<xsl:param name="lang" />
	    <xsl:apply-templates select="parent::page" mode="plh-page">
	    	<xsl:with-param name="lang" select="$lang" />
	    </xsl:apply-templates>
	    <xsl:value-of select="concat(item[@lang=$lang]/@handle, '/')"/>
	</xsl:template>


	
	
	<!--
		Populates with Page parameters. Overload this on all pages that have Page parameters.
		It should return the page parameters separated by slash '/':
		
		`page-param-1/page-param-2/page-param-3`
	-->
	<xsl:template name="plh-page-parameters">
		<xsl:param name="lang" />
	</xsl:template>
	
	
	
	
	<!--
		Populates with URL parameters. Call it whenever you need them as well.
		
		`?url-param-1=foo&url-param-2=bar`
	-->
	<xsl:template name="plh-url-parameters">
		<xsl:variable name="v_url_params" select="substring-after(/data/params/current-url, '?')" />
		
		<xsl:if test="$v_url_params != ''">
			<xsl:text>?</xsl:text>
			<xsl:value-of select="$v_url_params" />
		</xsl:if>
	</xsl:template>




</xsl:stylesheet>