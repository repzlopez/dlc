<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../admin/setup.php');
if(
	(!ISIN_DISTRI&&!ISIN_ADMIN)||
	(!verifyRef($_GET['ref']))
){	reloadTo(DLC_ROOT);exit; }
$_SESSION['lastURI']='mypage';
$title='| ORDER DETAILS';
$vieworder=true;
$forcart=true;
ob_start();
require('../head.php');
echo '<div class="print"><a href="javascript:window.print()"></a></div>';
echo '<div class="blue ct">ORDER DETAILS</div><div class="smaller ct">( '.getStamp($_GET['ref']).' )</div>';
viewOrder($_GET['ref']);
getDelivery($_GET['ref']);
echo '<div class="vieworders"><a href="'.(ISIN_ADMIN?$_SESSION['lastpage']:$_SERVER['HTTP_REFERER']).'" class="back" id="download">BACK</a></div>';
?>
</div>
<div id="foot">
	<div class="foot_sig">
		<span><?php echo DLC_FULL?> &copy;2009-<?php echo date('Y',time())?> All rights reserved.</span>
	</div>
</div>
</body>
</html>
<style type="text/css">.loading{display:none}</style>
<script type="text/javascript" src="../../js/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../js/jquery/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../js/cart.js"></script>
<?php
echo ISIN_ADMIN?'<script type="text/javascript" src="../../admin/common.js"></script>':'';
ob_end_flush();

function verifyRef($ref){
	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT * FROM tblorders WHERE refNo = '$ref'") or die(mysqli_error($con));
	$num=mysqli_num_rows($rs);
	return ($num>0)?true:false;
}

function getStamp($ref){
	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT stamp FROM tbllog WHERE refNo = '$ref' AND action=1") or die(mysqli_error($con));
	$rw=mysqli_fetch_assoc($rs);
	return date('m.d.Y h:ia',strtotime($rw['stamp']));
}

function getDelivery($ref){
	include('values.php');
	$rw=getRows($ref);
	if(ISIN_ADMIN){ $dlv=$dlv2; }
	else{ $dlv=$dlv1; }
	$paid=$paid1;
	$locked=(($rw['deliStat']!=1)?DISABLED:'');
	$name=getName($rw['dsdid'],'lfm');
	$delistat=$rw['deliStat'];
	$postdate=substr_replace($rw['payDate'],'.',2,0);
	$postdate=substr_replace($postdate,'.',5,0);
	echo '<div class="orders vieworders"><ul>';
	echo '<li><span class="s3">Distributor #:</span> <strong>'.$rw['dsdid'].'</strong></li>';
	echo '<li><span class="s3">Name:</span> <strong>'.$name.'</strong></li>';
	echo '<li><span class="s3">Ref #:</span> <strong class="blue"  id="refNo">'.$rw['refNo'].'</strong></li>';
	echo '<li><span class="s3">Method:</span><select name="payOp" '.$locked.'>';
	echo popSelects($met,$rw['payOp'],true);
	echo '</select></li>';
	echo '<li><span class="s3">Amount (Php):</span> <input type="text" class="s8" name="payAmt" value="'.number_format($rw['payAmt'],2).'" '.$locked.'/></li>';
	echo '<li><span class="s3">Note:</span> <textarea class="s8" name="payNote" '.$locked.'>'.$rw['payNote'].'</textarea></li>';
	echo '<li><span class="s3">Payment Posted:</span> <input type="text" class="s4" id="datePicker" name="payDate" value="'.$postdate.'" '.$locked.'/></li>';
	echo '<li><span class="s3">Payment Status:</span> <strong class="'.(($rw['payStat']==0)?'bad':'blue').'">'.array_search($rw['payStat'],$paid).'</strong></li>';
	echo '</ul>';
	echo '<ul>';
	echo '<li><span class="s3">Deliver To:</span> <input type="text" class="s8" name="deliName" value="'.ucwords(strtolower($rw['deliName'])).'" '.$locked.'/></li>';
	echo '<li><span class="s3">Contact #:</span> <input type="text" class="s8" name="deliCont" value="'.$rw['deliCont'].'" '.$locked.'/></li>';
	echo '<li><span class="s3">Delivery Address:</span> <textarea class="s8" id="deliAddy" name="deliAddy" '.$locked.'>'.$rw['deliAddy'].'</textarea></li>';
	echo '<li><span class="s3">Note:</span> <textarea class="s8" name="deliNote" '.$locked.'>'.$rw['deliNote'].'</textarea></li>';
	echo '<li><span class="s3">Box Size:</span> <span>'.strtoupper(array_search($rw['deliBox'],$box)).'</span>'.(($rw['deliAddto']!='')?': <input type="text" class="s5 gen1" name="deliAddto" value="'.$rw['deliAddto'].'" '.$locked.'/>':'').'</li>';
	echo '<li><span class="s3">Status:</span> <strong class="'.(($delistat==4)?'blue':'').'">'.array_search($delistat,$dlv).'</strong>';
	echo (ISIN_ADMIN&&$delistat>0&&$delistat<4)?'<a href="#'.$rw['refNo'].'" rel="'.$delistat.'" class="back verifypay" id="download">CONFIRM</a>':'';
	echo '</li></ul>';
	echo ($delistat==1)?'<a href="#" class="back updateOrder" id="download">UPDATE</a>':'';
	echo ($delistat>0&&$delistat<4)?'<a href="#" class="bad back cancelorder" id="download">CANCEL ORDER</a>':'';
	echo DIV_CLEAR.'</div>';
}

