Page LHandles
==============

Offers multilingual support for localised page handles in browser URL.

* Version: 2.3
* Build Date: 2011-12-28
* Authors:
	- [Vlad Ghita](http://www.xanderadvertising.com)
* Requirements:
	- Symphony 2.2.x
	- Extension [Frontend localisation](https://github.com/vlad-ghita/frontend_localisation)

Thank you all other Symphony Extensions developers for your inspirational work.




## 1 Features ##

* Offers support for translating Page handles in current supported languages (from Frontend Localisation).
* Adds a button in `System -> Preferences` to automatically fill empty localisation `title` and `handle` inputs.
* Offers a DS which outputs the current-page handle and title in all supported languages.
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

#### Note ####

* On enabling / installing the extension, any Datasource with source set to 'navigation' will be edited to include the new localised template.
* On disabling / uninstalling the extension, any Datasource with source set to 'navigation' will be edited to include the original Symphony template.





## 2 Installation ##

1. Upload the 'page_lhandles' folder found in this archive to your Symphony 'extensions' folder.    
2. Enable it by selecting the "Page LHandles" under System -> Extensions, choose Enable from the with-selected menu, then click Apply.
3. You can now add localised Titles and Handles to any Symphony Page.




## 3 Usage ##

1. Make sure that Frontend Localisation extension is Enabled. Read it's [Readme](https://github.com/vlad-ghita/frontend_localisation) if you don't know what it represents.
2. Go to Blueprints -> Pages. Create a new page or edit an existing one.
3. Fill the available localisation fields. Every Localised URL Handle filled will become accessible from the browser. The empty ones will redirect to 404 (Page Not Found).
4. Add the "PLH Page" Datasource to your page. Go to `?debug` and feel the difference.
5. To include the utility in your stylesheets copy `utilities/plh-toolkit.xsl` to `workspace/utilities` and include it in your XSLT stylesheets.




### 3.1 Example ###

Take 2 pages and 3 languages: Romanian (RO), English (EN) and French (FR).

No. | Parent     | Sym Title  | Sym Handle | RO Title   | RO Handle  | EN Title | EN Handle | FR Title   | FR Handle  | Parameters
----|------------|------------|------------|------------|------------|----------|-----------|------------|------------|-----------
1.  | -          | Sym Events | sym-events | Evenimente | evenimente | Events   | events    | Evenements | evenements | -
2.  | sym-events | Sym Title  | sym-title  | Titlu      | titlu      | Title    | title     | Titre      | titre      | event-title

All these 3 URLs will request "Sym Title" Page:

- www.example.com/ro/evenimente/titlu/primul-titlu ---> **sym-events/sym-title**/primul-titlu
- www.example.com/en/events/title/first-title --------> **sym-events/sym-title**/first-title
- www.example.com/fr/evenements/titre/premier-titre --> **sym-events/sym-title**/premier-titre



### 3.2 More examples regarding `plh-toolkit.xsl` utility ###

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
