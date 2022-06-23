<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$con=SQLi('products');
if(isset($_POST['submit'])) {
	if($_POST['submit']=='UPLOAD') {
		$file=$_FILES['file'];
		unset($_POST);
		unset($_FILES);
		$temp=$file['tmp_name'];

		$msg='FDA List upload failed';
		if(isValid($file['type'])) {
			$tbl='tblfda';
			$sqlcreate="CREATE TABLE IF NOT EXISTS $tbl (
				id INT(5) UNSIGNED ZEROFILL,
				fda VARCHAR NULL,
				renewal TINYINT 0,
				expiry VARCHAR NULL,
				url VARCHAR NULL
			)";
			postData($temp,$tbl);

			$msg='List successfully uploaded';
		}echo $msg;
	}else if($_POST['submit']=='RESET') {
		$rs=mysqli_query(SQLi('products'),"TRUNCATE ".DB."products.tblfda");
		$msg='List successfully truncated';
	}
	echo '<META HTTP-EQUIV=Refresh CONTENT="0;URL=index.php?p=fda">';
}
$data=getList();
$lic=isset($_SESSION['listcount'])?$_SESSION['listcount']:0;
$num=($lic>0)?$lic:0;
$msg='Found '.$num.' entries';
ob_start();
$x= '<form method="post" id="fdalist" enctype="multipart/form-data" action="index.php?p=fda"><ul>';
$x.='<li><span class="blue">Upload FDA List</span>';
$x.='<li><label>File:</label><input type="file" name="file" id="prodlist" /><span class="bad"></span></li>';
$x.='<li><span class="note">'.$msg.'</span></li>';
$x.='<input type="submit" name="submit" class="btn" value="UPLOAD" /><input type="button" class="btn" id="resetfda" value="RESET" /></ul></form>';
$x.='<br><br>';
// $x.='<div id="'.$adminpage.'" class="clear"><a href="../logistics" class="back" id="download">BACK</a></div>';
echo $x.$data;
ob_end_flush();

function getList() {
	$msg ='<ul class="fdalist list clear">';
	$msg.='<li class="hdr"><span class="s1">Code</span><span class="s5">Description</span><span class="s4">FDA #</span><span class="s3 ct">Expiry</span><span class="s1 ct">Renewal</span><span class="s6">FDA LINK</span></li>';

	$qry="SELECT f.*,name FROM tblfda f
		LEFT JOIN tbllist l ON l.id=f.id
		ORDER BY expiry";

	$con=SQLi('products');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$num=mysqli_num_rows($rs);
	$_SESSION['listcount']=$num;
	if($num>0) {
		while($rw=mysqli_fetch_array($rs,MYSQLI_BOTH)) {
			foreach($rw as $k=>$v) $$k=$v;

			$clsli=$clsdesc=$clsdate=$clsfda=$clsurl=$exp='';
			$testdesc=$name;
			$testfda=$fda;
			$testurl=$url;
			$testdate=formatDate($expiry,'Y-m-d','m/d/Y');

			if(strpos($testdate,'INVALID')!==false) {
				$clsdate='bad b';
			}else{
				if(strtotime('now')>strtotime("-6 months",strtotime($testdate))) {
					$clsli='breakfda';
				}
			}

			if($name=='') {
				$testdesc='PRODUCT NOT FOUND';
				$clsdesc='bad b';
			}

			if($fda=='') {
				$testfda='FDA NOT FOUND';
				$clsfda='bad b';
				$exp='FOR RENEWAL';
			}

			if($url==''||(filter_var($url,FILTER_VALIDATE_URL)===FALSE)) {
				$testurl='INVALID LINK';
				$clsurl=$clsli!=''?'':'bad b';
			}else $testurl='<a href="'.$url.'" target="_blank">Click to open FDA Link</a>';

			$msg.='<li class="'.$clsli.'">';
			$msg.='<span class="s1">'.$id.'</span>';
			$msg.='<span class="s5 '.$clsdesc.'">'.$testdesc.'</span>';
			$msg.='<span class="s4 '.$clsfda.'">'.$testfda.'</span>';
			$msg.='<span class="s3 ct '.$clsdate.'" title="'.$exp.'">'.$testdate.'</span>';
			$msg.='<span class="s1">'.$renewal.'</span>';
			$msg.='<span class="s6 '.$clsurl.'">'.$testurl.'</span>';
			$msg.='</li>';
		}
	}
	$msg.='</ul>';
	return $msg;
}

function isValid($ft) {
	if($ft=='text/csv'||$ft=='application/csv'||$ft=='application/octet-stream'||$ft=='application/vnd.ms-excel') return true;
}

function postData($file,$tbl) {
	$hdr=0;
	$data=fopen($file,'r');
	while($rw=fgets($data)) {
		$line=str_getcsv($rw);
		$x1=str_replace("'",'',$line[0]);
		$x2=str_replace("'",'',$line[1]);
		$x3=str_replace("'",'',$line[2]);
		$x4=str_replace("'",'',$line[3]);
		$x5=str_replace("'",'',$line[4]);

		$x4=date('Y-m-d',strtotime($x4));

		$idat="'$x1','$x2',$x3,'$x4','$x5'";
		$udat="fda='$x2',renewal=$x3,expiry='$x4',url='$x5'";
		if($hdr>0&&$x1!='') getData($idat,$udat,$tbl);
		$hdr++;
	}
}

function getData($idata,$udata,$tbl) {
	$con=SQLi('products');
	mysqli_query($con,"INSERT INTO $tbl VALUES ($idata) ON DUPLICATE KEY UPDATE $udata") or die(mysqli_error($con));
}
?>
