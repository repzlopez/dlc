<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require_once('../../admin/setup.php');
require_once('../func.php');
//testScope("global|gos",DLC_GORT);

if(isset($_POST['submit'])) {
	$testArr=array('dsdid','dslname','dsfname','dscont','dsprov','dsbday','dstin','dssid');
	$idata='';$udata='';$reqmiss=0;$dsscan='';$dsscanbak='';
	$_SESSION['post']=null;
	$_SESSION['post']['bad_distri']='';

	$con=SQLi('gos');
	foreach($_POST as $key=>$val) {
		if(in_array($key,$testArr)) {
			if(trim($val)=='') $reqmiss++;
		}
		$dat=trim_escape($val);
		$$key=$dat;
		$_SESSION['post'][$key]=$dat;
		if($key!='do'&&$key!='go'&&$key!='dsscanbak'&&$key!='submit') {
			$idata.=($key=='mod')?"$dat,":"'$dat',";
			$udata.=($key=='dsdid'||$key=='mod')?'':$key.($key=='mod'?"=$dat,":"='$dat',");
		}
	}
	unset($_POST);
	$dsscan=(isset($_FILES["dsscan"])?$_FILES["dsscan"]:null);
	$submit=($submit=='CONFIRM'||$submit=='Submit')?'Submit':null;
	if($submit=='Submit') {
		if(testExist($dsdid,'distributor','distributors','dsdid')&&!$mod&&$do<2) { $_SESSION['post']['bad_distri']='Distributor ID exists. Request update? <input type="button" id="cmod" value="YES">';}
		elseif(!testExist($dssid,'distributor','distributors','dsdid')&&!$mod&&$do<2) { $_SESSION['post']['bad_distri']='Unable to continue. Sponsor ID does not exist.';}
		elseif(!formatDate($dsbday,'mdY',1)&&!$mod) { $_SESSION['post']['bad_distri']='Unable to continue. You entered an invalid date. Please follow the date format (mmddyyyy).';}
		elseif(!$mod&&$reqmiss>0) { $_SESSION['post']['bad_distri']='Unable to continue. Fields in RED are required.';}
		elseif(!$mod&&$dsscan['error']>0) { $_SESSION['post']['bad_distri']='Unable to continue. SCANNED COPY is required for NEW applications.';}
		else{
			$idata.="'".setDatePosted()."','".LOGIN_BRANCH."','".LOGIN_ID."',".(IS_GOS?1:0);
			$udata=substr_replace($udata,'',-1);
			$udata=IS_GOS?"status=0":$udata;
			$con=SQLi('gos');
			$qry="INSERT INTO tbldistri VALUES ($idata) ON DUPLICATE KEY UPDATE $udata";
			mysqli_query($con,$qry) or die(mysqli_error($con));
			mysqli_close($con);
			unset($_SESSION['post']);

			if(isset($dsscan)) {
				$img=$_FILES["dsscan"];
				$ext=strtolower(pathinfo($img['name'],PATHINFO_EXTENSION));
				$fn="$dsdid.$ext";
				if(isImage($img['type'])&&$img['error']==0) {
					move_uploaded_file($img["tmp_name"],'scan/'.$fn);
				};
			}reloadTo(DLC_GORT.'/distri');
		}reloadTo($_SERVER['HTTP_REFERER']);
	}
}
?>
