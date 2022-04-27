<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
$path=isset($_GET['pa'])?$_GET['pa']:'';
$file=isset($_GET['dl'])?$_GET['dl']:'';
$src=(substr($file,0,3)=='cal')?'calendar':'activities';
$down='downloads/'.(($path!='')?$path:$src).'/'.$file;

require('admin/setup.php');
$con=SQLi('beta');
$date=date(TMDSET,time());
$user=isset($_SESSION['u_site'])?$_SESSION['u_site']:'guest';
$user=trim_escape($user);
mysqli_query($con,"INSERT INTO tbllogdl VALUES ('$user','$file','".$_SERVER['REMOTE_ADDR']."','$date');");
mysqli_close($con);

if(file_exists($down)){
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($down));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: '.filesize($down));
    ob_clean();
    flush();
    readfile($down);
	if($path=='recap') unlink($down);
    exit;
}
?>
