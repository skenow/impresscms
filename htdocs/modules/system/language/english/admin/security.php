<?php
// $Id: preferences.php 20671 2011-01-08 16:14:49Z m0nty_ $
//%%%%%%	Admin Module Name  AdminGroup 	%%%%%
// dont change
if (!defined('_AM_DBUPDATED')) {define("_AM_DBUPDATED","Database Updated Successfully!");}

define("_MD_AM_SEC_SITEPREF","Site Security Preferences");
define("_MD_AM_SEC_NONE","None");
define("_MD_AM_SEC_MINPASS","Minimum length of password required");
define("_MD_AM_SEC_UNAMELVL","Select the level of strictness for username filtering");
define("_MD_AM_SEC_STRICT","Strict (only alphabets and numbers)");
define("_MD_AM_SEC_MEDIUM","Medium");
define("_MD_AM_SEC_LIGHT","Light (recommended for multi-byte chars)");
define("_MD_AM_SEC_USERCOOKIE","Name for user cookies.");
define("_MD_AM_SEC_USERCOOKIEDSC","This cookie contains only a user name and is saved in a user pc for a year (if the user wishes). If a user has this cookie, username will be automatically inserted in the login box.");
define("_MD_AM_SEC_USEMYSESS","Use custom session");
define("_MD_AM_SEC_USEMYSESSDSC","Select yes to customise session related values.");
define("_MD_AM_SEC_SESSNAME","Session name");
define("_MD_AM_SEC_SESSNAMEDSC","The name of session (Valid only when 'use custom session' is enabled)");
define("_MD_AM_SEC_SESSEXPIRE","Session expiration");
define("_MD_AM_SEC_SESSEXPIREDSC","Maximum duration of session idle time in minutes (Valid only when 'use custom session' is enabled. Works only when you are using PHP4.2.0 or later.)");
define("_MD_AM_SEC_ALWDHTML","HTML tags allowed in all posts.");
define("_MD_AM_SEC_INVLDMINPASS","Invalid value for minimum length of password.");
define("_MD_AM_SEC_INVLDUCOOK","Invalid value for usercookie name.");
define("_MD_AM_SEC_INVLDSCOOK","Invalid value for sessioncookie name.");
define("_MD_AM_SEC_INVLDSEXP","Invalid value for session expiration time.");
define("_MD_AM_SEC_YES","Yes");
define("_MD_AM_SEC_NO","No");
define("_MD_AM_SEC_DONTCHNG","Don't change!");

define('_MD_AM_SEC_USESSL', 'Use SSL for login?');
define('_MD_AM_SEC_SSLPOST', 'SSL Post variable name');
define('_MD_AM_SEC_SSLPOSTDSC', 'The name of variable used to transfer session value via POST. If you are unsure, set any name that is hard to guess.');

define('_MD_AM_SEC_GENERAL', 'General Settings');
define('_MD_AM_SEC_USERSETTINGS', 'User Settings');
define('_MD_AM_SEC_IPBAN', 'IP Banning');
define('_MD_AM_SEC_BADEMAILS', 'Enter emails that should not be used in user profile');
define('_MD_AM_SEC_BADEMAILSDSC', 'Separate each with a <b>|</b>, case insensitive, regex enabled.');
define('_MD_AM_SEC_BADUNAMES', 'Enter names that should not be selected as username');
define('_MD_AM_SEC_BADUNAMESDSC', 'Separate each with a <b>|</b>, case insensitive, regex enabled.');
define('_MD_AM_SEC_DOBADIPS', 'Enable IP bans?');
define('_MD_AM_SEC_DOBADIPSDSC', 'Users from specified IP addresses will not be able to view your site');
define('_MD_AM_SEC_BADIPS', 'Enter IP addresses that should be banned from the site.<br />Separate each with a <b>|</b>, case insensitive, regex enabled.');
define('_MD_AM_SEC_BADIPSDSC', '^aaa.bbb.ccc will disallow visitors with an IP that starts with aaa.bbb.ccc<br />aaa.bbb.ccc$ will disallow visitors with an IP that ends with aaa.bbb.ccc<br />aaa.bbb.ccc will disallow visitors with an IP that contains aaa.bbb.ccc');
define('_MD_AM_SEC_PREFMAIN', 'Security Preferences Main');

define('_MD_AM_SEC_SSLLINK', 'URL where SSL login page is located');

//lang constants for secure password
define("_MD_AM_SEC_PASSLEVEL","Minimum security level");
define("_MD_AM_SEC_PASSLEVEL_DESC","Define which level of security you want for the user's password. It's recommeded not to set it too low or too strong, be reasonable.");
define("_MD_AM_SEC_PASSLEVEL1","Off(Insecure)");
define("_MD_AM_SEC_PASSLEVEL2","Weak");
define("_MD_AM_SEC_PASSLEVEL3","Reasonable");
define("_MD_AM_SEC_PASSLEVEL4","Strong");
define("_MD_AM_SEC_PASSLEVEL5","Secure");
define("_MD_AM_SEC_PASSLEVEL6","No classification");

