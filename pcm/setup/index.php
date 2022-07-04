<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

require('../../admin/setup.php');
require('../func.php');

if( !ISIN_PCM ) {
	reloadTo(DLC_PCRT);
	exit;
}

$_SESSION['pcm_last'] = DLC_PCRT;
$title = 'PCM | Setup';
$content = 'setup';
include('../head.php');
ob_start();

$tbl = 'tblsetup';
$con = SQLi('pcm');
$did = LOGIN_ID;
$dwh = LOGIN_BRANCH;
$drc = $dcn= $dad= $dcr= '';

$rs = $con->query("SELECT * FROM $tbl WHERE id='".LOGIN_BRANCH."'") or die(mysqli_error($con));
$rw = mysqli_fetch_array($rs);

if(mysqli_num_rows($rs)>0) {
	$did = $rw['id'];
	$dwh = $rw['wh'];
	$drc = $rw['dfrec'];
	$dcn = $rw['dfcon'];
	$dad = $rw['dfadd'];
	$dcr = $rw['dfcor'];
}

mysqli_close($con);

echo '<form id="updatedelivery"><input type="hidden" name="id" value="'.$did.'" /><input type="hidden" name="wh" value="'.$dwh.'" /><ul>';
echo '<div class="blue">DELIVERY</div>';
echo '<li><label>Receiver:</label><input type="text" name="dfrec" class="txt" value="'.$drc.'" /></li>';
echo '<li><label>Contact #:</label><input type="text" name="dfcon" class="txt" value="'.$dcn.'" /></li>';
echo '<li><label>Address:</label><input type="text" name="dfadd" class="txt" value="'.$dad.'" /></li>';
echo '<li><label>Courier:</label><input type="text" name="dfcor" class="txt" value="'.$dcr.'" /></li>';
echo '<span></span><input type="submit" name="submit" class="btn" value="Update" /></ul></form>';

echo '<form id="changepass"><input type="hidden" name="id" value="'.$did.'" /><input type="hidden" name="wh" value="'.$dwh.'" /><ul>';
echo '<div class="blue">ACCOUNT</div>';
echo '<li><label>Current Password:</label><input type="password" name="acpascur" class="txt" value="" /></li>';
echo '<li><label>New Password:</label><input type="password" name="acpasold" class="txt" value="" /></li>';
echo '<li><label>Confirm Password:</label><input type="password" name="acpasnew" class="txt" value="" /></li>';
echo '<span></span><input type="submit" name="submit" class="btn" value="Change" /></ul></form>';

include('../foot.php');
ob_end_flush();
?>
