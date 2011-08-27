<?xml version="1.0" encoding="UTF-8"?>
	<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:exsl="http://exslt.org/common">
	
	
	
	
	<!--
		Contains current-page hyerarchy.
	-->
	<xsl:variable name="current-page-hyerarchy">
		<xsl:call-template name="split-path" />
		<p><xsl:value-of select="/data/params/current-page" /></p>
	</xsl:variable>
	
	
	
	
	<!-- 
		Builds the URL without Parameters (page and URL).
		
		e.g.: www.domain.com/en/grand-parent-page/parent-page/page
	
		@param p
			- XML nodeset containig page hyerarchy
			- defaults to current-page hyerarchy
		@param languageCode
			- language code to search for
			- defaults to current-language set by Language Redirect
		@param naviNode
			- root node that holds the pages
			- defaults to 'navigare', the handle of my navigation Datasource
		@param cp = current iterated page
	-->
	<xsl:template name="plh-url">
		<xsl:param name="p" select="$current-page-hyerarchy" />
		<xsl:param name="languageCode" select="/data/events/language-redirect/current-language/@handle" />
		<xsl:param name="naviNode" select="'navigare'" />
		<xsl:param name="cp" select="1" />
		
		<xsl:variable name="p-handle" select="exsl:node-set($p)/p[$cp]" />
		
		<xsl:value-of select="concat(
			/data/params/root,
			'/',
			$languageCode,
			'/'
		)" />
	
		<xsl:apply-templates select="/data/*[local-name() = $naviNode]/page[ @handle = $p-handle ]" mode="plh">
			<xsl:with-param name="p" select="$p" />
			<xsl:with-param name="languageCode" select="$languageCode" />
			<xsl:with-param name="cp" select="$cp + 1" />
		</xsl:apply-templates>
		
	</xsl:template>	
	
	
	<xsl:template match="page" mode="plh">
		<xsl:param name="p" select="$current-page-hyerarchy" />
		<xsl:param name="languageCode" select="/data/events/language-redirect/current-language/@handle" />
		<xsl:param name="cp" select="1" />

		<xsl:variable name="p-handle" select="exsl:node-set($p)/p[$cp]" />
	
		<xsl:value-of select="item[ @lang = $languageCode ]/@handle" />
		<xsl:text>/</xsl:text>
		
		<xsl:apply-templates select="page[ descendant-or-self::page[@handle = $p-handle] ]" mode="plh">
			<xsl:with-param name="p" select="$p" />
			<xsl:with-param name="languageCode" select="$languageCode" />
			<xsl:with-param name="cp" select="$cp + 1" />
		</xsl:apply-templates>
		
	</xsl:template>
	
	
	
	
	<!--
		Generates links for current-page in all languages. Languages are add as:
		
			<a title="languageName">
				<img src="path-to-image" alt="languageName" />
			</a>
		
		In my case the images are located in `workspace/public/css/images/flags`. I took them from
	-->
	<xsl:template name="plh-site-languages">
		<xsl:apply-templates select="/data/events/language-redirect/supported-languages/item" mode="plh" />
	</xsl:template>
	
	
	<xsl:template match="language-redirect/supported-languages/item" mode="plh">
		
		<xsl:variable name="imagesPath" select="concat(/data/params/workspace, '/public/css/images/flags/')" />
		<xsl:variable name="imagesExtension" select="'png'" />
		
		<a title="{text()}">
			<xsl:attribute name="href">
			
				<!-- root-url and handles of pages -->
				<xsl:call-template name="plh-url">
					<xsl:with-param name="languageCode" select="@handle" />
					<xsl:with-param name="naviNode" select="'plh-page'" />
				</xsl:call-template>
				
				<!-- page params -->
				<xsl:call-template name="plh-page-parameters">
					<xsl:with-param name="languageCode" select="@handle" />
				</xsl:call-template>
				
				<!-- url params -->
				<xsl:call-template name="plh-url-parameters" />
				
			</xsl:attribute>
			
			<img src="{$imagesPath}{@handle}.{$imagesExtension}" alt="{text()}" />
		</a>
		
	</xsl:template>
	
	
	<!--
		Populates the URL with page parameters. Overload this on all pages that have PAGE parameters.
		It should return the page parameters separated by slash '/':
		
		`page-param-1/page-param-2/page-param-3`
	-->
	<xsl:template name="plh-page-parameters">
		<xsl:param name="languageCode" />
	</xsl:template>
	
	
	<!--
		Outputs page parameters. Call it whenever you need them as well.
		
		`?url_param_1=foo&url_param_2=bar`
	-->
	<xsl:template name="plh-url-parameters">
		<xsl:variable name="url-params" select="substring-after(/data/params/current-url, '?')" />
		
		<xsl:if test="$url-params != ''">
			<xsl:text>?</xsl:text>
			<xsl:value-of select="$url-params" />
		</xsl:if>
	</xsl:template>
	
	


	<!--
		Helper. Splits "current page path" in handles.
	-->
	<xsl:template name="split-path">
		<xsl:param name="parent-path">
			<xsl:value-of select="/data/params/parent-path" />
			<xsl:if test="/data/params/parent-path != '/'">
				<xsl:text>/</xsl:text>
			</xsl:if>
		</xsl:param>
		
		<xsl:variable name="path-crop" select="substring($parent-path, 2)" />
		
		<xsl:if test="$path-crop != ''">
			<p><xsl:value-of select="substring-before( $path-crop, '/')" /></p>
			
			<xsl:variable name="path-other" select="substring-after($path-crop, '/')" />
			
			<xsl:if test="$path-other != ''">
				<xsl:call-template name="split-path">
					<xsl:with-param name="parent-path" select="concat( '/', $path-other )" />
				</xsl:call-template>
			</xsl:if>
		</xsl:if>
	</xsl:template>




</xsl:stylesheet>