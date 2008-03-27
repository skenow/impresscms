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

function zarilia_imgchange(frm, selbox, img){
  var si =  document.frm.selbox.selectedIndex;
  var fname = document.frm.selbox.options[si].value
  document.img.src = fname
}

function zariliaSetElementProp(name, prop, val) {
	var elt=zariliaGetElementById(name);
	if (elt) elt[prop]=val;
}

function zariliaSetElementStyle(name, prop, val) {
	var elt=zariliaGetElementById(name);
	if (elt && elt.style) elt.style[prop]=val;
}

function zariliaGetFormElement(fname, ctlname) {
	var frm=document.forms[fname];
	return frm ? frm.elements[ctlname] : null;
}

function justReturn() {
	return;
}

function openWithSelfMain(url,name,width,height) {
	var options = "width=" + width + ",height=" + height + "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no";

	new_window = window.open(url, name, options);
	window.self.name = "main";
	new_window.focus();
}

function setElementColor(id, color){
	zariliaGetElementById(id).style.color = "#" + color;
}

function setElementFont(id, font){
	zariliaGetElementById(id).style.fontFamily = font;
}

function setElementSize(id, size){
	zariliaGetElementById(id).style.fontSize = size;
}

function changeDisplay(id){
	var elestyle = zariliaGetElementById(id).style;
	if (elestyle.display == "") {
		elestyle.display = "none";
	} else {
		elestyle.display = "block";
	}
}

function setVisible(id){
	zariliaGetElementById(id).style.visibility = "visible";
}

function setHidden(id){
	zariliaGetElementById(id).style.visibility = "hidden";
}

function makeBold(id){
	var eleStyle = zariliaGetElementById(id).style;
	if (eleStyle.fontWeight != "bold" && eleStyle.fontWeight != "700") {
		eleStyle.fontWeight = "bold";
	} else {
		eleStyle.fontWeight = "normal";
	}
}

function makeItalic(id){
	var eleStyle = zariliaGetElementById(id).style;
	if (eleStyle.fontStyle != "italic") {
		eleStyle.fontStyle = "italic";
	} else {
		eleStyle.fontStyle = "normal";
	}
}

function makeUnderline(id){
	var eleStyle = zariliaGetElementById(id).style;
	if (eleStyle.textDecoration != "underline") {
		eleStyle.textDecoration = "underline";
	} else {
		eleStyle.textDecoration = "none";
	}
}

function makeLineThrough(id){
	var eleStyle = zariliaGetElementById(id).style;
	if (eleStyle.textDecoration != "line-through") {
		eleStyle.textDecoration = "line-through";
	} else {
		eleStyle.textDecoration = "none";
	}
}

function appendSelectOption(selectMenuId, optionName, optionValue){
	var selectMenu = zariliaGetElementById(selectMenuId);
	var newoption = new Option(optionName, optionValue);
	selectMenu.options[selectMenu.length] = newoption;
	selectMenu.options[selectMenu.length].selected = true;
}

function disableElement(target){
	var targetDom = zariliaGetElementById(target);
	if (targetDom.disabled != true) {
		targetDom.disabled = true;
	} else {
		targetDom.disabled = false;
	}
}
function zariliaCheckAll(formname, switchid) {
	var ele = document.forms[formname].elements;
	var switch_cbox = zariliaGetElementById(switchid);
	for (var i = 0; i < ele.length; i++) {
		var e = ele[i];
		if ( (e.name != switch_cbox.name) && (e.type == 'checkbox') ) {
			e.checked = switch_cbox.checked;
		}
	}
}

function zariliaCheckGroup(formname, switchid, groupid) {
	var ele = document.forms[formname].elements;
	var switch_cbox = zariliaGetElementById(switchid);
	for (var i = 0; i < ele.length; i++) {
		var e = ele[i];
		if ( (e.type == 'checkbox') && (e.id == groupid) ) {
			e.checked = switch_cbox.checked;
			e.click(); e.click();  // Click to activate subgroups
									// Twice so we don't reverse effect
		}
	}
}

function zariliaCheckAllElements(elementIds, switchId) {
	var switch_cbox = zariliaGetElementById(switchId);
	for (var i = 0; i < elementIds.length; i++) {
		var e = zariliaGetElementById(elementIds[i]);
		if ((e.name != switch_cbox.name) && (e.type == 'checkbox')) {
			e.checked = switch_cbox.checked;
		}
	}
}

function zariliaCheckAllElementsButton(button, name) {
	var box, e = 0, grp = button.form[name];
	while (box = grp[e++])
	box.checked = (button.value == 'Check All');
	return (button.value == 'Check All') ? 'Uncheck All' : 'Check All';
}

