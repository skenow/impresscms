function showDiv(type,id){
	divs = document.getElementsByTagName('div');
	for (i=0; i<divs.length;i++){
		if (/opt_divs/.test(divs[i].className)){
			divs[i].style.display = 'none';
		}
	}
	document.getElementById(type+id).style.display = 'block';
}
function selFilter(id,filter){
	darg1 = document.getElementById(id+'_arg1');
	targ1 = document.getElementById(id+'_targ1');
	iarg1 = document.getElementById(id+'arg1');
	larg1 = document.getElementById(id+'_larg1');
	darg2 = document.getElementById(id+'_arg2');
	targ2 = document.getElementById(id+'_targ2');
	iarg2 = document.getElementById(id+'arg2');
	larg2 = document.getElementById(id+'_larg2');
	darg3 = document.getElementById(id+'_arg3');
	targ3 = document.getElementById(id+'_targ3');
	iarg3 = document.getElementById(id+'arg3');
	larg3 = document.getElementById(id+'_larg3');
	iarg1.value = '';
	iarg2.value = '';
	iarg3.value = '';
	if (filter == 'IMG_FILTER_BRIGHTNESS' || filter == 'IMG_FILTER_CONTRAST'){
		darg1.style.display = 'block';
		targ1.innerHTML = '<b>Level</b>';
		larg1.innerHTML = '<b>Values between -255 to 255 (0 = no change)</b>';
		iarg1.value = 0;
		
		darg2.style.display = 'none';
		targ2.innerHTML = '<b></b>';
		larg2.innerHTML = '<b></b>';
		iarg2.value = '';
		
		darg3.style.display = 'none';
		targ3.innerHTML = '<b></b>';
		larg3.innerHTML = '<b></b>';
		iarg3.value = '';
	}else if(filter == 'IMG_FILTER_COLORIZE'){
		darg1.style.display = 'block';
		targ1.innerHTML = '<b>Red</b>';
		larg1.innerHTML = '<b>Values between -255 to 255</b>';
		iarg1.value = 0;
		
		darg2.style.display = 'block';
		targ2.innerHTML = '<b>Green</b>';
		larg2.innerHTML = '<b>Values between -255 to 255</b>';
		iarg2.value = 0;
		
		darg3.style.display = 'block';
		targ3.innerHTML = '<b>Blue</b>';
		larg3.innerHTML = '<b>Values between -255 to 255</b>';
		iarg3.value = 0;
	}else if(filter == 'IMG_FILTER_SMOOTH'){
		darg1.style.display = 'block';
		targ1.innerHTML = '<b>Level</b>';
		larg1.innerHTML = '<b>any float value (in practice: 2048 or more) = no change</b>';
		iarg1.value = 0;
		
		darg2.style.display = 'none';
		targ2.innerHTML = '<b></b>';
		larg2.innerHTML = '<b></b>';
		iarg2.value = '';
		
		darg3.style.display = 'none';
		targ3.innerHTML = '<b></b>';
		larg3.innerHTML = '<b></b>';
		iarg3.value = '';
	}else{
		darg1.style.display = 'none';
		targ1.innerHTML = '<b></b>';
		larg1.innerHTML = '<b></b>';
		iarg1.value = '';
		
		darg2.style.display = 'none';
		targ2.innerHTML = '<b></b>';
		larg2.innerHTML = '<b></b>';
		iarg2.value = '';
		
		darg3.style.display = 'none';
		targ3.innerHTML = '<b></b>';
		larg3.innerHTML = '<b></b>';
		iarg3.value = '';
	}
}
function filter_preview(id,src,width,height){
	form = document.getElementById('filter_form'+id);
	params = '';
	for (i=0; i<form.elements.length; i++){
		if (form.elements[i].id == 'filter'){
			params += 'filter='+form.elements[i].value.toString();
		}
		if (form.elements[i].id == id+'arg1' && form.elements[i].value != ''){
			params += '&arg1='+form.elements[i].value;
		}
		if (form.elements[i].id == id+'arg2' && form.elements[i].value != ''){
			params += '&arg2='+form.elements[i].value;
		}
		if (form.elements[i].id == id+'arg3' && form.elements[i].value != ''){
			params += '&arg3='+form.elements[i].value;
		}
	}
	var w = window.open(XOOPS_URL+'/modules/system/admin/images/filter_preview.php?img='+src+'&'+params+'&root='+XOOPS_ROOT_PATH,'imagePreview','width='+width+',height='+height+',resizable=yes');
}
function overpanel(id,value){
	panel = document.getElementById(id+'overpanel');
	if (value == 1){
		panel.style.display = 'none';
	}else{
		panel.style.display = 'block';
	}
}