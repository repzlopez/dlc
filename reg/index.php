<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
defined('INCLUDE_CHECK') or define('INCLUDE_CHECK',1);

require_once('../admin/setup.php');
if( !ISIN_DISTRI && !ISIN_GOS && !ISIN_PCM && !ISIN_MIG && !GUEST && !RESELLER ) {
	reloadTo('../errors');
	exit;
}

if( ISIN_GOS || ISIN_PCM || ISIN_MIG ) unset($_SESSION['rfr']);

$_SESSION['lastURI'] = 'onreg';
$found = 0;
$x = '';
// echo GUEST . ' == ' . ISIN_GOS . ' == ' . ISIN_PCM . ' == ' . ISIN_MIG;
// print_r($_SESSION);

$arr = array('id','dsdid','dstin','dslnam','dsfnam','dsmnam','dsemail',
	'dsbday','spouse','dssex','dsstat','dssid','dsscont','dsbankact','dsbankno',
	'dscont','dsstrt','dsbrgy','dscity','dsprov','dscoun','noslot',
	'i','do','bad','styleis','baddate','badspon','badscan','scan');
foreach( $arr as $v ) $$v = null;

if( isset($_GET['i']) ) {
	$i   = $_GET['i'];
	$wer = ISIN_DISTRI&&$i===DIST_ID?"dsdid='$i' AND status=0":"id='$i' AND referrer='".( ISIN_DISTRI ? DIST_ID : ( ISIN_GOS||ISIN_PCM||ISIN_MIG ? LOGIN_BRANCH :'') )."'";
	$con = SQLi('beta');
	$rs  = mysqli_query($con,"SELECT * FROM tblolreg WHERE $wer");

	if( mysqli_num_rows($rs)>0 ) {
		$found = 1;
		$rw = mysqli_fetch_assoc($rs);
		foreach( $rw as $k=>$v ) $$k = $v;
	}//else if(!ISIN_DISTRI) reloadTo('../errors');

	$scan = testImg("scan/$i");
}

if( !$found && $i===DIST_ID && isset($_SESSION['dsmstp_update']) ) {
	foreach( $_SESSION['dsmstp_update'] as $k=>$v ) {
		$_SESSION['post'][$k] = $v;
	}

	$scan = 1;
}

if( isset($_SESSION['post']) ) {
	foreach( $_SESSION['post'] as $k=>$v ) $$k=$v;

	$styleis = (strpos($bad,'RED')!==false) ? RED :'';
	$baddate = (strpos($bad,'invalid date')!==false) ? RED :'';
	$badspon = (strpos($bad,'Sponsor ID')!==false) ? RED :'';
	$badscan = (strpos($bad,'SCANNED COPY')!==false) ? ' style="background-color:#f00;"' :'';
}

define( 'ITS_ME', (ISIN_DISTRI&&DIST_ID==$dsdid) );
$title = RESELLER ? 'Reseller Registration': ( ITS_ME ? 'Profile' : 'Online Registration' );

if( SPECTATOR&&!OVERRIDE ) { $responsor=''; }
else $responsor = ITS_ME ? '<a href="'.DLC_ROOT.'/reg/responsor.php" class="fnon btn">RESPONSOR ME</a>' :'';

$back_url = ISIN_DISTRI ? DLC_MYPAGE.(ITS_ME?'':'?get=pool') : (ISIN_GOS?DLC_GORT.'/distri': (ISIN_PCM?DLC_PCRT.'/distri': (ISIN_MIG?DLC_MGRT.'/distri': DLC_ROOT) ) );

if( !ISIN_DISTRI ) {
	$x .= '<div class="rt more noprint">Be sure to SUBMIT this form<br>DO NOT CLICK "BACK" OR CLOSE THIS BROWSER<br>Doing so may result in loss of data and you may need to repeat thw whole process</div><div class="printonly"><br><br></div>';
}

