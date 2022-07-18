<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');

if(!ISIN_DISTRI||UPDATE_ON) {
	reloadTo('/');
	exit;
}

define( 'GET_PAGE', (isset($_GET['get']) ? $_GET['get'] : 'pv'));
define( 'LEVEL', isset($_GET['lvl']) ? $_GET['lvl'] : 1);
define( 'MAXDOWN', (LEVEL=='all') ? MAXLOOP : LEVEL);

global $data,$getpv;

$getpv						 = '';
$_SESSION['mgr_arr']		 = array();
$_SESSION['lastURI']		 = 'mypage';
$_SESSION['managerlist']	 = '';
$_SESSION['total_downlines'] = 0;

$_SESSION['monyr'] = isset($_GET['monyr']) ?
					(($_SESSION['monyr']!=$_GET['monyr'])?$_GET['monyr']:$_SESSION['monyr']) :
					(isset($_SESSION['monyr'])?$_SESSION['monyr']:date('mY',time()));

$loggeduser		= ISIN_DISTRI ? DIST_ID : 'Guest';
$title			= "| Distributor's Page";
$link			= 'mycart';
$linkname		= 'myCart';
$tab			= array('pv'=>'','downline'=>'','bonus'=>'','order'=>'','mgrlist'=>'', 'pmbonus' =>'', 'bbb' => '','pool'=>'','slots'=>'');
$tab[GET_PAGE]	= ' class="current"';
$superuser		= SPECTATOR ? false : in_array(DIST_ID, array(REPZ));
$mgrlist		= (in_array(DIST_ID, array(REPZ)) || OVERRIDE) ? '<a href="?get=mgrlist"' . $tab['mgrlist'] . '>Manager List</a>' : '';
$search			= '<a href="#" id="download" class="lookup">Search Downline</a>';
$x				= '';

include('updatelist.php');
require 'head.php';
ob_start();

list($ref,$slots) = getRefnSlot(DIST_ID);

define('DIS_RFRL', $ref);
define('DIS_SLOT', 1); //$slots

list($distdata, $ppv, $has_bonus) = getDistributor(DIST_ID);

if(GET_PAGE != '' && GET_PAGE != 'pv') $getdata = getData(DIST_ID, $ppv, 0, MAXDOWN);

$x .= '<ul class="print"><li><a href="javascript:window.print()"></a></li>';
// $x .= '<li class="smaller rt"><label><input type="checkbox" id="override" />Prev Data</label></li>';
$x .= '</ul>';
$x .= $distdata;
$x .= '<div id="tab" rel="' . GET_PAGE . '">';
$x .= '<a href="?get=pv"' . $tab['pv'] . '>Group PV</a>';
$x .= '<a href="?get=downline"' . $tab['downline'] . '>Downlines</a>';
$x .= '<a href="?get=bonus"' . $tab['bonus'] . '>Bonus History</a>';
$x .= '<a href="?get=order"' . $tab['order'] . '>Order History</a>';
// if($has_bonus) $x .= '<a href="?get=pmbonus"' . $tab['pmbonus'] . '>PM Bonus</a>';
$x .= '<a href="?get=bbb"' . $tab['bbb'] . '>BB Bonus</a>';
$x .= '<a href="?get=pool"' . $tab['pool'] . '>BB Pool</a>';
//$x .= '<a href="?get=slots"'.$tab['slots'].'>Slots</a>';
$x .= $mgrlist . '</div>';
$x .= '<ul id="data_list">' . (GET_PAGE == 'downline' ? '<span class="lt"' . $search . '</span>' : '');
$x .= '<li class="updated">' . (RTOR ? 'RT' : 'AS Updated as of ' . getLastUpdate()) . '</li>';
echo $x;

if (GET_PAGE != '' && GET_PAGE != 'pv') {
	echo $getdata;
	ob_end_flush();

} else {
	ob_implicit_flush(true);
	ob_end_flush();
	getPV(DIST_ID,0,MAXDOWN);
}

ob_start();

echo '</ul>';
echo loadFoot('','',array('/js/jquery/jquery-1.7.1.min.js','/js/distrilog.js'));
ob_end_flush();

