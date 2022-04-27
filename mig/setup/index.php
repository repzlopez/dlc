<?php
if ( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

define('INCLUDE_CHECK',1);
require('../../admin/setup.php');
require('../func.php');

if ( !ISIN_MIG ) { reloadTo(DLC_MGRT);exit; }
$_SESSION['mig_last'] = DLC_MGRT;
$title   = 'MIG | Setup';
$content = 'setup';

ob_start();
include('../head.php');

$tbl = 'tblsetup';
$con = SQLi('beta');

echo '<form id="changepass"><input type="hidden" name="id" value="'.LOGIN_ID.'" /><input type="hidden" name="wh" value="'.LOGIN_BRANCH.'" /><ul>';
echo '<div class="blue">ACCOUNT</div>';
echo '<li><label>Current Password:</label><input type="password" name="acpascur" class="txt" value="" /></li>';
echo '<li><label>New Password:</label><input type="password" name="acpasold" class="txt" value="" /></li>';
echo '<li><label>Confirm Password:</label><input type="password" name="acpasnew" class="txt" value="" /></li>';
echo '<span></span><input type="submit" name="submit" class="btn" value="Change" /></ul></form>';
include('../foot.php');
ob_end_flush();
?>
