<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 22.01.2019
 * Time: 0:34
 */


require_once('include.php');
require_once('lib/engine.lib.php');

global $subsubdir;
global $subdir;
global $Input;

$subsubdir = isset($Input["subsubdir"]) ? $Input["subsubdir"] : '';
$subdir = isset($Input["subdir"]) ? $Input["subdir"] : 'fc';


$files = [
    'fc' => 'facecat.php',
    'facecat' => 'facecat.php',
];

if(array_key_exists( $subdir, $files))
    require_once($files[$subdir]);
else{
    wrap404();
    exit();
}