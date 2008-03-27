function info_validate() {
	valid = true;
    if ( !document.register.agree_disc.checked ) {
        alert ( "You must agree to this disclaimer before you can continue." );
		document.register.agree_disc.focus();
		valid = false;
    }
    return valid;
}

function coppa_validate() {
    valid = true;

	if ( !document.register.user_coppa_agree.checked ) {
        alert ( "You must agree that the age you have entered is your actual age." );
        valid = false;
    }
   return valid;
}

function zariliaFormValidate_userinfo() {
	myform = window.document.userinfo;

	if ( myform.uname.value == "" ) { window.alert("Please enter required value for 'Display Name'"); myform.uname.focus(); return false; }
	if ( myform.login.value == "" ) { window.alert("Please enter required value for 'Login Name'"); myform.login.focus(); return false; }
	if ( myform.pass.value == "" ) { window.alert("Please enter required value for 'Password'"); myform.pass.focus(); return false; }
	if ( myform.email.value == "" ) { window.alert("Please enter required value for 'email'"); myform.email.focus(); return false; }

	return true;
}