<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(ISIN_ADMIN&&!ISIN_GOS) {
	$_SESSION['gos_logged']=1;
	$_SESSION['gos_bad']=0;
	$_SESSION['a_logged']=0;
	reloadTo(DLC_GORT);
}

$actArr=array(0=>'create',1=>'edit',2=>'batpaym',3=>'batrepl',4=>'batconf',5=>'batclos');
$statArr=array('replen'=>'batrepl','paystat'=>'batconf','status'=>'batclos');

function setDatePosted() {
	return date('Ymd',time());
}

function setRefNo() {
	$con=SQLi('gos');
	$date=setDatePosted();
	$rs=mysqli_query($con,"SELECT refno FROM tblorders WHERE refdate=$date") or die(mysqli_error($con));
	$num=mysqli_num_rows($rs);
	mysqli_close($con);
	$num++;
	$str=$date.sprintf("%03d",$num);
	$_SESSION['gos_reftran']=$str;
	return $str;
}

function getWHID($user) {
	$con=SQLi('products');
	$id=null;$wh=null;$ds=null;$bn=null;
	$rs=mysqli_query($con,"SELECT id,wh,dsdid,bond FROM tblwarehouse WHERE id='$user' AND status=1") or die(mysqli_error());
	if(mysqli_num_rows($rs)>0) { $rw=mysqli_fetch_array($rs);$id=$rw['id'];$wh=$rw['wh'];$ds=$rw['dsdid'];$bn=$rw['bond'];}
	return array($id,$wh,$ds,$bn);
}
?>