function getDistributor($id) {
	$x= $dash= '';
	$ppv= $npv= $gpv= $lev= $ndpv=0; //$ref=0;$slots=0;

	$baseid = strpos(DIST_ID,'-')!==false ? substr(DIST_ID,0,strpos(DIST_ID,'-')) : DIST_ID;

	getTotalDownlines(DIST_ID,0);

	list($ppv,$npv,$gpv,$lev,$ndpv) = getCurrentPV($id);
	// list($ref,$slots)=getRefnSlot($id);

	for($i = 0; $i <= 4; $i++) {
		if($i>0) $dash = "-$i";
		$x .= "(SELECT dsdid FROM distributors WHERE dsdid='$baseid$dash') id$i,";
	}

	// $x=substr($x,0,-1);
	$reflink = DLC_ROOT.'/?rfr='.DIST_ID;
	$hlv = getHighLev($id);
	$pct = getPercent($lev>$hlv?$lev:$hlv,date('Y'),WEEK);
	$pin = getPin($id);
	$qry = "
		SELECT dssid,$x
			(SELECT CONCAT(dslnam,', ',dsfnam,' ',dsmnam) FROM distributors WHERE dsdid=d.dssid) sname,
			(SELECT COUNT(bhdid) FROM bohstp WHERE bhdid=d.dsdid) has_bonus
		FROM distributors d
		WHERE dsdid='$id'";

// if(DIST_ID=='630000038352') {
// 	echo $qry;
// }

	$con = SQLi('distributor');
	$rs  = $con->query($qry) or die(mysqli_error($con));
	$rw  = $rs->fetch_array();

	foreach($rw as $k=>$v) { $$k=$v; }
	$allid = "$id0|$id1|$id2|$id3|$id4";
	mysqli_close($con);

	$x  = '<div id="distdata">';
	$x .= '<div class="blue">'.strtoupper(DIST_NAME).$pin.'</div>';
	$x .= '<div><label class="s3">Distributor ID:</label> <span id="dist_id" class="s4">'.DIST_ID.'</span>';
	$x .= '<label class="s3">Sponsor ID:</label> <span id="spon_id" class="s4" title="Sponsor: '.ucwords(strtolower($sname)).'">'.$dssid.'</span>';
	$x .= '<span class="s4"></span><span class="s5"></span><a href="profile.php" class="btn s2">Profile</a></div>';

	if(isset($id1)) {
		$x .= '<div><label class="s3">Other Accounts:</label> ';

		foreach(explode('|',$allid) as $k) {
			if($k!=DIST_ID) $x .= '<a href="?otherid='.$k.'" class="s4">'.$k.'</a>';
		}

		$x .= '</div>';
	}

	$sponsor_slot = '';//'<label class="s3">Sponsor Slot:</label> <span class="s4">'.DIS_SLOT.'</span> '.(DIS_SLOT>0?'<a href="/reg" id="download" style="float:none">Use Slot</a>':'');
	$x .= '<div><label class="s3">BB Bonus:</label> <span id="referral" class="s4">Php '.number_format(DIS_RFRL,2).'</span> '.$sponsor_slot.'</div>
	<div><label class="s3">Referral Link:</label> '.$reflink.' <a class="reflink" id="download" style="float:none" rel="'.$reflink. '">Copy Link</a></div>
	<div class="rt">Data for Week ' . WEEKDESC . '</div>
	<div><label>Ending Level:</label> <span>'.$pct.'</span>
		<label>Personal PV:</label> <span>'.number_format($ppv,2).'</span>
		<label>Group PV:</label> <span>'.number_format($npv,2).'</span>
		<label>Total PV:</label> <span>'.number_format($gpv,2).'</span>
		<label># of Downlines:</label> <span>'.(($ppv>=MINPV||SPECTATOR)?number_format($_SESSION['total_downlines']):0).'</span>';
	$x .= preg_match("/pv|downline/i",GET_PAGE)?'<label>Level:</label> <select id="dist_level">'.generate_options(1,10,LEVEL).'</select>':'';
	$x .= '</div></div>';

	return array($x, $ppv, $has_bonus);
}