$x .= '<ul class="print"><li><a href="javascript:window.print()"></a></li></ul>';
$x .= '<form enctype="multipart/form-data" method="post" action="'.DLC_ROOT.'/reg/post.php" id="olreg"><ul>';
$x .= '<li><h2 class="blue ct">'.$title.'</h2>'.( ITS_ME && $found ? '<em class="smaller lite">(Update request pending)</em>':'').'<br><input type="hidden" name="id" value="'.$id.'" /></li>';
if( ITS_ME ) $x .= '<li'.($dsdid!=''?' class="hide"':'').'><label></label><label><input type="checkbox" /> Signed DAF (Distributor Application Form)</label></li>';
$x .= '<li'.( ( !ISIN_ADMIN && $dsdid=='' ) || ISIN_WP ? ' class="hide"' :'').'><label>Distributor ID *:</label><input type="text" name="dsdid" class="txt ex" value="'.$dsdid.'" '.READ_ONLY.' maxlength=32 required /></li>';
$x .= '<li><label>Last Name: * </label><input type="text" name="dslnam" class="txt" value="'.$dslnam.'" '.$styleis.' maxlength=32 required /></li>';
$x .= '<li><label>First Name: * </label><input type="text" name="dsfnam" class="txt" value="'.$dsfnam.'" '.$styleis.' maxlength=32 required /></li>';
$x .= '<li><label>Middle Name:</label><input type="text" name="dsmnam" class="txt ex" value="'.$dsmnam.'" maxlength=32 /></li>';
$x .= '<li><label>Birth Date: * </label><input type="text" name="dsbday" class="txt" value="'.$dsbday.'" '.$styleis.$baddate.' placeholder="mmddyyyy" pattern="[\d]{8}" maxlength=8 required /></li>';
$x .= '<li><label>Spouse Name:</label><input type="text" name="spouse" class="txt ex" value="'.$spouse.'" /></li>';
$x .= '<li><label>Gender:</label><label><input type="radio" name="dssex" class="txt s0" value="0" '.$styleis.' '.(!$dssex?C_K:'').' /> Male</label> <label><input type="radio" name="dssex" class="txt s0" value="1" '.($dssex?C_K:'').' /> Female</label></li>';
$x .= '<li><label>Marital Status:</label><label><input type="radio" name="dsstat" class="txt s0" value="0" '.$styleis.' '.(!$dsstat?C_K:'').' /> Single</label> <label><input type="radio" name="dsstat" class="txt s0" value="1" '.($dsstat==1?C_K:'').' /> Married</label> <label><input type="radio" name="dsstat" class="txt s0" value="2" '.($dsstat==2?C_K:'').' /> Separated</label> <label><input type="radio" name="dsstat" class="txt s0" value="3" '.($dsstat==3?C_K:'').' /> Widow/er</label></li>';
$x .= '<li><label>Contact #: * </label><input type="text" name="dscont" class="txt" value="'.$dscont.'" '.$styleis.' placeholder="639123456789" maxlength=16 required /></li>';
$x .= '<li><label>Unit / Street:</label><input type="text" name="dsstrt" class="txt ex" value="'.$dsstrt.'" /></li>';
$x .= '<li><label>Subd / Brgy:</label><input type="text" name="dsbrgy" class="txt ex" value="'.$dsbrgy.'" /></li>';
$x .= '<li><label>Town / City:</label><input type="text" name="dscity" class="txt ex" value="'.$dscity.'" /></li>';
$x .= '<li><label>Province / ZIP: * </label><input type="text" name="dsprov" class="txt" value="'.$dsprov.'" '.$styleis.' required /></li>';
$x .= '<li><label>Country: * </label><input type="text" name="dscoun" class="txt" value="'.($dscoun!=''?$dscoun:'PHILIPPINES').'" '.$styleis.' required /></li>';
$x .= '<li><label>TIN: '.($dstin!=''?'':'<span class="smaller">** 0 if none</span>').' * </label><input type="text" name="dstin" class="txt" value="'.$dstin.'" '.$styleis.' placeholder="000-000-000" maxlength=12 required /></li>';
$x .= '<li><label>BPI Acct Name:</label><input type="text" name="dsbankact" class="txt" value="'.$dsbankact.'" /></li>';
$x .= '<li><label>BPI Acct #:</label><input type="text" name="dsbankno" class="txt" value="'.$dsbankno.'" /></li>';
$x .= '<li><label>E-mail:</label><input type="text" name="dsemail" class="txt" value="'.$dsemail.'" maxlength=64 /></li>';
$x .= '<li class="'.(GUEST?' hide':'').'"><label>Sponsor ID: * </label><input type="text" name="dssid" class="txt ex" value="'.(GUEST?GUEST:$dssid).'" '.$styleis.$badspon.' '.(ITS_ME?READ_ONLY:'').' maxlength=32 required /> '.$responsor.'</li>';
$x .= '<li class="'.(GUEST?' hide':'').'"><label>Sponsor Contact:</label><input type="text" name="dsscont" class="txt" value="'.$dsscont.'" '.$styleis.' maxlength=32 /></li>';
$x .= '<li class="smaller"><label></label>* Required fields'.(ISIN_DISTRI&&$noslot?'<input type="hidden" name="noslot" value=1 />':'').'</li>';

if( !ISIN_DISTRI ) {
	$x .= '<li class="noprint"><label></label>'. ( $scan!='' ? '<a href="'.$scan.'" target="_blank">CLICK TO VIEW SCANNED COPY</a>' : '<input type="file" class="txt ex" name="dsscan" '. $badscan . ' /><br><span class="smaller noprint"><label></label>** copy of ID / DAF with signature:</span>') .'</li>';
	$x .= '<li class="ct noprint"><br /><h3>SAMPLE</h3><img src="/reg/scan/sample.jpg" class="sampleimg" title="Sample upload" /><img src="/reg/scan/sampledaf.jpg" class="hide" /><br /><br />';
	$x .= '<p class="bad ct">Requests will not be processed without a PHOTO/SCANNED COPY of a valid ID <br />and Signature</p></li>';
}

$x .= '<li>&nbsp;</li>';
$x .= '<li><input type="hidden" name="do" value="'. $do .'" />';
$x .= ( GUEST || ISIN_WP ) ? '': '<input type="button" class="btn" value="Cancel" onclick="document.location='."'". $back_url ."'".';return false;" /> ';
$x .= '<input type="submit" name="submit" class="btn" value="Submit" />';
$x .= '<span class="s1"></span></label><span class="bad">'. $bad .'</span></li>';
$x .= '</ul><div class="rt smaller"><br>'. date('m.d.Y') .'</div></form>';
$x .= '';

if( ( ISIN_GOS || ISIN_PCM || ISIN_MIG || !ISIN_WP ) && !RESELLER ) {
	$msg  = loadHead($title).loadLogo('');
	$msg .= '<div id="container">'. $x .'</div>';

	ob_start();
	echo $msg;
	unset($_SESSION['post']);

	$arrJs = array('/js/jquery/jquery-1.7.1.min.js','/js/common.js');
	echo loadFoot('','',$arrJs);
	ob_end_flush();
}
?>
