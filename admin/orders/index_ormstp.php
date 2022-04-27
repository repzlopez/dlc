<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$x='';$tbl=$content;
$all=isset($_GET['all'])?$_GET['all']:(isset($_SESSION['showall'])?$_SESSION['showall']:0);
$srt=isset($_GET['srt'])?$_GET['srt']:0;
$center=isset($_GET['c'])?$_GET['c']:null;
$err=isset($_SESSION['errmsg'])?$_SESSION['errmsg']:'';
$_SESSION['showall']=$all;
list($wk1,$yr,,$wed)=getDatWk(date('Y-m-d'));
list($wk,,$thu,)=getDatWk(($wk1>2?sprintf("%02d",$wk1-1):'01').$yr,1);

if($do==0){
	$_SESSION['ormstp']=$_SERVER['REQUEST_URI'];
	$sort=$srt?'omdid':'LENGTH(o.ominv) DESC,o.ominv DESC';
	$wer=$all?"WHERE o.ompyr='$yr' AND d.ompyr='$yr'".(sprintf("%02d",$wk1)=='01'?" OR (o.ompyr='".($yr-1)."' AND SUBSTRING(o.omodat,5,2)='12')":''):"WHERE (o.ompmo='".sprintf("%02d",$wk1)."' OR o.ompmo='".sprintf("%02d",$wk1-1)."') AND o.ompyr='$yr' AND UPPER(o.ominv)<>'VOID'";
	$wk0=sprintf("%02d",$wk1-1);

	$qry="SELECT o.ompmo,SUM(o.ompv)*25 oompv FROM ormstp o WHERE o.ompyr='$yr' AND o.ompmo BETWEEN $wk0 AND $wk1 GROUP BY o.ompmo";
	$con=SQLi('orders');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){ $r[$rw['ompmo']]=$rw['oompv']; }
	$curr=isset($r[$wk1])?number_format($r[$wk1],2):'NO DATA';
	$prev=isset($r[$wk0])?number_format($r[$wk0],2):'NO DATA';
	$approx="APPROX [ Week $wk1: ".$curr." ] [ Week $wk0: ".$prev." ]";
	$slots='';//'<a href="#" id="download" data-recalc="slots" class="btn recalc">RECALCULATE SLOTS</a>';

	$x.='<ul id="'.$content.'" class="list clear">';
	$x.='<li class="rt"><a href="#" id="download" data-recalc="bomstp" class="btn recalc">RECALCULATE BOMSTP</a>'.$slots.'<span id="recalcmsg" class="s5 rt"></span></li>';
	$x.='<li><label for="showall" class="s5"><input type="checkbox" id="showall" '.($all?C_K:'').' />SHOW ALL ORDERS</label><label for="sort_iv" class="s4"><input type="radio" name="sort" id="sort_iv" value=0 '.(!$srt?C_K:'').' />Sort by INVOICE</label><label for="sort_id" class="s5"><input type="radio" name="sort" id="sort_id" value=1 '.($srt?C_K:'').' />Sort by DIST ID</label>'.$approx.'</li>';
	$x.='<li class="hdr"><span class="s2">Week</span><span class="s2">Invoice</span><span class="s4">Distributor</span><span class="s5">Name</span><span class="s3 rt">RT Orders</span><span class="s3 rt">AS Orders</span><span class="s3 rt">Order Date</span></li>';

	$qry="SELECT o.omdid omdid,o.ominv oominv,d.ominv dominv,o.ompv oompv,d.ompv dompv,o.ompmo ompmo,o.omodat omodat,LOWER(CONCAT(dslnam,', ',dsfnam,' ',SUBSTRING(dsmnam,1,1),'.')) nam
		FROM ".DB."orders.ormstp o
		LEFT JOIN ".DB."distributor.ormstp d
			ON o.ominv=d.ominv
		LEFT JOIN ".DB."distributor.distributors s
			ON s.dsdid=o.omdid
		$wer
		ORDER BY ompmo DESC, $sort
		";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$x.='<li rel="'.$rw['oominv'].'" '.($rw['oompv']!=$rw['dompv']?'class="bad"':'').'>';
		$x.='<span class="s2"><a href="?p='.$content.'&do=2&i='.$rw['oominv'].'" class="s2">'.$rw['ompmo'].'</a></span>';
		$x.='<span class="s2"><a href="?p='.$content.'&do=2&i='.$rw['oominv'].'" class="s2">'.$rw['oominv'].'</a></span>';
		$x.='<span class="id s4">'.strtoupper($rw['omdid']).'</span>';
		$x.='<span class="id s5">'.ucwords($rw['nam']).'</span>';
		$x.='<span class="s3 rt">'.number_format($rw['oompv'],2).'</span>';
		$x.='<span class="s3 rt">'.number_format($rw['dompv'],2).'</span>';
		$x.='<span class="s3 rt">'.formatDate($rw['omodat'],'Ymd').'</span></li>';
	}mysqli_close($con);

	$x.='<li></li><li class="hdr blue">GOS PENDING</li>';
	$x.=loadCenter('gos',$wk1,$thu,$wed,$all,$yr);
	$x.='<li></li><li class="hdr blue">PCM PENDING</li>';
	$x.=loadCenter('pcm',$wk1,$thu,$wed,$all,$yr);
	$x.='</ul>';
}else{
	$x.='<li class="hdr blue">Sales</li>';
	$con=SQLi('orders');
	$rs=mysqli_query($con,"DESC ormstp");
	while($rw=mysqli_fetch_assoc($rs)) $$rw['Field']=null;

	if($do==1){
		$omodat=date('Ymd');$ompyr=$yr;$ompmo=$wk1;$ompov=0;$ompv=0;$pov=0;$pv=0;
		if($item!=''){
			$con=SQLi($center);
			$qry="SELECT * FROM tblorders WHERE refno='$item'";
			$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
			while($rw=mysqli_fetch_assoc($rs)) foreach($rw as $k=>$v){ $$k=$v;}
			$d=explode("~",($orders!=''?$orders:'||||'));
			foreach($d as $i){
				$e=explode("|",$i);
				$pov+=((float)$e[3]*$e[2]);
				$pv+=((float)$e[4]*$e[2]);
			}
			$omodat=$refdate;
			$omdid=$dsdid;
			$ompv=$pv;
			$ompov=$pov;
			mysqli_close($con);
			list($ompmo,$ompyr,,)=getDatWk(substr($omodat,0,4).'-'.substr($omodat,4,2).'-'.substr($omodat,-2));
		}
	}else{
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE ominv='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)) foreach($rw as $k=>$v){ $$k=$v;}
	}

	$x.='<li><span class="s2 lt">Date</span>: <input type="text" class="s2 rt req" id="omodat" name="omodat" value="'.$omodat.'" /><span class="s2"></span><span class="s1 lt">Year</span>: <input type="text" class="s1 ct req" name="ompyr" value="'.$ompyr.'" /><span class="s1"></span><span class="s1">Week</span>: <input type="text" class="s1 ct req" name="ompmo" value="'.sprintf("%02d",$ompmo).'"/></li>';
	$x.='<li><span class="s2 lt">Distributor</span>: <input type="text" class="s4 req" id="omdid" name="omdid" data-cod=2 value="'.$omdid.'" /> <span class="blue" id="omnam"></span></li>';
	$x.='<li><span class="s2 lt">Order#</span>: <input type="text" class="s2 rt" name="omord" value="'.$omord.'" /><span class="s2"></span><span class="s1">Invoice#</span>: <input type="text" class="s2 rt req" name="ominv" value="'.$ominv.'" /><span class="smaller">type "void" to cancel</span></li>';
	$x.='<li><span class="s2 lt">PPV</span>: <input type="text" class="s2 rt req" name="ompv" value="'.$ompv.'" /><span class="s2"></span><span class="s1">Amount</span>: <input type="text" class="s2 rt req" name="ompov" value="'.$ompov.'" /></li>';
	$x.='<li><span class="s2 lt">Package</span>: <label for="om40" class="s4"><input type="hidden" name="om40" value=0 /><input type="checkbox" id="om40" name="om40" value=1 '.($om40?C_K:'').' />40PV Package</label><label for="om100" class="s4"><input type="hidden" name="om100" value=0 /><input type="checkbox" id="om100" name="om100" value=1 '.($om100?C_K:'').' />100PV Package</label></li>';

	$x='<form id="realtime" method="post" action="post.php"><ul>'.$x;
	$x.='<br /><div><span class="rt" id="errmsg">'.$err.'</span><input type="hidden" name="do" value="'.$do.'" /><input type="hidden" name="oldpv" value="'.$ompv.'" /><input type="hidden" name="refno" value="'.$item.'" /><input type="submit" name="submit" class="s1 btn" value="SUBMIT" /><input type="button" id="cleartrans" class="s1 btn" value="CLEAR" /></div>';
	$x.='</ul></form>';
}echo $x;
unset($_SESSION['errmsg']);

