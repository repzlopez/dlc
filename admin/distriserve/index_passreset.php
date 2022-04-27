<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
if(isset($_POST['submit'])&&isset($_POST['id'])){
	$con=SQLi('distributor');
	$id=trim_escape($_POST['id']);
	$rs=mysqli_query($con,"SELECT dsdid FROM accounts WHERE dsdid='$id'") or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0){
		mysqli_query($con,"DELETE FROM accounts WHERE dsdid='$id'");
		$z='Password successfully reset';
	}else{
		$z='Distributor never logged-in';
	}
	mysqli_close($con);
	unset($_POST);
}else $z='';
$x ='<form method="post" action="'.$_SERVER['REQUEST_URI'].'"><ul>';
$x.='<li><span class="blue">Password Reset</span>';
$x.='<li><label>Distributor ID:</label><input type="text" name="id" class="txt" id="distid" /></li>';
$x.='<li><label>Distributor Name:</label><input type="text" name="nn" class="txt" id="distname" '.READONLY.' /></li>';
$x.='<li><span id="resetstat">'.$z.'</span></li>';
$x.='<input type="submit" name="submit" class="btn" value="Reset" /></ul></form>';
echo $x;
?>
