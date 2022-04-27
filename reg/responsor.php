<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../func.php');

if( !ISIN_DISTRI && !GUEST ){
	reloadTo(DLC_ROOT);
	exit;
}

$_SESSION['lastURI'] = 'onreg';
$title = ' Responsoring';
$x= $bad= $oldsponsor= $oldname= $newname= '';

// print_r($_POST);
// echo "<br><br>";

if( isset($_GET['cancelrequest']) && $_GET['cancelrequest'] ) {
	$qry = "UPDATE responsor SET status=0 WHERE dsdid='" . DIST_ID . "'";
	mysqli_query(SQLi('distributor'),$qry) or die(mysqli_error($con));

	$x  = loadHead($title).loadLogo('');
	$x .= '<div id="container" style="height:100%;text-align:center;"><h3>RESPONSORING REQUEST CANCELLED</h3></div>';
	$x .= '<style type="text/css">.loading{display:none}</style>';
	echo $x;
	reloadTo( '/read/account/profile/?i='. DIST_ID, 1 );
}

$qry = "SELECT r.*,d.dssid oldsponsor,r.id forresponsor,
		(SELECT CONCAT(dslnam,', ',dsfnam,' ',dsmnam) FROM distributors WHERE dsdid=d.dssid) oldname,
		(SELECT CONCAT(dslnam,', ',dsfnam,' ',dsmnam) FROM distributors WHERE dsdid=r.newsponsor) newname
	FROM distributors d
	LEFT JOIN (SELECT * FROM responsor WHERE status=1) r ON r.dsdid=d.dsdid
	WHERE d.dsdid='".DIST_ID."'
	ORDER BY date DESC
	LIMIT 1";

$con = SQLi('distributor');
$rs  = mysqli_query($con,$qry) or die(mysqli_error($con));
if( mysqli_num_rows($rs)>0 ) {
	$rw = mysqli_fetch_array($rs);
	foreach($rw as $k=>$v) $$k=$v;
}


if( isset($_POST['submit']) ) {
	$kdata= $idata= $udata= '';

	$con = SQLi('beta');
	foreach($_POST as $k=>$v) {
		if( strpos($k,'valid') !== false && !$v ) {
			$bad = 'Unable to continue. All requirements must be met.';
		}

		$dat = trim_escape($v);
		$$k  = $dat;

		if( $k!='submit' ) {
			$kdata .= "$k,";
			$idata .= preg_match("/dsdid|dssid|newsponsor/i",$k) ? "'$dat'," : "$dat,";
			$udata .= preg_match("/dsdid|dssid|newsponsor/i",$k) ? $k."='$dat'," : $k."=$dat,";
		}
	}

	unset($_POST);

	if( $bad=='' ) {
		$con = SQLi('distributor');
		$qry = "SELECT dsdid FROM distributors d WHERE dsdid='$newsponsor'";
		$rs  = mysqli_query($con,$qry) or die(mysqli_error($con));

		if( $dssid==$newsponsor ) {
			$bad='Unable to continue. NEW Sponsor is the same as OLD Sponsor.';

		} elseif( mysqli_num_rows($rs)<1 ) {
			$bad = 'Unable to continue. Invalid Sponsor ID.';

		} else {
			$idata .= "'". date(TMDSET) ."',1";
			$udata .= "date='". date(TMDSET) ."',status=1";

			$con = SQLi('distributor');
			$qry = "INSERT INTO responsor (id,$kdata date,status) VALUES (null,$idata) ON DUPLICATE KEY UPDATE $udata";
			mysqli_query($con, $qry) or die(mysqli_error($con));

			$x  = loadHead($title) . loadLogo('');
			$x .= '<div id="container" style="height:100%;text-align:center;"><h3>RESPONSORING REQUEST SUBMITTED</h3></div>';
			$x .= '<style type="text/css">.loading{display:none}</style>';
			echo $x;
			reloadTo( '/read/account/profile/?i='. DIST_ID, 1 );
		}
	}
}

$x .= '<div class="print"><a href="javascript:window.print()"></a></div><br>';
$x .= '<form id="responsor" method="post" action="'.$_SERVER['PHP_SELF'].'" ><h2 class="blue ct">'.$title.'</h2><br><br>';