function getData($dsdid,$ppv,$lvl) {
	global $data;
	$x = '';
	$fail = '<div class="getminpv">You need to fullfill your '.MINPV.'PV requirement to view this page.</div>';
	// list($ppv,$npv,$gpv,$lev,$ndpv)=getCurrentPV($dsdid);
	switch(GET_PAGE) {
		case 'bonus':
			$x .= getBonusHistory($dsdid);
			break;

		case 'order':
			$x .= getOrderHistory($dsdid);
			break;

		case 'pmbonus':
			$x .= getPMBonus($dsdid);
			break;

		case 'bbb':
			$x .= getBBBonus($dsdid);
			break;

		case 'pool':
			$x .= getPool($dsdid);
			break;

		case 'slots':
			$x .= getSlots($dsdid);
			break;

		case 'mgrlist':
			getManagerList();
			$x .= $data;
			break;

		default:
			if( SPECTATOR || MINPV<=$ppv ) {
				switch(GET_PAGE) {
					case 'downline':getDistList($dsdid,$lvl,MAXDOWN);break;
					default:getPV($dsdid,$lvl,MAXDOWN);break;
				}
				
				$x .= $data;

			} else {
				$x .= $fail;
			}
			break;
	}

	return $x;
}

function getBonusHistory($id) {
	$con = SQLi('distributor');
	$qry = "SELECT * FROM bohstp WHERE bhdid='$id' ORDER BY bhpyr DESC,bhpmo DESC";
	$x   = '<li class="hdr"><span class="s2">Year</span><span class="s2 ct">Mo/Wk</span><span class="w1 rt">Bonus Amount</span><span class="w1 rt">Personal PV</span><span class="w1 rt">Group PV</span><span class="w1 rt">Total PV</span><span class="w1 ct">End Level</span><span class="w1 ct">Mgr Lines</span></li>';
	$rs  = $con->query($qry) or die(mysqli_error($con));

	if($rs->num_rows==0) {
		$x .= '<li class="nodata more ct">No data found</li>';

	} else {

		while($rw=$rs->fetch_assoc()) {
			$ppv = $rw['bhppv'];
			$npv = $rw['bhnpv'];
			$gpv = $npv-$ppv;
			$x .= '<li><span class="s2">'.$rw['bhpyr'].'</span><span class="s2 ct">'.$rw['bhpmo'].'</span><span class="w1 rt">'.number_format($rw['bhtamt'],2).'</span><span class="w1 rt">'.number_format($ppv,2).'</span><span class="w1 rt">'.number_format($gpv,2).'</span><span class="w1 rt">'.number_format($npv,2).'</span><span class="w1 ct">'.getPercent($rw['bhelev'],$rw['bhpyr'],$rw['bhpmo']).'</span><span class="w1 ct">'.$rw['bhqumr'].'</span></li>';
		}

	}

	mysqli_close($con);
	return $x;
}

function getOrderHistory($id) {
	$con = SQLi('distributor');
	$qry = "SELECT * FROM ormstp WHERE omdid='$id' ORDER BY ompyr DESC,ompmo DESC,ominv DESC";
	$x   = '<li class="hdr"><span class="s2">Year</span><span class="s2 ct">Mo/Wk</span><span class="w1 rt">Order #</span><span class="w1 rt">Invoice #</span><span class="w1 ct">Order Date</span><span class="w1 rt">Order Amount</span><span class="w1 rt">Order PV</span></li>';
	$rs  = $con->query($qry) or die(mysqli_error($con));

	if($rs->num_rows==0) {
		$x .= '<li class="nodata more ct">No data found</li>';

	} else {

		while($rw=$rs->fetch_assoc()) {
			$x .= '<li><span class="s2">'.$rw['ompyr'].'</span><span class="s2 ct">'.$rw['ompmo'].'</span><span class="w1 rt">'.$rw['omord'].'</span><span class="w1 rt">'.$rw['ominv'].'</span><span class="w1 ct">'.substr($rw['omodat'],4,2).'.'.substr($rw['omodat'],6,2).'.'.substr($rw['omodat'],0,4).'</span><span class="w1 rt">'.number_format($rw['ompov'],2).'</span><span class="w1 rt">'.number_format($rw['ompv'],2).'</span></li>';
		}

	}

	mysqli_close($con);
	return $x;
}

