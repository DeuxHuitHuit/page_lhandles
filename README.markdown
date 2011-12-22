Localised Page Handles
==============

Offers multilingual support for localised page handles in browser URL.

* Version: 2.2
* Build Date: 2011-12-22
* Authors:
	- Vlad Ghita
* Requirements:
	- Symphony 2.2 or above
	- Extension [Frontend localisation](https://github.com/vlad-ghita/frontend_localisation)

Thank you all other Symphony Extensions developers for your inspirational work.




# 1 Features #

* Offers support for translating the handles in current supported languages.
* Adds a button in `preferences` to automatically fill empty localisation `title` and `handle` inputs.
* Outputs the current-page handle and title in all supported languages for easy configuration of page url.
* Provides a utility for easier URL manipulation.
* Overrides the navigation template Datasources to output the extra information.

Old XML for a navigation Datasource:

    <page handle="_HandleHere_" id="_IDhere_">
	    <name>_TitleHere_</name>
	    ...
    </page>

New XML supplies handle and title for the current page according to current-language:

    <page handle="_SymphonyHandleHere_" id="_IDhere_">
	    <item lang="_CurrentLanguageHere_" handle="_LocalisedHandleHere_">_LocalisedTitleHere_</item>
	    ...
    </page>

### Note ###

* On enabling / installing the extension, any Datasource with source set to 'navigation' will be edited to include the new localised template.
* On disabling / uninstalling the extension, any Datasource with source set to 'navigation' will be edited to include the original Symphony template.





# 2 Installation #

1. Upload the 'page_lhandles' folder found in this archive to your Symphony 'extensions' folder.    
2. Enable it by selecting the "Page LHandles" under System -> Extensions, choose Enable from the with-selected menu, then click Apply.
3. You can now add localised Titles and Handles to any Symphony Page.




# 3 Usage #

1. Make sure that Frontend Localisation extension is Enabled. Read it's [Readme](https://github.com/vlad-ghita/frontend_localisation) if you don't know what it represents.
2. Go to Blueprints -> Pages. Create a new page or edit an existing one.
3. Fill the available localisation fields. Every Localised URL Handle filled will become accessible from the browser. The empty ones will redirect to 404 (Page Not Found).
4. Add the "PLH Page" Datasource to your page. Go to ?debug and feel the difference.
5. To include the utility in your stylesheets copy `utilities/plh-toolkit.xsl` to `workspace/utilities` and include it in your XSLT stylesheets.




## 3.1 Example ##

Take 2 pages and 3 languages: Romanian (RO), English (EN) and French (FR).

No. | Parent     | Sym Title  | Sym Handle | RO Title   | RO Handle  | EN Title | EN Handle | FR Title   | FR Handle  | Parameters
----|------------|------------|------------|------------|------------|----------|-----------|------------|------------|-----------
1.  | -          | Sym Events | sym-events | Evenimente | evenimente | Events   | events    | Evenements | evenements | -
2.  | sym-events | Sym Title  | sym-title  | Titlu      | titlu      | Title    | title     | Titre      | titre      | event-title

All these 3 URLs will request "Sym Title" Page:

- www.example.com/ro/evenimente/titlu/primul-titlu ---> **sym-events/sym-title**/primul-titlu
- www.example.com/en/events/title/first-title --------> **sym-events/sym-title**/first-title
- www.example.com/fr/evenements/titre/premier-titre --> **sym-events/sym-title**/premier-titre



## 3.2 More examples regarding `plh-toolkit.xsl` utility ##

Recommendation: put the navigation DS output in a variable so you can access it easily:

    <xsl:variable name="nav" select="/data/navigation" />

Get the URL of a page with ID = 7:

    <xsl:apply-templates select="$nav//page[ @id=7 ]" mode="plh-url" />

Get the Title of a page with ID = 7:

	<xsl:value-of select="$nav//page[ @id=7 ]/item" />

If you have URL parameters attached to current URL and want to pass them forward, call template `plh-url-parameters` :

    <xsl:apply-templates select="$nav//page[ @id=7 ]" mode="plh-url" />
    <xsl:call-template name="plh-url-parameters" />

<br />
Building current-page link in all languages:

In `master.xsl`, or wherever you want to put the links, call this template:

    <xsl:call-template name="plh-site-languages" />

On pages without parameters it works fine out-of-the-box. However, what do you do with pages that have parameters, such as `title --> event-title` ?<br />
In generating current-page URL in all languages, the `plh-page-parameters` template is called along the way to populate the URL with localised parameters.<br />
So, on each page that has parameters, overload (define) this template with the contents you need.

Because I'm using Multilingual Text, this is how my template looks like (NB: my page parameter is for `Title` field) in `events_title.xsl`:

    <xsl:template name="plh-page-parameters">
        <xsl:param name="lang" />
        <xsl:variable name="country_handle" select="concat('handle-',$lang)" />
    
        <xsl:value-of select="/data/events-datasource-that-outputs-one-event-based-on-page-parameter/entry/title/@*[ local-name() = $country_handle ]" />
    </xsl:template>





# Compatibility #

   Symphony | Page LHandles
------------|----------------
2.0 — 2.1.* | Not compatible
2.2.*       | [latest](https://github.com/vlad-ghita/page_lhandles)

Frontend Localisation | Page LHandles
----------------------|----------------
      [0.5beta](https://github.com/vlad-ghita/frontend_localisation) - *     | [latest](https://github.com/vlad-ghita/page_lhandles)





# Changelog #

* 2.2, 22 December 2011
	* Compatibility release for Frontend Localisation 0.5 beta.

* 2.1, 13 December 2011
	* Refactored URL parsing. This functionality is available through PLHManagerURL class.

* 2.0.2, 09 December 2011
	* Reworked the templates from `plh-toolkit.xsl`. Thanks @phoque.

* 2.0.1, 29 November 2011
	* Fixed a preg_match mistake ...

* 2.0, 15 November 2011
	* Extension rewrite, code cleaning. It now depends on Frontend Localisation instead of Language Redirect.

* 1.2.8.1, 24 September 2011
	* Fixed a bug on Navigation Datasource creation.

* 1.2.8, 22 September 2011
	* Typo fix. Renamed `plh-toolikt.xsl` to `plh-toolkit.xsl`.
	* Added more templates in `plh-toolkit.xsl`.

* 1.2.7.1, 16 September 2011
	* Hotfix. Changed type return from string to array.

* 1.2.7, 16 September 2011
	* Readded the button in `preferences` to automatically fill empty localisation `title` and `handle` for Pages. It was lost somewhere in transition from 1.1 to 1.2.
	* Code cleanup.

* 1.2.6, 30 August 2011
	* Fix issue #13 where `sym_` was hardcoded in query. It now properly uses `tbl_`.

* 1.2.5, 27 August 2011
	* Added `utilities/plh-toolkit.xsl` utility for easier URL generation.
	* Updated README with more examples for utility methods. 

* 1.2.4, 20 August 2011
	* Fix issue #7: Doesn't work on index page

* 1.2.3, 18 August 2011
    * Fix a bug where the handle passed in the WHERE clause would filter based on the lastParent value, since two sub-pages could share the same handle

* 1.2.2, 30 June 2011
    * Language code is now properly retrieved whereas before only the language without the region was used.

* 1.2.1, 28 June 2011
	* Fix issue #8
    * added French language. Thanks @nitriques;

* 1.2, 28 June 2011
    * code cleanup;
    * compatibility with [Language Redirect v1.0.2](https://github.com/klaftertief/language_redirect);

* 1.1, 08 March 2011
    * entire extension rewrite.
	* added the missing support for page parents.
	* offers support for localised pages for Datasources with Source set to 'navigation'

* 1.0beta, 16 February 2011
	* initial beta release.