function zariliaSavePosition(id)
{
	var textareaDom = zariliaGetElementById(id);
	if (textareaDom.createTextRange) {
		textareaDom.caretPos = document.selection.createRange().duplicate();
	}
}

function zariliaInsertText(domobj, text)
{
	if (domobj.createTextRange && domobj.caretPos){
  		var caretPos = domobj.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1)
== ' ' ? text + ' ' : text;
	} else if (domobj.getSelection && domobj.caretPos){
		var caretPos = domobj.caretPos;
		caretPos.text = caretPos.text.charat(caretPos.text.length - 1)
== ' ' ? text + ' ' : text;
	} else {
		domobj.value = domobj.value + text;
  	}
}

function zariliaCodeSmilie(id, smilieCode) {
	var revisedMessage;
	var textareaDom = zariliaGetElementById(id);
	zariliaInsertText(textareaDom, smilieCode);
	textareaDom.focus();
	return;
}

function showImgSelected(imgId, selectId, imgDir, extra, zariliaUrl) {
	var imagewidth = 50;
	if (zariliaUrl == null) {
		zariliaUrl = "./";
	}
	imgDom = zariliaGetElementById(imgId);
	selectDom = zariliaGetElementById(selectId);
	imgDom.src = zariliaUrl + "/"+ imgDir + "/" + selectDom.options[selectDom.selectedIndex].value + extra;
}

function chooseImage(el, imgId, imgDir, zariliaUrl ){
	sel = el.options[el.selectedIndex];
	attributes = '';

	if(!sel.value){
		alert('Please choose an option');
	}
	else{
		sel_split = sel.value.split('|');

	if(sel_split[1]){
		attributes += ' width="' + sel_split[1] + '"'
	}
	if(sel_split[2]){
		attributes += ' height="' + sel_split[2] + '"';
	}
	url = imgDir+"/"+sel_split[0];
	img = '<img src="' + url + '"' + attributes + ' alt="' + sel.innerHTML +'" />';
	}
	document.getElementById(imgId).innerHTML = img
}


function resizeImage (imageOrImageName, width, height) {
  var image = typeof imageOrImageName == 'string' ? document[imageOrImageName] : imageOrImageName;
  if (document.layers) {
    image.currentWidth = width;
    image.currentHeight = height;
    var layerWidth = image.width > width ? image.width : width;
    var layerHeight = image.height > height ? image.height : height;
    if (!image.overLayer) {
      var l = image.overLayer = new Layer(layerWidth);
    }
    var l = image.overLayer;
    l.bgColor = document.bgColor;
    l.clip.width = layerWidth;
    l.clip.height = layerHeight;
    l.left = image.x;
    l.top = image.y;
    var html = '';
    html += '<img src="' + image.src + '"';
    html += image.name ? ' name="overlayer' + image.name + '"' : '';
    html += ' width="' + width + '" height="' + height + '" />';
    l.document.open();
    l.document.write(html);
    l.document.close();
    l.visibility = 'show';
  }
  else {
    image.width = width;
    image.height = height;
  }
}

function zariliaCodeUrl(id, enterUrlPhrase, enterWebsitePhrase){
	if (enterUrlPhrase == null) {
		enterUrlPhrase = "Enter the URL of the link you want to add:";
	}
	var text = prompt(enterUrlPhrase, "");
	var domobj = zariliaGetElementById(id);
	if ( text != null && text != "" ) {
		if (enterWebsitePhrase == null) {
			enterWebsitePhrase = "Enter the web site title:";
		}
		var text2 = prompt(enterWebsitePhrase, "");
		if ( text2 != null ) {
			if ( text2 == "" ) {
				var result = "[url=" + text + "]" + text + "[/url]";
			} else {
				var pos = text2.indexOf(unescape('%00'));
				if(0 < pos){
					text2 = text2.substr(0,pos);
				}
				var result = "[url=" + text + "]" + text2 + "[/url]";
			}
			zariliaInsertText(domobj, result);
		}
	}
	domobj.focus();
}

