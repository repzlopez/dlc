<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if((!isset($_SESSION['a_logged'])||!$_SESSION['a_logged'])&&(
	(isset($_SESSION['gos_logged'])&&$_SESSION['gos_logged'])||
     (isset($_SESSION['pcm_logged'])&&$_SESSION['pcm_logged'])||
     (isset($_SESSION['mig_logged'])&&$_SESSION['mig_logged']))){
	$_SESSION['a_logged']   = 1;
	$_SESSION['bad_admin']  = 0;
	$_SESSION['gos_logged'] = 0;
     $_SESSION['pcm_logged'] = 0;
     $_SESSION['mig_logged'] = 0;
}
require_once('setup.php');
define('countWH',countWHID());

global $exemgr;
$exemgr = array(
	'EDDY'=>EDDY,
	'PASTOR'=>CRIS,
	'GRACE'=>GRACE,
	'DIVINE'=>'630000000003',
	'CARLO'=>'630000010009',
	'JAN'=>'630000020257',
	'RANDY'=>'630000022918',
	'JR'=>'630000025891',
	'JEN'=>'630000000352',
	'SHE'=>'630000011846',
	'PEARL'=>'630000026663',
	'LHOU'=>'630000031598',
	'JOSHUA'=>'630000027795',
	'REPZ'=>REPZ,
);
$arrLinks = array(
	'navigation'=>'Navigation',
	'pages'=>'Pages',
	'faq'=>'FAQ',
	'managers'=>'Recognized Managers',
	'getmgr'=>'Get Managers',
	'admin'=>'Admin',
	'recap'=>'Recap',
	'notif'=>'Notifications',
	'getpv'=>'Get PV',
	'setweek'=>'Set Week',
);
$arrGets = array(
	'getlevel'=>'Get Level',
	'getpv'=>'Get PV Weekly bohstp',
	'getpvgrouped'=>'Get PV Grouped',
	'getpvpromo'=>'Get PV Promo ormstp',
	'getsignup'=>'Get Signup',
	'getrank'=>'Get Ranking PPV TPV VB',
	'getdistrilist'=>'Get Distrilist',
	'getleaders'=>'Get Leaders 1st Time',
	'getcontacts'=>'Get Contacts',
	'getpsconfirm'=>'Get PS / BBB Confirmation',
	'getweekly'=>'Get DP PV POV'
);
$arrSets = array(
	'getdllog'=>'View DL Log',

);

$homebtn  = IS_GLOB?'<div class="clear"><a href="/admin" class="back" id="download">HOME</a></div>':'';
$backbtn  = '<div class="clear"><a href="'.(isset($_SESSION['lastpage'])?$_SESSION['lastpage']:'#').'" class="back" id="download">BACK</a></div>';
$mainlogo = '<div id="main_logo"><img src="/src/dlc_logo.png" alt="'.DLC_FULL.'" /></div>';
$nicEditButtons = '<div id="nicEditButtons"><button id="addArea">Add Panel</button><button id="remArea">Remove Panel</button></div>';

function isEddyOrRick($id){
	return ($id==EDDY||$id==RICK);
}

function getWHID($id){
	$con = SQLi('products');
	$rs  = mysqli_query($con,"SELECT * FROM tblwarehouse WHERE wh='$id'") or die(mysqli_error($con));
	$rw  = mysqli_fetch_array($rs);
	return $rw['id'];
}

function countWHID(){
	$con = SQLi('products');
	$rs  = mysqli_query($con,"SELECT count(*) i FROM information_schema.columns WHERE table_name ='tblstocks'") or die(mysqli_error($con));
	$rw  = mysqli_fetch_array($rs);
	return $rw['i'];
}

