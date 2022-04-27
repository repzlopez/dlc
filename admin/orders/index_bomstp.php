<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
global $bmppv,$bmnpv,$bmelev;
$x='';$tbl=$content;
$all=isset($_GET['all'])?$_GET['all']:1;//1

// include('updateBomstp.php');//rem
// recalc(0,WEEK,WKYR);//rem

list($wk,$yr,$thu,$wed)=getDatWk(date('Y-m-d'));
$x.='<ul id="'.$content.'" class="list clear">';
$x.='<li><a href="#" id="download" data-recalc="bomstp" class="btn recalc">RECALCULATE</a><span id="recalcmsg"></span></li>';
$x.='<li class="hdr"><span class="s3">Distributor</span><span class="s4">Name</span><span class="s2 rt dark">RT PPV</span>';
if($all) $x.='<span class="s2 rt good">AS PPV</span>';
$x.='<span class="s2 rt dark">RT GPV</span>';
if($all) $x.='<span class="s2 rt good">AS GPV</span>';
$x.='<span class="s2 rt dark">RT TPV</span>';
if($all) $x.='<span class="s2 rt good">AS TPV</span>';
$x.='<span class="s1 ct dark">RT Lvl</span>';
if($all) $x.='<span class="s1 ct good">AS Lvl</span></li>';

$con=SQLi('orders');
$qry="SELECT o.bmdid bmdid,o.bmppv obmppv,o.bmnpv obmnpv,FORMAT(o.bmppv+o.bmnpv,2) otpv,o.bmelev obmelev";
$qry.=$all?",d.bmppv dbmppv,d.bmnpv dbmnpv,ROUND(d.bmppv+d.bmnpv,2) dtpv,d.bmelev dbmelev":'';
$join=$all?"LEFT JOIN ".DB."distributor.$content d ON o.bmdid=d.bmdid":'';
$qry.=" FROM ".DB."orders.$content o $join ORDER BY o.bmdid";

$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
while($rw=mysqli_fetch_assoc($rs)){
	foreach($rw as $k=>$v){ $$k=$v;}
	$x.='<li rel="'.$bmdid.'" class="'.($all?($otpv!=number_format($dbmppv+$dbmnpv,2)?'bad':''):'').' '.($obmelev>4||($all&&$dbmelev>4)?'breakaway':'').'">';
	$x.='<span class="s3">'.$bmdid.'</span>';
	$x.='<span class="s4">'.getName($bmdid,'lfm').'</span>';
	$x.='<span class="s2 rt dark '.($obmppv!=$dbmppv?'bad':'').'">'.number_format($obmppv,2).'</span>';
	if($all) $x.='<span class="s2 rt good '.($obmppv!=$dbmppv?'bad':'').'">'.number_format($dbmppv,2).'</span>';
	$x.='<span class="s2 rt dark '.($obmnpv!=$dbmnpv?'bad':'').'">'.number_format($obmnpv,2).'</span>';
	if($all) $x.='<span class="s2 rt good '.($obmnpv!=$dbmnpv?'bad':'').'">'.number_format($dbmnpv,2).'</span>';
	$x.='<span class="s2 rt dark">'.$otpv.'</span>';
	if($all) $x.='<span class="s2 rt good">'.number_format($dbmppv+$dbmnpv,2).'</span>';
	$x.='<span class="s1 ct dark">'.getPercent($obmelev).'</span>';
	if($all) $x.='<span class="s1 ct good">'.getPercent($dbmelev).'</span></li>';
}mysqli_close($con);
$x.='</ul>';
echo $x;
unset($_SESSION['errmsg']);
?>
