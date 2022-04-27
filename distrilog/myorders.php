<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');
require '../admin/getwebstat.php';
if((!ISIN_DISTRI&&!DIST_ID&&!GUEST)||(SPECTATOR&&!OVERRIDE)||UPDATE_ON){ reloadTo('../');exit; }

$_SESSION['lastURI']='mypage';
$_SESSION['lastpage']=$_SERVER['PHP_SELF'];
$title		="| My Orders";
$link		='mypage';
$linkname		='myPage';

require 'head.php';
require 'cart/values.php';

ob_start();
if(!GUEST) echo getArchive(DIST_ID,$met,$dlv1,$box);
echo loadFoot();
ob_end_flush();

function getArchive($id,$met,$dlv,$box){
	$x  = '<div class="print"><a href="javascript:window.print()"></a></div>';
	$x .='<div class="orders" id="archive"><div class="blue">ORDERS ARCHIVE</div>';
	$x .= '<ul>';
	$x .= '<li class="hdr"><span class="s4">Ref#</span>';
	$x .= '<span class="s3 ct">Posted</span>';
	$x .= '<span class="s4 lt">Delivered To</span>';
	$x .= '<span class="s3 lt">Contact</span>';
	$x .= '<span class="s3 rt">Amount</span>';
	$x .= '<span class="s3 ct">Status</span></li>';
	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT * FROM tblorders WHERE dsdid = '$id' ORDER BY refDate DESC,refNo") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		foreach($rw as $k=>$v) $$k=$v;
		$rdate=substr($refDate,4,2).'.'.substr($refDate,-2).'.'.substr($refDate,0,4);
		$postdate=substr_replace($payDate,'.',2,0);
		$postdate=substr_replace($postdate,'.',5,0);
		$payop=array_search($payOp,$met);
		$x .= '<li><a href="cart/vieworder.php?ref='.$refNo.'" class="s4">'.$refNo.'</a>';
		$x .= '<span class="s3 ct">'.$rdate.'</span>';
		$x .= '<span class="s4 lt">'.$deliName.'</span>';
		$x .= '<span class="s3 lt">'.$deliCont.'</span>';
		$x .= '<span class="s3 rt">'.number_format($payAmt,2).'</span>';
		$x .= '<span class="s3 ct">'.array_search($deliStat,$dlv).'</span></li>';
	}mysqli_close($con);
	return $x.'</ul></div>';
}
?>
