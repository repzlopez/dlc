<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
include('../fetch.php');
$submit	=isset($_POST['submit'])?$_POST['submit']:null;
$do		=isset($_POST['do'])?$_POST['do']:null;
$id		=isset($_POST['id'])?$_POST['id']:null;
$data	='';$x='';$z='';
if($submit=='.|.'){
	$con=SQLi('products');
	$tbl='tblstocks';
	switch($do){
		case 0:
			$rs=mysqli_query($con,"SELECT id FROM $tbl WHERE id='$id'") or die(mysqli_error($con));
			$rw=mysqli_num_rows($rs);
			$x=$rw;break;
		case 1:
			$rs=mysqli_query($con,"SELECT name,wsp FROM tbllist WHERE id=$id LIMIT 1") or die(mysqli_error($con));
			$rw=mysqli_fetch_array($rs);
			$x=json_encode(array(utf8_encode($rw['name']),$rw['wsp']));
			break;
		case 2:
			$con=SQLi('distributor');
			$rs=mysqli_query($con,"SELECT dslnam,dsfnam,dsmnam,dscoid FROM distributors WHERE dsdid='$id'") or die(mysqli_error($con));
			$rw=mysqli_fetch_array($rs);
			if(mysqli_num_rows($rs)>0){
				$z=utf8_encode($rw['dslnam'].', '.$rw['dsfnam'].' '.$rw['dsmnam']);
			}else $z='NOT FOUND';
			$x=json_encode(array($z));
			break;
	}echo $x;
}
mysqli_close($con);
unset($_POST);
?>
