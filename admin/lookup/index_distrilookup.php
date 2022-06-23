<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$x='<form id="distrilookup" class="totop" method="post" action="'.$_SERVER['REQUEST_URI'].'"><ul>';
$x.='<li><span class="blue">Distributor Lookup</span>';
$x.='<li><span class="s3 ct">LOOKUP:</span><input type="text" name="find" class="s5 txt" id="distid" /> <span class="small more">** dist.id, name</span></li></ul></form>';
if(isset($_GET['id'])) {
	$id=trim_escape($_GET['id']);
	unset($_GET);
	$con=SQLi('distributor');
	$rs=mysqli_query($con,"SELECT bhelev FROM bohstp WHERE bhdid LIKE '%$id%' AND bhelev<>'' ORDER BY bhelev DESC LIMIT 1") or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$lev=(int)$rw['bhelev'];
	$lvl=getPercent($lev);
	$data='';
	$rs=mysqli_query($con,"SELECT * FROM distributors WHERE dsdid LIKE '%$id%'") or die(mysqli_error($con));
	while($rw=mysqli_fetch_array($rs)) {
		for($i=0;$i<mysqli_num_fields($rs);$i++) {
			$rs1=mysqli_fetch_field_direct($rs,$i)->name;
			$dat=($rs1=='dsbrth')?sprintf('%08d',$rw[$i]):$rw[$i];
			$data.='<li><span class="s3">'.$rs1.'</span>'.$dat.' '.($rs1=='dssid'?strtoupper(getName($dat,'lfm')):'').'</li>';
		}
		$data.='<li class="breakoff"></li>';
	}mysqli_close($con);
	$x.='<form><ul><li class="blue">Distributor Details</li><li><span class="s3">Highest Level</span>Level <span class="blue">'.$lev.'</span> or <span class="blue">'.$lvl.'</span> at current system</li>'.$data.'</ul></form>';
}
ob_start();
echo $x;
ob_end_flush();
?>
