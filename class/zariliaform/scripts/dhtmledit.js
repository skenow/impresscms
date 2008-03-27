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