function getBBBonus($id) {
	$con = SQLi('orders');
	$rel_ok= $rel= $t=0;
	$old = '';

	$x  = '<li class="hdr"><span class="s0"></span><span class="s4 lt">From</span><span class="s5">Name</span><span class="s3 rt">BB Bonus</span><span class="s3 rt">Qualified</span><span class="s3 rt">Released</span></li>';
	$rs = $con->query("SELECT * FROM tblreferral WHERE rsdid='$id' ORDER BY released,rsdid,rsref DESC") or die(mysqli_error($con));

	if($rs->num_rows==0) {
		$x .= '<li class="nodata more ct">No data found</li>';

	} else {
		while($rw=$rs->fetch_assoc()) {
			foreach($rw as $k=>$v) { $$k=$v; }

			$rel_ok = ( $released && !$rel ? 1 : 0 );

			if(($old!=$rsdid&&$old!=''&&!$released)||($released&&$rel_ok)) {
				$x .= '<li><span class="s4"></span><strong class="s5 u">Total Unreleased</strong><strong class="s3 rt u">'.number_format($t,2).'</strong><strong class="s3 u">&nbsp;</strong><br></li>';
				$t = 0;
			}

			if($released&&$rel_ok) {
				$x .= '<li class="hdr"><br><span>Released</span></li>';
				$rel = 1;
			}

			$x .= '<li><span class="s0"></span><span class="s4">'.$rslid.'</span>';
			$x .= '<span class="s5">'.getName($rslid,'fml').'</span>';
			$x .= '<span class="s3 rt">'.number_format($rsamt,2).'</span>';
			$x .= '<span class="s3 rt">'.($rsref!=null?formatDate(substr($rsref,2,8)):'-').'</span>';
			$x .= '<span class="s3 rt">'.($released!=null?formatDate($released):'-').'</span></li>';

			$old = $rsdid;
			$t  += $rsamt;
		}
	}

	mysqli_close($con);
	return $x;
}

function getSlots($id) {
	// $con= SQLi('orders');
	// $x  = '<li class="hdr"><span class="s0"></span><span class="s3 lt">Received</span><span class="s6">Sponsor</span><span class="s6">Downline</span><span class="s2 rt">Encoded</span></li>';
	// $rs = $con->query("SELECT * FROM tblslots WHERE dsdid='$id' ORDER BY (CASE WHEN (used='' OR used IS NULL) THEN used ELSE 0 END),used DESC,slotid") or die(mysqli_error($con));

	// if($rs->num_rows==0) {
	// 	$x .= '<li class="nodata more ct">No data found</li>';

	// } else {
	// 	while($rw=$rs->fetch_assoc()) {
	// 		foreach($rw as $k=>$v) { $$k=$v; }

	// 		$x .= '<li><span class="s0"></span>';
	// 		$x .= '<span class="s3 lt">'.substr($ref,0,4).' '.substr($ref,4,2).'</span>';
	// 		$x .= '<span class="s6">'.($dsfor!=''?$dsfor.' '.getName($dsfor,'lfm'):'-').'</span>';
	// 		$x .= '<span class="s6">'.($dslid!=''?$dslid.' '.getName($dslid,'lfm'):'').'</span>';
	// 		$x .= '<span class="s3 rt">'.($used!=''?date('m.d.Y',strtotime($used)):'').'</span></li>';
	// 	}
	// }

	// mysqli_close($con);
	// return $x;

	//MODULE CURRENTLY DISABLED
}

function getPool($id) {
	$arr= array('PENDING','ENCODED','VOID');
	$con= SQLi('beta');
	$rs = $con->query("SELECT * FROM tblolreg WHERE dsdid<>'" . DIST_ID . "' AND referrer='$id' AND status<2 ORDER BY date") or die(mysqli_error($con));
	$x  = '<li class="hdr"><span class="s0"></span><span class="s4">Distributor ID</span> <span class="s6">Name</span> <span class="s3 rt">Submitted</span> <span class="s4 rt">TIN</span> <span class="s4 ct">Status</span></li>';

	if($rs->num_rows==0) {
		$x .= '<li class="nodata more ct">No data found</li>';

	} else {

		while($rw=$rs->fetch_assoc()) {
			foreach($rw as $k=>$v) { $$k=$v; }

			$x .= '<li><a href="../reg/?i='.$id.'"><span class="s0"></span>';
			$x .= '<span class="s4">'.$dsdid.'</span> ';
			$x .= '<span class="s6">'.ucwords(strtolower("$dslnam, $dsfnam $dsmnam")).'</span> ';
			$x .= '<span class="s3 rt">'.date('m.d.Y',strtotime($date)).'</span> ';
			$x .= '<span class="s4 rt">'.$dstin.'</span> ';
			$x .= '<span class="s4 ct">'.$arr[$status].'</span></a></li>';
		}
	}

	mysqli_close($con);
	return $x;
}

