<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
global $qlf;
$con=SQLi('distributor');
$h='<li class="hdr"><span class="s4 lt">ID</span><span class="s5">Name</span><span class="s6">Breakaway Manager</span><span class="s2 rt">Total PV</span></li>';
$x='<div class="rt"><a>Select Cut-off</a> <select id="smps50">'.getDrop($do).'</select><br><br></div>';
$x.='<ul id="'.$content.'" class="list">'.$h;

$y=substr($do,-4);$m=substr($do,0,2);

$qry="
	SELECT bhdid,bhnpv,bhqumr,CONCAT(d.dsfnam,' ',d.dslnam) nam
	FROM bohstp h
	LEFT JOIN ".DB."distributor.distributors d ON d.dsdid=bhdid
	WHERE bhpyr=$y AND bhpmo=$m
	AND bhdid<>'".EDDY."' AND bhdid<>'".RICK."'
	AND (bhnpv>=".$_SESSION['minpv'][5]." OR (bhnpv>=".$_SESSION['minpv'][4]." AND bhqumr=1))
	ORDER BY bhdid DESC";

$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
if(mysqli_num_rows($rs)>0){
	while($rw=mysqli_fetch_assoc($rs)){
		foreach($rw as $k=>$v) $$k=$v;
		if($bhnpv>=$_SESSION['minpv'][5]){ $mgr[$bhdid]=$nam;}
		else $qlf[$bhdid]=array($nam,null,$bhnpv);
	}

	if(!empty($qlf)){
		foreach($qlf as $k=>$v){
			foreach($mgr as $a=>$b){
				getQmr($k,$a,$a);
			}
			$x.='<li><span class="s4">'.$k.'</span>';
			$x.='<span class="s5">'.$qlf[$k][0].'</span>';
			$x.='<span class="s6">'.$qlf[$k][1].' '.$mgr[$qlf[$k][1]].'</span>';
			$x.='<span class="s2 rt">'.$qlf[$k][2].'</span></li>';
		}
	}
}
$x.='</ul>';
echo $x;

function getQmr($id,$f,$m){
	global $qlf;
	if($m!=EDDY){
		$con=SQLi('distributor');
		$rs=mysqli_query($con,"SELECT dssid,CONCAT(dsfnam,' ',dslnam) nam FROM distributors WHERE dsdid='$m'") or die(mysqli_error($con));
		if(mysqli_num_rows($rs)>0){
			$rw=mysqli_fetch_assoc($rs);
			foreach($rw as $k=>$v) $$k=$v;
			if($dssid==$id) $qlf[$id][1]=$f;
			else getQmr($id,$f,$dssid);
		}else getQmr($id,$f,$dssid);
	}
}

function getDrop($do=''){$n='';
	$con=SQLi('distributor');
	$qry="
		SELECT DISTINCT CONCAT(bhpmo,bhpyr) my
		FROM bohstp
		WHERE bhpyr>=2019
	";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		foreach($rw as $k=>$v) $$k=$v;
		$my=sprintf("%06d",$my);
		$n.='<option '.($my==$do?SELECTED:'').'>'.$my.'</option>';
	}return $n;
}
?>
