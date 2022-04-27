<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
include('../setup.php');
if(ISIN_DISTRI&&!in_array(DIST_ID,array(REPZ))){
	echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL='.DLC_MYPAGE.'">';exit;
}else{
	$useDate=substr($_SESSION['monyr'],0,2).substr($_SESSION['monyr'],2,4);
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: filename=managerlist_$useDate.csv");
	$csv='"ID#","NAME","PPV","GPV","TPV","LEVEL","Mgr Lines","Manager IDs"'."\n";
	$csv.=isset($_SESSION['managerlist'])?$_SESSION['managerlist']:null;
	print $csv;
}
?>
