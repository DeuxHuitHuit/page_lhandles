Localised Page Handles
==============

Offers multilingual support for localised page handle in browser URL.

* Version: 1.0
* Author: Vlad Ghita
* Build Date: 2011-02-11
* Requirements:
	- Symphony 2.2 or above
	- Extension Language Redirect by Jonas Coch (<http://github.com/klaftertief/language_redirect>)
* Based on [Multilingual Field] extension by Guillem Lorman. [1][https://github.com/6ui11em/multilingual_field]

Thank you all other Symphony Extensions developers for your inspirational work.

### Installation

1. Upload the 'page_lhandles' folder found in this archive to your Symphony 'extensions' folder.    
2. Enable it by selecting the "Page LHandles", choose Enable from the with-selected menu, then click Apply.
3. You can now add localised Titles and Handles to any Symphony Page.

## Usage

0. Make sure that Language Redirect extension is Enabled. Fill some language codes under System -> Preferences.
1. Go to Blueprints -> Pages. Create a new page or edit an existing one.
2. Fill the available localisation fields. Every Localised URL Handle filled will become accessible from the browser. The empty ones will redirect to 404 (Page Not Found).
3. Add the "Page LHandles" DataSource to your page.

### Attention

Localised Page Handles has not yet been used in a production environment. (use at your own risk!)