function getManagerList() {
	if( SPECTATOR && !OVERRIDE ) exit;

	global $data;

	$_SESSION['is_mgrlist'] = true;
	$dwk = (int)substr($_SESSION['monyr'],0,2);
	$dyr = substr($_SESSION['monyr'],2);
	$m_list= $x='';

	$qry="
		SELECT B.bddids,dsfnam,dslnam,bhdid,bhppv,bhnpv,bhelev,bhqumr
			,GROUP_CONCAT(DISTINCT B.bdtype ORDER BY B.bdtype DESC) btyp,GROUP_CONCAT(DISTINCT B.bdbamt ORDER BY B.bdtype DESC) wps
		FROM (SELECT * FROM bodstp WHERE bdyy='$dyr' AND bdmm='$dwk') B
		INNER JOIN distributors ON dsdid=B.bddids
		LEFT JOIN bohstp ON bhdid=B.bddids
		WHERE dsdid=B.bddids
		AND B.bddids NOT LIKE 'DLC%'
		AND bhpyr='$dyr' AND bhpmo='$dwk'
		AND (bdtype<>'B' OR (bdtype='B' AND (bddids='".EDDY."' OR bddids='".RICK."')))
		GROUP BY B.bddids
		ORDER BY IF(B.bddids RLIKE '^[a-z]',1,2),B.bddids
	";

	$data .= '<li class="hdr rt" id="dl_list"><div class="monyr"><a href="../admin/managerlist" id="download">Download List</a><label>Month/Year</label> <select id="mon_year">'.generate_date(). '</select></div></li>';
	$data .= '<li class="hdr"><span class="w0"></span><span class="s4 ct">ID#</span><span class="s5">Name</span><span class="s2 rt">PPV</span><span class="s2 rt">GPV</span><span class="s2 rt">TPV</span><span class="s1 ct">Level</span><span class="s2 ct">Mgr Lines</span><span class="s4">Manager IDs</span></li>';

	$con = SQLi('distributor');
	$rs  = $con->query($qry) or die(mysqli_error($con));

	while($rw=$rs->fetch_assoc()) {
		getMgrIDs($rw['bddids'],$dwk,$dyr);
	}

	$rs = $con->query($qry) or die(mysqli_error($con));
	if($rs->num_rows==0) {
		$data .= '<li class="nodata more ct">No data found</li>';

	} else {
		$c = 1;

		while($rw=$rs->fetch_assoc()) {
			$id  = $rw['bhdid'];
			$ppv = $rw['bhppv'];
			$npv = $rw['bhnpv'];
			$gpv = $npv-$ppv;
			$qmr = $rw['bhqumr'];
			$lev = $rw['bhelev'];

			$mgrids = '';
			$notmgr = '';
			$m_name = $rw['dsfnam'].' '.$rw['dslnam'];//getName($id,'full',true);

			if(!empty($_SESSION['mgr_ids'][$id])) {
				$mgrids=implode(', ',$_SESSION['mgr_ids'][$id]);
			}

			$notmgr = ($qmr>0) ? ' class="breakaway"' :'';

			$x  ='<li'.$notmgr.'>';
			$x .= '<span class="w0 ct">'.$c++.'</span>';
			$x .= '<span class="s4">'.$id.'</span>';
			$x .= '<span class="s5">'.$m_name.'</span>';
			$x .= '<span class="s2 rt">'.number_format($ppv,2).'</span>';
			$x .= '<span class="s2 rt">'.number_format($gpv,2).'</span>';
			$x .= '<span class="s2 rt">'.number_format($npv,2).'</span>';
			$x .= '<span class="s1 ct">'.($lev).'</span>';
			$x .= '<span class="s2 ct">'.$qmr.'</span>';
			$x .= '<span class="s4">'.($qmr>0?$mgrids:'').'</span></li>';

			$data .= $x;

			$m_list  ='"'.$id.'",';
			$m_list .= '"'.$m_name.'",';
			$m_list .= '"'.number_format($ppv,2).'",';
			$m_list .= '"'.number_format($gpv,2).'",';
			$m_list .= '"'.number_format($npv,2).'",';
			$m_list .= '"'.($lev).'",';
			$m_list .= '"'.$qmr.'",';
			$m_list .= '"'.$mgrids.'",';
			$m_list .= '"'.$rw['btyp'].'",';
			$m_list .= '"'.$rw['wps'].'"'."\n";

			$_SESSION['managerlist'] .= $m_list;
		}
	}

	mysqli_close($con);
}

