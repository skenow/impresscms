<?php
// $Id: user.php,v 1.3 2007/05/05 11:12:43 catzwolf Exp $
// %%%%%%		File Name user.php 		%%%%%
define( '_US_CREATEACCOUNT', '<b>Register</b>' );
define( '_US_CREATEACCOUNTTEXT', 'Sign up now to participate our forums, get access to our member only area\'s and much more. One registration can qualify you for these and other great features.' );
define( '_US_CREATEACCOUNTSIGNUP', 'Create New Member Account.  Click <a href="register.php">here</a>.' );
define( '_US_LOSTPASSWORD', 'Lost Password' );
define( '_US_LOSTPASSTEXT', 'If you have lost your password don\'t worry. Just follow the instructions below and you\'ll be up and running in no time.' );
define( '_US_PASSWORDRETRIEVAL', 'Password Retrieval' );
define( '_US_NOPROBLEM', 'If you have lost your Username and Password enter the email address currently listed in your Member account. All of your account details will be emailed to you at that email address. <br /><br />It will take a few moments to process your request.<br /><br />' );
define( '_US_LOSTCLICK', 'Please press the button once only.' );
define( '_US_PERSONAL', 'Personal' );
// define( '_US_NOTIFICATIONS', 'Notifications' );
define( '_US_BIRTHDATE', 'Birthdate' );
define( '_US_LOGIN', 'Login:' );
define( '_US_IAMOVER', 'I certify that the above date entered is my true age.' );
define( '_US_AOUTVTEAD', 'Allow other users to view this email address' );
define( '_US_YOUREMAIL', 'Registered Email address: ' );
define( '_US_SENDPASSWORD', 'Send Password' );
define( '_US_LOGGEDOUT', 'You are now logged out' );
define( '_US_THANKYOUFORVISIT', 'You are now logged out.<br />Please wait while we redirect you.' );
define( '_US_INCORRECTLOGIN', 'Your login information was incorrect!' );
define( '_US_LOGGINGU', 'Thank you for logging in, %s.' );
define( '_US_REGCHECK', '<b>Registration Check:</b><br /><div><small>Type the characters that you see in this picture.</small></div>' );
define( '_US_LOGINUSINGDETAILS', 'Enter Your Login Details' );
define( '_US_REMEBERME', 'Remember Me' );
define( '_US_LOGINANON', 'Login Anonymous' );
define( '_US_BROWSERCOOKIES', 'Please be aware that your browser must accept cookies in order for you to successfully log in and for us to identify your account. By registering you agree to abide by our Terms of Service.' );
define( '_US_LOGINNOTICE', 'To modify your registration information, you must log in using the username and password you provided when you registered.' );
define( '_US_DISCLAIMER', 'Disclaimer' );
define( '_US_LOGINENTER', 'login Name' );
define( '_US_LOGINPASSWORD', 'Password' );
define( '_US_LOGINBUTTON', 'Login' );
define( '_US_LOGINDETAILS', 'login Details' );
define( '_US_LANGUAGE', 'Language Choice:' );
define( '_US_THEME', 'Theme Choice:' );
// 2001-11-17 ADD
define( '_US_NOACTTPADM', 'The selected user has been deactivated or has not been activated yet.<br />Please contact the administrator for details.' );
define( '_US_ACTKEYNOT', 'Activation key not correct!' );
define( '_US_ACTKEYFAILED', 'Activation has failed, please contact the webmaster!' );
define( '_US_ACONTACT', 'Selected account is already activated!' );
define( '_US_ACTLOGIN', 'Your account has been activated. Please login with the registered password.' );
define( '_US_NOPERMISS', 'Sorry, you dont have the permission to perform this action!' );
define( '_US_SURETODEL', 'Are you sure you want to delete your account?' );
define( '_US_REMOVEINFO', 'This will remove all your info from our database.' );
define( '_US_BEENDELED', 'Your account has been deleted.' );
// %%%%%%		File Name register.php 		%%%%%

