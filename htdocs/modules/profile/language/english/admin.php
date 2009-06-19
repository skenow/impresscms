	<?php
/**
 * English language constants used in admin section of the module
 *
 * @copyright	The ImpressCMS Project <http://www.impresscms.org>
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author      Jan Pedersen
 * @author      Marcello Brandao <marcello.brandao@gmail.com>
 * @author	   	Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
 * @author		Gustavo Pilla (aka nekro) <nekro@impresscms.org>
 * @package		profile
 * @version		$Id$
 */

if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

// Requirements
define("_AM_PROFILE_REQUIREMENTS", "imProfile Requirements");
define("_AM_PROFILE_REQUIREMENTS_INFO", "We've reviewed your system, unfortunately it doesn't meet all the requirements needed for improfile to function. Below are the requirements needed.");
define("_AM_PROFILE_REQUIREMENTS_ICMS_BUILD", "imProfile requires at least ImpressCMS 1.1.1 RC 1.");
define("_AM_PROFILE_REQUIREMENTS_SUPPORT", "Should you have any question or concerns, please visit our forums at <a href='http://community.impresscms.org'>http://community.impresscms.org</a>.");

// General
define("_AM_PROFILE_FIRST_USE", "This is the first time you access this module. Please update the module in order to dynamically create the database scheme.");

// Field
//define("_AM_PROFILE_FIELDS", "Fields");
define("_AM_PROFILE_FIELDS_DSC", "All fields in the module");
define("_AM_PROFILE_FIELD_CREATE", "Add a field");
define("_AM_PROFILE_FIELD", "Field");
define("_AM_PROFILE_FIELD_CREATE_INFO", "Fill-out the following form to create a new field.");
define("_AM_PROFILE_FIELD_EDIT", "Edit this field");
define("_AM_PROFILE_FIELD_EDIT_INFO", "Fill-out the following form in order to edit this field.");
define("_AM_PROFILE_FIELD_MODIFIED", "The field was successfully modified.");
define("_AM_PROFILE_FIELD_CREATED", "The field has been successfully created.");
define("_AM_PROFILE_FIELD_VIEW", "Field info");
define("_AM_PROFILE_FIELD_VIEW_DSC", "Here is the info about this field.");

define("_AM_PROFILE_FIELD_TYPE_CHECKBOX", "Checkbox");
define("_AM_PROFILE_FIELD_TYPE_GROUP", "Group Select");
define("_AM_PROFILE_FIELD_TYPE_GROUPMULTI", "Group Multi Select");
define("_AM_PROFILE_FIELD_TYPE_LANGUAGE", "Language Select");
define("_AM_PROFILE_FIELD_TYPE_RADIO", "Radio Buttons");
define("_AM_PROFILE_FIELD_TYPE_SELECT", "Select");
define("_AM_PROFILE_FIELD_TYPE_SELECTMULTI", "Multi Select");
define("_AM_PROFILE_FIELD_TYPE_TEXTAREA", "Text Area");
define("_AM_PROFILE_FIELD_TYPE_DHTMLTEXTAREA", "DHTML Text Area");
define("_AM_PROFILE_FIELD_TYPE_TEXTBOX", "Text Field");
define("_AM_PROFILE_FIELD_TYPE_TIMEZONE", "Timezone");
define("_AM_PROFILE_FIELD_TYPE_YESNO", "Radio Yes/No");
define("_AM_PROFILE_FIELD_TYPE_DATE", "Date");
define("_AM_PROFILE_FIELD_TYPE_AUTOTEXT", "Auto Text");
define("_AM_PROFILE_FIELD_TYPE_DATETIME", "Date and Time");
define("_AM_PROFILE_FIELD_TYPE_LONGDATE", "Long Date");
define("_AM_PROFILE_FIELD_TYPE_IMAGE", "Image");