function getMgrIDs($id,$wk,$yr) {$x='';
	$con = SQLi('distributor');
	$arr = array();
	$qry = "SELECT bddids
		FROM bodstp
		WHERE bdsid='$id'
		AND bddids NOT LIKE 'DLC%'
		AND bdyy='$yr'
		AND bdmm='$wk'
		AND bdtype='L'";

	$rs = $con->query($qry) or die(mysqli_error($con));

	if($rs->num_rows>0) {
		while($rw=$rs->fetch_assoc()) {
			$d = $rw['bddids'];

			if(!empty($_SESSION['mgr_ids'])) {
				foreach($_SESSION['mgr_ids'] as $k=>$v) {
					foreach($v as $i=>$z) {
						if($z=$d) {
							unset($_SESSION['mgr_ids'][$k][$d]);
						}
					}
				}
			}

			$arr[$d]=$d;
		}

		$_SESSION['mgr_ids'][$id]=$arr;
	}
// if(!empty($_SESSION['mgr_ids'])) print_r($_SESSION['mgr_ids']);
// echo "<br><br>";
}

function getHighLev($id) {
	$con = SQLi('beta');
	$n   = 0;
	$rs  = $con->query("SELECT level FROM tblmanagers WHERE id='$id' AND status=1");

	if($rs->num_rows>0) {
		$rw = $rs->fetch_array();
		$n  = ($rw['level']>2?5:$rw['level']);

	} else {
		$con = SQLi('distributor');
		$rs  = $con->query("SELECT bhelev FROM bohstp WHERE bhdid LIKE '$id' AND bhelev<>'' ORDER BY bhelev DESC LIMIT 1") or die(mysqli_error($con));

		if($rs->num_rows>0) {
			$rw=$rs->fetch_array();
			$n=($rw['bhelev']>2)?2:$rw['bhelev'];
		}
	}

	mysqli_close($con);
	return $n;
}

function getTotalDownlines($dssid,$lvl) {
	$con = SQLi('distributor');

	if( GET_PAGE==null || GET_PAGE=='pv' ) {
		if($lvl>=MAXDOWN) {}
		else{
			$qry = "
				SELECT dsdid,dssid,bmdid,bmnpv
				FROM distributors,bomstp
				WHERE dssid='$dssid'
				AND bmdid=dsdid
			";

			$rs = $con->query($qry) or die(mysqli_error($con));
			if($rs->num_rows>0) {
				while($rw=$rs->fetch_assoc()) {
					$_SESSION['total_downlines']++;
					if($rw['bmnpv']>0) getTotalDownlines($rw['dsdid'],$lvl+1);
				}
			}
		}

	} else {

		if($lvl>=MAXDOWN) {}
		else{
			ini_set('memory_limit','-1');
			set_time_limit(600);
			$rs = $con->query("SELECT dsdid FROM distributors WHERE dssid='$dssid'") or die(mysqli_error($con));
			while($rw=$rs->fetch_assoc()) {
				$_SESSION['total_downlines']++;
				getTotalDownlines($rw['dsdid'],$lvl+1);
			}
		}
	}
}

function getPin($id) {
	$con = SQLi('beta');
	$rs  = $con->query("SELECT level FROM tblmanagers WHERE id='$id'") or die(mysqli_error($con));
	$rw  = $rs->fetch_array();
	$lvl = ($rw>0)?$rw['level']:0;

	mysqli_close($con);

	$lev = array('0%'=>0,'manager'=>1,'pearl_manager'=>2,'ruby_manager'=>3,'emerald_manager'=>4,'sapphire_manager'=>5,'diamond_manager'=>6);
	$pin = array_search($lvl,$lev);
	$pintitle = ucwords(substr($pin,0,strpos($pin,'_'))).' Manager Pin';

	return ($lvl>0)?'<img src="/src/pins/'.$pin.'.png" title="'.$pintitle.'" alt="'.$pintitle.'" />':'';
}

