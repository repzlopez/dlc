<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
$_SESSION['lastURI']=$page;

$firstlogin	=(isset($_SESSION['first_login'])&&$_SESSION['first_login'])?true:false;
$badlogin		=(isset($_SESSION['bad_login'])&&$_SESSION['bad_login'])?true:false;
$notfound		=(isset($_SESSION['not_found'])&&$_SESSION['not_found'])?true:false;
$userno		=(isset($_SESSION['u_site']))?$_SESSION['u_site']:'';
$linkproducts  ='<a href="/read/'.$shortlink.'lifestyle-shop/">Shop</a>';
$linkhome      ='<a href="/read" class="lite">Home</a>';

unset($_SESSION['first_login']);
unset($_SESSION['bad_login']);
unset($_SESSION['not_found']);

if($firstlogin){ header("Location: ".$_SERVER['PHP_SELF']);}
$rfrlinks='<a href="/reg/package.php" class="lite">Be One of Us</a>';
$frm='';$maintain=GUEST?$rfrlinks:(LOGIN_ON?'':'<span class="smaller">MAINTENANCE IN PROGRESS...</span>');
$errmsg=($badlogin)?($notfound?'** Distributor ID does not exist':'** Invalid Distributor ID or Password'):'';
$tail='<meta name="description" content="'.DLC_FULL.'. A company of the distributors, by the distributors, for distributors" /><meta name="keywords" content="dlc,mlm,network,marketing,networking,eddy chai,eddy,diamond,lifestyle,corporation,distributor" />';
if($page==='news') $tail='<link rel="stylesheet" type="text/css" media="screen" href="/css/lightbox.css"></link>'.$tail;
if(ISIN_DISTRI){
	$mypg=UPDATE_ON?'<span class="smaller">UPDATE IN PROGRESS...</span>':'<a href="'.DLC_MYPAGE.'">myPage</a>';
	$frm.='<div id="user"><strong class="blue">'.DIST_NAME.'</strong> (<a href="/logout" class="lite">logout</a>)'.$mypg.$linkhome.'</div>';
}$frm='<div id="topnav">'.(LOGIN_ON?$frm:$maintain).'</div>'; //login status

ob_start();
echo loadHead($title,$tail);
echo loadLogo($frm);
echo '<div id="container">';
ob_implicit_flush(true);
ob_end_flush();?>
