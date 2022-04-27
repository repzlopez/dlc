<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(isset($_POST['submit'])&&$_POST['submit']=='UPLOAD'){
	set_time_limit(600);
	$con=SQLi('beta');
	mysqli_query($con,"UPDATE tbladmin SET status=1 WHERE id='996'");
	mysqli_close($con);

	$_SESSION['msg1']='';
	$fs=$_FILES['files'];
	unset($_POST);
	unset($_FILES);
	for($i=0;$i<4;$i++){
		if($fs['error'][$i]==0){ postData($fs['tmp_name'][$i],$i); }
		$_SESSION['msg1'].='<p>'.returnMsg($fs['error'][$i]).$fs['name'][$i].'</p>';
	}
	// echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL=index.php?p=step1">';
}
ob_start();
echo '<form method="post" id="updb" enctype="multipart/form-data" action="index.php?p=step1"><ul>';
echo '<li><span class="blue">LOAD DATA</span>';
echo '<li><span class="s5">Distributor ( <span class="more">dsmstp-demo</span> )</span>:<input type="file" name="files[]" class="txt s5" /><span class="bad" rel="dsmstp-demo"></span></li>';
echo '<li><span class="s5">Group Production ( <span class="more">bomstp-demo</span> )</span>:<input type="file" name="files[]" class="txt s5" /><span class="bad" rel="bomstp-demo"></span></li>';
echo '<li><span class="s5">Order History ( <span class="more">ormstp-demo</span> )</span>:<input type="file" name="files[]" class="txt s5" /><span class="bad" rel="ormstp-demo"></span></li>';
echo '<li><span class="s5">Bonus History ( <span class="more">bohstp-demo</span> )</span>:<input type="file" name="files[]" class="txt s5" /><span class="bad" rel="bohstp-demo"></span></li>';
echo '<li><input type="submit" name="submit" value="UPLOAD" /><span class="more s7 rt">.CSV file format only</span></li>';
echo '<li>'.$updating.(isset($_SESSION['msg1'])?$_SESSION['msg1']:'').'</li>';
echo '<li class="rt">'.getLastUpdate().'</li></ul></form>';
echo '<div id="'.$adminpage.'" class="clear"><a href="'.$_SERVER['PHP_SELF'].'" class="back" id="download">BACK</a></div>';
ob_end_flush();

function postData($f,$x){
	$con=SQLi('distributor');
	$t=array('distributors','bomstp','ormstp','bohstp');
	mysqli_query($con,"TRUNCATE TABLE $t[$x]");

	if(($handle=fopen($f,'r'))!==FALSE){
		ini_set('max_execution_time',120);
		ini_set('memory_limit','-1');
		set_time_limit(600);
		while(($l=fgetcsv($handle,0,','))!==FALSE){
			if($x>0){
				switch($x){
					case 1:$i=($l[0]!='BMCOID')?"'$l[0]','$l[1]','$l[2]','$l[3]','$l[4]',$l[5],$l[6]":'';break;
					case 2:$i="'$l[0]','$l[1]',$l[2],$l[3],'$l[4]','$l[5]','$l[6]','$l[7]','$l[8]',$l[9]";break;
					case 3:$i="'$l[0]','$l[1]',$l[2],$l[3],'$l[4]','$l[5]','$l[6]','$l[7]','$l[8]'";break;
				}
			}else{
				$i=implode("','",$l);
				$i="'$i'";
			}
			// echo "INSERT INTO $t[$x] VALUES ($i)"."<br>";
			if($i!='') mysqli_query($con,"INSERT INTO $t[$x] VALUES ($i)") or die(mysqli_error($con));
		}fclose($handle);

		// if($x==3){
		// 	mysqli_query("INSERT INTO bohstp SELECT * FROM bohstp_correct");
		// }
	}mysqli_close($con);
}

function returnMsg($i){
	$r = array(
		0 => 'Successfully uploaded ',
		1 => 'Fail. UPLOAD_MAX_FILESIZE exceeds limit. ',
		2 => 'Fail. MAX_FILE_SIZE exceeds limit. ',
		3 => 'Fail. File was only partially uploaded. ',
		4 => 'No file uploaded. ',
		6 => 'Fail. Missing temporary folder. ',
		7 => 'Failed to write file to disk. ',
		8 => 'A PHP extension stopped the file upload.',
	);
	return $r[$i];
}
?>