// Regstep
//define("_AM_PROFILE_REGSTEPS", "Registration Steps");
define("_AM_PROFILE_REGSTEPS_DSC", "All Registration Steps in the module");
define("_AM_PROFILE_REGSTEP_CREATE", "Add a Registration step");
define("_AM_PROFILE_REGSTEP", "Registration Step");
define("_AM_PROFILE_REGSTEP_CREATE_INFO", "Fill-out the following form to create a new Registration Step.");
define("_AM_PROFILE_REGSTEP_EDIT", "Edit this Registration Step");
define("_AM_PROFILE_REGSTEP_EDIT_INFO", "Fill-out the following form in order to edit this Registration Step.");
define("_AM_PROFILE_REGSTEP_MODIFIED", "The Registration Step was successfully modified.");
define("_AM_PROFILE_REGSTEP_CREATED", "The Registration Step has been successfully created.");
define("_AM_PROFILE_REGSTEP_VIEW", "Registration Step info");
define("_AM_PROFILE_REGSTEP_VIEW_DSC", "Here is the info about this Registration Step.");

// Category
//define("_AM_PROFILE_CATEGORYS", "Categories");
define("_AM_PROFILE_CATEGORYS_DSC", "All categories in the module");
define("_AM_PROFILE_CATEGORY_CREATE", "Add a category");
define("_AM_PROFILE_CATEGORY", "Category");
define("_AM_PROFILE_CATEGORY_CREATE_INFO", "Fill-out the following form to create a new category.");
define("_AM_PROFILE_CATEGORY_EDIT", "Edit this category");
define("_AM_PROFILE_CATEGORY_EDIT_INFO", "Fill-out the following form in order to edit this category.");
define("_AM_PROFILE_CATEGORY_MODIFIED", "The category was successfully modified.");
define("_AM_PROFILE_CATEGORY_CREATED", "The category has been successfully created.");
define("_AM_PROFILE_CATEGORY_VIEW", "Category info");
define("_AM_PROFILE_CATEGORY_VIEW_DSC", "Here is the info about this category.");

define("_PROFILE_AM_SAVEDSUCCESS", "%s Saved Successfully");
define("_PROFILE_AM_DELETEDSUCCESS", "%s Deleted Successfully");
define("_PROFILE_AM_RUSUREDEL", "Are you sure you want to delete %s");

define("_PROFILE_AM_ADD", "Add");
define("_PROFILE_AM_EDIT", "Edit");
define("_PROFILE_AM_TYPE", "Field Type");
define("_PROFILE_AM_VALUETYPE", "Value Type");
define("_PROFILE_AM_NAME", "Name");
define("_PROFILE_AM_TITLE", "Title");
define("_PROFILE_AM_DESCRIPTION", "Description");
define("_PROFILE_AM_REQUIRED", "Required?");
define("_PROFILE_AM_MAXLENGTH", "Maximum Length");
define("_PROFILE_AM_WEIGHT", "Weight");
define("_PROFILE_AM_DEFAULT", "Default");
define("_PROFILE_AM_NOTNULL", "Not Null?");
//define("_PROFILE_AM_MODULE", "Module");

define("_PROFILE_AM_ARRAY", "Array");
define("_PROFILE_AM_EMAIL", "Email");
define("_PROFILE_AM_INT", "Integer");
define("_PROFILE_AM_TXTAREA", "Text Area");
define("_PROFILE_AM_TXTBOX", "Text field");
define("_PROFILE_AM_URL", "URL");
define("_PROFILE_AM_OTHER", "Other");

//define("_PROFILE_AM_PROF_VISIBLE_ON", "Field visible on these groups' profile");
//define("_PROFILE_AM_PROF_VISIBLE_FOR", "Field visible on profile for these groups");
define("_PROFILE_AM_PROF_VISIBLE", "Visibility");
define("_PROFILE_AM_PROF_EDITABLE", "Field editable from profile");
define("_PROFILE_AM_PROF_REGISTER", "Show in registration form");
define("_PROFILE_AM_PROF_SEARCH", "Searchable by these groups");
define("_PROFILE_AM_EXPORTABLE", "Exportable");

define("_PROFILE_AM_FIELDVISIBLE", "The field ");
define("_PROFILE_AM_FIELDVISIBLEFOR", " is visible for ");
define("_PROFILE_AM_FIELDVISIBLEON", " viewing a profile of ");
define("_PROFILE_AM_FIELDVISIBLETOALL", "- Everyone");
define("_PROFILE_AM_FIELDNOTVISIBLE", "is not visible");

