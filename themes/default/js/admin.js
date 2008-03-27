function cpShowHide(what) {
	var obj = zariliaGetElementById(what);
	if (obj.className)	{
		obj.className = (obj.className == 'navOpened') ? 'navClosed' : 'navOpened';
		setCookie(what + "_classname", obj.className, 30);
	} else {
		obj.style.display = (obj.style.display=='none')?'block':'none';
		setCookie(what + "_visible", obj.style.display, 30);
	}
	var olst = getCookie("togglable_objects");
	olst = ((olst==null)?'':olst);
	var items = olst.split(",");
	var b = false;
	for (x in items) {
		if (items[x]==what)	{
			b = true;
			break;
		}
	}
	if(b==false) items.push(what);
	olst = items.toString();
	if (olst.substr(0,1)==",") olst = olst.substr(1);
	setCookie("togglable_objects",olst,30);
}

function cpDoShow() {
	if (!window.getCookie)	{
		window.setTimeout('cpDoShow()',100);
		return;
	}
	var olst = getCookie("togglable_objects");
	if (olst==null) return;
	var items = olst.split(",");
	var obj;
	var rez;
	for (x in items) {		
		rez = getCookie(items[x] + "_visible");
		rez = (rez==null)?'':rez;
		if (!(rez=='')) {
			obj = zariliaGetElementById(items[x]);
			if (obj==null) continue;
			obj.style.display = rez;
			setCookie(items[x] + "_visible", rez, 30);
		} else {
			rez = getCookie(items[x] + "_classname");
			rez = (rez==null)?'':rez;
			if (!(rez=='')) {
				obj = zariliaGetElementById(items[x]);
				if (obj==null) continue;
				obj.className = rez;
				setCookie(items[x] + "_classname", rez, 30);
			}
		}
	}
}

function cpIMGOpenClose(what,parentwhat,opened,closed) {
	var img = zariliaGetElementById(what);
	var obj = zariliaGetElementById(parentwhat);
	var opn = false;
 	if (obj.className)	{
		opn = obj.className == 'navOpened';
	} else {
		opn = obj.style.display=='block';
	}   
	img.src = (!opn)?opened:closed;
}