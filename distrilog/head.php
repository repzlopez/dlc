<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
$root=(isset($forcart)&&$forcart)?'../':'';
$linkrecap	='<a href="/distrilog/recap.php">myRecap</a>';
$linkmpage	='<a href="'.DLC_MYPAGE.'">myPage</a>';
$linkmkart	=SHOPLIST?'<a href="/distrilog/mycart.php">myCart</a>':'';
$linkorder	='<a href="/distrilog/myorders.php">myOrders</a>';
$linkproducts  ='<a href="/read/'.$shortlink.'lifestyle-shop/">Shop</a>';
$linkhome      ='<a href="/read">Home</a>';
$title		=(isset($title))?$title:'';
$link		=(isset($link))?$link:'';
$linkname		=(isset($linkname))?$linkname:'';
$showrecap	=(isset($vieworder)&&$vieworder)?'':$linkrecap;
$isrecap		=(isset($_SESSION['is_recap']))?true:false;
$kart		=(CART_ON&&!SPECTATOR) ? $showrecap.$linkmkart.($link=='mypage'?$linkmpage:$linkorder) : $linkrecap;	/*cart*/
$recaplinks	=(SPECTATOR?$linkmpage:$linkmpage.$linkmkart.$linkorder);
$mypgelinks	=($isrecap)?$recaplinks:$kart;
$frm			=ISIN_DISTRI?'<div id="user"><strong class="blue">'.(ISIN_DISTRI?DIST_NAME:'').'</strong> (<a href="/logout" class="lite">logout</a>)'.$mypgelinks.$linkproducts.$linkhome.'</div>':'';
$tail		=(isset($vieworder)&&$vieworder)?'<link rel="stylesheet" href="/css/zebra_datepicker.css" type="text/css">':'';
$_SESSION['is_mgrlist']=false;
unset($_SESSION['is_recap']);
ob_start();
echo loadHead($title,$tail);
echo loadLogo($frm);
echo '<div id="container"'.($isrecap?' class="printrecap"':'').'>';
ob_end_flush();
?>
