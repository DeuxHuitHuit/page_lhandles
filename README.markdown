Page LHandles
==============

Offers support to localise Page titles and Handles.



## 1 Features ##

* Offers support for translating Page handles in current supported languages (from Frontend Localisation).
* Adds a button in `System -> Preferences` to automatically fill empty localisation `title` and `handle` inputs for Pages.
* Offers a DS which outputs the current-page handle and title in all supported languages.
* Overrides the navigation template Datasources to output the extra information.
* URL Router compatible. URLs like `www.site.com/clients/_param1_/projects/_param2_/` are possible if `Operating mode` is set to `Relax`. See Preferences page.

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




### 3.1 Example ###

Take 2 pages and 3 languages: Romanian (RO), English (EN) and French (FR).

No. | Parent     | Sym Title  | Sym Handle | RO Title   | RO Handle  | EN Title | EN Handle | FR Title   | FR Handle  | Parameters
----|------------|------------|------------|------------|------------|----------|-----------|------------|------------|-----------
1.  | -          | Sym Events | sym-events | Evenimente | evenimente | Events   | events    | Evenements | evenements | -
2.  | sym-events | Sym Single | sym-single | Single     | single     | Single   | single    | Single     | single     | event-single

All these 3 URLs will request "Sym Single" Page:

- www.example.com/ro/evenimente/single/primul-titlu ---> **sym-events/sym-single**/primul-titlu
- www.example.com/en/events/single/first-title --------> **sym-events/sym-single**/first-title
- www.example.com/fr/evenements/single/premier-titre --> **sym-events/sym-single**/premier-titre