define("_PROFILE_AM_CHECKBOX", "Checkbox");
define("_PROFILE_AM_GROUP", "Group Select");
define("_PROFILE_AM_GROUPMULTI", "Group Multi Select");
define("_PROFILE_AM_LANGUAGE", "Language Select");
define("_PROFILE_AM_RADIO", "Radio Buttons");
define("_PROFILE_AM_SELECT", "Select");
define("_PROFILE_AM_SELECTMULTI", "Multi Select");
define("_PROFILE_AM_TEXTAREA", "Text Area");
define("_PROFILE_AM_DHTMLTEXTAREA", "DHTML Text Area");
define("_PROFILE_AM_TEXTBOX", "Text Field");
define("_PROFILE_AM_TIMEZONE", "Timezone");
define("_PROFILE_AM_YESNO", "Radio Yes/No");
define("_PROFILE_AM_DATE", "Date");
define("_PROFILE_AM_AUTOTEXT", "Auto Text");
define("_PROFILE_AM_DATETIME", "Date and Time");
define("_PROFILE_AM_LONGDATE", "Long Date");

define("_PROFILE_AM_ADDOPTION", "Add Option");
define("_PROFILE_AM_REMOVEOPTIONS", "Remove Options");
define("_PROFILE_AM_KEY", "Key");
define("_PROFILE_AM_VALUE", "Value");

// User management
define("_PROFILE_AM_EDITUSER", "Edit User");
define("_PROFILE_AM_SELECTUSER", "Select User");
//define("_PROFILE_AM_AYSYWTDU","Are you sure you want to delete user %s?");
//define("_PROFILE_AM_BYTHIS","By doing this all the info for this user will be removed permanently.");
//define("_PROFILE_AM_YMCACF","You must complete all required fields");
//define("_PROFILE_AM_CNRNU","Could not register new user.");
//define("_PROFILE_AM_EDEUSER","Edit/Delete Users");
//define("_PROFILE_AM_NICKNAME","Nickname");
//define("_PROFILE_AM_MODIFYUSER","Modify User");
//define("_PROFILE_AM_DELUSER","Delete User");
//define("_PROFILE_AM_GO","Go!");
define("_PROFILE_AM_ADDUSER","Add User");
//define("_PROFILE_AM_OPTION","Option");
//define("_PROFILE_AM_AVATAR","Avatar");
define("_PROFILE_AM_THEME","Theme");
//define("_PROFILE_AM_AOUTVTEAD","Allow other users to view this email address");
define("_PROFILE_AM_RANK","Rank");
//define("_PROFILE_AM_NSRA","No Special Rank Assigned");
//define("_PROFILE_AM_NSRID","No Special Ranks in Database");
//define("_PROFILE_AM_ACCESSLEV","Access Level");
//define("_PROFILE_AM_PASSWORD","Password");
//define("_PROFILE_AM_INDICATECOF","* indicates required fields");
//define("_PROFILE_AM_NOTACTIVE","This user has not been activated. Do you wish to activate this user?");
//define("_PROFILE_AM_UPDATEUSER","Update User");
//define("_PROFILE_AM_USERINFO","User Info");
//define("_PROFILE_AM_USERID","User ID");
//define("_PROFILE_AM_RETYPEPD","Retype Password");
//define("_PROFILE_AM_CHANGEONLY","(for changes only)");
//define("_PROFILE_AM_USERPOST","User Posts");
//define("_PROFILE_AM_COMMENTS","Comments");
//define("_PROFILE_AM_PTBBTSDIYT","Push the button below to synchronize data if you think the above user posts info does not seem to indicate the actual status");
//define("_PROFILE_AM_SYNCHRONIZE","Synchronize");
define("_PROFILE_AM_USERDONEXIT","User doesn't exist!");
//define("_PROFILE_AM_STNPDNM","Sorry, the new passwords do not match. Click back and try again");
//define("_PROFILE_AM_CNGTCOM","Could not get total comments");
//define("_PROFILE_AM_CNUUSER","Could not update user");
//define("_PROFILE_AM_CNGUSERID","Could not get user IDS");
//define("_PROFILE_AM_LIST","List");
//define("_PROFILE_AM_NOUSERS", "No users selected");
define("_PROFILE_MA_ACTIVEUSER", "User Level");

define("_PROFILE_MA_ACTIVE", "Active");
define("_PROFILE_MA_INACTIVE", "Inactive");
define("_PROFILE_MA_DISABLED", "Disabled");
define("_PROFILE_MA_USERDISABLED", "This user account is disabled and cannot be activated by the user");

