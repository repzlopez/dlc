<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
$ref=isset($_GET['ref'])?$_GET['ref']:NULL;
define('INCLUDE_CHECK',1);
include('../fetch.php');
if(!verifyRef($ref)){
	reloadTo($_SESSION['lastpage']);exit;
}
$adminpage='phiorders';
$title='| Order Summary';
ob_start();
include('../head.php');
echo '<ul class="print"><li><a href="javascript:window.print()"></a></li></ul>';
echo '<div class="blue ct">ORDER SUMMARY</div><br />';
echo '<div class="orders"><ul>';
echo '<li><span class="s4">DISTRIBUTOR NAME / ID</span>';
echo '<span class="s1 rt">CODE</span>';
echo '<span class="s2 ct">QTY</span>';
echo '<span class="s6 ct">PRODUCT NAME</span>';
echo '<span class="s2 rt">PRICE</span>';
echo '<span class="s3 rt">Total Amt</span></li>';
viewOrder($ref,"refNo = '$ref'",1);
viewOrder($ref,"deliAddto = '$ref'",0);
echo '<li></li><li class="ct"><strong>** NOTHING FOLLOWS **</strong></li>';
echo '</ul></div>';
echo '<div><a href="./" class="back" id="download">BACK</a></div>';
?>
</div><div id="foot"><div class="foot_sig">
<span><?php DLC_FULL?> &copy;2009-<?php echo date('Y',time())?> All rights reserved.</span>
</div></div></body></html>
<style type="text/css">.loading{display:none} .print{margin-top:-28px}</style>
<?php
ob_end_flush();

function verifyRef($ref){
	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT refNo FROM tblorders WHERE refNo = '$ref'") or die(mysqli_error($con));
	return (mysqli_num_rows($rs)>0)?true:false;
}

function viewOrder($ref,$qry,$boxpay){
	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT dsdid,orders,deliBox FROM tblorders WHERE $qry") or die(mysqli_error($con));
	while($rw=mysqli_fetch_array($rs)){
		$id=$rw['dsdid'];
		$data=explode("~",$rw['orders']);
		$_SESSION['boxsize']=$rw['deliBox'];
		echo '<li></li><li>'.getName($id).'</li>';
		setData($id,$data,$boxpay);
	}
	mysqli_close($con);
}

function setData($id,$data,$boxpay){
	if($boxpay) include_once('../../distrilog/cart/values.php');
	$pass1=true;
	foreach($data as $val){
		$idata[]=explode("|",$val);
	}

	foreach($idata as $v){
		$v1=$v[1];//cod
		$v2=$v[2];//nam
		$v3=$v[3];//qy
		$v4=$v[4];//pvv
		$v5=$v[5];//wsp
		if($v3>0){
			$id=($pass1)?$id:array_search($_SESSION['boxsize'],$box);
			echo '<li><span class="s4 lt">'.$id.'</span>';
			echo '<span class="s1 rt">'.$v1.'</span>';
			echo '<span class="s2 ct">'.$v3.'</span>';
			echo '<span class="s6">'.$v2.'</span>';
			echo '<span class="s2 rt">'.number_format($v5,2).'</span>';
			echo '<span class="s3 rt">'.number_format($v5*$v3,2).'</span></li>';
			$pass1=false;
		}
	}
}
?>
