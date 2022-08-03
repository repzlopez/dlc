<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

require('../inc/setup.php');

if( isset($_SESSION['login']) && !isset($_SESSION['bad_login']) ) {
    header('Location:' . SHOP_URL);

} else {

    require_once('../../func.php');

    $badlogin   = (isset($_SESSION['bad_login']) && $_SESSION['bad_login']);
    $userno     = (isset($_SESSION['login']['dsdid'])) ? $_SESSION['login']['dsdid'] : '';
    $errmsg     = ($badlogin ? '** Invalid Distributor ID or Password' : '');

    unset($_SESSION['bad_login']);
    unset($_SESSION['login']);

    $x = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>
    <title>DLC Lifestyle Shop</title>
    <meta http-equiv="Content-Language" content="en" />
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="shortcut icon" href="'. BAK_ROOT . '/src/favicon.ico"></link>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Shadows+Into+Light">
    <link rel="stylesheet" type="text/css" media="screen" href="' . BAK_ROOT . '/css/login.css"></link>
    </head><body>';

    $x .= '<div class="box"><div class="box-content"><ul>';
    $x .= '<li><a href="' . BAK_ROOT . LOGO_LINK . '"><img src="' . BAK_ROOT . '/src/dlc_logo_min.png" alt="' . DLC_FULL . '" /></a>' . DIV_CLEAR;
    $x .= '<h1>Aim Higher!</h1><br><a href="/">HOME</a></li>';

    $x .= '<li><form method="post" action="login.php" id="login"><ul>';
    $x .= '<li><input type="text" name="un" placeholder="Distributor ID" value="' . $userno . '" /></li>';
    $x .= '<li><input type="password" name="pw" placeholder="Password" /></li>';
    $x .= '<li><input type="submit" name="submit" value="Login" /></li>';
    $x .= '<li><a href="' . DLC_ROOT . '/read/at-your-service/forgot-password/" class="lite">Forgot password?</a></li>';
    $x .= '<li class="bad">' . $errmsg . '</li>';
    $x .= '</ul></form>' . DIV_CLEAR . '</li>';
    $x .= '</ul></div></div></body></html>';

    ob_start();
    echo $x;
    ob_end_flush();
}

?>