//define("_PROFILE_AM_NOUSERNAME", "No Username Selected");
define("_PROFILE_AM_USERCREATED", "User Created");

define("_PROFILE_AM_CANNOTDELETESELF", "Deleting your own account is not allowed - use your profile page to delete your own account");

define("_PROFILE_AM_NOSELECTION", "No user selected");
define("_PROFILE_AM_CANNOTEDITWEBMASTERS", "You cannot edit a webmaster's account");
define("_PROFILE_AM_USER_ACTIVATED", "User activated");
define("_PROFILE_AM_USER_DEACTIVATED", "User deactivated");
define("_PROFILE_AM_USER_NOT_ACTIVATED", "Error: User NOT activated");
define("_PROFILE_AM_USER_NOT_DEACTIVATED", "Error: User NOT deactivated");

define("_PROFILE_AM_STEPNAME", "Step name");
define("_PROFILE_AM_STEPORDER", "Step order");
define("_PROFILE_AM_STEPSAVE", "Save after step");
define("_PROFILE_AM_STEPINTRO", "Step description");
define("_PROFILE_AM_NOSTEP", "--NONE--");

// Photo/Image field type
define("_PROFILE_AM_MAXWIDTH", "Max width (px)");
define("_PROFILE_AM_MAXHEIGHT", "Max height (px)");
define("_PROFILE_AM_MAXSIZE", "Max file size (KB)");

//Find user
define("_AM_SPROFILE_FINDUSER_CRIT", "%s contains:");
define("_AM_SPROFILE_FINDUSER", "Find Users");
define("_AM_SPROFILE_UNAME", "Username");
define("_AM_SPROFILE_UID", "Userid");
define("_AM_SPROFILE_EMAIL", "Email");
define("_AM_SPROFILE_BACK_TO_FORM", "<< Back to search form");
define("_AM_SPROFILE_EXPORT_ALL", "Export all matching users");
//define("_MA_PROFILE_BY","By");
//define("_MA_PROFILE_DESC","Description");
//define("_MA_PROFILE_CREDITS","Credits");
//define("_MA_PROFILE_CONTRIBUTORS","Contributors Information");
//define("_MA_PROFILE_DEVELOPERS","Developers");
//define("_MA_PROFILE_TESTERS","Testers");
//define("_MA_PROFILE_TRANSLATIONS","Translations");
//define("_MA_PROFILE_EMAIL","Email");
//define("_MA_PROFILE_MODDEVDET","Module Development details");
//define("_MA_PROFILE_RELEASEDATE","Release date");
//define("_MA_PROFILE_STATUS","Status");
//define("_MA_PROFILE_OFCSUPORTSITE","Official Support Site");
//define("_MA_PROFILE_VERSIONHIST","Version History");
define("_MA_PROFILE_CONFIGEVERYTHING","Make sure you've configured everything under the preferences tab ");
define("_MA_PROFILE_ALLTESTSOK","All tests must be OK for this module to work 100%:");
define("_MA_PROFILE_GDEXTENSIONOK","GD extension loaded: OK!");
define("_MA_PROFILE_MOREINFO","Here is more info on:");
define("_MA_PROFILE_GDEXTENSIONFALSE","GD extension loaded: FAILED ");
define("_MA_PROFILE_CONFIGPHPINI","Configure your php.ini or ask your server manager to install it and enable it for you.");
define("_MA_PROFILE_PHP5PRESENT","You have a compatible version of PHP:");
define("_MA_PROFILE_PHP5NOTPRESENT","Your PHP version is compatible, but many details would work better on a php5 server and above.");
define("_MA_PROFILE_MAXBYTESPHPINI","Your server limits the size of uploads to %s");
define("_MA_PROFILE_MEMORYLIMIT","The Memory Limit of your server is:");
define("_MA_PROFILE_MP3_IS_NOT_EXISTS","does not exist");
define("_MA_PROFILE_MP3_IS_NOT_WRITABLE","is not writable");
define("_MA_PROFILE_MP3_EXISTS_AND_WRITABLE","exists and writable");
define("_MA_PROFILE_MYSQL4_OR_HIGHER","You must use a version higher than 4.1");
?>