define("_MD_AM_SEC_REMEMBERME","Enable the 'Remember Me' feature in the login.");
define("_MD_AM_SEC_REMEMBERMEDSC","The 'Remember Me' feature can represent a security issue. Use it under your own risk.");


define("_MD_AM_SEC_ALLOW_ANONYMOUS_VIEW_PROFILE","Allow anonymous users to see user profiles.");

define("_MD_AM_SEC_ENC_TYPE","Change Password Encryption (default is SHA256)");
define("_MD_AM_SEC_ENC_TYPEDSC","Changes the Algorithm used for encrypting user passwords.<br />Changing this will render all passwords invalid! all users will need to reset their passwords after changing this preference");
define("_MD_AM_SEC_ENC_MD5","MD5 (not recommended)");
define("_MD_AM_SEC_ENC_SHA256","SHA 256 (recommended)");
define("_MD_AM_SEC_ENC_SHA384","SHA 384");
define("_MD_AM_SEC_ENC_SHA512","SHA 512");
define("_MD_AM_SEC_ENC_RIPEMD128","RIPEMD 128");
define("_MD_AM_SEC_ENC_RIPEMD160","RIPEMD 160");
define("_MD_AM_SEC_ENC_WHIRLPOOL","WHIRLPOOL");
define("_MD_AM_SEC_ENC_HAVAL1284","HAVAL 128,4");
define("_MD_AM_SEC_ENC_HAVAL1604","HAVAL 160,4");
define("_MD_AM_SEC_ENC_HAVAL1924","HAVAL 192,4");
define("_MD_AM_SEC_ENC_HAVAL2244","HAVAL 224,4");
define("_MD_AM_SEC_ENC_HAVAL2564","HAVAL 256,4");
define("_MD_AM_SEC_ENC_HAVAL1285","HAVAL 128,5");
define("_MD_AM_SEC_ENC_HAVAL1605","HAVAL 160,5");
define("_MD_AM_SEC_ENC_HAVAL1925","HAVAL 192,5");
define("_MD_AM_SEC_ENC_HAVAL2245","HAVAL 224,5");
define("_MD_AM_SEC_ENC_HAVAL2565","HAVAL 256,5");

define("_MD_AM_SEC_UNABLEENCCLOSED","Database Update Failed, You can't change password encryption whilst the site is closed");

