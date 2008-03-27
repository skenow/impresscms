<script language="javascript">
function Detect_OS()
{
	var OpSys;
	if(typeof(window.navigator.userAgent) != 'undefined')
	{
		Detected_Platform = window.navigator.userAgent.toUpperCase();
		
		if (Detected_Platform.indexOf('LINUX') != -1) 
		{
			navigator.OS = 'Linux';
		}
					
		else if (Detected_Platform.indexOf('SUN') != -1)
		{
			navigator.OS = 'SunOS';
		}
		
		else if (Detected_Platform.indexOf('UNIX') != -1)
		{
			navigator.OS = 'Unix';
		}
		
		else if (Detected_Platform.indexOf('WIN') != -1)
		{
			navigator.OS = 'Windows';
		}
		
		else if (Detected_Platform.indexOf('MAC') != -1)
		{
			navigator.OS = 'MacOS';
		}
		
		else
		{
			navigator.OS = 'Unknown';
			document.write("Detected Platform was: " +Detected_Platform +"<BR>");
			document.write("Please Notify http://www.phpdynatree.com");
		}
	}
	else
	{
		document.write("Your browser is not telling what it is");
	}
return;	
}


function Detect_Browser()
{
	var Detected_Browser;
	var Browser_Family;
	var Browser_Organization;
			
	if(typeof(window.navigator.userAgent) != 'undefined')
	{
		Detected_Browser = window.navigator.userAgent.toUpperCase();
		
		if (Detected_Browser.indexOf('OPERA') != -1)
		{
			navigator.Browser_Family  = 'Opera';
			navigator.Browser_Organization    = 'Opera';
		}
		
		else if (Detected_Browser.indexOf('MSIE') != -1)
		{
			navigator.Browser_Family = 'IE';
			navigator.Browser_Organization = 'Microsoft';
		}
		
		else if (Detected_Browser.indexOf('MOZILLA') != -1)
		{
			navigator.Browser_Family = 'Mozilla';
			navigator.Browser_Organization = 'Netscape';
		}
		
		else if (Detected_Browser.indexOf('AOL') != -1)
		{
			navigator.Browser_Family = 'aol';
			navigator.Browser_Organization = 'aol';
		}
		
		else if (Detected_Browser.indexOf('HOTJAVA') != -1)
		{
			navigator.Browser_Family = 'HotJava';
			navigator.Browser_Organization = 'Sun';
		}
		
		else
		{
			navigator.Browser_Family = 'Unknown';
			navigator.Browser_Organization = 'Unknown';
			document.write("Detected Browser was: " +Browser_Family +"<BR>");
			document.write("Detected Organization was: " +Browser_Organization +"<BR>");
			document.write("Please Notify http://www.phpdynatree.com");
		}
	}
	else
	{
		document.write("Your browser is not telling what it is");
	}
return;	
}

function Detect_Capability()
{
	var doc_ieb;
	var doc_all;
	var doc_lay;
	var Capable;

	// Detect all functions before using them
	doc_eib = (document.getElementById) ? true : false;
	doc_all = (document.all) ? true : false;
	doc_lay = (document.layers) ? true : false;
	
	Detect_OS();
	
	Mac = (Detected_Platform.indexOf('MAC') != -1);
	
	Detect_Browser();
	
	Capable = (doc_eib || doc_all || doc_lay);
	
	// If Internet Explorer4, will have to use document.all
	IE4 = doc_all && !doc_eib;
	IE4Mac = IE4 && Mac;
	
}

</script>
