<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
date_default_timezone_set('Asia/Manila');
require_once('info.config');

global $dlcuser,$shortlink;
$protocol  = strpos($_SERVER['SERVER_NAME'], 'local')!==false ? 'http://' : 'https://';
$shortlink = strpos($_SERVER['SERVER_NAME'], 'local')!==false ? 'index.php/' :'';

define( 'DLC_FULL', 'DLC Philippines Shareconomy' );
define( 'DLC_GOS', 'Group Order System' );
define( 'DLC_PCM', 'Product Center Manager' );
define( 'DLC_MIG', 'Mobile Inventory Group' );

define( 'DLC_ROOT', $protocol . $_SERVER['SERVER_NAME'] );
define( 'DLC_LOGIN', DLC_ROOT .'/login' );
define( 'DLC_ADMIN', DLC_ROOT .'/admin' );
define( 'DLC_GORT', DLC_ROOT .'/gos' );
define( 'DLC_PCRT', DLC_ROOT .'/pcm' );
define( 'DLC_MGRT', DLC_ROOT .'/mig' );
define( 'DLC_MYPAGE', DLC_ROOT .'/distrilog/mypage.php' );

define( 'ISIN_DISTRI', isset($_SESSION['isLogged']) ? $_SESSION['isLogged'] :0 );
define( 'ISIN_ADMIN', isset($_SESSION['a_logged']) ? $_SESSION['a_logged'] :0 );
define( 'ISIN_GOS', isset($_SESSION['gos_logged']) ? $_SESSION['gos_logged'] :0 );
define( 'ISIN_PCM', isset($_SESSION['pcm_logged']) ? $_SESSION['pcm_logged'] :0 );
define( 'ISIN_MIG', isset($_SESSION['mig_logged']) ? $_SESSION['mig_logged'] :0 );
define( 'ISIN_WP', isset($dlcuser)?true:false);

if( ISIN_DISTRI && isset($_GET['otherid']) ) {
	$_SESSION['u_site'] = $_GET['otherid'];
}

define( 'DIST_ID', isset($_SESSION['u_site']) ? $_SESSION['u_site'] :NULL);
define( 'DIST_NAME', isset($_SESSION['u_name']) ? $_SESSION['u_name'] :NULL);

define( 'LOGIN_ID', isset($_SESSION['login_id']) ? $_SESSION['login_id'] :NULL);
define( 'LOGIN_NAME', isset($_SESSION['login_name']) ? $_SESSION['login_name'] :NULL);
define( 'LOGIN_TYPE', isset($_SESSION['login_type']) ? $_SESSION['login_type'] :NULL);
define( 'LOGIN_BRANCH', isset($_SESSION['login_whid']) ? $_SESSION['login_whid'] :NULL);

define( 'SPECTATOR', isset($_SESSION['spectator']) && $_SESSION['spectator'] );
define( 'OVERRIDE', isset($_SESSION['override']) && $_SESSION['override'] );

define( 'GUEST', isset($_SESSION['rfr']) ? $_SESSION['rfr'] :NULL );
define( 'RESELLER', isset($_SESSION['reseller']) ? $_SESSION['reseller'] : NULL );
define( 'PCMSTORE', isset($_SESSION['pcmstore']) ? $_SESSION['pcmstore'] : NULL );
define( 'REF_PROD', isset($_SESSION['pid']) ? DLC_ROOT.'/products.php?pid='. $_SESSION['pid'] :NULL );
define( 'OLREG_REF', isset($_SESSION['olreg_ref']) ? $_SESSION['olreg_ref'] :NULL );
define( 'OLREG_min100', 100 );
define( 'OLREG_min500', 500 );
define( 'OLREG_Choice100', md5( OLREG_min100 .'pvselect') );
define( 'OLREG_Choice500', md5( OLREG_min500 .'pvselect') );
define( 'OLREG_NOSLOT', preg_match( "/18143|18144|". OLREG_Choice100 ."|" .OLREG_Choice500 ."/i", OLREG_REF ) );
define( 'PROC_FEE', 1500 );

define( 'IS_GLOB', ISIN_ADMIN ? ( testScope('global') ? 1:0 ) :0 );
define( 'IS_GOS', ISIN_ADMIN ? ( testScope("global|gos") ? 1:0 ) :0 );
define( 'IS_PCM', ISIN_ADMIN ? ( testScope("global|pcm") ? 1:0 ) :0 );
define( 'IS_MIG', ISIN_ADMIN ? ( testScope("global|mig") ? 1:0 ) :0 );

define( 'NOTIF_ON', getStatus('991') );
define( 'UPDATE_ON', getStatus('996') );
define( 'LOGIN_ON', GUEST ? 0 : getStatus('997') );
define( 'SRP_ON', GUEST ? 1 : getStatus('989') ) ;
define( 'CART_ON', GUEST ? 1 : (isset($_SESSION['cart_on']) ? $_SESSION['cart_on']:0) );
define( 'SHOPLIST', isset($_SESSION['shoplist']) );