// HTML Purifier preferences
define("_MD_AM_SEC_PURIFIER","HTMLPurifier Settings");
define("_MD_AM_SEC_PURIFIER_ENABLE","Enable HTML Purifier");
define("_MD_AM_SEC_PURIFIER_ENABLEDSC","Select 'yes' to enable the HTML Purifier filters, disabling this could leave your site vulnerable to attack if you allow your HTML content");
//HTML section
define("_MD_AM_SEC_PURIFIER_HTML_TIDYLEVEL","HTML Tidy Level");
define("_MD_AM_SEC_PURIFIER_HTML_TIDYLEVELDSC","General level of cleanliness the Tidy module should enforce.<br /><br />
None = No extra tidying should be done,<br />Light = Only fix elements that would be discarded otherwise due to lack of support in doctype,<br />
Medium = Enforce best practices,<br />Heavy = Transform all deprecated elements and attributes to standards compliant equivalents.");
define("_MD_AM_SEC_PURIFIER_NONE","None");
define("_MD_AM_SEC_PURIFIER_LIGHT","Light");
define("_MD_AM_SEC_PURIFIER_MEDIUM","Medium (recommended)");
define("_MD_AM_SEC_PURIFIER_HEAVY","Heavy");
define("_MD_AM_SEC_PURIFIER_HTML_DEFID","HTML Definition ID");
define("_MD_AM_SEC_PURIFIER_HTML_DEFIDDSC","Sets the default ID name of the purifier configuration (leave as is, unless you are creating custom configurations & that you know what you are doing");
define("_MD_AM_SEC_PURIFIER_HTML_DEFREV","HTML Definition Revision Number");
define("_MD_AM_SEC_PURIFIER_HTML_DEFREVDSC","Example: revision 3 is more up-to-date than revision 2. Thus, when this gets incremented, the cache handling is smart enough to clean up any older revisions of your definition as well as flush the cache.<br />You can leave this as is unless you know what you are doing & are editing the purifier files directly");
define("_MD_AM_SEC_PURIFIER_HTML_DOCTYPE","HTML DocType");
define("_MD_AM_SEC_PURIFIER_HTML_DOCTYPEDSC","Doctype to use during filtering. Technically speaking this is not actually a doctype (as it does not identify a corresponding DTD), but we are using this name for sake of simplicity. When non-blank, this will override any older directives like XHTML or HTML (Strict).");
define("_MD_AM_SEC_PURIFIER_HTML_ALLOWELE","Allowed Elements");
define("_MD_AM_SEC_PURIFIER_HTML_ALLOWELEDSC","Whitelist of HTML Elements that are allowed to be posted. Any elements entered here will not be filtered out of user posts. You should only allow safe html elements.");
define("_MD_AM_SEC_PURIFIER_HTML_ALLOWATTR","Allowed Attributes");
define("_MD_AM_SEC_PURIFIER_HTML_ALLOWATTRDSC","Whitelist of HTML Attributes that are allowed to be posted. Any attributes entered here will not be filtered out of user posts. You should only allow safe html attirbutes.<br /><br />Format your attributes as follows:<br />element.attribute (example: div.class) will allow you to use the class attribute with div tags. you can also use wildcards: *.class for example will allow the class attribute in all allowed elements.");
define("_MD_AM_SEC_PURIFIER_HTML_FORBIDELE","Forbidden Elements");
define("_MD_AM_SEC_PURIFIER_HTML_FORBIDELEDSC","This is the logical inverse of  HTML.Allowed Elements, and it will override that directive, or any other directive.");
define("_MD_AM_SEC_PURIFIER_HTML_FORBIDATTR","Forbidden Attributes");
define("_MD_AM_SEC_PURIFIER_HTML_FORBIDATTRDSC"," While this directive is similar to  HTML Allowed Attributes, for forwards-compatibility with XML, this attribute has a different syntax.<br />Instead of tag.attr, use tag@attr. To disallow href attributes in a tags, set this directive to a@href.<br />You can also disallow an attribute globally with attr or *@attr (either syntax is fine; the latter is provided for consistency with HTML Allowed Attributes).<br /><br />Warning: This directive complements  HTML Forbidden Elements, accordingly, check out that directive for a discussion of why you should think twice before using this directive.");
define("_MD_AM_SEC_PURIFIER_HTML_MAXIMGLENGTH","Max Image Length");
define("_MD_AM_SEC_PURIFIER_HTML_MAXIMGLENGTHDSC","This directive controls the maximum number of pixels in the width and height attributes in img tags. This is in place to prevent imagecrash attacks, disable with 0 at your own risk. ");
define("_MD_AM_SEC_PURIFIER_HTML_SAFEEMBED","Enable Safe Embed");
define("_MD_AM_SEC_PURIFIER_HTML_SAFEEMBEDDSC","Whether or not to permit embed tags in documents, with a number of extra security features added to prevent script execution. This is similar to what websites like MySpace do to embed tags. Embed is a proprietary element and will cause your website to stop validating. You probably want to enable this with HTML Safe Object. Highly experimental.");
define("_MD_AM_SEC_PURIFIER_HTML_SAFEOBJECT","Enable Safe Object");
define("_MD_AM_SEC_PURIFIER_HTML_SAFEOBJECTDSC","Whether or not to permit object tags in documents, with a number of extra security features added to prevent script execution. This is similar to what websites like MySpace do to object tags. You may also want to enable  HTML Safe Embed for maximum interoperability with Internet Explorer, although embed tags will cause your website to stop validating. Highly experimental.");
define("_MD_AM_SEC_PURIFIER_HTML_ATTRNAMEUSECDATA","Relax DTD Name Attribute Parsing");
define("_MD_AM_SEC_PURIFIER_HTML_ATTRNAMEUSECDATADSC","The W3C specification DTD defines the name attribute to be CDATA, not ID, due to limitations of DTD. In certain documents, this relaxed behavior is desired, whether it is to specify duplicate names, or to specify names that would be illegal IDs (for example, names that begin with a digit.) Set this configuration directive to yes to use the relaxed parsing rules.");
// URI Section
define("_MD_AM_SEC_PURIFIER_URI_DEFID","URI Definition ID");
define("_MD_AM_SEC_PURIFIER_URI_DEFIDDSC","Unique identifier for a custom-built URI definition. If you want to add custom URIFilters, you must specify this value. (leave as is unless you know what you are doing)");
define("_MD_AM_SEC_PURIFIER_URI_DEFREV","URI Definition Revision Number");
define("_MD_AM_SEC_PURIFIER_URI_DEFREVDSC","Example: revision 3 is more up-to-date than revision 2. Thus, when this gets incremented, the cache handling is smart enough to clean up any older revisions of your definition as well as flush the cache.<br />You can leave this as is unless you know what you are doing & are editing the purifier files directly");
define("_MD_AM_SEC_PURIFIER_URI_DISABLE","Disable all URI in user posts");
define("_MD_AM_SEC_PURIFIER_URI_DISABLEDSC","Disabling URI will prevent users from posting any links whatsoever, it is not recommended to enable this except for test purposes.<br />Default is 'No'");
define("_MD_AM_SEC_PURIFIER_URI_BLACKLIST","URI Blacklist");
define("_MD_AM_SEC_PURIFIER_URI_BLACKLISTDSC","Enter Domain names that should be filtered (removed) from user posts.");
define("_MD_AM_SEC_PURIFIER_URI_ALLOWSCHEME","Allowed URI Schemes");
define("_MD_AM_SEC_PURIFIER_URI_ALLOWSCHEMEDSC","Whitelist that defines the schemes that a URI is allowed to have. This prevents XSS attacks from using pseudo-schemes like javascript or mocha.<br />Accepted values (http, https, ftp, mailto, nntp, news)");
define("_MD_AM_SEC_PURIFIER_URI_HOST","URI Host Domain");
define("_MD_AM_SEC_PURIFIER_URI_HOSTDSC","Enter URI Host. Leave blank to disable!");
define("_MD_AM_SEC_PURIFIER_URI_BASE","URI Base Domain");
define("_MD_AM_SEC_PURIFIER_URI_BASEDSC","Enter URI Base. Leave blank to disable!");
define("_MD_AM_SEC_PURIFIER_URI_DISABLEEXT","Disable External Links");
define("_MD_AM_SEC_PURIFIER_URI_DISABLEEXTDSC","Disables links to external websites. This is a highly effective anti-spam and anti-pagerank-leech measure, but comes at a hefty price: nolinks or images outside of your domain will be allowed.<br />Non-linkified URIs will still be preserved. If you want to be able to link to subdomains or use absolute URIs, enable URI Host for your website.");
define("_MD_AM_SEC_PURIFIER_URI_DISABLEEXTRES","Disable External Resources");
define("_MD_AM_SEC_PURIFIER_URI_DISABLEEXTRESDSC","Disables the embedding of external resources, preventing users from embedding things like images from other hosts. This prevents access tracking (good for email viewers), bandwidth leeching, cross-site request forging, goatse.cx posting, and other nasties, but also results in a loss of end-user functionality (they can't directly post a pic they posted from Flickr anymore). Use it if you don't have a robust user-content moderation team. ");
define("_MD_AM_SEC_PURIFIER_URI_DISABLERES","Disable Resources");
define("_MD_AM_SEC_PURIFIER_URI_DISABLERESDSC","Disables embedding resources, essentially meaning no pictures. You can still link to them though. See  URI Disable External Resources for why this might be a good idea.");
define("_MD_AM_SEC_PURIFIER_URI_MAKEABS","URI Make Absolute");
define("_MD_AM_SEC_PURIFIER_URI_MAKEABSDSC","Converts all URIs into absolute forms. This is useful when the HTML being filtered assumes a specific base path, but will actually be viewed in a different context (and setting an alternate base URI is not possible).<br /><br />URI Base must be enabled for this directive to work.");
// Filter Section
define("_MD_AM_SEC_PURIFIER_FILTER_EXTRACTSTYLEESC","Escape Dangerous Characters in StyleBlocks");
define("_MD_AM_SEC_PURIFIER_FILTER_EXTRACTSTYLEESCDSC","Whether or not to escape the dangerous characters <, > and &  as \3C, \3E and \26, respectively. This can be safely set to false if the contents of StyleBlocks will be placed in an external stylesheet, where there is no risk of it being interpreted as HTML.");
define("_MD_AM_SEC_PURIFIER_FILTER_EXTRACTSTYLEBLKSCOPE","Enter StyleBlocks Scope");
define("_MD_AM_SEC_PURIFIER_FILTER_EXTRACTSTYLEBLKSCOPEDSC","If you would like users to be able to define external stylesheets, but only allow them to specify CSS declarations for a specific node and prevent them from fiddling with other elements, use this directive.<br />It accepts any valid CSS selector, and will prepend this to any CSS declaration extracted from the document.<br /><br />For example, if this directive is set to #user-content and a user uses the selector a:hover, the final selector will be #user-content a:hover.<br /><br />The comma shorthand may be used; consider the above example, with #user-content, #user-content2, the final selector will be #user-content a:hover, #user-content2 a:hover.");
define("_MD_AM_SEC_PURIFIER_FILTER_ENABLEYOUTUBE","Allowed Embedding YouTube Video");
define("_MD_AM_SEC_PURIFIER_FILTER_ENABLEYOUTUBEDSC","This directive enables YouTube video embedding in HTML Purifier. Check <a href='http://htmlpurifier.org/docs/enduser-youtube.html'>this</a> document on embedding videos for more information on what this filter does.");
define("_MD_AM_SEC_PURIFIER_FILTER_EXTRACTSTYLEBLK","Extract Style Blocks?");
define("_MD_AM_SEC_PURIFIER_FILTER_EXTRACTSTYLEBLKDSC","Requires CSSTidy Plugin to be installed).<br /><br />This directive turns on the style block extraction filter, which removes style blocks from input HTML, cleans them up with CSSTidy, and places them in the StyleBlocks context variable, for further use by you, usually to be placed in an external stylesheet, or a style block in the head of your document.<br /><br />Warning: It is possible for a user to mount an imagecrash attack using this CSS. Counter-measures are difficult; it is not simply enough to limit the range of CSS lengths (using relative lengths with many nesting levels allows for large values to be attained without actually specifying them in the stylesheet), and the flexible nature of selectors makes it difficult to selectively disable lengths on image tags (HTML Purifier, however, does disable CSS width and height in inline styling). There are probably two effective counter measures: an explicit width and height set to auto in all images in your document (unlikely) or the disabling of width and height (somewhat reasonable). Whether or not these measures should be used is left to the reader.");
define("_MD_AM_SEC_PURIFIER_FILTER_CUSTOM","Select Custom Filters");
define("_MD_AM_SEC_PURIFIER_FILTER_CUSTOMDSC","Select Custom Movie filters From the list");
// Core Section
define("_MD_AM_SEC_PURIFIER_CORE_ESCINVALIDTAGS","Escape Invalid Tags");
define("_MD_AM_SEC_PURIFIER_CORE_ESCINVALIDTAGSDSC","When enabled, invalid tags will be written back to the document as plain text. Otherwise, they are silently dropped.");
define("_MD_AM_SEC_PURIFIER_CORE_ESCNONASCIICHARS","Escape Non ASCII Characters");
define("_MD_AM_SEC_PURIFIER_CORE_ESCNONASCIICHARSDSC","This directive overcomes a deficiency in %Core.Encoding by blindly converting all non-ASCII characters into decimal numeric entities before converting it to its native encoding. This means that even characters that can be expressed in the non-UTF-8 encoding will be entity-ized, which can be a real downer for encodings like Big5. It also assumes that the ASCII repetoire is available, although this is the case for almost all encodings. Anyway, use UTF-8!");
define("_MD_AM_SEC_PURIFIER_CORE_HIDDENELE","Enable HTML Hidden Elements");
define("_MD_AM_SEC_PURIFIER_CORE_HIDDENELEDSC","This directive is a lookup array of elements which should have their contents removed when they are not allowed by the HTML definition. For example, the contents of a script tag are not normally shown in a document, so if script tags are to be removed, their contents should be removed to. This is opposed to a b  tag, which defines some presentational changes but does not hide its contents.");
define("_MD_AM_SEC_PURIFIER_CORE_COLORKEYS","Colour Keywords");
define("_MD_AM_SEC_PURIFIER_CORE_COLORKEYSDSC","Lookup array of color names to six digit hexadecimal number corresponding to color, with preceding hash mark. Used when parsing colors.");
define("_MD_AM_SEC_PURIFIER_CORE_REMINVIMG","Remove Invalid Image");
define("_MD_AM_SEC_PURIFIER_CORE_REMINVIMGDSC","This directive enables pre-emptive URI checking in img tags, as the attribute validation strategy is not authorized to remove elements from the document. Default = yes");
// AutoFormat Section
define("_MD_AM_SEC_PURIFIER_AUTO_AUTOPARA","Enable Paragraph Auto Format");
define("_MD_AM_SEC_PURIFIER_AUTO_AUTOPARADSC","This directive turns on auto-paragraphing, where double newlines are converted in to paragraphs whenever possible.<br /> Auto-paragraphing:<br /><br />* Always applies to inline elements or text in the root node,<br />* Applies to inline elements or text with double newlines in nodes that allow paragraph tags,<br />* Applies to double newlines in paragraph tags.<br /></br>p tags must be allowed for this directive to take effect. We do not use br tags for paragraphing, as that is semantically incorrect.<br />To prevent auto-paragraphing as a content-producer, refrain from using double-newlines except to specify a new paragraph or in contexts where it has special meaning (whitespace usually has no meaning except in tags like pre, so this should not be difficult.) To prevent the paragraphing of inline text adjacent to block elements, wrap them in div tags (the behavior is slightly different outside of the root node.)");
define("_MD_AM_SEC_PURIFIER_AUTO_DISPLINKURI","Enable Link Display");
define("_MD_AM_SEC_PURIFIER_AUTO_DISPLINKURIDSC","This directive turns on the in-text display of URIs in <a> tags, and disables those links. For example, <a href=\"http://example.com\">example</a> becomes example (http://example.com).");
define("_MD_AM_SEC_PURIFIER_AUTO_LINKIFY","Enable Auto Linkify");
define("_MD_AM_SEC_PURIFIER_AUTO_LINKIFYDSC","This directive turns on linkification, auto-linking http, ftp and https URLs. a tags with the href attribute must be allowed. ");
define("_MD_AM_SEC_PURIFIER_AUTO_PURILINKIFY","Enable Purifier Internal Linkify");
define("_MD_AM_SEC_PURIFIER_AUTO_PURILINKIFYDSC","Internal auto-formatter that converts configuration directives in syntax %Namespace.Directive to links. a tags with the href attribute must be allowed. (Leave this as is if you are not having any problems)");
define("_MD_AM_SEC_PURIFIER_AUTO_CUSTOM","Allowed Customised AutoFormatting");
define("_MD_AM_SEC_PURIFIER_AUTO_CUSTOMDSC","This directive can be used to add custom auto-format injectors. Specify an array of injector names (class name minus the prefix) or concrete implementations. Injector class must exist. please visit <a href='www.htmlpurifier.org'>HTML Purifier Homepage</a> for more info.");
define("_MD_AM_SEC_PURIFIER_AUTO_REMOVEEMPTY","Remove Empty Elements");
define("_MD_AM_SEC_PURIFIER_AUTO_REMOVEEMPTYDSC"," When enabled, HTML Purifier will attempt to remove empty elements that contribute no semantic information to the document. The following types of nodes will be removed:<br /><br />
 * Tags with no attributes and no content, and that are not empty elements (remove \<a\>\</a\> but not \<br /\>), and<br />
 * Tags with no content, except for:<br />
   o The colgroup element, or<br />
   o Elements with the id or name attribute, when those attributes are permitted on those elements.<br /><br />
Please be very careful when using this functionality; while it may not seem that empty elements contain useful information, they can alter the layout of a document given appropriate styling. This directive is most useful when you are processing machine-generated HTML, please avoid using it on regular user HTML.<br /><br />
Elements that contain only whitespace will be treated as empty. Non-breaking spaces, however, do not count as whitespace. See 'Remove Empty Spaces' for alternate behavior.");
define("_MD_AM_SEC_PURIFIER_AUTO_REMOVEEMPTYNBSP","Remove Non-Breaking Spaces");
define("_MD_AM_SEC_PURIFIER_AUTO_REMOVEEMPTYNBSPDSC","When enabled, HTML Purifier will treat any elements that contain only non-breaking spaces as well as regular whitespace as empty, and remove them when 'Remove Empty Elements' is enabled.<br /><br />
See 'Remove Empty Nbsp Override' for a list of elements that don't have this behavior applied to them.");
define("_MD_AM_SEC_PURIFIER_AUTO_REMOVEEMPTYNBSPEXCEPT","Remove empty Nbsp Override");
define("_MD_AM_SEC_PURIFIER_AUTO_REMOVEEMPTYNBSPEXCEPTDSC","When enabled, this directive defines what HTML elements should not be removed if they have only a non-breaking space in them.");
// Attribute Section
define("_MD_AM_SEC_PURIFIER_ATTR_ALLOWFRAMETARGET","Allowed Frame Targets");
define("_MD_AM_SEC_PURIFIER_ATTR_ALLOWFRAMETARGETDSC","Lookup table of all allowed link frame targets. Some commonly used link targets include _blank, _self, _parent and _top. Values should be lowercase, as validation will be done in a case-sensitive manner despite W3C's recommendation. XHTML 1.0 Strict does not permit the target attribute so this directive will have no effect in that doctype. XHTML 1.1 does not enable the Target module by default, you will have to manually enable it (see the module documentation for more details.)");
define("_MD_AM_SEC_PURIFIER_ATTR_ALLOWREL","Allowed Document Relationships");
define("_MD_AM_SEC_PURIFIER_ATTR_ALLOWRELDSC","List of allowed forward document relationships in the rel attribute. Common values may be nofollow or print.<br /><br />Default = external, nofollow, external nofollow & lightbox.");
define("_MD_AM_SEC_PURIFIER_ATTR_ALLOWCLASSES","Allowed Class Values");
define("_MD_AM_SEC_PURIFIER_ATTR_ALLOWCLASSESDSC","List of allowed class values in the class attribute. Leave This empty to allow all values in the Class Attribute.");
define("_MD_AM_SEC_PURIFIER_ATTR_FORBIDDENCLASSES","Forbidden Class Values");
define("_MD_AM_SEC_PURIFIER_ATTR_FORBIDDENCLASSESDSC","List of Forbidden class values in the class attribute. Leave This empty to allow all values in the Class Attribute.");
define("_MD_AM_SEC_PURIFIER_ATTR_DEFINVIMG","Default Invalid Image");
define("_MD_AM_SEC_PURIFIER_ATTR_DEFINVIMGDSC","This is the default image an img tag will be pointed to if it does not have a valid src attribute. In future versions, we may allow the image tag to be removed completely, but due to design issues, this is not possible right now.");
define("_MD_AM_SEC_PURIFIER_ATTR_DEFINVIMGALT","Default Invalid Image Alt Tag");
define("_MD_AM_SEC_PURIFIER_ATTR_DEFINVIMGALTDSC","This is the content of the alt tag of an invalid image if the user had not previously specified an alt attribute. It has no effect when the image is valid but there was no alt attribute present.");
define("_MD_AM_SEC_PURIFIER_ATTR_DEFIMGALT","Default Image Alt Tag");
define("_MD_AM_SEC_PURIFIER_ATTR_DEFIMGALTDSC","This is the content of the alt tag of an image if the user had not previously specified an alt attribute.<br />This applies to all images without a valid alt attribute, as opposed to Default Invalid Alt Tag, which only applies to invalid images, and overrides in the case of an invalid image.<br />Default behavior with null is to use the basename of the src tag for the alt.");
define("_MD_AM_SEC_PURIFIER_ATTR_CLASSUSECDATA","Use NMTokens or CDATA specifications");
define("_MD_AM_SEC_PURIFIER_ATTR_CLASSUSECDATADSC","If null, class will auto-detect the doctype and, if matching XHTML 1.1 or XHTML 2.0, will use the restrictive NMTOKENS specification of class. Otherwise, it will use a relaxed CDATA definition. If true, the relaxed CDATA definition is forced; if false, the NMTOKENS definition is forced. To get behavior of HTML Purifier prior to 4.0.0, set this directive to false. Some rational behind the auto-detection: in previous versions of HTML Purifier, it was assumed that the form of class was NMTOKENS, as specified by the XHTML Modularization (representing XHTML 1.1 and XHTML 2.0). The DTDs for HTML 4.01 and XHTML 1.0, however specify class as CDATA. HTML 5 effectively defines it as CDATA, but with the additional constraint that each name should be unique (this is not explicitly outlined in previous specifications).");
define("_MD_AM_SEC_PURIFIER_ATTR_ENABLEID","Allow ID Attribute?");
define("_MD_AM_SEC_PURIFIER_ATTR_ENABLEIDDSC","Allows the ID attribute in HTML. This is disabled by default due to the fact that without proper configuration user input can easily break the validation of a webpage by specifying an ID that is already on the surrounding HTML. If you don't mind throwing caution to the wind, enable this directive, but I strongly recommend you also consider blacklisting IDs you use (ID Blacklist) or prefixing all user supplied IDs (ID Prefix).");
define("_MD_AM_SEC_PURIFIER_ATTR_IDPREFIX","Set Attribute ID Prefix");
define("_MD_AM_SEC_PURIFIER_ATTR_IDPREFIXDSC","String to prefix to IDs. If you have no idea what IDs your pages may use, you may opt to simply add a prefix to all user-submitted ID attributes so that they are still usable, but will not conflict with core page IDs. Example: setting the directive to 'user_' will result in a user submitted 'foo' to become 'user_foo' Be sure to set 'Allow ID Attribute' to yes before using this.");
define("_MD_AM_SEC_PURIFIER_ATTR_IDPREFIXLOCAL","Allowed Customised AutoFormatting");
define("_MD_AM_SEC_PURIFIER_ATTR_IDPREFIXLOCALDSC","Temporary prefix for IDs used in conjunction with Attribute ID Prefix. If you need to allow multiple sets of user content on web page, you may need to have a seperate prefix that changes with each iteration. This way, seperately submitted user content displayed on the same page doesn't clobber each other. Ideal values are unique identifiers for the content it represents (i.e. the id of the row in the database). Be sure to add a seperator (like an underscore) at the end. Warning: this directive will not work unless Attribute ID Prefix is set to a non-empty value!");
define("_MD_AM_SEC_PURIFIER_ATTR_IDBLACKLIST","Attribute ID Blacklist");
define("_MD_AM_SEC_PURIFIER_ATTR_IDBLACKLISTDSC","Array of IDs not allowed in the document.");
// CSS Section
define("_MD_AM_SEC_PURIFIER_CSS_ALLOWIMPORTANT","Allow !important in CSS Styles");
define("_MD_AM_SEC_PURIFIER_CSS_ALLOWIMPORTANTDSC","This parameter determines whether or not !important cascade modifiers should be allowed in user CSS. If no, !important will stripped.");
define("_MD_AM_SEC_PURIFIER_CSS_ALLOWTRICKY","Allow Tricky CSS Styles");
define("_MD_AM_SEC_PURIFIER_CSS_ALLOWTRICKYDSC","This parameter determines whether or not to allow \"tricky\" CSS properties and values. Tricky CSS properties/values can drastically modify page layout or be used for deceptive practices but do not directly constitute a security risk. For example, display:none; is considered a tricky property that will only be allowed if this directive is set to no.");
define("_MD_AM_SEC_PURIFIER_CSS_ALLOWPROP","Allowed CSS Properties");
define("_MD_AM_SEC_PURIFIER_CSS_ALLOWPROPDSC","If HTML Purifier's style attributes set is unsatisfactory for your needs, you can overload it with your own list of tags to allow. Note that this method is subtractive: it does its job by taking away from HTML Purifier usual feature set, so you cannot add an attribute that HTML Purifier never supported in the first place.<br /><br />Warning: If another preference conflicts with the elements here, that preference will win and override.");
define("_MD_AM_SEC_PURIFIER_CSS_DEFREV","CSS Definition Revision");
define("_MD_AM_SEC_PURIFIER_CSS_DEFREVDSC","Revision identifier for your custom definition. See HTML Definition Revision for details.");
define("_MD_AM_SEC_PURIFIER_CSS_MAXIMGLEN","CSS Max Image Length");
define("_MD_AM_SEC_PURIFIER_CSS_MAXIMGLENDSC","This parameter sets the maximum allowed length on img tags, effectively the width and height properties. Only absolute units of measurement (in, pt, pc, mm, cm) and pixels (px) are allowed. This is in place to prevent imagecrash attacks, disable with null at your own risk. This directive is similar to HTML Max Image Length, and both should be concurrently edited, although there are subtle differences in the input format (the CSS max is a number with a unit).");
define("_MD_AM_SEC_PURIFIER_CSS_PROPRIETARY","Allow Safe Proprietary CSS");
define("_MD_AM_SEC_PURIFIER_CSS_PROPRIETARYDSC","Whether or not to allow safe, proprietary CSS values.");
// purifier config options
define("_MD_AM_SEC_PURIFIER_401T","HTML 4.01 Transitional");
define("_MD_AM_SEC_PURIFIER_401S","HTML 4.01 Strict");
define("_MD_AM_SEC_PURIFIER_X10T","XHTML 1.0 Transitional");
define("_MD_AM_SEC_PURIFIER_X10S","XHTML 1.0 Strict");
define("_MD_AM_SEC_PURIFIER_X11","XHTML 1.1");
define("_MD_AM_SEC_PURIFIER_WEGAME","WEGAME Movies");
define("_MD_AM_SEC_PURIFIER_VIMEO","Vimeo Movies");
define("_MD_AM_SEC_PURIFIER_LOCALMOVIE","Local Movies");
define("_MD_AM_SEC_PURIFIER_GOOGLEVID","Google Video");
define("_MD_AM_SEC_PURIFIER_LIVELEAK","LiveLeak Movies");

define("_MD_AM_SEC_UNABLECSSTIDY", "CSSTidy Plugin is not found, Please copy the make sure you have CSSTidy located in your plugins folder.");

// added in 1.3
define("_MD_AM_SEC_PURIFIER_OUTPUT_FLASHCOMPAT","Enable IE Flash Compatibility");
define("_MD_AM_SEC_PURIFIER_OUTPUT_FLASHCOMPATDSC","If true, HTML Purifier will generate Internet Explorer compatibility code for all object code. This is highly recommended if you enable HTML.SafeObject.");
define("_MD_AM_SEC_PURIFIER_HTML_FLASHFULLSCRN","Allow FullScreen in Flash objects");
define("_MD_AM_SEC_PURIFIER_HTML_FLASHFULLSCRNDSC","If true, HTML Purifier will allow use of 'allowFullScreen' in embedded flash content when using HTML.SafeObject.");
define("_MD_AM_SEC_PURIFIER_CORE_NORMALNEWLINES","Normalize Newlines");
define("_MD_AM_SEC_PURIFIER_CORE_NORMALNEWLINESDSC","Whether or not to normalize newlines to the operating system default. When false, HTML Purifier will attempt to preserve mixed newline files.");
define('_MD_AM_SEC_AUTHENTICATION_DSC', 'Manage security settings related to accessibility. Settings that will effect how users accounts are handled.');
define('_MD_AM_SEC_AUTOTASKS_PREF_DSC', 'Preferences for the Auto Tasks system.');
define('_MD_AM_SEC_CAPTCHA_DSC', 'Manage the settings used by captcha throughout your site.');
define('_MD_AM_SEC_GENERAL_DSC', 'The primary settings page for basic information needed by the system.');
define('_MD_AM_SEC_PURIFIER_DSC', 'HTMLPurifier is used to protect your site against common attack methods.');
define('_MD_AM_SEC_MAILER_DSC', 'Configure how your site will handle mail.');
define('_MD_AM_SEC_METAFOOTER_DSC', 'Manage your meta information and site footer as well as your crawler options.');
define('_MD_AM_SEC_MULTILANGUAGE_DSC', 'Manage your sites Multi-language settings. Enable, and configure what languages are available and how they are triggered.');
define('_MD_AM_SEC_PERSON_DSC', 'Personalize the system with custom logos and other settings.');
define('_MD_AM_SEC_PLUGINS_DSC', 'Select which plugins are used and available to be used throughout your site.');
define('_MD_AM_SEC_SEARCH_DSC', 'Manage how the search function operates for your users.');
define('_MD_AM_SEC_USERSETTINGS_DSC', 'Manage how users register for your site. ser names length, formatting and password options.');
define('_MD_AM_SEC_CENSOR_DSC', 'Manage the language that is not permitted on your site.');
?>