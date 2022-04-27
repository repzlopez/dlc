<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require('../admin/setup.php');
require '../admin/getwebstat.php';

if( (!ISIN_DISTRI && !DIST_ID&&!GUEST) || (SPECTATOR && !OVERRIDE) || UPDATE_ON ) { reloadTo('../');exit; }
date_default_timezone_set('Asia/Manila');

$_SESSION['lastURI']  = 'mypage';
$_SESSION['lastpage'] = $_SERVER['PHP_SELF'];
$title		= "| My Cart";
$link		= 'mypage';
$linkname	= 'myPage';
$data		= '';
$vieworder	= true;
$id			= GUEST?GUEST:DIST_ID;
$bak		= '<a href="/read/'.$shortlink.'lifestyle-shop/" class="back" id="download">Back to Products Page</a>'.DIV_CLEAR;

$con  = SQLi('distributor');
$rs   = mysqli_query($con,"SELECT * FROM distributors WHERE dsdid = '$id'") or die(mysqli_error($con));
$rw   = mysqli_fetch_array($rs);
$name = $rw['dslnam'].', '.$rw['dsfnam'].' '.(($rw['dsmnam']!='')?substr($rw['dsmnam'],0,1).'.':'');
$cont = $rw['dsmph'].' / '.$rw['dshph'].' / '.$rw['dsoph'];
$addy = $rw['dsstrt'].', '.$rw['dsbarn'].', '.$rw['dscity'].', '.$rw['dsprov'];
mysqli_close($con);

require 'head.php';
require 'cart/values.php';
require 'cart/cartdata.php';
require 'cart/freight.php';

ob_start();
// if(!GUEST) echo getArchive(DIST_ID,$met,$dlv1,$box);
if( GUEST && !SHOPLIST ) {
	echo '<div><br><br><br><h2 class="blue ct">Order submitted.<br><br>Feel free to contact the person who gave you the link to follow-up on your order.</h2><br><br></div>';
	if( ( isset($_SESSION['guestpv']) && $_SESSION['guestpv']>=GUESTMINPV ) || OLREG_NOSLOT ) {
		echo '<div style="height:500px;"><h2 class="blue ct">Loading Online Registration...</h2></div>';
		reloadTo('/read/'.$shortlink.'registration/',2);
	} else reloadTo(DLC_ROOT,3);
}

echo '<form method="POST" action="cart/post.php">';
if( SHOPLIST&&count($_SESSION['shoplist'])>0 ) echo getOrders();
echo "\n\n".getDetails($id,$name,$addy,$cont);

if( SHOPLIST&&count($_SESSION['shoplist'])>0 ) {
	echo getDelivery($dlv1,$box);
	echo $freight;
	echo getPayment($met);
	echo getNotice();
	echo '<div id="totals"><input type="submit" data-order=1 name="submit" value="_ _" /></div>';
}
echo '</form>';
echo $bak;

$arrJs = array('/js/jquery/jquery-1.7.1.min.js','/js/jquery/zebra_datepicker.js','/js/cart.js');
echo loadFoot('','',$arrJs);
ob_end_flush();

function getArchive($id,$met,$dlv,$box) {
	echo '<div class="orders" id="archive"><div class="blue">ORDERS ARCHIVE</div>';
	echo '<ul>';
	echo '<li class="hdr"><span class="s4">Ref#</span>';
	echo '<span class="s3 ct">Posted</span>';
	echo '<span class="s2 lt">Box</span>';
	echo '<span class="s4 lt">Pay Thru</span>';
	echo '<span class="s3 rt">Amount</span>';
	echo '<span class="s3 ct">Date Paid</span>';
	echo '<span class="s3 lt">Status</span></li>';
	$con = SQLi('orders');
	$rs  = mysqli_query($con,"SELECT * FROM tblorders WHERE dsdid = '$id' ORDER BY deliStat,refDate,refNo") or die(mysqli_error($con));
	while( $rw=mysqli_fetch_assoc($rs) ) {
		foreach( $rw as $k=>$v ) $$k = $v;
		$rdate    = substr($refDate,4,2).'.'.substr($refDate,-2).'.'.substr($refDate,0,4);
		$postdate = substr_replace($payDate,'.',2,0);
		$postdate = substr_replace($postdate,'.',5,0);
		$settobad = ($deliStat==0) ? 'bad' :'';
		$payop    = array_search($payOp,$met);
		echo '<li><a href="cart/vieworder.php?ref='.$refNo.'" class="s4">'.$refNo.'</a>';
		echo '<span class="s3 ct">'.$rdate.'</span>';
		echo '<span class="s2 lt">'.(($deliBox==3)?$deliAddto:array_search($deliBox,$box)).'</span>';
		echo '<span class="s4 lt nowrap" title="'.$payop.'">'.$payop.'</span>';
		echo '<span class="s3 rt">'.number_format($payAmt,2).'</span>';
		echo '<span class="s3 ct">'.$postdate.'</span>';
		echo '<span class="s3 lt '.$settobad.'">'.array_search($deliStat,$dlv).'</span>';
	} mysqli_close($con);
	echo '</ul></div>';
}
?>
