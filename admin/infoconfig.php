<?php require_once('info.config');
$goto='<META HTTP-EQUIV=Refresh CONTENT="0;URL=http://'.$_SERVER['SERVER_NAME'].'/errors/404.php">';
$con=mysqli_connect(HOST,DB.USN,PSW,DB.$dbsrc);
if(!$con){ die('Connection failed: '.mysqli_connect_error());exit;}
?>