function zariliaCodeImg(id, enterImgUrlPhrase, enterImgPosPhrase, imgPosRorLPhrase, errorImgPosPhrase){
	if (enterImgUrlPhrase == null) {
		enterImgUrlPhrase = "Enter the URL of the image you want to add:";
	}
	var text = prompt(enterImgUrlPhrase, "");
	var domobj = zariliaGetElementById(id);
	if ( text != null && text != "" ) {
		if (enterImgPosPhrase == null) {
			enterImgPosPhrase = "Now, enter the position of the image.";
		}
		if (imgPosRorLPhrase == null) {
			imgPosRorLPhrase = "'R' or 'r' for right, 'L' or 'l' for left, or leave it blank.";
		}
		if (errorImgPosPhrase == null) {
			errorImgPosPhrase = "ERROR! Enter the position of the image:";
		}
		var text2 = prompt(enterImgPosPhrase + "\n" + imgPosRorLPhrase, "");
		while ( ( text2 != "" ) && ( text2 != "r" ) && ( text2 != "R" ) && ( text2 != "l" ) && ( text2 != "L" ) && ( text2 != null ) ) {
			text2 = prompt(errorImgPosPhrase + "\n" + imgPosRorLPhrase,"");
		}
		if ( text2 == "l" || text2 == "L" ) {
			text2 = " align=left";
		} else if ( text2 == "r" || text2 == "R" ) {
			text2 = " align=right";
		} else {
			text2 = "";
		}
		var result = "[img" + text2 + "]" + text + "[/img]";
		zariliaInsertText(domobj, result);
	}
	domobj.focus();
}

function zariliaCodeEmail(id, enterEmailPhrase){
	if (enterEmailPhrase == null) {
		enterEmailPhrase = "Enter the email address you want to add:";
	}
	var text = prompt(enterEmailPhrase, "");
	var domobj = zariliaGetElementById(id);
	if ( text != null && text != "" ) {
		var result = "[email]" + text + "[/email]";
		zariliaInsertText(domobj, result);
	}
	domobj.focus();
}

function zariliaCodeQuote(id, enterQuotePhrase){
	if (enterQuotePhrase == null) {
		enterQuotePhrase = "Enter the text that you want to be quoted:";
	}
	var text = prompt(enterQuotePhrase, "");
	var domobj = zariliaGetElementById(id);
	if ( text != null && text != "" ) {
		var pos = text.indexOf(unescape('%00'));
		if(0 < pos){
			text = text.substr(0,pos);
		}
		var result = "[quote]" + text + "[/quote]";
		zariliaInsertText(domobj, result);
	}
	domobj.focus();
}

function zariliaCodeCode(id, enterCodePhrase){
	if (enterCodePhrase == null) {
		enterCodePhrase = "Enter the codes that you want to add.";
	}
	var text = prompt(enterCodePhrase, "");
	var domobj = zariliaGetElementById(id);
	if ( text != null && text != "" ) {
		var result = "[code]" + text + "[/code]";
		zariliaInsertText(domobj, result);
	}
	domobj.focus();
}

function zariliaCodeText(id, hiddentext, enterTextboxPhrase){
	var textareaDom = zariliaGetElementById(id);
	var textDom = zariliaGetElementById(id + "Addtext");
	var fontDom = zariliaGetElementById(id + "Font");
	var colorDom = zariliaGetElementById(id + "Color");
	var sizeDom = zariliaGetElementById(id + "Size");
	var zariliaHiddenTextDomStyle = zariliaGetElementById(hiddentext).style;
	var textDomValue = textDom.value;
	var fontDomValue = fontDom.options[fontDom.options.selectedIndex].value;
	var colorDomValue = colorDom.options[colorDom.options.selectedIndex].value;
	var sizeDomValue = sizeDom.options[sizeDom.options.selectedIndex].value;
	if ( textDomValue == "" ) {
		if (enterTextboxPhrase == null) {
			enterTextboxPhrase = "Please input text into the textbox.";
		}
		alert(enterTextboxPhrase);
		textDom.focus();
	} else {
		if ( fontDomValue != "FONT") {
			textDomValue = "[font=" + fontDomValue + "]" + textDomValue + "[/font]";
			fontDom.options[0].selected = true;
		}
		if ( colorDomValue != "COLOR") {
			textDomValue = "[color=" + colorDomValue + "]" + textDomValue + "[/color]";
			colorDom.options[0].selected = true;
		}
		if ( sizeDomValue != "SIZE") {
			textDomValue = "[size=" + sizeDomValue + "]" + textDomValue + "[/size]";
			sizeDom.options[0].selected = true;
		}
		if (zariliaHiddenTextDomStyle.fontWeight == "bold" || zariliaHiddenTextDomStyle.fontWeight == "700") {
			textDomValue = "[b]" + textDomValue + "[/b]";
			zariliaHiddenTextDomStyle.fontWeight = "normal";
		}
		if (zariliaHiddenTextDomStyle.fontStyle == "italic") {
			textDomValue = "[i]" + textDomValue + "[/i]";
			zariliaHiddenTextDomStyle.fontStyle = "normal";
		}
		if (zariliaHiddenTextDomStyle.textDecoration == "underline") {
			textDomValue = "[u]" + textDomValue + "[/u]";
			zariliaHiddenTextDomStyle.textDecoration = "none";
		}
		if (zariliaHiddenTextDomStyle.textDecoration == "line-through") {
			textDomValue = "[d]" + textDomValue + "[/d]";
			zariliaHiddenTextDomStyle.textDecoration = "none";
		}
		zariliaInsertText(textareaDom, textDomValue);
		textDom.value = "";
		zariliaHiddenTextDomStyle.color = "#000000";
		zariliaHiddenTextDomStyle.fontFamily = "";
		zariliaHiddenTextDomStyle.fontSize = "12px";
		zariliaHiddenTextDomStyle.visibility = "hidden";
		textareaDom.focus();
	}
}

