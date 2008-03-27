<script language="JavaScript1.2">
// global variables
var gtdiff;         // diff between server and local time
var counttime=60;    // number of minutes to count down
//var counttime=2;    // number of minutes to count down
var onesec=1000; // milliseconds
var oneminute=60*onesec;
var gservertime = co_get_servertime();
var gfuturetime;

var countdownwidth='156px';
var countdownheight='30px';
var countdownbgcolor='lightblue';
var opentags='<font face="Verdana" size="1" color="white">';
var closetags='</font>';

var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
var crosscount='';

// general function
function co_pad ( num ) { return ( ( num > 9 ) ? num : "0" + num ); }

var debugCounter = 0;
function co_debugMsg(msg) {
  // un rem the next line when you go live to prevent debug messages from showing
  return;
  debugCounter++;
  var t = document.createTextNode("\n" + co_pad(debugCounter) + ": " + msg);
  var br = document.createElement("br");
  var debug = document.getElementById("debug");
  debug.appendChild(t);
  debug.appendChild(br);
}

function co_get_servertime() {
  var stime = "October 12, 1988 23:59:55"; // Date format
  stime = new Date() // get date from server
  return new Date(stime);
}

function co_setfuturetimestamp(idate,tnum) {
  var tyy, tmm, tdd, thh, tmi=0;
  var rdate, rsec;
  var ttime = idate.getTime()+(tnum*oneminute);
  rdate = new Date(ttime);
//  tyy = rdate.getYear();
  tyy = rdate.getFullYear();
  tmm = rdate.getMonth();
  tdd = rdate.getDate();
  thh = rdate.getHours();
  if (thh%2 != 0) { thh = thh-1;} // Not even adj to even hrs count down
  if (tnum < 60) { tmi = rdate.getMinutes(); }
  return new Date(tyy,tmm,tdd,thh,tmi,0); // set new time w/ 0 sec
}

function co_redirect_refresh_page() {
  window.location="http://testing.mmwebsites.com";
}

function co_refresh_alert() {
  alert("The site is being refreshed");
}

function co_get_futuretime(tnum) {
  var curtime = new Date();
  gtdiff=Date.parse(gservertime)-Date.parse(curtime);
  return co_setfuturetimestamp(gservertime,tnum);
}

function co_seconds_elapsed (istarttime) {
  curtime = new Date();
  var time_diff=Date.parse(istarttime)-(Date.parse(curtime)+gtdiff); // adj to get server time
  return Math.floor ( (time_diff) / onesec ); // sec elapsed
}
// general function

function co_dmsg(ehour,emin,esec) {
//  var msg1 = opentags + "Site refresh in " + co_pad(ehour) + ":" + co_pad(emin) + ":" + co_pad(esec);
  var msg1 = opentags + "Count Down: " + co_pad(ehour) + ":" + co_pad(emin) + ":" + co_pad(esec);
  if(ehour<=0 && emin<=0 && esec<=1){
//    window.location.reload();  this is like DOS and put high load on server, BAD!!!
    gservertime=gfuturetime;
    gfuturetime=co_get_futuretime(counttime);
    co_debugMsg("server "+gservertime);
    co_debugMsg("future "+gfuturetime);
    co_redirect_refresh_page();
//    co_refresh_alert();
  }
  else{
    if (document.layers){
      co_debugMsg("else "+gservertime);
      document.countdownnsmain.document.countdownnssub.document.write(msg1);
      document.countdownnsmain.document.countdownnssub.document.close();
    }
    else if (document.all||document.getElementById){
      crosscount.innerHTML = msg1;
    }
  }
}

// THIS FUNCTION TAKES THE SECONDS ELAPSED AND CONVERTS THEM FOR OUTPUT
function co_countdown(){
  setTimeout("co_countdown()",onesec); // set timer delay
  var secs = co_seconds_elapsed (gfuturetime);   // TAKE THE SECONDS ELAPSED
  var mins = Math.floor ( secs / 60 );   // CONVERT SECONDS TO MINUTES AND SECONDS
  secs = secs - (mins * 60);
  var hour = Math.floor ( mins / 60 );   // CONVERT MINUTES TO HOURS AND MINUTES
  mins = mins - (hour * 60);
  co_dmsg(hour,mins,secs);
}

function co_start_countdown(){
  if (document.layers) document.countdownnsmain.visibility="show";
  else if (document.all||document.getElementById)
    crosscount = document.getElementById&&!document.all ? document.getElementById("countdownie") : countdownie;
  co_countdown();
}

<!-- main -->
if (document.all||document.getElementById)
  document.write('<span id="countdownie" style="width:'+countdownwidth+'; background-color:'+countdownbgcolor+'"></span>');

gfuturetime=co_get_futuretime(counttime);
window.onload=co_start_countdown;
</script>
<span><br></span>
<ilayer width=&{countdownwidth}; height=&{countdownheight}; bgColor=&{countdownbgcolor}; visibility=hide>
<layer width=&{countdownwidth}; height=&{countdownheight}; left=0 top=0></layer>
</ilayer>