define( 'LOGO_LINK', DLC_ROOT .'/'. LOGIN_TYPE);
define( 'DIV_CLEAR', '<div class="clear"></div>' );
define( 'READ_ONLY', 'readonly="readonly"' );
define( 'DISABLED', 'disabled="disabled"' );
define( 'SELECTED', 'selected="selected"' );
define( 'C_K', 'checked="checked"');
define( 'PM_START', '2022-08-01');
define( 'TMDSET', 'Y-m-d H:i:s' );
define( 'DATSET', 'M d, Y' );
define( 'TIMSET', 'h:ia' );
define( 'RED', ' style="border-color:#f00;"' );
define( 'EDDY', 'A130665869' );
define( 'RICK', 'E121480987' );
define( 'CRIS', 'EC00196035' );
define( 'GRACE', '630000000001' );
define( 'REPZ', '630000002865' );

list($wk,$yr,$thu,$wed) = getDatWk( date('Y-m-d') );
define( 'RTOR', preg_match("/\b1\b|\b2\b|\b3\b|16|17|18/i", date('j')) ? 0 : getStatus(990) );
define( 'RTDB', RTOR ? 'orders' : 'distributor' );
define( 'WEEK', $wk );
define( 'WKYR', $yr );
define( 'WEEKDESC', WEEK .' '. WKYR .' ( '. date('M d - ',strtotime($thu)) . date('M d',strtotime($wed)) .' )' );
define( 'BMPMO', RTOR ? " AND bmpmo='$wk'" :'' );
define( 'MAXLOOP', 999999 );
define( 'SLOTMIN', 100 ); 						//min pv to get slot
define( 'GUESTMINPV', OLREG_REF ? 20 : 100 ); 	//min pv to signup
define( 'minAllow', 100 );						//min pv to allow to sponsor (discontinued)
define( 'MINPEARL', getMinPV(WKYR,WEEK) );		//min pv to qualify for pearl
define( 'MINPV', $_SESSION['minpv'][0]);		//min pv per cutoff
define( 'MINPV_6pct', $_SESSION['minpv'][2]);	//min pv to be 6%
define( 'newDistriDays', 30 ); 					//min pv to get slot

unset( $_SESSION['minpv'] );
unset( $_SESSION['browser'] );

global $c_y,$c_n;
$ynArr = array('No'=>0,'Yes'=>1);

function SQLi($dbsrc) {
	require('infoconfig.php');
	return $con;
}

function getName($id,$fmt,$nomid=false) {
	$con = SQLi('distributor');
	$rs  = $con->query("SELECT dsfnam,dsmnam,dslnam FROM distributors WHERE dsdid='$id'") or die(mysqli_error($con));
	$rw  = $rs->fetch_array();
	$fn  = $rw['dsfnam'];
	$mn  = $nomid?'':$rw['dsmnam'];
	$ln  = $rw['dslnam'];
	$nam = '';

	switch($fmt) {
		case 'full': $nam = $fn .' '. (($mn!='') ? $mn :'' ) .' '. $ln; break;
		case 'fml' : $nam = $fn .' '. (($mn!='') ? substr($mn,0,1) .'.' :'') .' '. $ln; break;
		case 'lfm' : $nam = $ln .', '. $fn .' '. ( ($mn!='') ? substr($mn,0,1) .'.' :''); break;
		case 'lff' : $nam = $ln .', '. $fn .' '. $mn; break;
		default: break;
	}

	return titleCase($nam);
}

function getPName($id) {
	$con = SQLi('products');
	$rs  = $con->query("SELECT name FROM tbllist WHERE id='$id'") or die(mysqli_error($con));
	$rw  = $rs->fetch_array();
	return utf8_encode($rw['name']);
}

function getMinPV($dyr,$dwk) {
	if( ( $dyr<'2012' ) || ( $dyr=='2012' && $dwk<8 ) ) {
		$prl = 4000;	/* old */
		$_SESSION['minpv'] = array( 0=>100, 1=>500, 2=>1500, 3=>3000, 4=>5000, 5=>8000, 6=>12000 );

	} elseif( $dyr<'2016' ) {
		$prl = 500;		/* 2012-2015 */
		$_SESSION['minpv'] = array( 0=>5, 1=>250, 2=>500, 3=>1000, 4=>1500, 5=>2000, 6=>3000 );

	} elseif( $dyr=='2016' && $dwk<32 ) {
		$prl = 500;		/* 2016 */
		$_SESSION['minpv'] = array( 0=>5, 1=>500, 2=>500, 3=>1000, 4=>1500, 5=>2000, 6=>2000 );

	} elseif( $dyr=='2017' && $dwk<36 ) {
		$prl = 250;		/* 2016 uip */
		$_SESSION['minpv'] = array( 0=>5, 1=>250, 2=>250, 3=>500, 4=>1000, 5=>2000, 6=>2000 );

	} else {
		$prl = 2000;	/* 2017 shareconomy */
		$_SESSION['minpv'] = array( 0=>40, 1=>500, 2=>500, 3=>1000, 4=>2000, 5=>4000, 6=>4000);
	}

	return $prl;
}

