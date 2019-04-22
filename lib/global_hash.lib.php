<?php
/* Я скачал это с гитхаба */
/**
 *
 * @var string|null $__custom_secret
 */

$__custom_secret = null;

/**
 * Change to custom secret
 *
 * @param string|null $secret
 */

function setCustomGlobalHashSecret ($secret) {
    global $__custom_secret;
    
    $__custom_secret = $secret;
}

/**
 * Get the ugly md5 hash
 *
 * @param $string
 * @return string
 */

function decodeGlobalHash ($string) {
    $c = '';
//echo 'string '.$string."\n";
//echo '1) substr($string,strlen($string)-5) '.substr($string,strlen($string)-5)."\n";
//echo '2) substr($string,4,strlen($string)-12) '.substr($string,4,strlen($string)-12)."\n";
    $b = substr($string,strlen($string)-5).substr($string,4,strlen($string)-12);
//echo 'b '.$b."\n";
    for ($d=0;$d<strlen($b);++$d) {
        $c.=$b{(strlen($b)-$d-1)}; // спи****жено, строго не судите, пожайлуйста
    }

    return $c;
}

/**
 * Get hash from http request
 *
 * @param $hash
 * @return string
 */

function getGlobalHash ($hash) {
    if (empty($hash)) {
        $hash = isset($_REQUEST['hash']) ? strval($_REQUEST['hash']) : '';
    }

    return $hash;
}

/**
 * Perform a hash by params
 *
 * @param mixed ...$params
 * @return string
 */

function globalHash (...$params) {
    global $__custom_secret;
    
    $secret = getenv('GLOBAL_HASH_SECRET');
    
    if ($__custom_secret !== null) {
        $secret = $__custom_secret;
    }

    if ($secret === FALSE) {
        $secret = '';

        trigger_error("globalHash secret not set in env. Example: putenv('GLOBAL_HASH_SECRET=secret'), or other your environment variables handler.", E_USER_WARNING);
    }

    array_push($params, $secret);

    $params = array_map('strval', $params);

    $hash_string = implode('_', $params);

    return decodeGlobalHash(decodeGlobalHash(md5($hash_string)));
}

/**
 * Check a hash from var/http request with params
 *
 * @param $hash
 * @param mixed ...$params
 * @return bool
 */

function checkGlobalHash ($hash, ...$params) {
    $hash = getGlobalHash($hash);

    return hash_equals(globalHash (...$params), $hash);
}

/**
 * Perform a hash by params && timestamp
 *
 * @param mixed ...$params
 * @return string
 */

function globalTimeHash (...$params) {
    $ts = time();

    array_push($params, $ts);

    return $ts . '_' . globalHash (...$params);
}

function setHashTimeout ($sec = 0) {
    putenv('GLOBAL_HASH_LIFETIME=' . $sec);
}

function hashTimeout () {
    $hash_lifetime = (int) ini_get('global_hash_lifetime');
    
    if (empty($hash_lifetime)) {
        $hash_lifetime = (int) getenv('GLOBAL_HASH_LIFETIME');
    }

    if ($hash_lifetime < 1) {
        $hash_lifetime = 25200;
    }
    
    return $hash_lifetime;
}

/**
 * Check a hash && timestamp from var/http request with params. Default hash time life: 7 hours.
 * Can be changed with php.ini variable. Example: global_hash_lifetime=86400 # 1 hour
 *
 * @param $hash
 * @param mixed ...$params
 * @return bool
 */

function checkGlobalTimeHash ($hash, ...$params) {
    $hash = getGlobalHash($hash);

    $hash_params = explode('_', $hash);

    if (count($hash_params) < 2) {
        return false;
    }

    $hash_lifetime = hashTimeout ();

    $hash_ts = (int) $hash_params[0];

    $max_ts = $hash_ts + $hash_lifetime;

    if (time() > $max_ts) {
        return false; // expired hash
    }

    array_push($params, $hash_ts);

    return checkGlobalHash ($hash_params[1], ...$params);
}
