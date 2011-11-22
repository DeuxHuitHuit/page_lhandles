<?xml version="1.0" encoding="UTF-8"?>
	<xsl:stylesheet version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:exsl="http://exslt.org/common"
		xmlns:string="http://symphony-cms.com/functions">
	
	
	
	
	<xsl:import href="string-op.xsl" />
	
	
	
	
	<!-- Default values -->
	
	<!--
		Current-page hierarchy as XML, based on "PLH Page" datasource
		
		e.g.:
		<p>handle_grand_parent</p>
		<p>handle_parent</p>
		<p>handle_current_page</p>
	-->
	<xsl:variable name="plh_current_page_hierarchy">
		<xsl:apply-templates select="/data/plh-page/page" mode="parent-path" />
	</xsl:variable>
	

	<!--
		Contains current language code, based on Language Redirect event
	-->
	<xsl:variable name="plh_language_code" select="/data/events/language-redirect/current-language/@handle" />
	
	
	<!--
		Contains handle of your main navigation Datasource. This Datasource SHOULD contain all pages that will be referenced in an link (`a` tag)
	-->
	<xsl:variable name="plh_navi_node" select="'navigare'" />
	
	
	
	
	<!-- 
		Builds the URL without Parameters (page params and URL params)
		
		e.g.: www.domain.com/en/grand-parent-page/parent-page/page
	
		@param h
			- XML nodeset containig page hierarchy
			- defaults to `$plh_current_page_hierarchy` current-page hierarchy
		@param p_language_code
			- language code to search for
			- defaults to `$plh_language_code` current-language set by Language Redirect
		@param p_pages
			- root node that holds the pages
			- defaults to `$plh_navi_node`, handle of navigation Datasource
		@param p_cur_page
			- current iterated page
	-->
	<xsl:template name="plh-url">
		<xsl:param name="h" select="$plh_current_page_hierarchy" />
		<xsl:param name="p_language_code" select="$plh_language_code" />
		<xsl:param name="p_pages" select="/data/*[local-name() = $plh_navi_node]" />
		<xsl:param name="p_cur_page" select="1" />
		
		<xsl:variable name="v_next_page_handle" select="exsl:node-set($h)/p[$p_cur_page]" />
		<xsl:variable name="v_next_pages" select="$p_pages/page[ @handle = $v_next_page_handle ]" />
		
		
		<xsl:choose>
			<xsl:when test="$p_cur_page = 1">
				<xsl:value-of select="concat(/data/params/root,'/',$p_language_code)" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$p_pages/item[ @lang = $p_language_code ]/@handle" />
			</xsl:otherwise>
		</xsl:choose>
		<xsl:text>/</xsl:text>
		
		<xsl:if test="$v_next_pages">
			<xsl:call-template name="plh-url">
				<xsl:with-param name="h" select="$h" />
				<xsl:with-param name="p_language_code" select="$p_language_code" />
				<xsl:with-param name="p_pages" select="$v_next_pages" />
				<xsl:with-param name="p_cur_page" select="$p_cur_page + 1" />
			</xsl:call-template>
		</xsl:if>
	</xsl:template>




	<!--
		Retrieves localised title for required page
		
		@param h
			- XML nodeset containig required page hierarchy
			- defaults to `$plh_current_page_hierarchy` current-page hierarchy
		@param p_language_code
			- language code to search for
			- defaults to `$plh_language_code` current-language set by Language Redirect
		@param p_pages
			- root node that holds the pages
			- defaults to `$plh_navi_node`, handle of navigation Datasource
		@param p_cur_page
			- current iterated page
	-->
	<xsl:template name="plh-page-title">
		<xsl:param name="h" select="$plh_current_page_hierarchy" />
		<xsl:param name="p_language_code" select="$plh_language_code" />
		<xsl:param name="p_pages" select="/data/*[local-name() = $plh_navi_node]" />
		<xsl:param name="p_cur_page" select="1" />
		
		<xsl:variable name="v_next_page_handle" select="exsl:node-set($h)/p[$p_cur_page]" />
		<xsl:variable name="v_next_pages" select="$p_pages/page[ @handle = $v_next_page_handle ]" />
		
		<xsl:choose>
			<xsl:when test="$v_next_pages">
				<xsl:call-template name="plh-page-title">
					<xsl:with-param name="h" select="$h" />
					<xsl:with-param name="p_language_code" select="$p_language_code" />
					<xsl:with-param name="p_pages" select="$v_next_pages" />
					<xsl:with-param name="p_cur_page" select="$p_cur_page + 1" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$p_pages/item[ @lang = $p_language_code ]" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	
	
	
	<!--
		Retrieves localised handle for required page
		
		@param h
			- XML nodeset containig required page hierarchy
			- defaults to `$plh_current_page_hierarchy` current-page hierarchy
		@param p_language_code
			- language code to search for
			- defaults to `$plh_language_code` current-language set by Language Redirect
		@param p_pages
			- root node that holds the pages
			- defaults to `$plh_navi_node`, handle of navigation Datasource
		@param p_cur_page
			- current iterated page
	-->
	<xsl:template name="plh-page-handle">
		<xsl:param name="h" select="$plh_current_page_hierarchy" />
		<xsl:param name="p_language_code" select="$plh_language_code" />
		<xsl:param name="p_pages" select="/data/*[local-name() = $plh_navi_node]" />
		<xsl:param name="p_cur_page" select="1" />
		
		<xsl:variable name="v_next_page_handle" select="exsl:node-set($h)/p[$p_cur_page]" />
		<xsl:variable name="v_next_pages" select="$p_pages/page[ @handle = $v_next_page_handle ]" />
		
		<xsl:choose>
			<xsl:when test="$v_next_pages">
				<xsl:call-template name="plh-page-handle">
					<xsl:with-param name="h" select="$h" />
					<xsl:with-param name="p_language_code" select="$p_language_code" />
					<xsl:with-param name="p_pages" select="$v_next_pages" />
					<xsl:with-param name="p_cur_page" select="$p_cur_page + 1" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$p_pages/item[ @lang = $p_language_code ]/@handle" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	
	
	
	
	
	<!--
		Generates links for current-page in all languages. Languages are add as:
		
			<a title="languageName">
				<img src="path-to-image" alt="languageName" />
			</a>
		
		In my case the images are located in `workspace/public/css/images/flags`.
		I took them from "Backend languages" extension
	-->
	<xsl:template name="plh-site-languages">
		<xsl:apply-templates select="/data/events/language-redirect/supported-languages/item" mode="plh" />
	</xsl:template>
	
	
	<xsl:template match="language-redirect/supported-languages/item" mode="plh">
		<xsl:variable name="v_images_path" select="concat(/data/params/workspace, '/public/css/images/flags/')" />
		<xsl:variable name="v_images_extension" select="'png'" />
		
		<xsl:choose>
			<xsl:when test="../../current-language/@handle = current()/@handle">
				<div class="selectedLanguage">
					<xsl:value-of select="string:upper-case(@handle)" />
				</div>
			</xsl:when>
			<xsl:otherwise>
				<div>
					<a title="{text()}">
						<xsl:attribute name="href">
						
							<!-- root-url and handles of pages -->
							<xsl:call-template name="plh-url">
								<xsl:with-param name="p_language_code" select="@handle" />
								<xsl:with-param name="p_pages" select="/data/*[local-name() = 'plh-page']" />
							</xsl:call-template>
							
							<!-- page params -->
							<xsl:call-template name="plh-page-parameters">
								<xsl:with-param name="p_language_code" select="@handle" />
							</xsl:call-template>
							
							<!-- url params -->
							<xsl:call-template name="plh-url-parameters" />
							
						</xsl:attribute>
						
						<xsl:value-of select="string:upper-case(@handle)" />
					</a>
				</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	
	<!--
		Populates the URL with page parameters. Overload this on all pages that have PAGE parameters.
		It should return the page parameters separated by slash '/':
		
		`page-param-1/page-param-2/page-param-3`
	-->
	<xsl:template name="plh-page-parameters">
		<xsl:param name="p_language_code" />
	</xsl:template>
	
	
	<!--
		Outputs page parameters. Call it whenever you need them as well.
		
		`?url_param_1=foo&url_param_2=bar`
	-->
	<xsl:template name="plh-url-parameters">
		<xsl:variable name="v_url_params" select="substring-after(/data/params/current-url, '?')" />
		
		<xsl:if test="$v_url_params != ''">
			<xsl:text>?</xsl:text>
			<xsl:value-of select="$v_url_params" />
		</xsl:if>
	</xsl:template>




	<!--
		Helper. Generates Parent page path
	-->
	<xsl:template match="page" mode="parent-path">
		<p><xsl:value-of select="@handle" /></p>
		
		<xsl:apply-templates select="page" mode="parent-path" />
	</xsl:template>




</xsl:stylesheet>