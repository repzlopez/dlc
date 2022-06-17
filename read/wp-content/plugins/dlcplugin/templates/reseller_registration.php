<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
global $dlcuser;
$dlcuser['reseller']=1;
$_SESSION['rfr']='reseller';
$_SESSION['reseller']=1;

if(isset($_SESSION['post']['bad'])){
     echo '<h4 class="bad ct">'.$_SESSION['post']['bad'].'</h4>';
}
require_once( '../reg/index.php' );
echo $x;
?>
