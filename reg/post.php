<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');
// print_r($_POST);
// echo GUEST .' == '. ISIN_GOS .' == '. ISIN_PCM .' == '. ISIN_MIG;
// echo "<br><br>";
if ( isset($_POST['submit']) ) {
	$_SESSION['post'] = null;
	$testArr = array('dslname','dsfname','dscont','dsprov','dsbday','dstin','dssid','dsscon');
	$idata   = $udata = $dsscan = '';
	$reqmiss = $noslot = 0;

	$con = SQLi('beta');
	foreach ($_POST as $k=>$v) {
		if ( in_array($k,$testArr) ) {
			if ( GUEST && $k=='dssid' ) {}
			else if (trim($v)=='') $reqmiss++;
		}

		$dat = trim_escape($v);
		$dat = $k!='dsemail' ? strtoupper($dat) : $dat;
		$$k  = $dat;
		$_SESSION['post'][$k] = $dat;

		if ( $k!='do' && $k!='go' && $k!='submit' ) {
			$idata .= $k=='noslot' ? '': ( $k == 'id' ? (int)$dat . "," : "'$dat'," );
			$udata .= $k=='dsdid' ? '': $k."='$dat',";
		}
	}

	unset($_POST);
// echo $idata.'<br><br>';
// echo $udata.'<br><br>';
	$url    = ( ISIN_GOS || ISIN_PCM || ISIN_MIG ) ? '/reg' : '/read/'. $shortlink .'account/registration/';
	$dsscan = (isset($_FILES["dsscan"])?$_FILES["dsscan"]:null);

	if ( $submit == 'SUBMIT' ) {
		$_SESSION['post']['bad'] = 'Unable to continue. ';

		if ( !testExist($dssid,'distributor','distributors','dsdid') && $do<2 && !GUEST && !ISIN_GOS && !ISIN_PCM && !ISIN_MIG ) {
			$_SESSION['post']['bad'].='Sponsor ID does not exist.';
	
		} elseif ( $reqmiss>0 ) {
			$_SESSION['post']['bad'].='Fields in RED are required.';

		} elseif ( !formatDate($dsbday,'mdY',1) ) {
			$_SESSION['post']['bad'].='Invalid date. Please follow the date format (mmddyyyy).';

		} elseif ( $dsdid==''&&$dsscan['error']>0 ) {
			$_SESSION['post']['bad'].='SCANNED COPY is required for NEW applications.';

		} else {
			$idata .= "'".date(TMDSET,time())."','".( GUEST ? GUEST : ( DIST_ID ? DIST_ID : ( ISIN_GOS || ISIN_PCM || ISIN_MIG ? LOGIN_BRANCH :'') ) )."',".(OLREG_NOSLOT?1:$noslot).",0";
			$udata  = substr_replace($udata,'',-1).',status=0';
// echo "INSERT INTO tblolreg VALUES ($idata) ON DUPLICATE KEY UPDATE $udata<br><br>";
			$con    = SQLi('beta');
			mysqli_query($con,"INSERT INTO tblolreg VALUES ($idata) ON DUPLICATE KEY UPDATE $udata") or die(mysqli_error($con));
			$idno   = sprintf('%016d',mysqli_insert_id($con));

			if ( !$noslot ) mysqli_query($con,"UPDATE ".DB."orders.tblslots SET olregid='$idno',dsfor='$dssid' WHERE dsdid='".DIST_ID."' AND (dsfor='' OR dsfor IS NULL) ORDER BY slotid LIMIT 1") or die(mysqli_error($con));

			mysqli_close($con);
			unset($_SESSION['post']);

			if ( isset($dsscan) ) {
				$img = $_FILES["dsscan"];
				$ext = strtolower(pathinfo($img['name'],PATHINFO_EXTENSION));
				$fn  = "$idno.$ext";
				move_uploaded_file($img["tmp_name"],'scan/'.$fn);
			}

			if( ISIN_GOS ) {
				reloadTo(DLC_GORT.'/distri');

			} elseif( ISIN_PCM ) {
				reloadTo(DLC_PCRT.'/distri');

			} elseif( ISIN_MIG ) {
				reloadTo(DLC_MGRT.'/distri');

			} else {
				$req = ISIN_DISTRI ? 'REQUEST' : 'APPLICATION';
				$x   = loadHead('| Online Registration').loadLogo('');
				$x  .= '<div id="container" style="height:100%;text-align:center;"><h3>'.$req.' SUBMITTED</h3></div>';
				$x  .= '<style type="text/css">.loading{display:none}</style>';

				$url = GUEST ?
						DLC_ROOT.'?rfr='.GUEST :
							( $dsdid==DIST_ID ?
								'/read/'.$shortlink.'account/profile/?i='.DIST_ID :
									( $id!='' ? "../reg?i=$id" :'' ) );
			}
		}

		reloadTo($url, 1);
	}

} else {
	echo '<h3>UPDATE ERROR</h3>';
	echo '<h4>Contact DLC Admin</h4>';
	echo '<input action="action" onclick="window.history.go(-1); return false;" type="submit" value="Back" />';
}
?>
