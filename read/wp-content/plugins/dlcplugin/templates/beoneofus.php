<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
global $dlcuser;
require_once( '../reg/package.php' );
?>
