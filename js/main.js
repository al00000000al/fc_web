
function showBox(title, html, js='',id='modal_wrap'){
	var modalhtml = 
`<div class="modal__wrap" id="modal_wrap">
<div id="`+id+`1" class="modal">
  <div class="modal__content">
    <div class="modal__header">
<span class="modal__close" onclick="closeBox(\'modal_wrap\');">&times;</span>`
	+ '<h2 class="modal__header--h2">'+title+'</h2>'+
`    </div>
    <div class="modal__body">`
		+ html+
 `   </div>
</div></div>`;

var modal = document.getElementById(id);
if(modal == null){
	$( ".page__main" ).append( modalhtml);
	eval(js);
}  else {
    $( ".modal__header--h2" ).html(title);
    $( ".modal__body" ).html(html);
}
	
	var x = document.getElementById(id);
	x.style.display = "block";

}

function closeBox(id){
	var modal = document.getElementById(id);
	if(modal != null){
	modal.style.display = "none";
}
}

var Ajax = {
post : function(url, params, callback, type = 'POST'){
  var xhr = new XMLHttpRequest();

  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4){
		var respJson = JSON.parse(xhr.response);
           callback(respJson);
    }
  };

  xhr.open(type, url+serialize(params), true);
  xhr.setRequestHeader("X-REQUESTED-WITH",'xmlhttprequest');
  xhr.send();
}
};

function serialize( obj ) {
  return '?'+Object.keys(obj).reduce(function(a,k){a.push(k+'='+encodeURIComponent(obj[k]));return a},[]).join('&')
}

function getScrollXY() {
var scrOfX = 0, scrOfY = 0;
if( typeof( window.pageYOffset ) == 'number' ) {
    //Netscape compliant
    scrOfY = window.pageYOffset;
    scrOfX = window.pageXOffset;
} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
    //DOM compliant
    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
    //IE6 standards compliant mode
    scrOfY = document.documentElement.scrollTop;
    scrOfX = document.documentElement.scrollLeft;
}
return [ scrOfX, scrOfY ];
}

function getDocHeight() {
var D = document;
return Math.max(
    D.body.scrollHeight, D.documentElement.scrollHeight,
    D.body.offsetHeight, D.documentElement.offsetHeight,
    D.body.clientHeight, D.documentElement.clientHeight
);
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}


function go(url)
{
   document.location.href = url;
}

function changeLang(lang_id){
	Ajax.post('lang.php',{act:'change_lang', lang_id:lang_id},function(data){
		if(data.success === true){
			window.location.href = '/';
		}
	});
}

function toTimestamp(d){
    dArr = d.split('.'),
        ts = new Date(dArr[1] + "-" + dArr[0] + "-" + dArr[2]).getTime();
    return ts/1000;
}



//функция ParseData
function ParseData(timestamp) {
    var date =  new  Date();
    date.setTime(timestamp*1000);
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    var day = date.getDate();
    var month = date.getMonth() + 1;
    var year = date.getFullYear();
    hours = hours < 10?"0"+hours:hours;
    minutes = minutes < 10?"0"+minutes:minutes;
    seconds = seconds < 10?"0"+seconds:seconds;
    day = day < 10?"0"+day:day;
    month = month < 10?"0"+month:month;
    return day + "." + month + "." + year + " " + hours + ":" + minutes;
}

function showMenu(){
    if($('.menu_nav').children().eq(1).css('display') === 'none') {
        $('.menu_nav').children().css('display', 'flex');
    }else{
        $('.menu_nav').children().css('display', 'none');
        $('.menu_nav').children().eq(0).css('display', 'flex');

    }
}

function addLangkeys(keys){
    lang = keys;
}
$(document).ready(function() {
    if($('.menu_nav').children().eq(1).css('display') === 'none'){
        $('.menu_nav').children().eq(0).html('<a href="#" class="menu_arr" onclick="showMenu(); return false;"><b>TRAINZland.ru ▼</b></a>');
}});


function autosize(){
    var el = this;
    setTimeout(function(){
        el.style.cssText = 'height:auto; padding:0';
        // for box-sizing other than "content-box" use:
        // el.style.cssText = '-moz-box-sizing:content-box';
        el.style.cssText = 'height:' + el.scrollHeight + 'px';
    },0);
}
