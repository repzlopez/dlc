<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

$shortlink = strpos($_SERVER['SERVER_NAME'],'local')!==false ? 'index.php/' :'';
$url = '/read';

if( !isset($_SESSION['isLogged']) && isset($_GET['rfr']) ) {
	$_SESSION['rfr'] = $_GET['rfr'];
	$_SESSION['pid'] = isset($_GET['pid'])?$_GET['pid']:NULL;
	$url = isset($_GET['pid'])?
          '/read/'.$shortlink.'lifestyle-shop/?rfr='.$_GET['rfr'].'&pid='.$_GET['pid'] :
          '/read/'.$shortlink.'account/welcome-to-dlc-philippines/';
}

?>
<META HTTP-EQUIV=Refresh CONTENT="0;URL=<?php echo $url ?>">