define( '_US_IAGREE', 'I agree to the above' );
define( '_US_UNEEDAGREE', 'Sorry, you have to agree to our disclaimer to get registered.' );
define( '_US_NOREGISTER', 'Sorry, we are currently closed for new user registrations' );
define( '_US_CREATEPASSWORD', 'Generate Password<div style="padding-top: 8px;"><span style="font-weight: normal;">Auto create Password. Remember to write this for future reference.</span></div>' );
define( '_US_REG_FORM_HEADING', 'User Registration Form' );
define( '_US_REG_COMPLETE', 'Registration Complete' );
/*
* Coppa
*/
define( '_US_PLZCONTACT', 'For further information, please contact: %s' );
// %s is username. This is a subject for email
define( '_US_USERKEYFOR', 'User activation key for %s' );
define( '_US_YOURREGISTERED', 'Thank-you %s for registering at %s.<br /><br />An email containing an user activation key has been sent to the email address you provided.
	Please follow the instructions in the mail to activate your account. <br /><br />
	If you do not receive an activation email within 24 hours, please contact us at %s for assistance stating your account details.' );
define( '_US_YOURREGMAILNG', 'Thank you <b>%s</b> for registering with our website.<br /><br />
	You are now registered. However, we were unable to send the activation mail to your email account due to an internal error that had occurred on our server.
	We are sorry for the inconvenience, please contact us at %s for assistance stating your account details.' );
define( '_US_YOURREGISTERED2', 'Hi %s,<br /><br />You are now registered.
	Please wait for your account to be activated by the adminstrators.
	You will receive an email once you are activated.  This could take a while so please be patient.
	If you do not receive an activation email within 24 hours, please contact us at %s for assistance stating your account details.' );
// Thank you for registering, MasterIncubus. An email has been dispatched to masterincubus@gameinatrix.com with details on how to activate your account. Click here to return to where you were previously.
// You will receive an email in your inbox. You MUST follow the link in that email before you can post on these forums. Until you do that, you will be told that you do not have permission to post.
// %s is your site name
define( '_US_NEWUSERREGAT', 'New user registration at %s' );
// %s is a username
define( '_US_HASJUSTREG', '%s has just registered!' );
define( '_US_INVALIDMAIL', 'ERROR: Invalid email' );
define( '_US_EMAILNOSPACES', 'ERROR: Email addresses do not contain spaces.' );
define( '_US_INVALIDNICKNAME', 'ERROR: Invalid display name' );
define( '_US_NICKNAMETOOLONG', 'Login or display name is too long. It must be less than %s characters.' );
define( '_US_NICKNAMETOOSHORT', 'Login or display name is too short. It must be more than %s characters.' );
define( '_US_NAMERESERVED', 'ERROR: Name is reserved.' );
define( '_US_NICKNAMENOSPACES', 'There cannot be any spaces in the login or display names.' );
define( '_US_NICKNAMETAKEN', 'ERROR: Display name already exists.' );
define( '_US_EMAILTAKEN', 'ERROR: Email address already registered.' );
define( '_US_ENTERPWD', 'ERROR: You must provide a password.' );
define( '_US_SORRYNOTFOUND', 'Sorry, no corresponding user info was found.' );
define( '_US_LOGINNAMETAKEN', 'ERROR: Login name already exists' );
define( '_US_LOGINSAME', 'ERROR: Login and Display names are the same. Both must not be identical.' );
define( '_US_REGFORM', 'Is this registration info correct?' );
define( '_US_INVALIDLOGIN', 'Invalid login' );
define( '_US_LOGINNOSPACES', 'ERROR: Login name can\'t have space simbols in it' );
define( '_US_PASSNOTSAME', 'ERROR: Both passwords are different. They must be identical.' );
define( '_US_PWDTOOSHORT', 'ERROR: Sorry, your password must be at least <b>%s</b> characters long.' );
// %s is your site name
define( '_US_NEWPWDREQ', 'New Password Request at %s' );
define( '_US_YOURACCOUNT', 'Your account at %s' );
define( '_US_MAILPWDNG', 'mail_password: could not update user entry. Contact the Administrator' );
define( '_US_MAILERROR', 'Sorry, we seem to have an issue with our mailing system. This email was not sent to you. Please contact the administrator.' );
// %s is a username
define( '_US_PWDMAILED', 'Password for %s mailed.' );
define( '_US_CONFMAIL', 'Confirmation Mail for %s mailed.' );
define( '_US_ACTVMAILNG', 'Failed sending notification mail to %s' );
define( '_US_ACTVMAILOK', 'Notification mail to %s sent.' );
// %%%%%%		File Name userinfo.php 		%%%%%
define( '_US_SELECTNG', 'No User Selected! Please go back and try again.' );
//define( '_US_PROFILE_TITLE_HEADING', 'Viewing Profile: ' );
//define( '_US_EDITPROFILE', 'Account Details' );
//define( '_US_AVATAR', 'Avatar' );
//define( '_US_INBOX', 'Messages' );
/*
define( '_US_MEMBERSINCE', 'Member Since' );
define( '_US_RANK', 'Rank' );
define( '_US_POSTS', 'Comments/Posts' );
define( '_US_LASTLOGIN', 'Last Login' );
define( '_US_ALLABOUT', 'All about %s' );
define( '_US_STATISTICS', 'Statistics' );
define( '_US_MYINFO', 'My Info' );
define( '_US_BASICINFO', 'Basic information' );
define( '_US_MOREABOUT', 'More About Me' );
*/
//define( '_US_SHOWALL', 'Show All' );
//define( '_US_SENDPMTO', 'Send PM' );
//define( '_US_SENDEMAIL', 'Send Email' );
//define( '_US_ONLINE', 'Online Status' );
// %%%%%%		File Name edituser.php 		%%%%%
define( '_US_PROFILE', 'Profile' );
define( '_US_REALNAME', 'Real Name' );
define( '_US_SHOWSIG', 'Always attach my signature' );
define( '_US_CDISPLAYMODE', 'Comments Display Mode' );
define( '_US_CSORTORDER', 'Comments Sort Order' );
define( '_US_TYPEPASSTWICE', '(type a new password twice to change it)' );
define( '_US_SAVECHANGES', 'Save Changes' );
define( '_US_NOEDITRIGHT', "Sorry, you don't have the right to edit this user's info." );
define( '_US_PROFUPDATED', 'Your Profile Updated!' );
define( '_US_USECOOKIE', 'Store my user name in a cookie for 1 year' );
define( '_US_NO', 'No' );

define( '_US_PRESSLOGIN', 'Press the button below to login' );
define( '_US_ADMINNO', 'User in the webmasters group cannot be removed' );
define( '_US_GROUPS', 'User\'s Groups' );

define( '_US_SUBMISSION_HEAD', 'User Submission' );
define( '_US_SUBMISSION_HEAD_TEXT', 'Please feel free to contribute to Gameinatrix.com using the following links:' );

// error notices//
define( '_US_ERROR_CANNOTLOGIN', 'You cannot use this method to login, please use the login form supplied for this.' );
define( '_US_ERROR_ALREADYLOGIN', 'You have already logged into our website.' );
define( '_US_ERROR_NOTLOGIN', 'It seems you were not logged into our website. We could not log you out.' );


//Register Form
?>