var currentid = -1
var languages_ids = new Array();

if (!(window.zariliaGetElementById)) {
	function zariliaGetElementById(id){
		if (document.getElementById) {
			return (document.getElementById(id));
		} else if (document.all) {
			return (document.all[id]);
		} else {
			if ((navigator.appname.indexOf("Netscape") != -1) && parseInt(navigator.appversion == 4)) {
				return (document.layers[id]);
			}
		}
	}
}

function ChangeLanguageFormItem(formid, visible) {
	var obj = zariliaGetElementById('form-' + currentid + '-' + formid);
	obj.style.display = (visible)?'block':'none';
	obj.visible = visible;
}

function SelectLanguageForm(languageid,formid) {
	if (currentid>0) ChangeLanguageFormItem(formid, false);
	currentid = languageid;
	ChangeLanguageFormItem(formid, true)	
}

function ChangeAllFormsItems(cobj, name, id) {
	var obs = null;
	var xname = null;
	for (var i in languages_ids) {
		if (parseInt(languages_ids[i])!=languages_ids[i]) continue;
		if (languages_ids[i]==id) continue;				
		for (var g=0;g<2;g++) {
			switch (g) {
				case 1:
					xname = name;
				break;
				default:
					xname = 'zarilia_language[' + (languages_ids[i]).toString()+ '][' + name + ']';
			}
			obs = document.getElementsByName(xname);
			if (obs.length<1) {
				xname = xname + '[]';
				obs = document.getElementsByName(xname);
			}
			for(var k=0;k<obs.length;k++) {
				switch (cobj.type){
					case 'radio':	
					case 'checkbox':
						obs[k].checked = (obs[k].value == cobj.value);			
					break;
					case 'select-one':
						obs[k].value = cobj.value;
					break;
					case 'select-multiple':
						for(var m = 0;m < cobj.length;m++){
							obs[k].options[m].selected = cobj.options[m].selected;
						}
					break;
					default:
//						alert(cobj.type);
				}			
			}
		}
	}
}

function getFormElementName(languageid, name) {
	return 'zarilia_language[' + languageid.toString() +  '][' + name.toString() + ']';
}

function getFormDefaultElementName(mlname) {
	var txt=mlname;
	var m = txt.indexOf(']', 0) + 1;
	var i = txt.indexOf(']', m);
	var rez = txt.substring(m+1, i);
	if (rez.substring(rez.length-1)=='[') rez = rez.substring(0,rez.length-1)
	return rez;
}

function getFormDefaultElementLanguage(mlname) {
	var txt=mlname;
	var m = txt.indexOf(']', 0);
	var i = txt.indexOf('[', 0)+1;
	return txt.substring(i, m);
}

function getFormDefaultElementMValue(mlname) {
	var name = getFormDefaultElementName(mlname);
	var lang = getFormDefaultElementLanguage(mlname);
	return (mlname != getFormElementName(lang,name));
}

function getFormDefaultFields(cobj) {
	var items = Array();
	var str;
	var key = 'zarilia_language[';
	var len = key.length;
	for (var m = 0;m < cobj.length;m++)	{
		obj = cobj.elements[m];
		str = obj.name + '';
//		alert(str + ' ' + obj.type);
		if (str.substring(0,len) == key ) {
			items[getFormDefaultElementName(str)] = getFormDefaultElementMValue(str);
		}
	}
	return items;
}

function SubmitForm(cobj, url) {
	MultilanguageDataWriter(cobj);
	cobj.action = url;
	cobj.submit();
}

function getFormDefaultField(name) {
	var obj = document.getElementsByName(name);
	return obj[0];
}

String.prototype.replace_all = function(find, replace) {
	var rez = this;
	var r2 = '';
	while (true) {
		r2 = rez.replace(find, replace);
		if (r2==rez) break;
		rez = r2;
	}
	return rez;
}

String.prototype.replace_mass = function(what) {
	var rez = this;
	for (var i in what) {
		rez = rez.replace_all(i, what[i]);
	}
	return rez;
}

function getMultilanguageCode(name) {
	var txt = '', txt2;
	var obj = null;
	var xname;
	for (var i in languages_ids) {
		xname = getFormElementName(languages_ids[i], name);
		obj = document.getElementsByName(xname);
		txt2 = '';
		for (var m = 0;m < obj.length;m++)	{
			txt2 = txt2 + obj[m].value;		
		}
		if (txt2) {
			txt2 = txt2.replace_mass({'&':'&amp;','[':'&#91;',']':'&#93;'});
			txt = txt + '[' + i + ']' + txt2 + '[/' + i + ']';
		}
	}
	return txt;
}

function MultilanguageDataWriter(cobj) {
	var items = getFormDefaultFields(cobj);	
	var obj = null;
	for (var name in items) {
		obj = getFormDefaultField(name);
//		alert(name + ' '+obj);
		if (obj) {
			switch (obj.type) {
				case 'text':
				case 'textarea':
					obj.value = getMultilanguageCode(name);
				break;
/*				case 'hidden':
				case 'radio':
				case 'checkbox':
				case 'select-one':
				case 'select-multiple':
				case 'submit':
				case 'reset':
				case 'button':*/
					//do nothing
/*				return;*/
				default:
//					alert(obj.type);
				break;
			}
		};
	}
//	return; 
}