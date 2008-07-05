window.moveTo(screen.availWidth/2-492,screen.availHeight/2-235);

function showDiv(type,id){
	divs = document.getElementsByTagName('div');
	for (i=0; i<divs.length;i++){
		if (/opt_divs/.test(divs[i].className)){
			divs[i].style.display = 'none';
		}
	}
	if (!id)id = '';
	document.getElementById(type+id).style.display = 'block';
}

function overpanel(id,value){
	panel = document.getElementById(id+'overpanel');
	if (value == 1){
		panel.style.display = 'none';
	}else{
		panel.style.display = 'block';
	}
}

function actField(value,id){
	var field = document.getElementById(id);
	if (value == 'file'){
		field.disabled = false;
	}else{
		field.value = '';
		field.disabled = true;
	}
}

function addItem(itemurl, name, target, cat, url) {
	var win = opener;
	var campo = win.document.getElementById(target);
	var opcoes = win.document.getElementById('img_cat_'+cat);
	var imagem = win.document.getElementById(target+'_img');
	if(opcoes){
		for(x=0; x<campo.options.length; x++){
			if(campo.options[x].value == itemurl){
				campo.options[x].selected = true;
				imagem.src = url+itemurl;
				var found = true;
			}
		}
		if(!found){
			var newOption = win.document.createElement("option");
			opcoes.appendChild(newOption);
			newOption.text = name;
			newOption.value = itemurl;
			newOption.selected = true;
			imagem.src = url+itemurl;
		}
	}
	window.close();
	return;
}

function appendCode(addCode,target) {
	var targetDom = window.opener.xoopsGetElementById(target);
	if (targetDom.createTextRange && targetDom.caretPos){
  		var caretPos = targetDom.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) 
== ' ' ? addCode + ' ' : addCode;  
	} else if (targetDom.getSelection && targetDom.caretPos){
		var caretPos = targetDom.caretPos;
		caretPos.text = caretPos.text.charat(caretPos.text.length - 1)  
== ' ' ? addCode + ' ' : addCode;
	} else {
		targetDom.value = targetDom.value + addCode;
  	}
	window.close();
	return;
}