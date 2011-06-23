Localised Page Handles
==============

Offers multilingual support for localised page handle in browser URL.

* Version: 1.2beta
* Author: Vlad Ghita
* Build Date: 2011-06-22
* Requirements:
	- Symphony 2.2 or above
	- Extension [Language Redirect ](https://github.com/klaftertief/language_redirect) by Jonas Coch, at least version 1.0.2.
* Based on [Multilingual Field](https://github.com/6ui11em/multilingual_field) extension by Guillem Lorman.

Thank you all other Symphony Extensions developers for your inspirational work.


### Features
* Offers support for translating the handles in current supported languages.
* Outputs the current-page handle and title in all supported languages for easy configuration of page translation url.
* Overrides the navigation template Datasources to output the extra information.

Old XML for a navigation Datasource:

    <page handle="_HandleHere_" id="_IDhere_">
	    <name>_TitleHere_</name>
	    ...
    </page>

New XML supplies handle and title for the current page according to current-language:

    <page handle="_HandleHere_" id="_IDhere_">
	    <item lang="_CurrentLanguage_" handle="_LocalisedHandleHere_">_LocalisedTitleHere_</item>
	    ...
    </page>

#### Note

* On enabling / installing the extension, any Datasource with source set to 'navigation' will be edited to include the new template.
* On disabling / uninstalling the extension, any Datasource with source set to 'navigation' will be edited to include the original template.



### Installation

1. Upload the 'page_lhandles' folder found in this archive to your Symphony 'extensions' folder.    
2. Enable it by selecting the "Page LHandles" under System -> Extensions, choose Enable from the with-selected menu, then click Apply.
3. You can now add localised Titles and Handles to any Symphony Page.



### Usage

1. Make sure that Language Redirect extension is Enabled. Fill some language codes under System -> Preferences.
2. Go to Blueprints -> Pages. Create a new page or edit an existing one.
3. Fill the available localisation fields. Every Localised URL Handle filled will become accessible from the browser. The empty ones will redirect to 404 (Page Not Found).
4. Add the "PLH Page" Datasource to your page. Go to ?debug and feel the difference.



### Example:

Take 2 pages and 3 languages:

<table>
	<th>No.</th>
	<th>Parent</th>
	<th>Symphony</th>
	<th>Romanian</th>
	<th>English</th>
	<tbody>

	</tbody>
</table>

No. | Parent     |         Symphony        |         Romanian        |     English     |         French          | Parameters
    |            |   Title    |   Handle   |   Title    |   Handle   | Title  | Handle |   Title    |  Handle    |
----|------------|-------------------------|-------------------------|-----------------|-------------------------|-----------
1.  | null       | Sym Events | sym-events | Evenimente | evenimente | Events | events | Evenements | evenements | null
2.  | sym-events | Sym Title  | sym-title  | Titlu      | titlu      | Title  | title  | Titre      | titre      | title

All these 3 URLs will request "Sym Title" Page:

- www.mydomain.com/ro/evenimente/titlu/primul-titlu
- www.mydomain.com/en/events/title/first-title
- www.mydomain.com/fr/evenements/titre/premier-titre

by converting the URL to 'symphony-events/symphony-title' etc. 



### Compatibility

   Symphony | Page LHandles
------------|----------------
2.0 — 2.1.* | Not compatible
2.2.*       | [latest](https://vlad-ghita@github.com/vlad-ghita/page_lhandles.git)


Language Redirect | Page LHandles
------------------|----------------
    1.0.0 - 1.0.1 | [1.1](https://vlad-ghita@github.com/vlad-ghita/page_lhandles/tree/1.1)
    1.0.2 -       | [latest](https://vlad-ghita@github.com/vlad-ghita/page_lhandles.git)



### Changelog

* 1.2beta, 22 June 2011
    * code cleanup;
    * compatibility with [Language Redirect v1.0.2](https://github.com/klaftertief/language_redirect);

* 1.1, 08 March 2011
    * entire extension rewrite.
	* added the missing support for page parents.
	* offers support for localised pages for Datasources with Source set to 'navigation'

* 1.0beta, 16 February 2011
	* initial beta release.

	<table border="1">
<tr>
<th>no</th>
<th>Parent</th>
<th>Symphony</th>
</tr>
<tr>
  <td>400</td>
  <td>500</td>
  <td>600</td>
</tr>
</table>