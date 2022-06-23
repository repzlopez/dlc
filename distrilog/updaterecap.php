<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!defined('INCLUDE_CHECK')) {
	define('INCLUDE_CHECK',1);
	require('../admin/setup.php');
}
$_SESSION['u_site']=isset($_SESSION['u_site'])?$_SESSION['u_site']:null;
$_SESSION['recap_data']=isset($_SESSION['recap_data'])?$_SESSION['recap_data']:null;
$_SESSION['recap_grpbonus']='';
$_SESSION['recap_ppvgpv']='';
$_SESSION['recap_rebate']='';
$_SESSION['recap_ndpv']='';
$_SESSION['recap_ppv']='';
$_SESSION['recap_tax']='';
$_SESSION['recap_pct']='';
$_SESSION['selmonyer']='';
$_SESSION['recap0']='';

$_SESSION['recap_data']=getRecap();

function getRecap() {
	$dist_id	=$_SESSION['u_site'];
	$defmonyer	=getDefaultMonYear(date('Y',time()));
	$selmonyer	=isset($_GET['w'])?$_GET['w']:WKYR;//$defmonyer
	$selmonyer	=isset($_SESSION['multirecap'])&&$_SESSION['multirecap']?(isset($_SESSION['recapdate'])?$_SESSION['recapdate']:null):$selmonyer;
	$selmon		=substr($selmonyer,0,2);
	$selyer		=substr($selmonyer,-4);
	$_SESSION['selmonyer']=$selmonyer;
	list(,,$fst,$lst)=getDatWk($selmonyer,1,1);

	$rd ='<div class="recap recaphead">PHILIPPINES - '.DLC_FULL.'<br />';
	$rd.=date('M d',strtotime($fst)).' - '.date('M d',strtotime($lst))." $selyer (Week ".$selmon.")</div>";
	$rd.=getDistributor($dist_id,$selmon,$selyer);
	$rd.='<ul id="data_list" class="recap">';
	$rd.=getData($dist_id,(int)$selmon,$selyer);
	$rd.='</ul>';
	$rd.=getTotals();
	return $rd;
}

function getDistributor($id,$wk,$yr) {
	$qry="
		SELECT dsdid,dsstrt,dsbarn,dscity,dsprov
		FROM distributors
		WHERE dsdid='$id'
	";
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$iid=str_replace('--','-',implode("-",str_split($id,3)));
	list($now,$bak)=getBonusRec($id,(int)$wk,$yr);
	$dat ='<ul class="recap">';
	$dat.='<li>'.$iid.'</li>';
	$dat.='<li>'.strtoupper(getName($_SESSION['u_site'],'lfm')).'</li>';
	$dat.='<li>'.$rw['dsstrt'].'</li>';
	$dat.='<li>'.$rw['dsbarn'].'</li>';
	$dat.='<li>'.$rw['dscity'].'</li>';
	$dat.='<li>'.$rw['dsprov'].'</li>';
	$dat.='<li><label class="s4">Bonus %</label>';
	$dat.='<label class="s4">Last Week :</label> <span class="s3">'.number_format($bak,1).' %</span>';
	$dat.='<label class="s4">This Week :</label> <span class="s3">'.number_format($now,1).' %</span>';
	$dat.='</li></ul>';
	return $dat;
}

function getData($sponsor,$wk,$yr) {
	$_SESSION['recap_grpbonus']=0;
	$_SESSION['recap_ppvgpv']=0;
	$_SESSION['recap_rebate']=0;
	$_SESSION['recap_ndpv']=0;
	$_SESSION['recap_ppv']=0;
	$_SESSION['recap_tax']=0;

	$dat ='<li class="hdr">';
	$dat.='<span class="s8">- - - D I S T R I B U T O R - - -</span>';
	$dat.='<span class="s3 rt">PPV</span>';
	$dat.='<span class="s3 rt">POV</span>';
	$dat.='<span class="s2 rt">%</span>';
	$dat.='<span class="s3 rt">BONUS</span></li>';

	$qry="
		SELECT *
		FROM bodstp
		WHERE bdsid='$sponsor'
		AND bdyy='$yr'
		AND bdmm='$wk'
	";
	$dat.=getList($qry." AND bdtype='B' AND bddids='$sponsor'");
	$dat.=getList($qry." AND bdtype='B' AND bddids!='$sponsor'");
	$dat.=getList($qry." AND bdtype='L'",'L');
	$dat.=getList($qry." AND bdtype<>'B' AND bdtype<>'L' ORDER BY bdtype ".($yr>=2016&&$wk>=14?'DESC':'DESC'),'W');
	return $dat;
}

