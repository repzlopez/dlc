<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();

testScope("global|distri", DLC_ADMIN);

$x  = '<form id="distrilookup" class="totop" method="post" action="'.$_SERVER['REQUEST_URI'].'"><ul>';
$x .= '<li><span class="blue">Distributor Data</span>';
$x .= '<li><span class="s3 ct">LOOKUP:</span><input type="text" name="find" class="s5 txt" id="distid" /> <span class="small more">** dist.id, name</span></li></ul></form>';

if( isset($_GET['id']) ) {
	$r  = '';
	$id = trim_escape($_GET['id']);
	unset($_GET);

	$con = SQLi('distributor');
	$rs  = mysqli_query($con,"SELECT bhelev FROM bohstp WHERE bhdid='$id' AND bhelev<>'' ORDER BY bhelev DESC LIMIT 1") or die(mysqli_error($con));
	$rw  = mysqli_fetch_array($rs);
	$lev = (int)$rw['bhelev'];
	$lvl = getPercent($lev);

	$rs = mysqli_query($con,"SELECT * FROM distributors WHERE dsdid='$id'") or die(mysqli_error($con));
	while($rw=mysqli_fetch_array($rs)) {
		for($i=0;$i<mysqli_num_fields($rs);$i++) {
			$rs1 = mysqli_fetch_field_direct($rs,$i)->name;
			$dat = ($rs1=='dsbrth')?sprintf('%08d',$rw[$i]):$rw[$i];
			$r  .= '<li><span class="s3">'.$rs1.'</span><input type="text" name="'.$rs1.'" class="txt" value="'.$dat.'" /> '.($rs1=='dssid'?'<span class="s3"></span> '.strtoupper(getName($dat,'lfm')):'').'</li>';
		}
		// $r.='<li class="breakoff"></li>';
	}

	mysqli_close($con);
	$x .= '<form method="post" id="dsmstp" action="/admin/update.php?t='.$content.'"><ul>';
	$x .= '<li class="blue">Distributor Details</li><li><span class="s3">Highest Level</span>Level <span class="blue">'.$lev.'</span> or <span class="blue">'.$lvl.'</span> at current system</li>'.$r;
	$x .= '<input type="hidden" name="do" value="2,'.(isset($id)?$id:'').'" /><input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}

ob_start();
echo $x;
ob_end_flush();
?>
