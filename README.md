# UM Backend & Frontend Local Language from Browser setting
Extension to Ultimate Member for Addition of Browser or User Profile Local Language support to UM Backend and Frontend.

Browser language is being used for UM Frontend/Backend if a local language file is available otherwise the language selected in the WP User Profile setup is used. This language switch is valid for all UM Pages including UM Form Designer Pages Profile, Login, Registration and Password reset and Account Pages.
## Shortcode for selecting custom localized UM Forms
Additional shortcode for customizing the frontend where the user designed UM Pages with form id's can be selected depending on the user browsers language code. Pages designed for different language requirements can be hosted on the same URL and the shortcode displays the page according to the user browser language. 

The shortcode can be used for UM Registration Forms, Profile Forms and Login Forms.

Shortcode example: [um_locale_language_setup en_US 1025 fr_FR 1061 default 1025]

The Form with id 1025 is used for US English users and the Form with id 1061 for French speaking users. For othe languages the default English page is being used. This is the only available format with a language code followed by a space and the Form id without quotes and equal signs or separating commas or new lines.

This example may be used to replace a page with current UM basic shortcode like [ultimatemember form_id="1061"]




## Installation
1. Create a plugin sub directory  /plugins/local-language-um-backend 
2. Upload the script to the sub directory with the script name "local-language-um-backend.php"
3. Activate the Plugin "Ultimate Member - Local Language Backend/Frontend"

You can't install this script to your child-theme's functions.php file or use the "Code Snippets" plugin.
