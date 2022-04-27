<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('fetch.php');
setupdb();

// print_r($_POST);
// echo "<br><br>\n\n";

$idata=$udata='';
$int="/global|distri|data|orders|logis|bizdev|proddev|accounting|gos|pcm|apc|distonly|sort_order|contact|imp/i";

foreach ( $_POST as $k=>$v ) {
	$con = setupdb($_GET['t']);
     $$k  = trim_real_escape($con,$v);
	if ( preg_match($int,$k) || ($k=='status'&&is_numeric($v)) ) {
		$udata .= $k."=".trim_escape($v).",";
		$idata .= trim_escape($v).",";
	} elseif ( $k=='pw'||$k=='un' ) {
		if ( $k=='pw') $v=md5($un.$v.'@)!)');
		if ( !get_magic_quotes_gpc() ) {
			$v = addslashes($v);
		}
		$idata .= "'".($v!=''?$v:'')."',";
		$udata .= ($k=='pw'&&$_POST['pw']=='')?'':$k."='".trim_escape($v)."',";
	} else {
		if ( $k=='released' ) $v=str_replace('.','',$v);
		$udata .= $k."='".trim_real_escape($con,$v)."',";
		$idata .= "'".trim_real_escape($con,$v)."',";
	}
}

$oldid	= substr($do,2,strlen($do));
$do		= substr($do,0,1);
$url	= ( $do==3 ) ? $tbl : (isset($_GET['t']) ? $_GET['t'] :null);
$tbl	= 'tbl'.( $url=='setweek' ? 'admin' : $url );
$cdate	= isset($date)?$date:null; //for calendar
$safe	= isset($safeqty)?trim_escape($safeqty):null;
$img	= ( $do==2 ) ? (isset($img) ? $img :null) : (isset($imgpath) ? $imgpath :null);
$udata	= substr_replace($udata,'',strrpos($udata,',do'));
$idata	= substr_replace($idata,'',-14);
$qry	= "WHERE id='".$oldid."'";
$dl_cat	= (isset($cat))?$cat:'';