function getPercent($lvl, $yr=null, $wk=52) {
	$yr = isset($yr) ? $yr : date('Y');

	if( $yr<=2013 ) { $lev = array(0,6,9,12,15,18,21); }
	elseif( $yr<2015 ) { $lev = array(0,6,9,12,15,18,21); }
	elseif( $yr<2016 ) { $lev = array(0,3,6,9,12,15,18); }
	elseif( $yr==2016 && $wk<32 ) { $lev = array(0,6,6,8,10,12,12); }
	elseif( $yr==2017 && $wk<36 ) { $lev = array(0,6,6,18,24,27,27); }
	else { $lev = array(0,6,6,15,21,24,24); }

	return $lev[(int)$lvl].'%';
}

function getEndLvl($pv) {
	foreach($_SESSION['minpv'] as $k=>$v) { if($pv<$v) break; }
	return $k>0 ? $k-1 :0;
}

function getCsvWk($yr,$wk) {
	$path = '../src/calendar/boweek'.$yr.'.csv';
	if(realpath($path)) {
		$csv = file($path);
		return trim(str_replace(',','',$csv[(int)$wk]));
	}
}

function getDatWk($dat,$wk=0,$recap=0) {
	$or = getStatus('995');
	if( $or&&!$recap ) {
		$dat = sprintf("%02d", $or) . ( $or=='24' ? date('Y')-1 : date('Y') );
		$wk  = 1;
	}

	$ad  = ( $wk ? "wk='".substr($dat,0,2)."' AND yr='".substr($dat,-4)."'" : "'$dat' BETWEEN fst AND lst" );
	$con = SQLi('beta');

	$s = $con->query("SELECT * FROM tblsched WHERE $ad") or die(mysqli_error($con));
	$r = mysqli_fetch_assoc($s);

	return array(sprintf("%02d", $r['wk']), $r['yr'], $r['fst'], $r['lst'], $r['vb']);
	mysqli_close($con);
}

function getStatus($id) {
	$con = SQLi('beta');
	$rs  = $con->query("SELECT status FROM tbladmin WHERE id=$id") or die(mysqli_error($con));
	$rw  = $rs->fetch_array();
	return $rw['status'];
	mysqli_close($con);
}

function getDat($db,$tbl,$ret,$wer,$weris) {
	$con= SQLi($db);
	$rs = $con->query("SELECT $ret FROM $tbl WHERE $wer=$weris") or die(mysqli_error($con));

	if($rs->num_rows>0) {
		$r  = $rs->fetch_array();
		return $r[$ret];

	} else {
		return '';
	}

	mysqli_close($con);
}

function titleCase($n) {
	$split = array(' ','-',"O'","L'","D'",'St.','Mc');
	$lcase = array('the','van','den','von','und','der','de','da','of','and',"l'","d'");
	$ucase = array('II','III','IV','VI','VII','VIII','IX','JR');

	$n = strtolower($n);
	foreach($split as $d) {
		$words = explode($d,$n);
		$new = array();

		foreach($words as $word) {
			if(in_array(strtoupper($word),$ucase)) $word = strtoupper($word);
			else if(!in_array($word,$lcase)) $word = ucfirst($word);
			$new[] = $word;
		}

		if(in_array(strtolower($d),$lcase)) $d = strtolower($d);

		$n = join($d,$new);
	}

	return $n;
}

function formatDate($dat,$fm='Ymd',$ret='Y.m.d') {
	if(strlen($dat)<8) { $bak='INVALID DATE';

	} else {
		$d=DateTime::createFromFormat($fm,$dat);

		if(($d&&$d->format($fm)==$dat)) {
			// switch($ret) {
				// case 0:$bak=$d->format('Y.m.d');break;
				// case 1:
				$bak=$d->format($ret);
				// break;
			// }
		} else $bak='INVALID DATE';
	}

	return $bak;
}

function dropDate($sel,$f) {
	$op = '';

	switch($f) {
		case 'mo':

			for($i=1;$i<=12;$i++) {
				$i   = sprintf("%02d", $i);
				$obj = DateTime::createFromFormat('!m',$i);
				$op .= '<option value="'.$i.'" '.($sel==$i?SELECTED:'').'>'.$obj->format('F').'</option>';
			}
			break;

		case 'yr':

			for($i=2010;$i<=date('Y');$i++) {
				$obj = DateTime::createFromFormat('!Y',$i);
				$op .= '<option value="'.$i.'" '.($sel==$i?SELECTED:'').'>'.$obj->format('Y').'</option>';
			}
			break;

	}

	return '<select class="monyr" name="'.$f.'"><option>--</option>'.$op.'</select>';
}