function getList($qry,$hdr=null) {
	$dat='';$n=0;
	$namelimit=20;
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$num=mysqli_num_rows($rs);
	if($hdr=='L'&&$num>0) $dat.='<li><span class="s5 rt">Leadership Bonus</span></li>';
	if($hdr=='W'&&$num>0) $dat.='<li><span class="s5 rt">Weekly Profit Sharing</span></li>';
	if(mysqli_num_rows($rs)>0) {
		while($rw=mysqli_fetch_assoc($rs)) {
			if(isset($_SESSION['recap_tax'])&&$_SESSION['recap_tax']=='') $_SESSION['recap_tax']=$rw['bdctxp'];
			$id=str_replace('--','-',implode("-",str_split($rw['bddids'],3)));
			$uname=getName($rw['bddids'],'lfm');
			if(strlen($uname)>$namelimit) $uname=substr($uname,0,$namelimit);
			$dat.='<li><span class="s4">'.($hdr=='W'?'WPS L'.$n++:$id).'</span>';
			$dat.='<span class="s6">'.$uname.'</span>';
			$dat.='<span class="s3 rt">'.number_format($rw['bdppv'],2).'</span>';
			$dat.='<span class="s3 rt">'.number_format($rw['bdpov'],2).'</span>';
			$dat.='<span class="s2 rt">'.number_format($rw['bdbpct']*100,1).'</span>';
			$dat.='<span class="s3 rt">'.number_format($rw['bdbamt'],2).'</span></li>';
			if($rw['bddids']==strtoupper($_SESSION['u_site'])&&!isset($hdr)) {
				$_SESSION['recap_ppv']=$rw['bdppv'];
				$_SESSION['recap_rebate']=$rw['bdbamt'];
				$_SESSION['recap_pct']=$rw['bdbpct'];
				$dat.=getOrders($rw['bddids'],$rw['bdyy'],$rw['bdmm']);
			}else{
				$_SESSION['recap_grpbonus']+=$rw['bdbamt'];
				$_SESSION['recap_ppvgpv']+=$rw['bdppv'];
				if($rw['bdbpct']==$_SESSION['recap_pct']) {
					$_SESSION['recap_ndpv']+=$rw['bdppv'];
				}
			}
		}
	}
	return $dat;
}

function getTotals() {
	$ttp  =array(''=>'0','Domestic '=>'10','Domestic'=>'15','Foreigner'=>'25') ;
	$tax  =$_SESSION['recap_tax'];
	$rppv =$_SESSION['recap_ppv'];
	$preb =$_SESSION['recap_rebate'];
	$gbon =$_SESSION['recap_grpbonus'];
	$ctax =($tax>'0')?number_format($gbon*($tax/100),2,'.',''):0;
	$txtp =array_search($tax,$ttp);
	$pgpv =$rppv+$_SESSION['recap_ppvgpv'];
	$bnet =$preb+$gbon-$ctax;
	$ndpv =$_SESSION['recap_ndpv'];
	$dat ='<ul class="recap recaptotals">';
	$dat.='<li><span class="s5">Personal Rebate :</span><span class="s5 rt">'.number_format((float)$preb,2).'</span></li>';
	$dat.='<li><span class="s5">Group Bonus :</span><span class="s5 rt">'.number_format((float)$gbon,2).'</span></li>';
	$dat.='<li><span class="s5">'.$tax.'% '.$txtp.' Tax :</span><span class="s5 rt">'.number_format($ctax,2).'</span></li>';
	$dat.='<li><span class="s5">Net Bonus :</span><span class="s5 rt">'.number_format($bnet,2).'</span></li>';
	$dat.='</ul>';
	$dat.='<div class="recap"><span class="s4">PV - Personal :</span><span class="s3">'.number_format((float)$rppv,2).'</span>';
	$dat.='<span class="s2">ND :</span><span class="s3">'.number_format((float)$ndpv,2).'</span>';
	$dat.='<span class="s4">PPV + GPV :</span><span class="s3">'.number_format($pgpv,2).'</span></div>';
	$dat.='<div class="ct">'.str_pad('*',138,'*').'</div>';
	return $dat;
}

function getOrders($id,$yr,$wk) {
	$dat='';
	$qry="
		SELECT ominv,omodat
		FROM ormstp
		WHERE omdid='$id'
		AND ompyr='$yr'
		AND ompmo='$wk'
		ORDER BY ominv
	";
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		while($rw=mysqli_fetch_assoc($rs)) {
			$date=str_split($rw['omodat'],2);
			$date=$date[0].$date[1].'/'.$date[2].'/'.$date[3];
			$dat.='<li><span class="s4"></span><span class="s3 rt">'.$rw['ominv'].'</span><span class="s3 rt">'.$date.'</span></li>';
		}return $dat;
	}
}

function getBonusRec($id,$wk,$yr) {
	$qry="
		SELECT bdbpct
		FROM bodstp
		WHERE bdsid='$id'
		AND bddid='$id'
		AND bddids='$id'
		AND bdmm=
	";
	$con=SQLi('distributor');
	$rs1=mysqli_query($con,$qry."'$wk' AND bdyy='$yr'") or die(mysqli_error($con));
	$rw1=mysqli_fetch_array($rs1);
	$now=$rw1['bdbpct']*100;

	$rs2=mysqli_query($con,$qry.(($wk>1)?"'".($wk-1)."' AND bdyy='$yr'":"'52' AND bdyy='".($yr-1)."'")) or die(mysqli_error($con));
	$rw2=mysqli_fetch_array($rs2);
	$bak=$rw2['bdbpct']*100;
	return array($now,$bak);
}

function getDefaultMonYear($yr) {
	$qry="
		SELECT DISTINCT bdmm,bdyy
		FROM bodstp
		ORDER BY bdyy DESC,bdmm DESC
	";
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		$rw=mysqli_fetch_array($rs);
		return sprintf('%02d',$rw['bdmm']).$rw['bdyy'];
	}
}
?>
