<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../fetch.php');
if(!ISIN_ADMIN||!testScope("global|data")){
	reloadTo(DLC_ADMIN);exit;
}else{
	$id=isset($_GET['id'])?$_GET['id']:null;		//id
	$yr=isset($_GET['yr'])?$_GET['yr']:null;		//yr
	$wk=isset($_GET['wk'])?$_GET['wk']:null;		//wk

	$xml='<?xml version="1.0" encoding="UTF-8"?><fields xmlns:xfdf="http://ns.adobe.com/xfdf-transition/">'."\n";
	$xml.="<dsdid>$id</dsdid><dsnam>".getName($id,'lfm')."</dsnam><dscon></dscon>\n";
	$xml.="<psyr>$yr</psyr><pslev></pslev><spslev></spslev>\n";
	$xml.=weeks($id,$yr,$wk);
	$xml.='</fields>';

	$title='| PS / BBB Confirmation';
	ob_start();
	require('../../head.php');
	echo '<pre>'.htmlentities($xml).'</pre>';
	require('../../foot.php');
	ob_end_flush();
	// file_put_contents($id.'.xml',$xml);
}

function weeks($id,$yr,$wk){
	$x='';$d=13;$wk+=$d-1;
	while($d>0){
		$d=sprintf("%02d",$d);
		list($ppv,$gpv,$tpv,$lev,$mgr)=getData($id,$yr,$wk);
		$x.="<wk$d>$wk</wk$d><ppv$d>$ppv</ppv$d><gpv$d>$gpv</gpv$d><tpv$d>$tpv</tpv$d><lev$d>$lev</lev$d><mgr$d>$mgr</mgr$d>\n";
		$d--;$wk--;
	}return $x;
}

function getData($id,$yr,$wk){
	$con=SQLi('distributor');
	$qry="SELECT *
		FROM bohstp
		WHERE bhdid='$id'
		AND bhpyr=$yr
		AND bhpmo=$wk
	";
	// echo $qry."<br>";
	$empty=array('0.00','0.00','0.00','0%','0');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	if(mysqli_num_rows($rs)==0){ return $empty; }
	else{
		$rw=mysqli_fetch_array($rs);
		$ppv=number_format($rw['bhppv'],2,'.','');
		$npv=number_format($rw['bhnpv'],2,'.','');
		$gpv=number_format($npv-$ppv,2,'.','');
		$lev=getPercent($rw['bhelev']);
		$mgr=$rw['bhqumr'];
		// if(isEddyOrRick($rw['bhdid'])&&($ppv==0&&$mgr<3)){ return $empty; }
		// else
		return array($ppv,$gpv,$npv,$lev,$mgr);
	}mysqli_close($con);
}
?>