function loadHead($title,$tail='',$add=0) {
	$pixel = "
<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function() {n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '514297152776038');
  fbq('track', 'PageView');
</script>
";

	$pixel .= '
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=514297152776038&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->
';

	$msg = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title>DLC '.$title.'</title>
	<meta http-equiv="Content-Language" content="en" />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="shortcut icon" href="/src/favicon.ico"></link>
	<link rel="stylesheet" type="text/css" media="screen" href="/css/styles.css"></link>
	<link rel="stylesheet" type="text/css" media="print" href="/css/print.css"></link>';

	$msg .= $tail.$pixel.'</head><body><div id="bg"> </div><div class="loading"><img src="/src/loading.gif" alt="" /></div>';
	return $msg;
}

function loadLogo($frm) {
	return '<div id="head"><a href="'.LOGO_LINK.'"><img src="/src/dlc_logo_min.png" alt="'.DLC_FULL.'" /></a>'.$frm.'</div>';
}

function loadFoot($idx='',$add='',$arr=array(),$bot=0) {
	$x= $idx=='' ? '</div>' :'';

	$x .= $bot?'':'<div id="foot">'.$idx.'<div class="foot_sig">'.$add.'<span>'.DLC_FULL.' &copy;2009-'.date('Y',time()).' All rights reserved.</span></div></div>';
	$x .= '</body></html>';

	if(!empty($arr)) {
		foreach($arr as $i) { $i!=''?$x.='<script type="text/javascript" src="'.$i.'"></script>':'';}
	}

	$x .= '<style type="text/css">.loading{display:none}</style>';
	return $x;
}

function isImage($f) {$r=false;
	$a = array('gif','jpg','jpeg','pjpeg','png');

	foreach($a as $v) {
		if($f=='image/'.$v) {
			$r = true;
			break;
		}
	}

	return $r;
}

function testScope($n,$url='') {
	$i = 0;
	if( ISIN_ADMIN || ISIN_GOS || ISIN_PCM || ISIN_MIG ) {
		$con = SQLi('beta');
		$m   = explode( '|', $n);

		foreach($m as $a=>$b) {
			$rs = $con->query("SELECT $b FROM tbladmin WHERE un='".LOGIN_ID."'") or die(mysqli_error($con));
			$rw = $rs->fetch_array();
			$i += (int)$rw[$b];
		}

		if( (bool)$i<1 && $url!='' ) {
			reloadTo($url);
			exit;
		}
	}

	return (bool)$i;
}

function test_input(&$dat) {
    return htmlspecialchars(stripslashes(trim($dat)),ENT_QUOTES);
}

function testExist($id,$db,$tbl,$fld) {
	$con = SQLi($db);
	$rs  = $con->query("SELECT $fld FROM $tbl WHERE $fld='$id'");
	return ($rs->num_rows==1);
}

function testImg($path,$full=0) {
	$fl  = '';
	$arr = array('jpg','jpeg','gif','png','wdp');

	foreach($arr as $v) {
		$p = $full ? $path : "$path.$v";

		if(file_exists($p)) {
			$fl = $p;
			break;
		}
	}

	return $fl;
}

function testAllow($id) {
	$test  = 0;

	$con   = SQLi('distributor');
	$qry   = "SELECT bhppv FROM bohstp WHERE bhdid='$id'";
	$rs    = $con->query($qry) or die(mysqli_error($con));
	$test += $rs->num_rows;

	$qry   = "SELECT SUM(ompv) pv FROM ormstp WHERE omdid='$id' GROUP BY ompyr,ompmo HAVING pv>=".minAllow;
	$rs    = $con->query($qry);
	$test += $rs->num_rows;

	$con   = SQLi('orders');
	$rs    = $con->query($qry);
	$test += $rs->num_rows;

	mysqli_close($con);
	return $test;
}

function trim_escape($dat) {
	if(!is_array($dat)) return (stripslashes(trim($dat)));
}

function logout($url) {
	if(isset($_COOKIE[session_name()])) setcookie(session_name(),'',time()-3600,'/');
	$_SESSION=array();

	if(ini_get("session.use_cookies")) {
		$params=session_get_cookie_params();
		setcookie(session_name(),'',time() - 42000,
		$params["path"],$params["domain"],
		$params["secure"],$params["httponly"]
		);
	}

	session_destroy();
	header('Location:'.$url);
}

function reloadTo($url,$n=0) {
	echo '<META HTTP-EQUIV=Refresh CONTENT="'.$n.';URL='.$url.'">';exit;
}
?>
