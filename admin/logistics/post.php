<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
include('../fetch.php');
$con=SQLi('products');
date_default_timezone_set('Asia/Manila');
$idata='';$trans='';$reldesc='';$recdesc='';$uptdesc='';$submit='';
$isasm=(isset($_SESSION['isAssembly'])&&$_SESSION['isAssembly']);
$issales=(isset($_SESSION['isSales'])&&$_SESSION['isSales']);
$release=(isset($_SESSION['releasing'])&&$_SESSION['releasing']);
//print_r($_POST);echo "<br>";

foreach($_POST as $key=>$v) {
	if(is_array($v)) $v='array';
	$$key=trim_escape("$v");
	$idata.="'".trim_escape("$v")."',";
}
if(isset($_POST['transrel'])) {
	$trel=stripArray($_POST['transrel']);
}
if(isset($_POST['transrec'])) {
	$trec=stripArray($_POST['transrec']);
	$trem=stripArray($_POST['remarks']);
	$idata="'',''";
}else $idata=substr_replace($idata,'',-10);

//echo "<br>===".$idata."===<br>";

$rem=isset($_POST['remarks'])?implode('~',$_POST['remarks']):'';
if(isset($_SESSION['transfers'])) {
	foreach($_SESSION['transfers'] as $v) {
		$v0=$v[0];
		$v1=$v[1];
		$v2=$v[2];
		$trans.="$v0|$v1|$v2~";
	}
}

if(isset($trel)) {
	$i=0;
	foreach($trel as $v) {
		$reldesc.="$v~";
		$i++;
	}
}

if(isset($trec)) {
	$i=0;
	foreach($trec as $v) {
		$x=$trem[$i];
		$recdesc.="$v|$x~";
		$uptdesc.="$v~";
		$i++;
	}
}

if(isset($_POST['submit'])&&$submit=='SUBMIT') {
	$time=date(TMDSET,time());
	$admin=$_SESSION['login_id'];
	$asmid=substr($trans,0,5);
	$trans=substr_replace($trans,'',-1);
	$trans=substr($trans,($isasm?strpos($trans,'~')+1:0),strlen($trans));
	$recdesc=substr_replace($recdesc,'',-1);
	$uptdesc=substr_replace($uptdesc,'',-1);
	if(isset($_SESSION['isAssembly'])&&$_SESSION['isAssembly']) {
		$tbl='tbllogassembly';
		$idata="'$asmid','$time','$trans'";
		$udata="reqdesc='$trans'";
	}else{
		$tbl='tbllogtransfer';
		$newref=isset($_POST['id'])?$id:setRefNo($tbl,$whto);
		$idata="'$newref','$time','$trans','$admin',$idata,'','','','','','',0";
		$udata=$release&&!$issales?"relstamp='$time',reldesc='".$reldesc."',relid='$admin'":"recstamp='$time',recdesc='".($issales?$rem:$recdesc)."',recid='$admin',status=".($issales?0:1);
		if($issales) { if(!isset($_POST['remarks'])) runSales($newref,$tbl,$trans,$_POST['whfr']); }
		else if(!$release) { runStocks($newref,$tbl,$uptdesc); }
	}
	$qry="INSERT INTO $tbl VALUES ($idata) ON DUPLICATE KEY UPDATE $udata";
//echo $qry."<br>";
	mysqli_query($con,$qry) or die(mysqli_error($con));
	echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL='.$_SESSION['lastpage'].'">';

}
mysqli_close($con);
unset($_SESSION['transfers']);
unset($_SESSION['csverror']);
unset($_POST);

function stripArray($array) {
	foreach($array as $key=>$v) {
		$arr[]=trim_escape($v);
	}
	return $arr;
}

function runStocks($id,$tbl,$reciv) {
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT reqdesc,whfrom,whto FROM $tbl WHERE id='$id'") or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		$rw=mysqli_fetch_array($rs);
		$i=0;
		$wfr=$rw['whfrom'];
		$wto=$rw['whto'];
		$req=explode("~",$rw['reqdesc']);
		$rec=explode("~",$reciv);
		foreach($req as $v) {
			$ireq[]=explode("|",$v);
			updateStocks($ireq[$i][0],$rec[$i],$wfr,$wto);
			$i++;
		}
	}
}

function runSales($id,$tbl,$reciv,$wfr) {
	$rec=explode("~",$reciv);
	$i=0;
	foreach($rec as $v) {
		$irem[]=explode("|",$v);
			updateStocks($irem[$i][0],$irem[$i][2],$wfr,$wfr,1);
		$i++;
	}
}

function updateStocks($id,$val,$fr,$to,$sale=false) {
	if($sale) {
		$setfr="w$fr=w$fr-$val";
	}else{
		$setfr=((int)$fr<100)?'':"w$fr=w$fr-$val";
		$setto=((int)$to<100)?'':"w$to=w$to+$val";
		$setfr.=($setfr!=''&&$setto!='')?',':'';
	}
	$con=SQLi('products');

	$i=str_pad('',countWH*2-4,',0');	//init stocks
	$qry="INSERT INTO tblstocks VALUES ($id,0$i) ON DUPLICATE KEY UPDATE id=id";
	mysqli_query($con,$qry) or die(mysqli_error($con));

	$qry="INSERT INTO tblstocks VALUES ($id,0$i) ON DUPLICATE KEY UPDATE $setfr $setto";
	mysqli_query($con,$qry) or die(mysqli_error($con));
}

function setRefNo($tbl,$whto) {
	$date=date('Ymd',time());
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT id FROM $tbl WHERE id LIKE '$date%'") or die(mysqli_error($con));
	$num=mysqli_num_rows($rs);
	return $date.sprintf("%03d",$num++).sprintf("%05d",$whto);
}
?>
