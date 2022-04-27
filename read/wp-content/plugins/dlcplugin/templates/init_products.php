<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK', 1);
require_once( '../admin/setup.php' );

$pid = isset($_GET['pid']) ? $_GET['pid'] :null;
global $dlcuser;

$dlcuser['pid'] = $pid;
$dlcuser['product_name'] = getPName($pid);
?>
