<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
if(!ISIN_GOS) { reloadTo(DLC_GORT);exit; }
$con=SQLi('gos');
foreach($_POST as $key=>$val) {
	$dat=trim_escape($val);
	$$key=$dat;
}unset($_POST);

if(isset($submit)) {
	$tord='tblorders';
	$trem='tblremit';
	$trep='tblreplenish';
	if($submit=='_ _') {
		$usetrn=LOGIN_BRANCH.date("8YmdHis");
		$idata="'$usetrn','$rdate'";
		$rdata=explode("~",$rdata);
		foreach($rdata as $val) {
			$dat=explode("|",$val);
			$rline="'$dat[0]','$dat[1]','$dat[2]',0";
			mysqli_query($con,"INSERT INTO $trem VALUES ($idata,$rline,0)") or die(mysqli_error($con));
		}

		$rmids=explode("|",$rmids);
		foreach($rmids as $val) {
			$rs=mysqli_query($con,"SELECT orders FROM $tord WHERE refNo='$val'") or die(mysqli_error($con));
			$rw=mysqli_fetch_array($rs);
			$data=explode("~",$rw['orders']);
			foreach($data as $x) {
				$rep=explode("|",$x);
				$cod=substr($val,0,5).$rep[0];
				mysqli_query($con,"INSERT INTO $trep VALUES ('$cod',$rep[2],0) ON DUPLICATE KEY UPDATE qty=qty+$rep[2]") or die(mysqli_error($con));
			}
			mysqli_query($con,"UPDATE $tord SET remitid='$usetrn' WHERE refno='$val'");
			updateLog($val,2);
		}
	}elseif($submit=='! !') {
		$rd=explode("~",substr($rdat,0,-1));
		foreach($rd as $val) {
			$dat=explode("|",$val);
			if(IS_GOS) updateStocks($bid,$dat[0],$dat[1]);
			$con=SQLi('gos');
			$cod=$bid.$dat[0];
			$set=IS_GOS?"qty=qty-$dat[1],req=0":"req=$dat[1]";
			$qry="UPDATE $trep SET $set WHERE code='$cod'";
			mysqli_query($con,$qry);
		}mysqli_query($con,"DELETE FROM $trep WHERE qty=0");
	}elseif(substr($submit,0,1)=='@') {
		$a=array_search(substr($submit,1,strlen($submit)),$actArr);
		$s=array_search(substr($submit,1,strlen($submit)),$statArr);
		$dat=explode("|",substr($payid,0,-1));
		foreach($dat as $key=>$val) {
			mysqli_query($con,"UPDATE $tord SET $s='1' WHERE refno='".$val."'");
			updateLog($val,$a);
		}
	}mysqli_close($con);echo true;
}

function updateLog($trans,$stat) {
	$con=SQLi('gos');
	mysqli_query($con,"INSERT INTO tbllog VALUES (null,'$trans','".date(TMDSET,time())."',$stat,'".LOGIN_ID."')") or die(mysqli_error($con));
}

function updateStocks($brn,$cod,$qty) {
	$con=SQLi('products');
	$brn='w'.$brn;
	mysqli_query($con,"UPDATE tblstocks SET $brn=$brn+$qty WHERE id='$cod'") or die(mysqli_error($con));
}
?>
