<?
require_once 'include.php';
require_once 'lib/engine.lib.php';

global $lang;
global $lang_pack;

$lang = switchLang($lang_pack) + switchLang(0);

 try{
	callAct('lang',$Input['act']);	
 } catch (Exception $e){
	wrapPage('error',$e);
 }
 
function a_lang_(){
	
}
 
function a_lang_change_lang(){
	global $Input;
	
	if(isset($Input['lang_id'])){
		$lang_id = intval($Input['lang_id']);
		setLang($lang_id);
		wrapArray(array("success"=>true));
	} else {
		wrapArray(array("error"=>''));
	}
}


function a_lang_a_change_lang(){
    global $Input;

    if(isset($Input['lang_id'])){
        $lang_id = intval($Input['lang_id']);
        setLang($lang_id);
        if(isset($Input['to'])){
            wrapRedirect($Input['to']);
        }
       wrapRedirect('blog');
    } else {
        wrapRedirect('blog');
    }
}

function switchLang($lang_id){
	global $lang_pack;
	$file_lang = "lang/{$lang_id}.ini";
	if(file_exists($file_lang)){
		return parse_ini_file($file_lang);
	}
	else{
		$lang_pack = 0;
		setLang(0);
		$file_default =  "lang/0.ini";
		return parse_ini_file($file_default);
	}
}





