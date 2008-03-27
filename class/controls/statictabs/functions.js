function staticTabsSelect(name, tab) {
	var obj = document.getElementById(name +'_content');
	var cname = name + '_tab' + tab;
	var code = 'var rez = ' + cname;
	eval(code);
	rez = rez.substr(2, rez.length);
	obj.innerHTML = unescape(rez);
	xajax_ZariliaControlHandler(name, 'StaticTabs', 'ZariliaControl_StaticTabs_Handler', false, tab);
}