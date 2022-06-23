<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
if(!IS_GLOB) {reloadTo(DLC_ADMIN);exit;}
$wkyr=isset($_POST['wkyr'])?$_POST['wkyr']:null;
$msg= '<form method="post" class="totop" enctype="multipart/form-data" action="index.php?p='.$content.'"><ul>';
$msg.= '<li><span class="blue">'.$arrLinks[$content].'</span>';
$msg.= '<li><label>Week/Year:</label><input type="text" name="wkyr" maxlength=6 /> <span class="more">**mmyyyy</span></li>';
$msg.= '<li><label>PV:</label><label class="s4">GPV<input type="radio" name="pv" value="bhnpv" '.C_K.' /></label><label class="s4">PPV<input type="radio" name="pv" value="bhppv" /></label></li>';
$msg.= '<li><label>File:</label><input type="file" name="file" /><span class="bad"></span></li>';
$msg.= '<li><span class="note">'.$wkyr.'</span></li>';
$msg.= '<input type="submit" name="submit" class="btn" value="UPLOAD" /></ul></form>';
$msg.= $homebtn;

if(isset($_POST['submit'])&&$_POST['submit']=='UPLOAD') {
	$wk=substr($wkyr,0,2);
	$yr=substr($wkyr,2,4);
	$pv=isset($_POST['pv'])?$_POST['pv']:'bhppv';
	$file=$_FILES['file'];
	unset($_POST);
	unset($_FILES);
	$temp=$file['tmp_name'];
	if(isValid($file['type'])) {
		$msg.= getData($temp,$wk,$yr,$pv);
	}
}
ob_start();
echo $msg;
ob_end_flush();

function isValid($ft) {
	if($ft=='text/csv'||$ft=='application/csv'||$ft=='application/vnd.ms-excel') return true;
}

function getData($file,$wk,$yr,$pv) {
	$dat=fopen($file,'r');$r='';
	while($rw=fgets($dat)) {
		$id=trim($rw);//trim(str_replace('-','',$rw))
		$r.=getPV($id,$wk,$yr,$pv).'<br>';
	}return $r;
}

function getPV($id,$wk,$yr,$pv) {
	ini_set('max_execution_time',60);
	$con=SQLi('distributor');
	$qry="SELECT $pv FROM bohstp WHERE bhdid='$id' AND bhpmo=$wk AND bhpyr=$yr";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$dat=number_format($rw[$pv],2);
	return $dat;
	mysqli_close($con);
}
?>