$x .= '<ul><lh>Responsoring</lh><li>
	<p>Any active distributor can responsor other distributors after six (6) months of inactivity. Distributors who requested termination or are terminated can also be responsored after they have been inactive for six (6) months subject to the approval of the Company.</p>
	<p>All downlines of an International Distributor can only be responsored after the downlines of the International Distributor have been inactive for three (3) years.</p>
</li></ul><br>

<ul><lh>Definition of Inactivity</lh><li>
	<p>Products purchased from the Company within six (6) months is less than 100PV.</p>
	<p>Not sponsoring any individual to participate into the DLC Sales Organization within six (6) months.</p>
	<p>Not attending any Company-related meetings, including, but not limited to, OPP, trainings, seminars, conferences, home parties, and other activities in connection with DLC within six (6) months. Any photos, persons, documents, records or videotapes will be valid evidence to prove the Distributor’s participation in the activities.</p>
</li></ul><br>

<ul><lh>Acknowledgement</lh><li>
	<p class="b">By submitting this form, you acknowledge that you have read, understood and agree to the terms of this Responsoring Agreement.<p>
	<p>YOU WILL NOT RETAIN ANY DOWNLINE and relinquish all rights, if any, you previously obtained and it will not be possible to reinstate that position.</p>
	<p>With this responsoring, YOU WILL BEGIN AS A 6% LEVEL DISTRIBUTOR (<em>if you were at least 6% before the responsoring AND the new sponsor is at least 6%</em>), or AS A NEW DISTRIBUTOR in the DLC marketing program.</p>
</li></ul><br><br>';
// $x.=$id;
$x .= '<ul><input type="hidden" name="dsdid" value="'.DIST_ID.'" /><input type="hidden" name="dssid" value="'.$oldsponsor.'" />';
$x .= '<lh>To proceed, kindly confirm the following:</lh>';
$x .= '<li><input type="hidden" name="validtime" value=0 /><input type="checkbox" name="validtime" value=1 '.($validtime?C_K:'').' /> I have BEEN INACTIVE in the past six (6) months</li>';
$x .= '<li><input type="hidden" name="validpv" value=0 /><input type="checkbox" name="validpv" value=1 '.($validpv?C_K:'').' /> I have NOT PURCHASED MORE THAN 100PV of DLC products in any one (1) cycle in the past six (6) months</li>';
$x .= '<li><input type="hidden" name="validspon" value=0 /><input type="checkbox" name="validspon" value=1 '.($validspon?C_K:'').' /> I have NOT SPONSORED any individual into DLC in the past six (6) months</li>';
$x .= '<li><input type="hidden" name="validmeet" value=0 /><input type="checkbox" name="validmeet" value=1 '.($validmeet?C_K:'').' /> I have NOT ATTENDED any DLC-related online/offline meetings in the past six (6) months</li>';
$x .= '</ul><br>';

$x .= '<ul><lh>Old Sponsor</lh>';
$x .= '<li><p><strong>'.$oldsponsor.'</strong> '.$oldname.'</p></li>';
$x .= '</ul><br>';

$x .= '<ul><lh>New Sponsor</lh>';
$x .= '<li><p><input type="text" name="newsponsor" placeholder="enter Sponsor ID here" value="'.$newsponsor.'" class="s4" /> '.$newname.'</p></li>';
$x .= '</ul><br>';

$x .= '<em>* As per policy, responsoring requests are processed AFTER three (3) WORKING DAYS<em><br><br>';

$x .= '<ul><li><p><input type="button" class="btn" value="Back" onclick="document.location='."'".DLC_MYPAGE."'".';return false;" /> ';
$x .= ($forresponsor?'<input type="button" class="btn" value="Cancel Request" onclick="document.location='."'?cancelrequest=1'".';" /> ':'');
$x .= '<input type="submit" name="submit" class="btn" value="'.($forresponsor?'Update':'Submit').'" /> ';
$x .= '<span class="bad">'.$bad.'</span></p></li></ul>';
$x .= '<div class="rt smaller"><br>'.date('m.d.Y').'</div></form>';

ob_start();
echo loadHead($title) . loadLogo('');
echo '<div id="container">'. $x;
$arrJs = array('/js/jquery/jquery-1.7.1.min.js');
echo loadFoot('', '', $arrJs, 1);
ob_end_flush();
?>
