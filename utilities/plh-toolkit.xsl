<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform">




	<!--
			These templates **assume** you attached the following Datasources to your page:

			- FL: Languages
			- PLH: Page
			- a navigation datasource with your Pages

			If you didn't, you should attach them ;)
		 -->




	<!-- 
			Page URL generator.

			If your navigation DS is "navigation", and want the URL for page with id=7, call like this:

			<xsl:apply-templates select="/data/navigation//page[ @id=7 ]" mode="plh-url"/>
		 -->

	<xsl:template match="page" mode="plh-url">
		<xsl:param name="lang" select="/data/fl-languages/current-language/@handle"/>

		<xsl:variable name="lang-for-url">
			<xsl:choose>
				<xsl:when test="/data/fl-languages/supported-languages/item[ @handle = $lang ]/@reference = 'yes'">
					<!-- Best practice: when main language, omit it from URL -->
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="concat($lang, '/')"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<xsl:value-of select="concat(/data/params/root, '/', $lang-for-url, item[@lang=$lang]/@handle, '/')"/>
	</xsl:template>


	<xsl:template match="page/page" mode="plh-url">
		<xsl:param name="lang" select="/data/fl-languages/current-language/@handle"/>

		<xsl:apply-templates select="parent::page" mode="plh-url">
			<xsl:with-param name="lang" select="$lang"/>
		</xsl:apply-templates>

		<xsl:value-of select="concat(item[@lang=$lang]/@handle, '/')"/>
	</xsl:template>




	<!-- Helper to generate the URL for current-page in all languages -->
	<xsl:template name="plh-site-languages">
		<xsl:apply-templates select="/data/fl-languages/supported-languages/item[ @handle!= /data/fl-languages/current-language/@handle ]" mode="plh"/>
	</xsl:template>


	<!-- The ID of the page to generate the localised URLs for. Defaults to current page ID -->
	<xsl:variable name="target-page-id">
		<xsl:call-template name="plh_target-page-id"/>
	</xsl:variable>

	<!-- This template should be overriden if the links must be generated for another page -->
	<xsl:template name="plh_target-page-id">
		<xsl:value-of select="/data/params/current-page-id"/>
	</xsl:template>


	<xsl:template match="supported-languages/item" mode="plh">
		<li>
			<a>
				<xsl:attribute name="href">
					<xsl:call-template name="plh-multilang-url">
						<xsl:with-param name="lang" select="@handle"/>
					</xsl:call-template>
				</xsl:attribute>

				<xsl:value-of select="."/>
			</a>
		</li>
	</xsl:template>


	<!-- Generates the URL for current page -->
	<xsl:template name="plh-multilang-url">
		<xsl:param name="lang"/>

		<!-- root-url and handles of pages -->
		<xsl:apply-templates select="/data/plh-page//page[ @id=$target-page-id ]" mode="plh-url">
			<xsl:with-param name="lang" select="$lang"/>
		</xsl:apply-templates>

		<!-- page params -->
		<xsl:call-template name="plh-page-parameters">
			<xsl:with-param name="lang" select="$lang"/>
		</xsl:call-template>

		<!-- query string -->
		<xsl:call-template name="plh-query-string"/>
	</xsl:template>


	<!--
		Populates with Page parameters. Overload this on all pages that have Page parameters.
		It should return the page parameters separated by slash '/':
		
		`page-param-1/page-param-2/page-param-3`
	-->
	<xsl:template name="plh-page-parameters">
		<xsl:param name="lang"/>
	</xsl:template>


	<!--
		Populates with the query string. Call it whenever you need it as well.
		
		`?url-param-1=foo&url-param-2=bar#just-a-hash`
	-->
	<xsl:template name="plh-query-string">
		<xsl:variable name="v_query-string" select="substring-after(/data/params/current-url, '?')"/>

		<xsl:choose>
			<!-- Show Query string -->
			<xsl:when test="$v_query-string != ''">
				<xsl:value-of select="concat('?', $v_query-string)"/>
			</xsl:when>

			<!-- Show Hash -->
			<xsl:otherwise>
				<xsl:variable name="v_hash" select="substring-after(/data/params/current-url, '#')"/>

				<xsl:if test="$v_hash != ''">
					<xsl:value-of select="concat('#', $v_hash)"/>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>




</xsl:stylesheet>
