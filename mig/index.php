<?php
if ( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');
require('func.php');

$_SESSION['mig_last'] = DLC_MGRT;
$title   = 'MIG | '.DLC_MIG;

unset($_SESSION['post']);

if ( !ISIN_MIG ) {
	$_SESSION['catch_login'] = 'mig';
	echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=/login">';exit;
}

$y  = '';
ob_start();
include('head.php');
$y .= '<ul class="home">';
$y .= IS_MIG?'<li><a href="admin">ADMIN</a></li>':'';
$y .= !IS_MIG?'<li><a href="distri">DISTRIBUTORS</a></li>':'';
$y .= !IS_MIG?'<li><a href="setup">SETUP</a></li>':'';
$y .= DIV_CLEAR.'</ul>';

echo $y;
include('foot.php');
ob_end_flush();
?>
