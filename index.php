<?php
 require_once 'include.php';
 require_once 'lib/engine.lib.php';

 global $Input;
 
  try{
	callAct('index',$Input['act']);	
 } catch (Exception $e){
	wrapPage('error',$e);
 }
 
 // вывод главной страницы
 function a_index_(){

	 wrapRedirect('/fc');
 }