function getPMBonus($id) {
	$con = SQLi('orders');

	$hdr = '<li class="hdr"><span class="s0"></span><span class="s4">Dist ID</span>
		<span class="s6">Name</span>
		<span class="s2 rt">Invoice</span>
		<span class="s3 rt">Bonus</span>
		<span class="s2 ct">Status</span></li>';

	$qry = "
		SELECT b.*,o.omdid,
			CONCAT(dslnam,', ',dsfnam,' ',dsmnam) nam
		FROM pm_bonus b
		LEFT JOIN ormstp o
			ON o.ominv=b.invoice
		LEFT JOIN " . DB . "distributor.distributors d
			ON d.dsdid=o.omdid
		WHERE b.dsdid='$id'
	";

	$x  = '';
	$rs = $con->query($qry) or die(mysqli_error($con));
	if($rs->num_rows > 0) {
		while ($r = $rs->fetch_assoc()) {

			foreach ($r as $k => $v) $$k = $v;

			$x .= '<li><span class="s0"></span><span class="s4">' . $omdid . '</span>
			<span class="s6" title="' . $nam . '">' . $nam . '</span>
			<span class="s2 rt">' . $invoice . '</span>
			<span class="s3 rt">' . number_format($bonus, 2, '.', ',') . '</span>
			<span class="s2 ct">' . $status . '</span></li>';
		}
	}

	mysqli_close($con);
	return $hdr.$x;
}

function getLastUpdate() {
	$con = SQLi(RTDB);
	$get = RTOR ? 'stamp' : 'date_updated';
	$tbl = RTOR ? 'ormstp' : 'updates';

	$rs = $con->query("SELECT $get FROM $tbl ORDER BY $get DESC LIMIT 1") or die(mysqli_error($con));
	$rw = $rs->fetch_assoc();

	return date('h.ia M d, Y',strtotime($rw[$get]));
	mysqli_close($con);
}

function getRefnSlot($id) {
	$con = SQLi('orders');
	$qry = "SELECT
		(SELECT SUM(rsamt) amt FROM tblreferral WHERE rsdid='$id' AND (released='' OR released IS NULL)) ref,
		(SELECT COUNT(*) slots FROM tblslots WHERE dsdid='$id' AND (dsfor='' OR dsfor IS NULL)) slots
	";

	$rs = $con->query($qry) or die(mysqli_error($con));
	$rw = $rs->fetch_assoc();

	return array($rw['ref'],(int)$rw['slots']);
	mysqli_close($con);
}

function generate_options($from,$to,$sel) {
	$x = array();
	$r = array(15,20,25,30);

	for($i=$from;$i<=$to;$i++) {
		$x[] = '<option value='.$i.' '.(($i==$sel)?SELECTED:'').'>'.$i.'</option>';
	}

	foreach($r as $i) {
		$x[] = '<option value='.$i.' '.(($i==$sel)?SELECTED:'').'>'.$i.'</option>';
	}

	$x[] .= '<option value="all" '.(($sel=='all')?SELECTED:'').'>All</option>';
	return join('',$x);
}

function generate_date() {
	$con = SQLi('distributor');
	$qry = "SELECT DISTINCT bhpyr,bhpmo FROM bohstp ORDER BY bhpyr DESC,bhpmo DESC";
	$x   = array();
	$rs  = $con->query($qry) or die(mysqli_error($con));

	if($rs->num_rows==0) {
		echo '<li class="nodata more ct">No data found</li>';

	} else {
		$x[] = '<option value='.date('mY',time()).' '.(($_SESSION['monyr']==date('mY',time()))?SELECTED:'').'>'.sprintf('%02d',date('m',time())).' / '.date('Y',time()).'</option>';
		while($rw=$rs->fetch_assoc()) {
			if(sprintf('%02d',$rw['bhpmo']).$rw['bhpyr']!='000') {
				$x[] = '<option value='.sprintf('%02d',$rw['bhpmo']).$rw['bhpyr'].' '.(($_SESSION['monyr']==sprintf('%02d',$rw['bhpmo']).$rw['bhpyr'])?SELECTED:'').'>'.sprintf('%02d',$rw['bhpmo']).' / '.$rw['bhpyr'].'</option>';
			}
		}

		return join('',$x);
	}

	mysqli_close($con);
}
?>
