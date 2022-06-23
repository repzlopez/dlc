<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../admin/setup.php');
require('../func.php');
if ( !ISIN_MIG ) { reloadTo(DLC_MGRT);exit; }
$title   = 'MIG | Distributor Service';
$content = 'distri';
ob_start();
include('../head.php');

$old = null;
$con = SQLi('beta');
$_SESSION['mig_last'] = DLC_MGRT;
$x  = '<ul class="list">';

$qry = "SELECT o.*,o.status stat,CONCAT(d.dslnam,', ',d.dsfnam) sponsor FROM tblolreg o
     LEFT JOIN ".DB."distributor.distributors d ON d.dsdid=o.dssid
     WHERE o.referrer='".LOGIN_BRANCH."'
     AND o.status<2
     ORDER BY o.status,o.id";

     $rs = mysqli_query($con,$qry) or die(mysqli_error($con));
     while( $r=mysqli_fetch_assoc($rs) ) {
     $test = testAllow($r['dssid']);
     $new  = $r['status'];

     if ( isset($old) && $old<$new ) {
          $x .= '<li><br></li>';
     }

     if ( $old<$new ) {
          $x .= '<li><h3>'. ( $r['status'] ? 'ENCODED' : 'PENDING' ) .'</h3></li>';
          $x .= '<li class="hdr nobg">';
          $x .= '<span class="s4">Distributor ID</span>';
          $x .= ( IS_MIG ? '<span class="s3">Branch</span>' :'' );
          $x .= '<span class="s5">Name</span>';
          $x .= '<span class="s3">Posted</span>';
          if ( !$r['status'] ) {
               $x .= '<span class="s5">Sponsor</span>';
               $x .= '<span class="s4 ct">Allowed to Sponsor</span>';
          }
          $x .= '</li>';
     }

     $x .= '<li rel="'.$r['dsdid'].'">';
     $x .= '<span class="s4">'.$r['dsdid'].'</span>';
     $x .= ( IS_MIG ? '<span class="s3">'.$r['referrer'].'</span>' :'' );
     $x .= '<span class="s5"><a href="../../reg?i='.$r['id'].'">'.ucwords(strtolower($r['dsfnam'].' '.$r['dslnam'])).'</a></span>';
     $x .= '<span class="s3">'.date('m.d.Y',strtotime($r['date'])).'</span>';
     if ( !$r['status'] ) {
          $x .= '<span class="s5">'.( $r['sponsor']!='' ? $r['sponsor'] :'<span class="bad ct">SPONSOR PENDING</span>' ).'</span>';
          $x .= '<span class="s4 ct">'.( $test ? 'ALLOWED' : minAllow . 'PV REQUIRED').'</span>';
     }
     $x .= '</li>';

     $old = $new;
} mysqli_close($con);

if ( !IS_MIG ) $x .= '<li><br><a href="../../reg">+ ADD REQUEST</a></li>';
$x .= '</ul>';

echo $x;
include('../foot.php');
ob_end_flush();
?>
