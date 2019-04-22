<?php
require_once 'template.class.php';

function wrapPage( $title = '', $html = '', $js = '', $options = array() ){
	require_once 'ui.lib.php';
	global $staticStyles;
	global $staticJs;
	global $lang_pack;
	global $lang;
	global $lang_keys;
	
	$lang_code = 'en';
	
	if( !isset($lang_pack) )
		$lang_pack = 0;
	
	if( $lang_pack === 0 )
		$lang_code = 'ru';
	
	addStatic('common.css');
	
	

	$meta_descr = isset($options['descr']) ? '<meta name="description" content="'.$options['descr'].'">' : '';
	$meta_keys = isset($options['keywords']) ? '<meta name="keywords" content="'.$options['keywords'].'">' : '';
	$meta = $meta_keys ."\n  ". $meta_descr;
	
        $tname = 'facecat';
	

	$layout = new Text_Template($tname);
	$layout->setVar([
	'lang_code' => $lang_code,
	'title' => $title,
	'body' => $html,
	'meta_keys' => $meta,
	'styles' => $staticStyles.$staticJs,
	'js' => $js.$lang_keys,
	]);
	print $layout->render();
	die();
}

/**
 * @param string $title
 * @param string $html
 * @param string $js
 * @param array $options
 */
function wrapBox( $title = '', $html = '', $js = '', $options = array() ){
	if(isAjax() !== true)
		wrapRedirect();
	
	return wrapArray(array($title, $html, $js, $options) );
}

/**
 * @param string $loc
 */
function wrapRedirect($loc = 'index.php'){
	redirectTo($loc);
}

/**
 * @param array $arr
 */
function wrapArray($arr = array()){
	header('Content-Type: application/json; charset=utf-8');
	print json_encode($arr/*, JSON_UNESCAPED_UNICODE */);
	die();
}

/**
 *
 */
function wrapError(){
    header('HTTP/1.0 503 Internal Server Error');
	include('50x.html');
	die();
}


/**
 *
 */
function wrap404(){
    header('HTTP/1.0 404 Not Found');
    include('err404.html');
    die();
}

/**
 * @param $file_name
 * @param string $act
 */
function callAct($file_name, $act = ''){
	$function_action  =  "a_{$file_name}_{$act}";
	if (function_exists($function_action)) {
		$function_action();
	} else {
		$function_default =  "a_{$file_name}_";
		if (function_exists($function_default)) {
			$function_default();
		}
	}
}

/**
 * @param mixed ...$static
 */
function addStatic(...$static) {
	global $staticStyles;
	global $staticJs;
	
	foreach($static as $c) {
		$s_ext = explode('.', $c)[1];
		if($s_ext==="css") {
			if( stripos($c, 'http')=== false)
				$c = '/css/'.$c;
			$staticStyles.=
<<<html
<link type="text/css" rel="stylesheet" href="{$c}?6"></link>
html;
		} else {
			if( stripos($c, 'http')=== false)
				$c = '/js/'.$c;
			$staticJs.=
<<<html
<script type="text/javascript" src="{$c}?5"></script>
html;
		}
	}
}

/**
 * @return int
 */
function id(){
    return isset($_SESSION['user']) ? $_SESSION['user']['id'] : 0;
}

/**
 * @param null $location
 */
function redirectTo($location = NULL) {
	if($location != NULL) {
		header("Location: {$location}");
		exit;
	}
}

/**
 * @return bool
 */
function isAjax(){
	return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
	&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
	? true : false;
}

/**
 * @param $date_str
 * @param string $point
 * @param int $lang_forms
 * @return string
 */
function time_format($date_str, $point = '.', $lang_forms = 1){
	global $lang;
	
    $time = time();
    $tm = date('H:i', $date_str);
    $d = date('d', $date_str);
    $m = date('m', $date_str);
    $y = date('Y', $date_str);
    if($lang_forms !== 1)
        return "{$y}{$point}{$m}{$point}{$d}";
	if($d.$m.$y == date('dmY',$time)) return "{$lang['today']} {$tm}";
    elseif($d.$m.$y == date('dmY', strtotime('-1 day'))) return "{$lang['yesterday']} {$tm}";
    elseif($y == date('Y',$time)) return "{$d}{$point}{$m}{$point}{$y} {$tm}";
    else return "{$d}{$point}{$m}{$point}{$y}";
}

/**
 * @param $link
 * @param string $innerHtml
 * @param string $class
 * @param string $id
 * @return string
 */
function sHref($link, $innerHtml = 'Link', $class = '', $id = ''){
	if($class !== ''){
		$class = <<<HTML
 class="{$class}"
HTML;
	}
	if($id !== ''){
		$id = <<<HTML
 id="{$id}"
HTML;
	}
	return <<<HTML
<a href="{$link}"{$id}{$class}>{$innerHtml}</a>
HTML;
}


