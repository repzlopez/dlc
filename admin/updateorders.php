<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!defined('INCLUDE_CHECK')) define('INCLUDE_CHECK',1);
require_once('setup.php');
$con=SQLi('orders');
$stat=(isset($_POST['admin'])&&$_POST['admin'])?$_POST['stat']:1;
$shed=(isset($_POST['statheader'])?$_POST['statheader']:0);
$anul=($stat==4?"AND refDate LIKE '".WKYR."%'":'');
$rs=mysqli_query($con,"SELECT * FROM tblorders WHERE deliStat=$stat $anul ORDER BY deliStat,refDate,refNo") or die(mysqli_error($con));
if($shed) {
	$dat ='<li><a href="?get=1">Incoming ( '.getOrderCount(1).' )</a></li>';
	$dat.='<li><a href="?get=2">Processing ( '.getOrderCount(2).' )</a></li>';
	$dat.='<li><a href="?get=3">Outgoing ( '.getOrderCount(3).' )</a></li>';
	$dat.='<li><a href="?get=4">Delivered ( '.getOrderCount(4).' )</a></li>';
	$dat.='<li><a href="?get=0">Cancelled</a></li>';
}else{
	$dat ='<li class="hdr">';
	$dat.='<span class="s4">Reference #</span>';
	$dat.='<span class="s3 lt">Distributor</span>';
	$dat.='<span class="s2 ct">Posted</span>';
	$dat.='<span class="s3 ct">Box</span>';
	$dat.='<span class="s4 lt">Deliver To</span>';
	$dat.='<span class="s3 rt">Amount</span>';
	$dat.='<span class="s2 ct">Paid</span>';
	$dat.='<span class="s1 lt">Status</span></li>';

	$goup=(strpos($_SERVER['PHP_SELF'],'updateorders.php')==true)?'':'../';
	include_once($goup.'../distrilog/cart/values.php');
	$dlv=$dlv2;
	$pay=$met;
	$box=$box;

	while($rw=mysqli_fetch_assoc($rs)) {
		$rdate=substr($rw['refDate'],4,2).'.'.substr($rw['refDate'],-2).'.'.substr($rw['refDate'],0,4);
		$paiddate=substr_replace($rw['payDate'],'.',2,0);
		$paiddate=substr_replace($paiddate,'.',5,0);
		$payop=array_search($rw['payOp'],$pay);
		$deli_order=($_SESSION['a_page']=='orders')?'<a href="order_summary.php?ref='.$rw['deliAddto'].'">'.$rw['deliAddto'].'</a>':'<span>'.$rw['deliAddto'].'</span>';
		$dat.='<li><a href="/distrilog/cart/vieworder.php?ref='.$rw['refNo'].'" class="s4">'.$rw['refNo'].'</a>';
		$dat.='<span class="s3 lt">'.$rw['dsdid'].'</span>';
		$dat.='<span class="s2 ct">'.$rdate.'</span>';
		$dat.='<span class="s3 ct">'.(($rw['deliBox']==3)?$deli_order:array_search($rw['deliBox'],$box)).'</span>';
		$dat.='<span class="s4 lt nowrap">'.$rw['deliName'].'</span>';
		$dat.='<span class="s3 rt">'.number_format($rw['payAmt'],2).'</span>';
		$dat.='<span class="s2 ct">'.$paiddate.'</span>';
		$dat.='<span class="s1 lt delistat" rel="'.$rw['deliStat'].'">'.array_search($rw['deliStat'],$dlv).'</span></li>';
	}mysqli_close($con);
}echo $dat;

function getOrderCount($stat) {
	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT * FROM tblorders WHERE deliStat=$stat") or die(mysqli_error($con));
	return mysqli_num_rows($rs);
}
?>