if ( isset($submit) && $submit=='Submit' ) {

	$con = setupdb($tbl);
	if ( $do==1 ) {

		if ( $url=='admin' ) {
			$idata = substr($idata,0,-1);
			update($con,$tbl,$idata,$udata);

		} elseif ( $url=='slots' ) {
			$con   = SQLi('orders');
			$idata = substr($idata,0,-1);
			$idata = str_replace("''","null",$idata);
			mysqli_query($con,"INSERT INTO $tbl VALUES ($idata)");

		} else {
// echo "INSERT INTO $tbl VALUES ($idata);"."\n\n";			// ##### REMOVE #####
			mysqli_query($con,"INSERT INTO $tbl VALUES ($idata);");
			if ( $url=='warehouse' ) mysqli_query($con,"ALTER TABLE `tblstocks` ADD `w$id` INT NOT NULL;") or die(mysqli_error($con));
		}

	} else if ( $do==2 ) {

		if ( $url=='admin' ) {
			$idata = substr($idata,0,-4);
			update($con,$tbl,$idata,$udata);

		} elseif ( $url=='setweek' ) {
// echo "UPDATE tbladmin SET status=$status";
			mysqli_query($con,"UPDATE tbladmin SET status=$status WHERE id=995") or die(mysqli_error($con));

		} elseif ( $url=='stocks' ) {
			mysqli_query($con,"UPDATE tblstocks SET safeqty=$safe $qry");

		} elseif ( $url=='products' ) {
			$dbsrc = 'products';
			require('infoconfig.php');
			$idata = substr_replace($idata,'',-6);
			update($con,$tbl,$idata,$udata);

		} elseif ( $url=='olreg' ) {
			$idata = substr($idata,0,-17);
			update($con,$tbl,$idata,$udata);

			$qry = "UPDATE " . DB . "orders.tblslots SET dslid='$dsdid',used=(CASE WHEN used IS NULL THEN '" . date(TMDSET) . "' ELSE used END) WHERE olregid='$oldid'";
			mysqli_query($con, $qry) or die(mysqli_error($con));

			$qry = "UPDATE " . DB . "orders.tblslots SET dslid='$dsdid',used=(CASE WHEN used IS NULL THEN '" . date(TMDSET) . "' ELSE used END) WHERE olregid='$oldid'";
			mysqli_query($con, $qry) or die(mysqli_error($con));

			if ( $status==1 ) {
				$idata = "'DLCPH','$dsdid','$dsfnam','$dsmnam','$dslnam','-','-','$dscont','$dsstrt','$dsbrgy','$dscity','$dsprov','DLCPH','$dssid','$dsbday','','$dstin','$dsemail','".date('Ymd')."'";
				$udata = "dsfnam='$dsfnam',dsmnam='$dsmnam',dslnam='$dslnam',dsmph='$dscont',dsstrt='$dsstrt',dsbarn='$dsbrgy',dscity='$dscity',dsprov='$dsprov',dssid='$dssid',dsbrth='$dsbday',dstin='$dstin',dseadd='$dsemail'";
				update($con,DB.'distributor.distributors',$idata,$udata);
			}

		} elseif ( $url=='slots' ) {
			$con = SQLi('orders');
			mysqli_query($con,"UPDATE $tbl SET $udata WHERE slotid='$oldid'") or die(mysqli_error($con));

		} elseif ( $url=='newdistri' ) {
			update($con,$tbl,$idata,$udata);

		} elseif ( $url=='dsmstp' ) {
			$idata=substr($idata,0,-13);
			update($con, DB.'distributor.distributors',$idata,$udata);

		} elseif ( $url=='responsor' ) {
			$con=SQLi('distributor');

			if ( !$wholeline ) mysqli_query($con,"UPDATE distributors SET dssid='$oldsp' WHERE dssid='$oldid'") or die(mysqli_error($con));
			echo ( $wholeline ? 'WHOLE LINE MOVE SUCCESS' :'' );
			mysqli_query($con,"UPDATE distributors SET dssid='$dssid' WHERE dsdid='$oldid'") or die(mysqli_error($con));
			mysqli_query($con,"UPDATE responsor SET status=0 WHERE dsdid='$oldid'") or die(mysqli_error($con));
			$_SESSION['lastpage']='distriserve/?p=responsor&do=0';

		} else {
			mysqli_query($con,"UPDATE $tbl SET $udata $qry") or die(mysqli_error($con));

		}

	} else if ( $do==3 ) {
		mysqli_query($con,"DELETE FROM $tbl WHERE id='$id'");
	}

	mysqli_close($con);

	if ( isset($_FILES) && isset($id) ) {
		$fimg = isset($_FILES['file_img'])?$_FILES['file_img']:null;
		$ffda = isset($_FILES['img_fda'])?$_FILES['img_fda']:null;
		$fdwn = isset($_FILES['upfile'])?$_FILES['upfile']:null;
		unset($_FILES);

		$ext = pathinfo($fimg['name'],PATHINFO_EXTENSION);
		$file_name="$id.$ext";

		if ( !isset($fimg) || $fimg===NULL ) {
		} elseif ( $fimg['error']==0 ) {
//print_r($fimg);			// ##### REMOVE #####
//echo '<br />';			// ##### REMOVE #####
			$ft = $fimg['type'];
			if ( isImage($ft) && ($fimg['size']<=2000000) ) {
//echo 'mod 1<br />';		// ##### REMOVE #####
				move_uploaded_file($fimg['tmp_name'],'../images/'.$url.'/'.$file_name);
				setFiles($tbl,$file_name,$id,'img');
			}
		}
//echo 'img ok <br />';	// ##### REMOVE #####

		$ext = pathinfo($ffda['name'],PATHINFO_EXTENSION);
		$file_name = "$id.$ext";
		if ( !isset($ffda) || $ffda===NULL ) {
		} elseif ( $ffda['error']==0 ) {
// print_r($ffda);			// ##### REMOVE #####
// echo '<br />';			// ##### REMOVE #####
			$ft = $ffda['type'];
			if ( isImage($ft) && ($ffda['size']<=2000000) ) {
//echo 'mod 1<br />';		// ##### REMOVE #####
				move_uploaded_file($ffda['tmp_name'],'../images/'.$url.'/fda/'.$file_name);
				setFiles($tbl,$file_name,$id,'img_fda');
			}
		}
//echo 'img ok <br />';	// ##### REMOVE #####

		$ext = pathinfo($fdwn['name'],PATHINFO_EXTENSION);
		$file_name = "$id.$ext";
		if ( !isset($fdwn) || $fdwn===NULL ) {
		} elseif ( $fdwn['error']==0 ) {
//print_r($fdwn);			// ##### REMOVE #####
//echo '<br />';			// ##### REMOVE #####
			$ft = $fdwn['type'];
			if ( (isImage($ft)||isDownload($ft)) && ($fdwn['size']<=20000000) ) {
//echo 'mod 2<br />';		// ##### REMOVE #####
				move_uploaded_file($fdwn['tmp_name'],'../downloads/'.(($dl_cat=='downloads')?'':($url=='activities')?'activities/':$dl_cat.'/').$file_name);
				setFiles($tbl,$file_name,$id,'dlfile');
			}
		}
//echo 'dwn ok';			// ##### REMOVE #####
	}
}

if ( $submit=='del_img' ) {
	unlink("..$del_file");
	echo "Image deleted";
}

if ( $url=='calendar') {
	$gohere='list&i='.$cdate;
} else $gohere=0;

$lpage = isset($_SESSION['lastpage'])?$_SESSION['lastpage']:null;
$start = (strpos($lpage,'do=')!==false)?strpos($lpage,'do=')+3:0;
$stlen = strlen($lpage)-$start;
$meta_url = isset($lpage)?$lpage:'./?p='.$url.'&do='.$gohere;

unset($_SESSION['lastpage']);
unset($_POST);
// echo "$meta_url | $lpage | $start | $stlen";
echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL='.$meta_url.'">';exit;

function update($con,$tbl,$idata,$udata){
	$qry = "INSERT INTO $tbl VALUES ($idata) ON DUPLICATE KEY UPDATE $udata";
// echo "$idata<br><br>$udata<br><br>$qry";
	mysqli_query($con,$qry) or die(mysqli_error($con));
}

function setupdb($tbl=''){
	return SQLi( $tbl=='tblreferral' ? 'orders' : (isset($_SESSION['dbprod']) ? 'products' : 'beta') );
}

function setFiles($tbl,$file,$id,$setto){
	$con = setupdb();
	mysqli_query($con,"UPDATE $tbl SET $setto='$file' WHERE id='$id'");
	mysqli_close($con);
}

function trim_real_escape($con,$str) {
	return mysqli_real_escape_string ( $con , stripslashes( trim( $str ) ) );
}
?>
