<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);

require('../func.php');
$path="../remit/scan/";

// print_r($_FILES);
if($_FILES) {
	$img = $_FILES["gc_scan"];
	$ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));

	$fn = isset($_SESSION['gos_reftran']) ? $_SESSION['gos_reftran'] : $_SESSION['gos_edit'];
	$fn = LOGIN_BRANCH . "$fn.$ext";

	if(isImage($img['type']) && $img['error'] == 0) {
		move_uploaded_file($img["tmp_name"], $path . $fn);
	};
}
?>
