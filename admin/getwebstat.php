<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

if(!defined('INCLUDE_CHECK')) {
	define('INCLUDE_CHECK',1);
	require('setup.php');
}

$isadmin = (strpos($_SERVER['PHP_SELF'], 'admin') !== false);
if(!$isadmin && !getStatus('999') && !isset($_SESSION['maintenance'])) {
	reloadTo(DLC_ROOT. '/errors/maintenance.php');exit;
}

$_SESSION['cart_on'] = ( isset($_SESSION['cart_on'] ) ? $_SESSION['cart_on'] : getStatus('998') );

?>