function viewOrder($ref){
	echo '<div class="orders"><ul>';
	echo '<li class="hdr"><span class="s1 lt">CODE</span>';
	echo '<span class="s6">DESCRIPTION</span>';
	echo '<span class="s2 rt">PV</span>';
	echo '<span class="s2 rt">PRICE</span>';
	echo '<span class="s2 rt">QTY</span>';
	echo '<span class="s3 rt">Total PV</span>';
	echo '<span class="s3 rt">Total Amt</span></li>';
	$shopqty=0;$shopamt=0;$shopppv=0;$shopwt=0;

	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT * FROM tblorders WHERE refNo = '$ref'") or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$data=explode("~",$rw['orders']);
	mysqli_close($con);
	foreach($data as $val){
		$idata[]=explode("|",$val);
	}

	foreach($idata as $v){
		$v1=$v[1];
		$v2=$v[2];
		$v3=$v[3];
		$v4=$v[4];
		$v5=$v[5];
		$v6=isset($v[6])?$v[6]:null;
		$shopqty+=$v3;
		$shopppv+=$v4*$v3;
		$shopamt+=$v5*$v3;
		$shopwt+=$v6*$v3;
		if($v3>0){
			echo '<li>';
			echo '<span class="s1 lt">'.$v1.'</span>';
			echo '<span class="s6 nowrap">'.utf8_encode($v2).'</span>';
			echo '<span class="s2 rt">'.number_format($v4,2).'</span>';
			echo '<span class="s2 rt">'.number_format($v5,2).'</span>';
			echo '<span class="s2 rt">'.number_format($v3).'</span>';
			echo '<span class="s3 rt">'.number_format($v4*$v3,2).'</span>';
			echo '<span class="s3 rt">'.number_format($v5*$v3,2).'</span></li>';
		}
	}
	echo '<li class="ct smaller">** NOTHING FOLLOWS **</li>';
	echo '<li>';
	echo '<span class="s1"></span>';
	echo '<span class="s6">APPROX WEIGHT: '.number_format($shopwt,2).' Kg</span>';
	echo '<span class="s2"></span>';
	echo '<span class="s2 rt">TOTALS:</span>';
	echo '<span class="s2 rt">'.number_format($shopqty).'</span>';
	echo '<span class="s3 rt">'.number_format($shopppv,2).'</span>';
	echo '<span class="s3 rt">'.number_format($shopamt,2).'</span>';
	echo '</li>';
	echo '</ul></div>';
}

function getRows($ref){
	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT * FROM tblorders WHERE refNo = '$ref'") or die(mysqli_error($con));
	return mysqli_fetch_array($rs);
}
?>
