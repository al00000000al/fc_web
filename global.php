<?php
require_once 'config.php';
require_once 'include.php';
require_once 'lib/db.class.php';
require_once 'lib/engine.lib.php';
require_once 'lib/global_hash.lib.php';




global $Input;
global $DB;
global $lang_pack;
global $Static;
global $staticStyles;
global $staticJs;
global $lang_keys;
global $secret;

setCustomGlobalHashSecret($secret);


$Input = array_merge($_POST, $_GET);
if(!isset($Input['act'])){
	$Input['act'] = '';
}

$lang_pack = isset($_COOKIE['lang']) ? (int) $_COOKIE['lang'] : setLang();

try{
$DB = new DB($host,$db_name,$username, $password );
} catch (Exception $e){
	wrapError();
}

