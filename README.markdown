Localised Page Handles
==============

Offers multilingual support for localised page handle in browser URL.

* Version: 1.2.5
* Build Date: 2011-08-27
* Authors:
	- Vlad Ghita
	- Solutions Nitriques
* Requirements:
	- Symphony 2.2 or above
	- Extension [Language Redirect](https://github.com/klaftertief/language_redirect) by Jonas Coch, at least version 1.0.2.
* Based on [Multilingual Field](https://github.com/6ui11em/multilingual_field) extension by Guillem Lorman.

Thank you all other Symphony Extensions developers for your inspirational work.

<br />
## Features ##
* Offers support for translating the handles in current supported languages.
* Outputs the current-page handle and title in all supported languages for easy configuration of page translation url.
* Provides a utility for easier URL generation.
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

<br />
## Installation ##

1. Upload the 'page_lhandles' folder found in this archive to your Symphony 'extensions' folder.    
2. Enable it by selecting the "Page LHandles" under System -> Extensions, choose Enable from the with-selected menu, then click Apply.
3. You can now add localised Titles and Handles to any Symphony Page.

<br />
## Usage ##

1. Make sure that Language Redirect extension is Enabled. Fill some language codes under System -> Preferences.
2. Go to Blueprints -> Pages. Create a new page or edit an existing one.
3. Fill the available localisation fields. Every Localised URL Handle filled will become accessible from the browser. The empty ones will redirect to 404 (Page Not Found).
4. Add the "PLH Page" Datasource to your page. Go to ?debug and feel the difference.
5. To include the utility in your stylesheets copy `utilities/plh-toolkit.xsl` to `workspace/utilities`.

<br />
## Example ##

Take 2 pages and 3 languages: Romanian (RO), English (EN) and French (FR).

No. | Parent     | Sym Title  | Sym Handle | RO Title   | RO Handle  | EN Title | EN Handle | FR Title   | FR Handle  | Parameters
----|------------|------------|------------|------------|------------|----------|-----------|------------|------------|-----------
1.  | null       | Sym Events | sym-events | Evenimente | evenimente | Events   | events    | Evenements | evenements | null
2.  | sym-events | Sym Title  | sym-title  | Titlu      | titlu      | Title    | title     | Titre      | titre      | title

All these 3 URLs will request "Sym Title" Page:

- www.example.com/ro/evenimente/titlu/primul-titlu ---> **sym-events/sym-title**/primul-titlu
- www.example.com/en/events/title/first-title --------> **sym-events/sym-title**/first-title
- www.example.com/fr/evenements/titre/premier-titre --> **sym-events/sym-title**/premier-titre

<br />
## More examples regarding `plh-toolkit.xsl` utility ##

Given this structure of pages:

    foo > alfa > bar
    foo > beta > bar

Page `foo` is parent of `alfa` who is parent of a page called `bar`.<br />
Page `foo` is parent of `beta` who is parent of a page called `bar` as well.

`bar` of `alfa` is *different* than `bar` of `beta`. They simply have the same handle.

These handles are **Symphony handles**, not localised handles. Remember that Symphony handles are used for various processing and localised handles are displayed in the link. `foo` could be localised as `ro-foo`, `en-foo`,`pieceofcake` or whatever.

<br />
*Example #1* on `foo > beta`<br />
By default, calling `plh-url` template generates `href` for current page

    <xsl:call-template name="plh-url" />
    
    href = www.example.com/lang-code/foo/beta

<br />
*Example #2*<br />
Setting a `href` to another page is as simple as calling the template with appropriate param:

    <xsl:call-template name="plh-url">
        <xsl:with-param name="p">
            <p>foo</p>   // grand-parent
            <p>beta</p>  // parent
            <p>bar</p>   // target-page
        </xsl:with-param>
    </xsl:call-template>
    
    href = www.example.com/lang-code/foo/beta/bar

<br />
*Example #3* on `foo > alfa`<br />
On this page I want to set a link to parent page `foo`. I can hardcode it like this (more readable):

    <xsl:call-template name="plh-url">
        <xsl:with-param name="p">
            <p>foo</p>
        </xsl:with-param>
    </xsl:call-template>
    
    href = www.example.com/lang-code/foo/alfa

or I can make it future proof using the `root-page` parameter Symphony provides (in case parent page handle changes, this is safest):

    <xsl:call-template name="plh-url">
        <xsl:with-param name="p">
            <p><xsl:value-of select="/data/params/root-page" /></p>
        </xsl:with-param>
    </xsl:call-template>
    
    href = www.example.com/lang-code/foo/alfa

<br />
*Example #4a*<br />
Building URL with Page parameters.

Take these pages: `events > title`. `title` page displays one event and has a parameter called `$event-title`

    <xsl:template match="/data/events-datasource-that-outputs-all-events-titles/entry">
    
        <a title="title">
            <xsl:attribute name="href">
                <xsl:call-template name="plh-url">
                    <xsl:with-param name="p">
                        <p>events</p>
                        <p>title</p>
                    </xsl:with-param>
                </xsl:call-template>
                
                <xsl:value-of select="title/@handle" />    // handle of title
            </xsl:attribute>
            
            <xsl:value-of select="title" />    // title
        </a>
    </xsl:template>
    
    href = www.example.com/lang-code/events/title/handle-of-an-event-title
    
<br />
*Example #4b*<br />
Building URL with Page parameters and URL parameters.

Take these pages: `events > title`. `title` page displays one event and has a parameter called `$event-title`

    <xsl:template match="/data/events-datasource-that-outputs-all-events-titles/entry">
    
        <a title="title">
            <xsl:attribute name="href">
                <xsl:call-template name="plh-url">
                    <xsl:with-param name="p">
                        <p>events</p>
                        <p>title</p>
                    </xsl:with-param>
                </xsl:call-template>
                
                <xsl:value-of select="title/@handle" />    // handle of title
                
                <xsl:call-template name="plh-url-parameters" /> // URL parameters
            </xsl:attribute>
            
            <xsl:value-of select="title" />    // title
        </a>
    </xsl:template>
    
    href = www.example.com/lang-code/events/title/handle-of-an-event-title?one_param=foo&another_param=bar

<br />
*Example #5*<br />
Building current-page link in all languages

In `master.xsl`, wherever you want to put the links, call this template:

    <xsl:call-template name="plh-site-languages" />

On pages without parameters it works fine out-of-the-box. However, what do you do with pages that have parameters, such as `events > title > event-title` ?<br />
In generating current-page URL in all languages, the `plh-page-parameters` template is called along the way to populate the URL with localised parameters.<br />
So, on each page that has parameters, overload (define) this template with the contents you need.

Because I'm using Multilingual Field, this is how my template looks like (nb. my page parameter is for `Title` field) in `events_title.xsl`:

    <xsl:template name="plh-page-parameters">
        <xsl:param name="languageCode" />
        <xsl:variable name="countryHandle" select="concat('handle-',$languageCode)" />
    
        <xsl:value-of select="/data/events-datasource-that-outputs-one-event-based-on-page-parameter/entry/title/@*[ local-name() = $countryHandle ]" />
    </xsl:template>

<br />
## Compatibility ##

   Symphony | Page LHandles
------------|----------------
2.0 — 2.1.* | Not compatible
2.2.*       | [latest](https://github.com/vlad-ghita/page_lhandles)

Language Redirect | Page LHandles
------------------|----------------
    1.0.0 - 1.0.1 | [1.1](https://vlad-ghita@github.com/vlad-ghita/page_lhandles/tree/1.1)
    1.0.2 - *     | [latest](https://github.com/vlad-ghita/page_lhandles)

<br />
## Changelog ##

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