function zariliaValidate(subjectId, textareaId, submitId, plzCompletePhrase, msgTooLongPhrase, allowedCharPhrase, currCharPhrase) {
	var maxchars = 65535;
	var subjectDom = zariliaGetElementById(subjectId);
	var textareaDom = zariliaGetElementById(textareaId);
	var submitDom = zariliaGetElementById(submitId);
	if (textareaDom.value == "" || subjectDom.value == "") {
		if (plzCompletePhrase == null) {
			plzCompletePhrase = "Please complete the subject and message fields.";
		}
		alert(plzCompletePhrase);
		return false;
	}
	if (maxchars != 0) {
		if (textareaDom.value.length > maxchars) {
			if (msgTooLongPhrase == null) {
				msgTooLongPhrase = "Your message is too long.";
			}
			if (allowedCharPhrase == null) {
				allowedCharPhrase = "Allowed max chars length: ";
			}
			if (currCharPhrase == null) {
				currCharPhrase = "Current chars length: ";
			}
			alert(msgTooLongPhrase + "\n\n" + allowedCharPhrase + maxchars + "\n" + currCharPhrase + textareaDom.value.length + "");
			textareaDom.focus();
			return false;
		} else {
			submitDom.disabled = true;
			return true;
		}
	} else {
		submitDom.disabled = true;
		return true;
	}
}

function setCookie(c_name,value,expiredays) {
	var exdate=new Date();
	exdate.setDate(expiredays);
	document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate);
}

function getCookie(c_name) {
	if (document.cookie.length>0)  {
	  c_start=document.cookie.indexOf(c_name + "=");
	  if (c_start!=-1)    {
		c_start=c_start + c_name.length+1 ;
	    c_end=document.cookie.indexOf(";",c_start);
		if (c_end==-1) c_end=document.cookie.length;
		return unescape(document.cookie.substring(c_start,c_end));
	  }
	}
	return null;
}

function cpUpdateValue(mvalue) {
	var obj = zariliaGetElementById('findnext');
		if (obj) {
			obj.elements['start'].value=mvalue;
				obj.submit();
		}
}

/* start popup window javascript */
if (!document.layers&&!document.all&&!document.getElementById('tooltip')) {
  event="test";
}

function showtip(current,e,text) {
  if (document.all) {
    thetitle=text.split('<BR>');

    if (thetitle.length>1) {
       thetitles='';

       for (i=0;i<thetitle.length;i++) {
         thetitles+=thetitle[i];
       }
       current.title=thetitles;
    } else {
      current.title=text;
    }

  } else if (document.layers){
      document.tooltip.document.write('<layer bgColor="white" style="border:1px solid black;font-size:12px;">'+text+'</layer>');
      document.tooltip.document.close();
      document.tooltip.left=e.pageX+5;
      document.tooltip.top=e.pageY+5;
      document.tooltip.visibility="show";

  } else if (toolTipElement = document.getElementById('tooltip')) {
      toolTipElement.innerHTML = '<DIV CLASS="normal" STYLE="border:1px solid black;font-size:11px; background-color: #FFFFFF;"> &nbsp; ' + text + ' &nbsp; </DIV>';
      toolTipElement.style.left = e.pageX+10;
      toolTipElement.style.top = e.pageY+5;

      toolTipElement.style.visibility = "visible";
  }

}

function hidetip(){
  if (document.layers) {
   document.tooltip.visibility="hidden";
  } else if (toolTipElement = document.getElementById('tooltip')) {
      toolTipElement.style.visibility = "hidden";
  }
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
/* end popup window javascript */

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}