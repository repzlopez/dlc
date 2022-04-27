<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../setup.php');
$_SESSION['upcal']=0;
$temp=$_FILES['template'];
$dlod=$_FILES['download'];
$cmon=$_POST['calmonth'];

if(isset($_POST['submit'])&&$_POST['submit']=='UPLOAD'){
	if($temp['error']==0&&(pathinfo($temp['name'],PATHINFO_EXTENSION)=='csv')){
		$_SESSION['upcal']=1;
		$handle=fopen($temp['tmp_name'],"r");
		while (($data = fgetcsv($handle,1000,","))!== FALSE){
			if($data[0]!='DATE'){
				setCalendar($data[0],$data[1],$data[2]);
			}
		}
	}
	if($dlod['error']==0){
		$ext=pathinfo($dlod['name'],PATHINFO_EXTENSION);
		$fn="$cmon.$ext";
		$ft=$dlod['type'];
		if((isImage($ft)||isDownload($ft))&&($dlod['size']<=20000000)){
			$_SESSION['upcal']=1;
			move_uploaded_file($dlod['tmp_name'],'../../downloads/calendar/cal_'.$fn);
		}
	}
}echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL=/admin/bizdev/?p=calendar&do=0">';

function setCalendar($date,$title,$desc){
	$con=SQLi('beta');
	$date=sprintf('%08d',$date);
	mysqli_query($con,"INSERT INTO tblcalendar VALUES ('','$date','$title','$desc',1);");
	mysqli_close($con);
}
?>