/**
 * @return bool
 */
function checkCaptcha(){
	global $Input;
	global $recaptcha_secret;
	
$response = $Input["g-recaptcha-response"];
  $url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = [
    'secret' => $recaptcha_secret,
    'response' => $response
  ];
  $options = [
    'http' => [
	  'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                   // "Content-Length: ".strlen($data)."\r\n".
                    "User-Agent:MyAgent/1.0\r\n",
      'method' => 'POST',
      'content' => http_build_query($data)
    ]
  ];
  $context  = stream_context_create($options);
  $verify = file_get_contents($url, false, $context);
  $captcha_success=json_decode($verify);
  if ($captcha_success->success==false) {
    return false;
  } else if ($captcha_success->success==true) {
            return true;
  }
}

/**
 * @param $text
 * @return string|string[]|null
 */
function nl2p($text){
 return preg_replace('/[^\r\n]+/', "<p>$0</p>", $text);  
}

/**
 * @param int $lang_id
 * @return int
 */
function setLang($lang_id = -1){
	$country_code = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
	? strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) :'ru';
	
	if($lang_id == -1){
	    switch ($country_code){
            case "ru":
            case "be":
            case "uk":
                $lang_id = 0;
                break;
            case "cn":
                $lang_id = 2;
                break;
            default:
                $lang_id = 1;
                break;
        }
        setcookie('lang',$lang_id,strtotime( '+365 days' ));
        return $lang_id;
	} else {
		setcookie('lang', intval($lang_id), strtotime( '+365 days' ));
	}
}




/**
 * @param $hash
 * @param array $arr
 * @return bool
 */
function csrfCheck($hash, $arr=array()){

	if(trim($hash) == trim(globalHash(...$arr))){
		return true;
	} else
		return false;
}

/**
 * @param string $html
 * @param string $title
 * @param string $page
 * @param string $class
 * @return string
 */
function sPage($html = '', $title = '', $page = 'index', $class = 'main'){
return <<<HTML
    <section class="{$page} {$page}_{$class}">
    <h1 class="{$page}__header {$page}_{$class}__header">{$title}</h1>
    {$html}
</section>
HTML;
}

/**
 * @param $array
 * @param $file
 */
function write_php_ini($array, $file){
    $res = array();
    foreach($array as $key => $val)
    {
        if(is_array($val))
        {
            $res[] = "[$key]";
            foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
        }
        else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
    }
    safefilerewrite($file, implode("\r\n", $res));
}

/**
 * @param $fileName
 * @param $dataToSave
 */
function safefilerewrite($fileName, $dataToSave){
 if ($fp = fopen($fileName, 'w'))
    {
        $startTime = microtime(TRUE);
        do
        {            $canWrite = flock($fp, LOCK_EX);
           // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
           if(!$canWrite) usleep(round(rand(0, 100)*1000));
        } while ((!$canWrite)and((microtime(TRUE)-$startTime) < 5));

        //file was locked so now we can store information
        if ($canWrite)
        {            fwrite($fp, $dataToSave);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

}

/**
 * @param integer $n number
 * @param array $forms array('секунда', 'секунды', 'секунд' )
 * @return string
 */
function plural_form($n, $forms) {
    return $n%10==1&&$n%100!=11?$forms[0]:($n%10>=2&&$n%10<=4&&($n%100<10||$n%100>=20)?$forms[1]:$forms[2]);
}

function issetInput(...$input){
    global $Input;

    foreach($input as $i){
        if(!isset($Input[$i])){
            wrapRedirect('/');
        }
    }
}

function addLangKeys($_lang_keys = array()){
    global $lang_keys;
   $keys = json_encode($_lang_keys, JSON_UNESCAPED_UNICODE);
   return $lang_keys .= "addLangkeys({$keys});";
}

// Транслитерация строк.
function transliterate($st) {
    $st = strtr($st,
        "абвгдежзийклмнопрстуфыэАБВГДЕЖЗИЙКЛМНОПРСТУФЫЭ",
        "abvgdegziyklmnoprstufieABVGDEGZIYKLMNOPRSTUFIE"
    );
    $st = strtr($st, array(
        'ё'=>"yo",    'х'=>"h",  'ц'=>"ts",  'ч'=>"ch", 'ш'=>"sh",
        'щ'=>"shch",  'ъ'=>'',   'ь'=>'',    'ю'=>"yu", 'я'=>"ya",
        'Ё'=>"Yo",    'Х'=>"H",  'Ц'=>"Ts",  'Ч'=>"Ch", 'Ш'=>"Sh",
        'Щ'=>"Shch",  'Ъ'=>'',   'Ь'=>'',    'Ю'=>"Yu", 'Я'=>"Ya",
    ));
    return $st;
}

//File Size Conversion
function convert_filesize($bytes, $decimals = 2){
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}