function generate( form ) {
    var type = form . type . selectedIndex;
    var i, tmpstr, rnx;
    tmpstr = "";		
	var canbe = "1234567890";
	switch (type){
		case 3:
			canbe += "QWERTYUIOPASDFGHJKLZXCVBNM";
		case 2:
			canbe += "qwertyuioplkjhgfdsazxcvbnm";
		break;
		default:
			canbe += "QWERTYUIOPASDFGHJKLZXCVBNM";
		break;
	}
	var length = canbe.length;
	for(i=0;i<form.length.selectedIndex;i++) {
		rnx = -1;
		while (rnx < 1)	rnx = parseInt( Math . random() * ( length ) );
		tmpstr += canbe.charAt(rnx);
	}	
    form . password . value = tmpstr;
	if (form.autoupdate.value == "true")	{
		form . vpass . value = tmpstr;
		form . pass . value = tmpstr;
	}
} 