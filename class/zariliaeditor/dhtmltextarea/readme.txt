XK_Editor class v 1.0.52

Credits:
Developer Samuels [The Zarilia Project]
Many thanks to Twitaman, FrankBlack, Hervé, Herko, Mith, Assniok, Astuni, phppp, Justin Koivisto e.t.c
Special mention to french and german zarilia community for their incredible support.
and Sorry if I forget somebody. 

Instructions:

1.Copy wysiwyg folder into your zarilia "class" folder

2.Replace your system_imagemanager.html template with the new one.

3.Follow instructions in docs folder for implement wysiwyg class in addons.

4.Make sure to clear the cache data in your browser, specially if you use mozilla or firefox.





Changelog:

1.052.1:
-Solved a bug under gecko when you work with full html page and body doesn't contains any attribute.
-Solved a bug introduced in 1.04. Submitting in "show HTML" mode.
-Non HTML linebreaks are destroyed for avoiding extra space problems with zarilia textsanitizer.
-Solved a bug with contextmenu positioning under gecko when the editor scrollbar isn't at the top.
-By now, disabled the function which translates special characters to HTML characters. (It improves comp.)


1.052:
-Now all default editor preferences are saved on "preferences.php"
-Solved bug with unwanted br tags under gecko browsers.
-Now is possible to edit a complete html page and not only the body content.
-Solved problem with table properties dialog, it applied border collapsed when you selected some table border color.
-Solved bug with table class input(it was impossible to edit).
-Solved some xhtml parsing bugs.
-Improved clean word function.
-Improved code identation.
-Solved little bug with newparagraph checkbox.
-Solved some xhtml compilance problems on formwysiwygtextarea.php
-Added italian language to package.

1.051:
-Solved problem with fullpage mode editor under Gecko Browsers (the toolbar was selected)
-Solved bug with width and height data when using the create table dialog.
-Now dialogs are ordered with tabs.
-Added german language to the package.
-Removed a useless event on dialog in "class" input.

1.05:
-Added context menus. Cool!!!
-Now Dialog input's only allow to write allowed characters.
-All styles simplified (Quickie loading).
-Colors on colorpicker are web safe now.
-All dialogs rewrited (Now all dialogs are tableless and very light).
-ColorPalette rewrited (faster and more flexible).
-All skins revisited (lighter CSS and recompressed image files).
-Added option to table properties, "Border collapse".
-Updated language files.
-Solved a mistype on cellalign alt text.
-Removed harcoded charsets.
-Added new "Paste Special" dialog.
-Some functions simplified.
-Now theme css can be attached directly on editor to show a true wysiwyg result (thx for the idea frankblack).
-XP css with comments. (To help skin developers if any lol)
-Removed hidenc variable and related funcs.

1.0.4.1:
-Solved some visual bugs with xp skin.
-Added missing words to language files.
-Solved linebreak problems.
-Added a new clean option which changes linebreaks by spaces.
-Solved a bug with html tags indentation.

1.0.4:
-Solved problem when applying format.
-Solved bug on characters array.
-Solved bug on table properties dialog.
-Now the settimeout function used on floating toolbar is cancelled after use, it avoids performace problems.
-Removed harcoded styles.
-Fixed some visuals.
-Fixed minor style bugs on CSS files.
-Editor Layout is now tableless.
-New XHTML parser (solves duplicated HTML code bug).
-Removed useless event handlers.
-Now Dialogs are modal to prevent focus issues.
-Added three editor states (hided, floating, maximized).
-Solved a problem with table tools loading.
-Solved bug on advanced image properties dialog.

1.0.3:
Plans to add a method to edit a complete html page.
Solved a bug under mozilla, (it failed sometimes on loading).
Rewrited change direction js function.
Added a method to attach a css file to the editor.(now under test)
Now you can change the default skin with a new parameter.
Solved a problem on loading under gecko when you change the encoding charset.
Improved toggleborders function.
Solved a bug with hideNC function.
Changed relative paths to absolute paths to avoid problems.

1.0.2:

Removed some hardcoded styles.
Solved bug with double quoted attr.
Rewrited events handler functions.
Now menus are hided when you click out.
Solved bug when you try to submit a document and editor is on html mode.
Updated docs with new examples.
Added rtl (right to left writing) function.
Added new method to start directly with "rtl"

1.0.1:
Solved problem with caption.
Solved incompatibility with non iso charsets.
Added gecko redirect workaround on docs.

1.0 final
-Solved a problem with checkbrowser function and some alpha versions of mozilla.

1.0rc3
-Finish imageproperties dialog.(ok)
-Solve xp styles problems under iexplore.(ok)
-Add Show Hide Toolbar palette.(ok)
-Solve cell borders div on floating toolbar.(ok)
-Hide Palettes on click out.(ok)
-Finish cell props dialog(ok)
-Show Table Borders.(ok)
-Add table height.(ok)
-Solve colorpalette problems.(ok)
-Make dialogs XHTML compilant.(ok)
-Insert anchor.(ok)
-Now zariliadhtmlarea shows if browser is not comp.

1.0 rc2:
-Compress JS files.(ok)
-Solve problems with hilite under mozilla.(ok)
-Write Docs.(ok)
-Solved problem with fonts
-Finished table props dialog.

0.944:
Added fully skin support for each editor instance. (including in the same page).
Solved XX-Large problem under iexplore.
Added hilite function under Mozilla 1.7.
Added new example skin ("office xp 2003 look")

0.943:
Added insert symbol.
Solved color palette problems with more than 1 instance.
Solved some Init problems(I hope).
Solved wysiwyg popup bugs.
Solved problems with css.
The first stable version is near xdxdxd.

0.942:
Now paste button runs under iexplore.
Solved bug on XK_Init function.
Solved bug with table options.
Solved bug on destroyptag js function.

0.9:
Added imagemanager integration.
Added row span.
Added floating toolbar.
Solved a lot of bugs.
Solved bugs with css
Added skins option.
Changed color picker.
Now width can be % or px