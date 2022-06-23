<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$x='';$tbl=$content;
$all=isset($_GET['all'])?$_GET['all']:0;

$x.='<ul id="'.$content.'" class="list clear">';
$x.='<li class="hdr"><span class="s4">Reference</span>';
$x.='<span class="s3">Order Date</span>';
$x.='<span class="s4">Distributor</span>';
$x.='<span class="s5">Name</span>';
$x.='<span class="s4">Remittance</span>';
$x.='<span class="s2">Invoice</span>';
list($wk1,$yr,,$wed)=getDatWk(date('Y-m-d'));
list($wk,,$thu,)=getDatWk(($wk1>2?sprintf("%02d",$wk1-1):'01').$yr,1);
$ad1=($all?'':",CONCAT(SUBSTRING(refdate,1,4),'-',SUBSTRING(refdate,5,2),'-',SUBSTRING(refdate,-2)) rdate");
$ad2=($all?'':"HAVING rdate BETWEEN '$thu' AND '$wed'");
$qry="SELECT * $ad1
	FROM tblorders
	WHERE status<9
	$ad2
	ORDER BY refno DESC";
//echo "$qry<br>";
$con=SQLi('pcm');
$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
while($rw=mysqli_fetch_assoc($rs)) {
	foreach($rw as $k=>$v) { $$k=$v;}
	$x.='<li><a '.(IS_GLOB||IS_PCM?'href="/pcm/remit/?p=remit&do=1&i='.$refno.'"':'').' class="s4" target="_blank">'.$refno.'</a>';
	$x.='<span class="s3">'.formatDate($refdate,'Ymd').'</span>';
	$x.='<span class="s4">'.$dsdid.'</span>';
	$x.='<span class="s5">'.getName(str_replace('*','',$dsdid),'lfm').'</span>';
	$x.='<a '.(IS_GLOB||IS_PCM?'href="/pcm/remit/?tab=remittance&sum='.$remitid.'"':'').' class="s4" target="_blank">'.$remitid.'</a>';
	$x.='<span class="s2">'.$invoice.'</span>';
	$x.='</li>';
}mysqli_close($con);
$x.='</ul>';
echo $x;
unset($_SESSION['errmsg']);
?>
