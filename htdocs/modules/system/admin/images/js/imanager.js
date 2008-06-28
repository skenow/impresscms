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