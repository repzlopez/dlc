<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require('../admin/setup.php');

if ( !ISIN_DISTRI && !GUEST && !RESELLER ) { reloadTo(DLC_ROOT); exit; }
if ( !defined('SHOPLIST') ) define('SHOPLIST',isset($_SESSION['shoplist']));

$_SESSION['lastURI'] = 'onreg';
$title    = ' | Online Registration';
$item     = isset($_GET['i'])?$_GET['i']:'';
$do       = isset($_GET['do'])?$_GET['do']:'';
$reseller = isset($_GET['reseller'])&&$_GET['reseller'];
$imgpath  = '/images/products/';
$reselleronly = $reseller ? '' :'NOT';

$p=$msg='';
$con = SQLi('products');
$qry = "SELECT l.id,name,srp,img,contains
	FROM tblpackages k
	LEFT JOIN tbllist l
		ON l.id=k.id
	LEFT JOIN tblproducts p
		ON p.id=k.id
	WHERE k.status=1
	AND l.name $reselleronly LIKE 'Reseller%'
	AND l.status=1
	ORDER BY name";

$rs = mysqli_query($con,$qry) or die(mysqli_error($con));
while ( $rw=mysqli_fetch_assoc($rs) ) {
	foreach ( $rw as $k=>$v ) $$k = $v;
    if ( $reseller ) $_SESSION['reseller_packages'][$id] = $id;

	$previmg = ( $img!='' || file_exists($imgpath.$img) ) ? $imgpath.$img : $imgpath.'default_product.jpg';
	$p   .= '<li data-code="'.$id.'" data-price="'.$srp.'" '.($contains!=''?'title="Contains:'."\n".str_replace('<br>','',$contains).'"':'').'><p id="overlay" class="'.(OLREG_REF==$id?'blue':'').'">'."$name Php ".number_format($srp,0).'</p><img src="'.$previmg.'" /></li>';
}

if ( !$reseller ) {
	$c100 = 'code_100.jpg';
	$c500 = 'code_500.jpg';
	$poc  = 'worth of products of your choice';

	$p   .= '<li data-code="'.OLREG_Choice100.'" data-price="" title="100PV '.$poc.'"><p id="overlay" class="'.(OLREG_REF==OLREG_Choice100?'blue':'').'">'.OLREG_min100.'PV Products<br>of your choice</p><a href="/read/'.$shortlink.'/lifestyle-shop?olreg_ref='.OLREG_Choice100.'"><img src="'.(file_exists($imgpath.$c100)?$imgpath.$c100:$imgpath.'default_product.jpg').'" /></a></li>';
	$p   .= '<li data-code="'.OLREG_Choice500.'" data-price="" title="500PV '.$poc.'"><p id="overlay" class="'.(OLREG_REF==OLREG_Choice500?'blue':'').'">'.OLREG_min500.'PV Products<br>of your choice</p><a href="/read/'.$shortlink.'/lifestyle-shop?olreg_ref='.OLREG_Choice500.'"><img src="'.(file_exists($imgpath.$c500)?$imgpath.$c500:$imgpath.'default_product.jpg').'" /></a></li>';

} else {

	$_SESSION['reseller'] = 1;

}

$cartpath  = '../distrilog/cart/';
$pak       = '<br><div class="orders" id="package"><h3 class="blue ct">Choose Package</h3><ul>'.$p.DIV_CLEAR.'</ul></div><br>';
$pak      .= '<form method="POST" action="'.DLC_ROOT.'/distrilog/cart/post.php" id="package_data">';

require $cartpath.'values.php';
require $cartpath.'cartdata.php';
require $cartpath.'freight.php';

$x   = getOrders();//process only
if( preg_match("/".OLREG_Choice100."|".OLREG_Choice500."/i",OLREG_REF) ) $pak.=$x.'<br>';
$btn_label = (strpos($_SERVER['REQUEST_URI'],'read')!==false) ? 'Checkout' :'_ _';

$pak .= getDelivery($dlv1,$box).'<br>';
$pak .= getPayment($met).'<br>';
$pak .= getNotice().'<br>';
$pak .= '<div id="totals"><input type="submit" data-order='.(OLREG_REF&&isset($_SESSION['payamount'])?1:0).' name="submit" value="'.$btn_label.'" /></div>';
$pak .= '</form>';

$frm  = '<div id="topnav"><div id="user"><a href="/read/'.$shortlink.'lifestyle-shop/" class="lite">Buy Products</a></div></div>';
if( strpos($_SERVER['REQUEST_URI'],'read')!==false ) {
	$msg     .= '<link rel="stylesheet" href="/css/zebra_datepicker.css" type="text/css">';
	$bottom   = 1;
} else {
	$msg     .= loadHead($title,'<link rel="stylesheet" href="/css/zebra_datepicker.css" type="text/css">');
	$msg     .= loadLogo($frm);
	$bottom   = 0;
}
$msg .= '<div id="container">'.$pak.'</div>';

ob_start();
echo $msg;
$arrJs=array('/js/jquery/jquery-1.7.1.min.js','/js/jquery/zebra_datepicker.js','/js/cart.js');
echo loadFoot('','',$arrJs,$bottom);
ob_end_flush();
?>
