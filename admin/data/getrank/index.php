<?php session_set_cookie_params(0);
if(!isset($_SESSION)) session_start();
define('INCLUDE_CHECK',1);
require('../../fetch.php');
if(!ISIN_ADMIN||!testScope("global|data")) {
	reloadTo(DLC_ADMIN);exit;
}else{
	date_default_timezone_set('Asia/Manila');
	global $set;
	$set=array('ppv'=>'PPV','npv'=>'TPV','tamt'=>'VB','pov'=>'POV');
	$do=isset($_GET['do'])?$_GET['do']:'';		//aabbyyyy REQUIRED
	$st=isset($_GET['set'])?$_GET['set']:'*';
	$aa=substr($do,0,2);
	$bb=substr($do,2,2);
	$yr=substr($do,-4);

	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=All_'.($st=='*'?'DATA':$set[$st]).'_for_Weeks_'.$aa.'-'.$bb.'.csv');
	if($st=='*') {
		foreach($set as $x=>$y) {
			$st=$x;
			$csv=listPV($aa,$bb,$yr,$st);
			print $csv;
		}
	}else{
		$csv=listPV($aa,$bb,$yr,$st);
		print $csv;
	}
}

function listPV($aa,$bb,$yr,$st) {
	global $set;$ttl=$set[$st];
	$isPOV=($st=='pov');
	$st=($isPOV?'ppv':$st);
	$rnd='';$hdr='';$t=1;
	for($n=(int)$aa;$n<=(int)$bb;$n++) {
		$rnd.="ROUND((SELECT SUM(bh".$st.") FROM bohstp WHERE bhdid=ID AND bhpmo='".sprintf("%02d",$n)."' AND bhpyr='$yr'),2) wk".sprintf("%02d",$n).",";
		$hdr.='"Week '.sprintf("%02d",$n).'",';
	}
	$qry="
		SELECT DISTINCT bhdid ID,dsdid,dslnam,dsfnam,dsmnam,dssetd,$rnd
			ROUND((SELECT SUM(bh".$st.") FROM bohstp WHERE bhdid=ID AND bhpmo BETWEEN '$aa' AND '$bb' AND bhpyr='$yr'),2) TOTAL
		FROM bohstp,distributors
		WHERE dsdid=bhdid
		AND bhpmo BETWEEN '$aa' AND '$bb'
		AND bhpyr='$yr'
		ORDER BY TOTAL DESC
	";
	$str="All $ttl [ $aa-$bb $yr]\n";
	$str.='"ID#","MGR","SETUP","#","NAME","TENURE","TOTAL","AVE '.$yr.'","AVE Q1","AVE Q2","AVE Q3","AVE Q4",'.$hdr."\n";
	ini_set('max_execution_time',300);
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		$c=0;$s='';
		$q1=0;$q2=0;$q3=0;$q4=0;
		$d1=0;$d2=0;$d3=0;$d4=0;
		$id=$rw['ID'];
		$tt=$rw['TOTAL']*($isPOV?25:1);
		$dd=$rw['dssetd'];
		$nm=$rw['dslnam'].', '.$rw['dsfnam'].' '.substr($rw['dsmnam'],0,1).'.';
		for($n=(int)$aa;$n<=(int)$bb;$n++) {
			$dat=$rw['wk'.sprintf("%02d",$n)]*($isPOV?25:1);
			$s.='"'.($dat).'",';
			switch($n) {
				case $n<=13:$q1+=$dat;$d1++;break;
				case $n<=26:$q2+=$dat;$d2++;break;
				case $n<=39:$q3+=$dat;$d3++;break;
				case $n<=52:$q4+=$dat;$d4++;break;
			}$c++;
		}

		$str.='"'.$id.'","",';
		$str.='"'.substr($dd,4,2).'/'.substr($dd,-2).'/'.substr($dd,0,4).'",';
		$str.='"'.$t++.'",';
		$str.='"'.$nm.'","",';
		$str.='"'.$tt.'",';
		$str.='"'.number_format($tt/$c,2).'",';
		$str.='"'.number_format(($d1>0?$q1/$d1:0),2).'",';
		$str.='"'.number_format(($d2>0?$q2/$d2:0),2).'",';
		$str.='"'.number_format(($d3>0?$q3/$d3:0),2).'",';
		$str.='"'.number_format(($d4>0?$q4/$d4:0),2).'",';
		$str.=$s."\n";
	}
	$str.="\n\n";
	return $str;
}
?>