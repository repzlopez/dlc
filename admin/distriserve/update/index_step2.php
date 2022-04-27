<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(isset($_POST['submit'])&&$_POST['submit']=='UPLOAD'){
	$stat=0;
	$files=$_FILES['files'];
	unset($_POST);
	unset($_FILES);
	$_SESSION['msg2']='';
	if($files['error']==0){
		postData($files['tmp_name']);
		$stat=($_SESSION['csvfilerows']==$_SESSION['updatedrows']);
		$_SESSION['msg2'].='<p><span class="s5">CSV File Rows</span>: <span class="s4 rt">'.$_SESSION['csvfilerows'].'</span></p>';
		$_SESSION['msg2'].='<p><span class="s5">Updated Records</span>: <span class="s4 rt">'.$_SESSION['updatedrows'].'</span></p>';
		$_SESSION['msg2'].='<p><span class="'.($stat?'blue':'bad').'">DATABASE '.($stat?'':'NOT ').'UPDATED</span></p>';
		$con=SQLi('distributor');
		if($stat) mysqli_query($con,"INSERT INTO updates VALUES ('".getDateUpdated()."')");
		mysqli_close($con);
	}
	if($stat){ updateOFF();echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=index.php?p=step2">';}
}
ob_start();
echo '<form method="post" id="updb" action="index.php?p=step2" enctype="multipart/form-data"><ul>';
echo '<li><span class="blue">LOAD DATA</span></li>';
echo '<li><span class="s5">Distributor ( <span class="more">dsmstp-demo</span> )</span>:<input type="file" name="files" class="txt s5" /><span class="bad" rel="dsmstp-demo"></span></li>';
echo '<li><input type="submit" name="submit" value="UPLOAD" /><span class="more s7 rt">.CSV file format only</span></li>';
echo '<li class="smaller">Update the Birthdate field of Distributors Table</li>';
echo '<li>'.$updating.(isset($_SESSION['msg2'])?$_SESSION['msg2']:'').'</li>';
echo '<li class="rt">'.getLastUpdate().'</li></ul></form>';
echo '<div id="'.$adminpage.'" class="clear"><a href="'.$_SERVER['PHP_SELF'].'" class="back" id="download">BACK</a></div>';
ob_end_flush();

function postData($file){
	$cr=0;$ur=0;
	if(($handle=fopen($file,'r'))!==FALSE){
		while(($rw=fgetcsv($handle,0,','))!==FALSE){
			// ini_set('max_execution_time',60);
			// $con=SQLi('distributor');
			// echo "UPDATE distributors SET dsbrth='".$rw[14]."' WHERE dsdid='".$rw[1]."'<br>";
			// $rs=mysqli_query($con,"UPDATE distributors SET dsbrth='".$rw[14]."' WHERE dsdid='".$rw[1]."'") or die(mysqli_error($con));
			// mysqli_close($con);
			$ur++;$cr++;
		}fclose($handle);
	}
	$_SESSION['csvfilerows']=number_format($cr);
	$_SESSION['updatedrows']=number_format($ur);
}

function getDateUpdated(){
	date_default_timezone_set("Asia/Manila");
	$now=date("Y-m-d H:i:s",time());
	return $now;
}

function updateOFF(){
	$con=SQLi('beta');
	mysqli_query($con,"UPDATE tbladmin SET status='no' WHERE id='996'");
	mysqli_close($con);
}
?>
