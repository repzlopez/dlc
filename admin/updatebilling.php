<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
$tbl	= 'tblorders';
$paystat= $_POST['paystat'];
$status = $_POST['status'];
$id		= $_POST['id'];
$update	= $status;

if(isset($_POST['submit'])&&$_POST['submit']=='Submit'){
	date_default_timezone_set('Asia/Manila');
	$con=SQLi('orders');
	$time=date('Y-m-d H:i:s',time());
	mysqli_query($con,"INSERT INTO tbllog VALUES ('$id','$time','".$_SESSION['login_id']."','$update')");
	mysqli_query($con,"UPDATE $tbl SET deliStat='$status',payStat='$paystat' WHERE refNo='$id'");
	if($status==2) setReferral($id);
	mysqli_close($con);
	unset($_POST);
	echo $status;
}

function setReferral($id){
	$con=SQLi('orders');
	$rs=mysqli_query($con,"SELECT dsdid,orders FROM tblorders WHERE refNo='$id'") or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$dsdid=$rw['dsdid'];
	$dslid=$dsdid;

	$d=explode("~",($rw['orders']!=''?$rw['orders']:'||||'));
	foreach($d as $i){						//find package code
		$e=explode("|",$i);
		$r=getPackage($e[0]);
		if($r!==true){$n=$e[3];				//if package found run function
			$arrRef=explode("|",$r);

			while($n>0){
				$dsdid=$dslid;
				foreach($arrRef as $k=>$v){
					if($v!=''&&$v>0){
						$con=SQLi('orders');
						mysqli_query($con,"INSERT INTO tblreferral VALUES ('','$dsdid','$dslid','$id',$v,'')") or die(mysqli_error($con));

						$con=SQLi('distributor');
						$rs=mysqli_query($con,"SELECT dssid FROM distributors WHERE dsdid='$dsdid'") or die(mysqli_error($con));
						$rw=mysqli_fetch_array($rs);
						$dsdid=$rw['dssid'];
					}
				}$n--;
			}
		}
	}
}

function getPackage($id){
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT referral FROM tblpackages WHERE id='$id' AND status=1") or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	return $rw['referral'];
}

function SQLi($dbsrc){
	require('infoconfig.php');
	$con=mysqli_connect(HOST,DB.USN,PSW,DB.$dbsrc);
	if(!$con) die('Connection failed: '.mysqli_connect_error());
	return $con;
}
?>