function loadCenter($db,$wk,$thu,$wed,$all=0,$yr=''){
	$ad1=($all?'':",CONCAT(LEFT(refdate,4),'-',SUBSTRING(refdate,5,2),'-',RIGHT(refdate,2)) rdate");
	$ad2=($all?"AND LEFT(refdate,4)='".($yr)."' OR LEFT(refdate,6)='".($yr-1)."12'":"HAVING rdate BETWEEN '$thu' AND '$wed'");
	$qry="SELECT refno,refdate,dsdid $ad1 FROM tblorders WHERE invoice='' $ad2 ORDER BY refdate DESC";
	$con=SQLi($db);$r='';
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		if($all) list($wk,,,)=getDatWk(substr($rw['refdate'],0,4).'-'.substr($rw['refdate'],4,2).'-'.substr($rw['refdate'],-2));
		$href='?p=ormstp&do=1&c='.$db.'&i='.$rw['refno'];
		$r.='<li rel="'.$rw['refno'].'"><span class="s2"><a href="'.$href.'" class="s2">'.sprintf("%02d",$wk).'</a></span>';
		$r.='<span class="s2"><a href="'.$href.'" class="s2">PENDING</a></span>';
		$r.='<span class="id s4">'.$rw['dsdid'].'</span>';
		$r.='<span class="id s5">'.ucwords(strtolower(getName($rw['dsdid'],'lfm'))).'</span>';
		$r.='<span class="s3 rt">-</span><span class="s3 rt">-</span>';
		$r.='<span class="s3 rt">'.formatDate($rw['refdate'],'Ymd').'</span></li>';
	}mysqli_close($con);
	return $r;
}
?>
