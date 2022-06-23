<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!defined('INCLUDE_CHECK')) {
	define('INCLUDE_CHECK',1);
	require('../admin/setup.php');
}
global $data,$isNDPV;$data='';

if(isset($_POST)) {$x='';
	$con=SQLi('distributor');
	foreach($_POST as $k=>$v) $$k=trim_escape($v);
	unset($_POST);

	if(isset($find)) {
		$src=strtolower($find);
		$_SESSION['src_found']=0;
		$_SESSION['src_result']='';

		if($src!=='') findDist(DIST_ID,$src,0);
		$x.='<tr><th colspan="4" class="lt">'.$_SESSION['src_found'].' result(s) found: <strong>'.$src.'</strong></th></tr>';
		$x.='<tr><th>Dist. ID</th><th>Name</th><th>Address</th><th>Phone</th></tr>';
		$x.=$data;
	}elseif(isset($collapse)) {
		$l=explode('|',$collapse);
		switch($tab) {
			case 'pv':getPV($l[0],$l[1],$l[1]+1);
				// $x.=$data;
				break;
			case 'downline':getDistList($l[0],$l[1],$l[1]+1,1);$x.=$data;break;
		}
	}echo $x;
}

function getDistList($sponsor,$lvl,$max,$col=false) {
	global $data;
	if($lvl>=$max) {}
	else{
		$con=SQLi('distributor');
		$qry="SELECT * FROM distributors WHERE dssid='$sponsor' ORDER BY dssetd";
		$_SESSION['total_downlines']++;
		$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
		if(mysqli_num_rows($rs)==0) {
			if($lvl==0) $data.='<li class="nodata more ct">No data found</li>';
		}else{
			if($lvl==0) $data.='<ul><li class="hdr"><span class="s0"></span><span class="w1">Dist. ID</span> <span class="w2">Name</span> <span class="s6">Address</span> <span class="s3 ct">Phone</span> <span class="s3 ct">Joined</span></li>';
			while($rw=mysqli_fetch_assoc($rs)) {
				if(strpos($rw['dslnam'],'**TERMINATED**')===false) {
					$did=$rw['dsdid'];
					$dname=$rw['dsfnam'].' '.(($rw['dsmnam']!='')?substr($rw['dsmnam'],0,1).'.':'').' '.$rw['dslnam'];
					$setup=substr($rw['dssetd'],4,2).'.'.substr($rw['dssetd'],6,2).'.'.substr($rw['dssetd'],0,4);

					$data.='</ul>'."\n\n".'<ul>';	/* if($lvl<$lastlvl) <li><ul> */
					$data.='<li class="l'.($lvl+1).' '.(($lvl==0)?'gen1':'').'" rel="'."$did|".($lvl+1).'"><span class="s0 '.($lvl+1<$max?'">':'updn">+').'</span> <span class="w1">'.$did.'</span><span class="w2">'.($lvl+1).'] <span'.(($lvl==0)?' class="gen1"':'').'>'.$dname.'</span></span><span class="s6">'.$rw['dsstrt'].' '.$rw['dsbarn'].' '.$rw['dscity'].' '.$rw['dsprov'].'</span><span class="s3 rt">'.$rw['dsmph'].'</span><span class="s3 rt">'.$setup.'</span></li>';
					if(!$col) getDistList($did,$lvl+1,$max);
				}
			}
		}
	}
}

function getPV($sponsor,$lvl,$max) {
	global $data,$isNDPV;
//	ob_start();

	// $hav=RTOR?'':'';//HAVING tpv>0	HAVING bmppv>0 OR bmnpv>0
	if($lvl>=$max) {}
	else{
		$con=SQLi(RTDB);
		$qry="
			SELECT dsdid,dsfnam,dsmnam,dslnam,bmelev,bmppv,bmnpv,ROUND(bmppv+bmnpv,2) tpv
			FROM ".DB."distributor.distributors
			LEFT JOIN ".DB.RTDB.".bomstp
				ON bmdid=dsdid
			WHERE dssid='$sponsor'
			".BMPMO."
		";
// echo $qry;
		// ini_set('memory_limit','-1');
		set_time_limit(0);
		// ignore_user_abort(1);
		$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
		if(mysqli_num_rows($rs)==0&&!RTOR) {
			// isNoPVandNextIsMgr($sponsor,$lvl);
			// if($isNDPV!='') { $x.=$isNDPV; }
			// else
			if($lvl==0) echo '<li class="nodata more ct">No data found</li>';
		}else{
			if($lvl==0) echo '<li class="hdr"><span class="updn s0"> </span><span class="w1">Dist. ID</span><span class="w2">Name</span><span class="s1 ct">Level</span><span class="w1 rt">Personal PV</span><span class="w1 rt">Group PV</span><span class="w1 rt">Total PV</span></li>';
			if(mysqli_num_rows($rs)>0) {
				while($rw=mysqli_fetch_assoc($rs)) {
					$z='';
					$did=$rw['dsdid'];
					$ppv=$rw['bmppv'];
					$npv=$rw['bmnpv'];
					$lev=$rw['bmelev'];
					$tpv=$rw['tpv'];
					if($tpv==0&&$lev<1) {}
					else{
						$pct=getPercent($lev,date('Y'),WEEK);
						$dname=$rw['dsfnam'].' '.(($rw['dsmnam']!='')?substr($rw['dsmnam'],0,1).'.':'').' '.$rw['dslnam'];
						$isbreakaway=(isMgr($ppv,$lev)&&$tpv>=$_SESSION['minpv'][5])?' class="breakaway"':'';

						$z.='<li'.$isbreakaway.' class="l'.($lvl+1).' '.(($lvl==0)?'gen1':'').'" rel="'."$did|".($lvl+1).'">';
						$z.='<span class="s0 '.($lvl+1<$max?'">':'updn">+').'</span> ';
						$z.='<span class="w1">'.$did.'</span>';
						$z.='<span class="w2">'.($lvl+1).'] <span'.(($lvl==0)?' class="gen1"':'').'>'.$dname.'</span></span>';
						$z.='<span class="s1 ct">'.$pct.'</span>';
						$z.='<span class="w1 rt">'.number_format($ppv,2).'</span>';
						$z.='<span class="w1 rt">'.number_format($npv,2).'</span>';
						$z.='<span class="w1 rt">'.number_format($tpv,2).'</span></li>';
						echo $z;
					     ob_flush();
						flush();
					}
					getPV($did,$lvl+1,$max);
				}
			}
		}
	}
	// $data.=$x;
	// return $data;
}

