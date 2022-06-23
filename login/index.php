<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../func.php');
$firstlogin	=(isset($_SESSION['first_login'])&&$_SESSION['first_login']);
$badlogin		=(isset($_SESSION['bad_login'])&&$_SESSION['bad_login']);
$notfound		=(isset($_SESSION['not_found'])&&$_SESSION['not_found']);
$userno		=(isset($_SESSION['u_site']))?$_SESSION['u_site']:'';
$login		=(isset($_SESSION['catch_login']))?$_SESSION['catch_login']:'';
$errmsg		=($badlogin)?($notfound?'** Distributor ID does not exist':'** Invalid Distributor ID or Password'):'';
$path          =($login!=''?'/'.$login:'').'/login/';
$page		='login';

unset($_SESSION['first_login']);
unset($_SESSION['bad_login']);
unset($_SESSION['not_found']);
if($firstlogin) { header("Location: ".$_SERVER['PHP_SELF']);}

$x='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>DLC '.strtoupper($login).'</title>
<meta http-equiv="Content-Language" content="en" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="shortcut icon" href="/src/favicon.ico"></link>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Shadows+Into+Light">
<link rel="stylesheet" type="text/css" media="screen" href="/css/login.css"></link>
</head><body>';

$x.='<div class="box"><div class="box-content"><ul>';
$x.='<li><a href="'.LOGO_LINK.'"><img src="/src/dlc_logo_min.png" alt="'.DLC_FULL.'" /></a>'.DIV_CLEAR;
$x.='<h1>Aim Higher!</h1><br><a href="'.DLC_ROOT.'/read/">HOME</a></li>';
$x.='<li><form method="post" action="'.$path.'login.php" id="login"><ul>';
$x.='<li><input type="text" name="un" placeholder="'.($login!=''?'Username':'Distributor ID').'" value="'.$userno.'" /></li>';
$x.='<li><input type="password" name="pw" placeholder="Password" /></li>';
$x.='<li><input type="submit" name="submit" value="Login" /></li>';
$x.='<li><a href="'.DLC_ROOT.'/read/at-your-service/forgot-password/" class="lite">Forgot password?</a></li>';
$x.='<li class="bad">'.$errmsg.'</li>';
$x.='</ul></form>'.DIV_CLEAR.'</li>';
$x.='</ul></div></div></body></html>';
ob_start();
echo $x;
ob_end_flush();
?>
