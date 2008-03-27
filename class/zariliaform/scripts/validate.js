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