function getTransfers($transfers,$reciv=null,$reles=null){
     $i       = 0;
	$tran    = '';
	$lev0    = testScope("global|logis|gos|pcm");
	$isasm   = ( isset($_SESSION['isAssembly']) && $_SESSION['isAssembly'] );
	$issales = ( isset($_SESSION['isSales']) && $_SESSION['isSales'] );
	$istransfer = isset($_SESSION['transfers']);
	$release = ( isset($_SESSION['releasing']) && $_SESSION['releasing'] );
	foreach ( $transfers as $v ) {
		$v0 = $v[0];
		$v1 = $v[1];
		$v2 = $v[2];
		$trec  = explode('|',$reciv[$i]);
		$dat21 = ($release?($lev0?'<input type="text" class="transrec rt s2" name="transrel[]" value="'.$v2.'" />':'<span class="bad">Pending</span>'):$reles[$i]);
		$dat22 = (!$release?'<span class="s3"><input type="text" class="transrec rt s2" name="transrec[]" value="'.$reles[$i].'" /></span>':'');
		$dat1  = ($isasm?'':'<span class="s3 rt">'.(isset($reles[$i])?number_format((double)$reles[$i]):'').'</span>'.'<span class="s3 rt">'.(isset($trec[0])?number_format((double)$trec[0]):'').'</span>');
		$dat2  = ($isasm||$issales?'':'<span class="s3 rt">'.$dat21.'</span>'.$dat22);
		$dat3  = ($isasm||$release?'':'<input type="text" class="transrec ct s2" name="remarks[]" value="'.($issales?$trec[0]:'').'" />');
		$transrec = isset($reciv)&&!$isasm&&!$issales?$dat1.'<span class="s3 ct">'.(isset($trec[1])?$trec[1]:'').'</span>':$dat2.'<span class="s3">'.$dat3.'</span>';
		$tran .= '<li><span class="s3 ct">'.$v0.'</span><span class="s5">'.$v1.'</span><span class="s3 rt">'.number_format($v2).'</span>'.($istransfer?'':$transrec).'</li>';
		$i++;
	} return $tran;
}

function populateCat($tbl,$id,$val,$distinct='',$qry='',$selected='',$rel='',$db=''){
	$pCat = '';
	$con  = SQLi( $db!='' ? $db : 'beta' );
	$rs   = mysqli_query($con,"SELECT $distinct FROM $tbl $qry") or die(mysqli_error($con));
	while ( $rw=mysqli_fetch_assoc($rs) ) {
		$rel = ($rel!='') ? 'rel="'.$rw['parent_id'].'"' : '';
		$pCat .= '<option value="'.$rw[$id].'" '.$rel.' '.(($selected==$rw[$id])?SELECTED:'').'>'.ucwords($rw[$val]).'</option>';
	} return $pCat;
}

function getButtons($con,$item,$do){
	$cal_date	= ($con=='calendar')?"&i=$item":'';
	$btn		= '<ul id="buttons" rel="'.$con.'">';
	$add		= '<li class="add"><a href="?p='.$con.'&do=1'.$cal_date.'" title="Add"></a></li>';
	$edit	= '<li class="edit"><a href="?p='.$con.'&do=2" title="Edit"></a></li>';
	$cancel	= '<li class="cancel"><a href="javascript:history.go(-1)" title="Cancel"></a></li>';
	$home	= '<li class="home"><a href="./" title="Home"></a></li>';
	$del		= IS_GLOB?'<li class="del"><a href="#" title="Delete"></a></li>':'';
	$save	= '';
	$back	= '<li class="back"><a href="javascript:history.go(-1)" title="Back"></a></li>';
	$homeonly = "/passreset|transfer|\bstocks\b|distrilookup|getmgr|olreg|referral/i";

	if ( $con == 'calendar' ) {
		if ( $do=='list' ) { $add=$add; }
		elseif ( $do==0 ) $add = '';
	} elseif ( preg_match($homeonly,$con) ) {
		$btn .= $home;
          $do   = '';
	}
	switch($do){
		case '' : break;
		case 0 : $btn.=$home.$add; break;
		case 1 : $btn.=$save.$cancel; break;
		case 2 : $btn.=$del.$cancel.$save; break;
		case 'list' : $btn.=$home.$add.$back; break;
		default : break;
	}
	return $btn.='</ul>';
}

function isDownload($f){
	if (
          $f=='application/vnd.ms-powerpoint' ||
          $f=='application/pdf'
     ) return true;
}
?>