function findDist($sponsor,$src,$lvl) {	//search downline
	global $data;$x='';
	if($lvl>=MAXLOOP) {}
	else{
		$con=SQLi('distributor');
		$qry="
			SELECT * FROM distributors
			WHERE dssid='$sponsor'
			ORDER BY dssetd
		";
		$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)) {
			foreach($rw as $k=>$v) $$k=$v;
			if(
				strpos($dsdid,$src)!==false||
				strpos(strtolower($dsfnam),$src)!==false||
				strpos(strtolower($dsmnam),$src)!==false||
				strpos(strtolower($dslnam),$src)!==false
			) {
				$x.='<tr>';
				$x.="<td>$dsdid</td>";
				$x.="<td>$dslnam, $dsfnam $dsmnam</td>";
				$x.="<td>$dsstrt $dsbarn $dscity $dsprov</td>";
				$x.="<td>$dsmph</td></tr>";
				// $_SESSION['src_result'].=$x;
				$_SESSION['src_found']++;
			}
			findDist($dsdid,$src,$lvl+1);
		}
	}
	$data.=$x;
	return $data;
}

function isNoPVandNextIsMgr($id,$lvl) {
	global $isNDPV;
	$con=SQLi('distributor');
	$rs=mysqli_query($con,"SELECT dsdid,dsfnam,dsmnam,dslnam FROM distributors WHERE dssid='$id'") or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		ini_set('max_execution_time',120);
		ini_set('memory_limit','-1');
		set_time_limit(600);
		while($rw=mysqli_fetch_assoc($rs)) {
			$did=$rw['dsdid'];
			list($ppv,$npv,$gpv,$lev,$ndpv)=getCurrentPV($did);
			if(isMgr($ppv,$lev)) {
				$dname=$rw['dsfnam'].' '.(($rw['dsmnam']!='')?substr($rw['dsmnam'],0,1).'.':'').' '.$rw['dslnam'];
				if($lvl==1) $isNDPV.= '<li class="hdr"><span class="w1">Dist. ID</span><span class="w2">Name</span><span class="w1 ct">Ending Level</span><span class="w1 rt">Personal PV</span><span class="w1 rt">Group PV</span><span class="w1 rt">Total PV</span></li>';
				$isNDPV.='<li><span class="w1">'.$id.'</span><span class="w2"><span class="pad">'.str_repeat('_',$lvl-1).'</span>'.($lvl).'> <span'.(($lvl==1)?' class="gen1"':'').'>'.$dname.'</span></span><span class="w1 ct">0%</span><span class="w1 rt">0.00</span><span class="w1 rt">0.00</span><span class="w1 rt">0.00</span></li>';
				getPV($id,$lvl);
				break;
			}else{isNoPVandNextIsMgr($did,$lvl+1);}
		}
	}
}

function getCurrentPV($id) {
	$_SESSION['ndpv']=0;
	$con=SQLi(RTDB);
// echo RTDB." | SELECT * FROM bomstp WHERE bmdid='$id' ".BMPMO."<br>";
	$rs=mysqli_query($con,"SELECT * FROM bomstp WHERE bmdid='$id' ".BMPMO) or die(mysqli_error($con));
	$rw=mysqli_fetch_assoc($rs);
	return array($rw['bmppv'],$rw['bmnpv'],$rw['bmppv']+$rw['bmnpv'],$rw['bmelev'],getNDPV($id));
}

function getNDPV($dssid) {
	$con=SQLi('distributor');
	$qry="
		SELECT dsdid,bmppv
		FROM distributors,bomstp
		WHERE dsdid=bmdid
		AND dssid='$dssid'
		AND bmelev=0
	";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>=0) {
		while($rw=mysqli_fetch_assoc($rs)) {
			$_SESSION['ndpv']+=$rw['bmppv'];
			getNDPV($rw['dsdid']);
		}
	}return $_SESSION['ndpv'];
	mysqli_close($con);
}

function isMgr($tpv,$lev) {
	if($tpv>=MINPV&&$lev>=5) return true;
}
?>
