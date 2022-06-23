<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
if(!IS_GLOB && !testScope("accounting")) {reloadTo(DLC_ADMIN);exit;}
$con=SQLi('distributor');
$msg=$old=getRows();
$smdata=$bonussum='';
$rec=(isset($_SESSION['recapcreated'])&&$_SESSION['recapcreated'])?'Recap File successfully created <a href="../download.php?pa=recap&dl=recap.pdf" class="dlrecap" id="download">DOWNLOAD RECAP</a>':'';
$rdat=isset($_POST['recapavail'])?$_POST['recapavail']:null;
$_SESSION['multirecap']=true;
$availrecap=getAvailRecap($rdat);

if(isset($_POST['submit'])) {
	if(isset($_FILES['file'])) $file=$_FILES['file'];
	if($_POST['submit']=='UPLOAD') {
		postData($file['tmp_name']);
/*
		$temp=$file['tmp_name'];												//live
		$temp_file='c://dlcwebtemp/bodstptemp_'.date('Ymd',time()).'.csv';		//local
		if(isValid($file['type'])) {
			move_uploaded_file($temp,$temp_file);
			$sqlcreate="CREATE TABLE IF NOT EXISTS bodstp (
				bdyy VARCHAR(4) NOT NULL default '',
				bdmm VARCHAR(2) NOT NULL default '',
				bdsid VARCHAR(16) NOT NULL default '',
				bddid VARCHAR(16) NOT NULL default '',
				bddids VARCHAR(16) NOT NULL default '',
				bdtype VARCHAR(2) NOT NULL default '',
				bdbpct FLOAT NOT NULL default '0',
				bdpov FLOAT NOT NULL default '0',
				bdppv FLOAT NOT NULL default '0',
				bdbamt FLOAT NOT NULL default '0',
				bdctxp INT NOT NULL default '0'
			)";
			mysqli_query($con,$sqlcreate) or die(mysqli_error($con));

			$usefile=(strpos($_SERVER['SERVER_NAME'],'dlc')!==false)?$temp_file:$temp;
			$qry='LOAD DATA LOCAL INFILE \''.$usefile.'\'
				REPLACE INTO TABLE bodstp
				FIELDS TERMINATED BY \',\'
				ENCLOSED BY \'"\'
				ESCAPED BY \'\\\\\'
				LINES TERMINATED BY \'\r\n\'
				IGNORE 1 LINES
				(bdyy,bdmm,@x,bdsid,@x,bddid,@x,bddids,bdtype,bdbpct,@x,@x,bdpov,bdppv,bdbamt,bdctxp)';
			mysqli_query($con,$qry) or die(mysqli_error($con));
	*/
		$adrow=getRows();
		$msg=' Upload successful. '.($adrow-$old).' rows added.';
		reloadTo('index.php?p=recap');
	//	}
	}elseif($_POST['submit']=='GET RECAP') {
		getRecapData($file['tmp_name']);
		$_SESSION['recapdate']=$rdat;
		$rec='PREPARING RECAP FILE';
		reloadTo('../distrilog/print_pdf.php');
	}elseif($_POST['submit']=='GET BONUS SUM') {
		ob_end_clean();

		header('Content-type: application/vnd.ms-excel');
		header('Content-disposition: filename=BONUS_SUM '.substr($rdat,-4).' '.substr($rdat,0,2).'.csv');
		print getBonusSum($rdat);
	}elseif($_POST['submit']=='GET SM DATA') {
		$smdata=getSMData($rdat);
	}
	unset($_POST);
	unset($_FILES);
}
ob_start();
echo '<div><a href="../distrilog/recap.php?w=&u=" class="back" id="download">VIEW RECAP</a></div>';
echo '<form method="post" id="recap" enctype="multipart/form-data" action="index.php?p=recap"><ul>';
echo '<li><span class="blue">Upload Recap</span></li>';
echo '<li><label>File:</label><input type="file" name="file" id="uprecap" class="iscsv" /><span class="bad"></span><input type="hidden" id="lastrecap" value="'.$_SESSION['lastrecap'].'" /></li>';
echo '<li><span class="note">Last Recap Uploaded: '.(($_SESSION['lastrecap']>0)?'Week '.$_SESSION['lastrecap']:'None').'</span>'.getRecapSched().'</li>';
echo '<li><span class="note">'.$msg.'</span></li>';
echo '<input type="submit" name="submit" class="btn" value="UPLOAD" /></ul></form>';

echo '<form method="post" id="getrec" enctype="multipart/form-data" action="index.php?p=recap"><ul>';
echo '<li><span class="blue">Get Recap</span></li>';
echo '<li><label>File:</label><input type="file" name="file" id="printrecap" /><span class="bad"></span></li>';
echo '<li><span class="note">Available Recap: </span>'.$availrecap.DIV_CLEAR.'</li>';
echo '<li><span class="note recapstat">'.$rec.'</span></li>';
echo '<input type="submit" name="submit" id="getrecap" class="btn" value="GET RECAP" /></ul></form>';

echo '<form method="post" id="getbonussum" enctype="multipart/form-data" action="index.php?p=recap"><ul>';
echo '<li><span class="blue">Get Bonus Sum</span></li>';
echo '<li><span class="note">Available Recap: </span>'.$availrecap.DIV_CLEAR.'</li>';
echo '<li><span>'.$bonussum.'</span></li>';
echo '<input type="submit" name="submit" id="getbonussumdata" class="btn" value="GET BONUS SUM" /></ul></form>';

echo '<form method="post" id="getsm" enctype="multipart/form-data" action="index.php?p=recap"><ul>';
echo '<li><span class="blue">Get SM Data</span></li>';
echo '<li><span class="note">Available Recap: </span>'.$availrecap.DIV_CLEAR.'</li>';
echo '<li><span>'.$smdata.'</span></li>';
echo '<input type="submit" name="submit" id="getsmdata" class="btn" value="GET SM DATA" /></ul></form>';

echo $homebtn;
ob_end_flush();
unset($_SESSION['uprecap']);
unset($_SESSION['lastrecap']);
unset($_SESSION['recap_data']);
unset($_SESSION['recapcreated']);

function isValid($ft) {
	if($ft=='text/csv'||$ft=='application/csv'||$ft=='application/vnd.ms-excel') return true;
}

function getRecapSched() {
	$yr=($_SESSION['lastrecap']>0)?substr($_SESSION['lastrecap'],-4):date('Y');
	$qry="SELECT *	FROM tblsched WHERE yr='$yr'";
	$con=SQLi('beta');$dat='';
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		while($rw=mysqli_fetch_array($rs)) {
			foreach($rw as $k=>$v) $$k=$v;
			if($wk>substr($_SESSION['lastrecap'],0,2)) $dat.='<option>Week '.sprintf('%02d',$wk).': upload on '.date('M. d, Y',strtotime($vb)).'</option>';
		}
	}echo '<select id="uploadrecapsched">'.$dat.'</select>';
}

function getAvailRecap($my='') {$x='';
	$con=SQLi('distributor');
	$qry="SELECT DISTINCT bdmm,bdyy FROM bodstp ORDER BY bdyy DESC,CAST(bdmm AS unsigned) DESC";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		$mmyy=sprintf('%02d',$rw['bdmm']).$rw['bdyy'];
		$x.='<option value="'.$mmyy.'" '.($mmyy==$my?SELECTED:'').'>'.$mmyy.'</option>';
	}
	mysqli_close($con);
	return '<select name="recapavail" id="getavailrecap">'.$x.'</select>';
}

function getRecapData($file) {$x='';
	$addDistri=array(EDDY,RICK);
	$data=fopen($file,'r');
	while($rw=fgets($data)) $x.=$rw.',';
	$_SESSION['recapdistri']=array_merge($addDistri,explode(',',rtrim($x,',')));
}

function getSMData($mmyy) {$x=$a=$b='';
	$m=substr($mmyy,0,2);
	$y=substr($mmyy,-4);

	if($y<=2017&&$m<36) { $smWk='Weekly';$smMgr=12;$smHalf=2000; }
	else{ $smWk='Semi-Monthly';$smMgr=10;$smHalf=1000; }

	$r1=array('Z','Y','X','W','V');
	foreach($r1 as $k) {
	     $a.="(SELECT ROUND(".($k=='Z'?'':'AVG')."(bdbamt)) FROM bodstp WHERE bdyy=$y AND bdmm=$m AND bdtype='$k'".($k=='Z'?' ORDER BY bdbamt DESC LIMIT 1':'').") $k,";
	}
	$r2=array('Z1'=>' AND bdbamt=(SELECT Z)','Z2'=>' AND bdbamt=(SELECT Zz)','Y1'=>'','X1'=>'','W1'=>'','V1'=>'');
	foreach($r2 as $k=>$v) {
	     $b.="(SELECT COUNT(*) FROM bodstp WHERE bdyy=$y AND bdmm=$m AND bdtype='".substr($k,0,1)."' $v) $k,";
	}
	$b=substr($b,0,-1);
	$qry="SELECT $a (SELECT ROUND(Z/2)) Zz,$b FROM bodstp LIMIT 1";
// echo $qry;

	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	foreach($rw as $k=>$v) $$k=$v;
	$x.='<ul>';
	$x.='<li>Philippines '.$y.' '.$smWk.' '.$m.', POV = 0</li>';
	$x.='<li>Manager '.$smMgr.'% = '.$Z1.' person Amount = '.number_format($Z,0).'</li>';
	$x.='<li>'.$smHalf.'pv = '.$Z2.' person Amount = '.($Z2>0?number_format($Zz,0):0).'</li>';
	$x.='<li>L1 = '.$Y1.' person Amount = '.number_format($Y,0).'</li>';
	$x.='<li>L2 = '.$X1.' person Amount = '.number_format($X,0).'</li>';
	$x.='<li>L3 = '.$W1.' person Amount = '.number_format($W,0).'</li>';
	$x.='<li>L4 = '.$V1.' person Amount = '.number_format($V,0).'</li>';
	$x.='</ul>';
	return $x;
}

function getBonusSum($mmyy) {
	$m=substr($mmyy,0,2);
	$y=substr($mmyy,-4);
	$qry="SELECT b.bdsid,CONCAT(dslnam,', ',dsfnam,' ',dsmnam) nam,
			ROUND(pvb,2) pb,ROUND(gvb,2) gb,ROUND(gvb*(bdctxp/100),2) tax,ROUND(pvb+gvb-(gvb*(bdctxp/100)),2) vb,NULL bnk,'D' typ,dstin
		FROM (SELECT bdsid,bdctxp,
			SUM(CASE WHEN bdtype='B' AND bdsid=bddids THEN bdbamt ELSE 0 END) pvb,
			SUM(CASE WHEN bdtype='B' AND bdsid<>bddids THEN bdbamt ELSE 0 END)+SUM(CASE WHEN NOT (bdtype='B' OR bdtype='L') THEN bdbamt ELSE 0 END) gvb
			FROM bodstp WHERE bdyy=$y AND bdmm=$m GROUP BY bdsid) b
		LEFT JOIN distributors ON dsdid=b.bdsid
		HAVING vb>0";
	$str ='';

	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		foreach($rw as $k=>$v) $$k=$v;

		$str.='"",';
		$str.='"'.$nam.'",';
		$str.='"'.$pb.'",';
		$str.='"'.$gb.'",';
		$str.='"'.$tax.'",';
		$str.='"'.$vb.'",';
		$str.='"",';
		$str.='"'.$bnk.'",';
		$str.='"'.$dstin.'",';
		$str.='"'.$typ.'",';
		$str.='"'.$bdsid.'"';
		$str.="\n";
	}$str.="\n\n";
echo $str;
	return $str;
}

function getRows() {
	$n=0;
	$_SESSION['lastrecap']=0;
	$con=SQLi('distributor');
	$qry="SELECT bdmm,bdyy FROM bodstp ORDER BY bdyy DESC,CAST(bdmm AS unsigned) DESC";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$n=mysqli_num_rows($rs);
	$rw=mysqli_fetch_array($rs);
	$m=$rw['bdmm'];$y=$rw['bdyy'];
	$last=sprintf('%02d',$m).' '.$y;

	$con=SQLi('beta');
	$qry="SELECT count(*) c FROM tblsched WHERE yr='$y'";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$_SESSION['lastrecap']=($m<$rw['c']?$last:0);

	mysqli_close($con);
	return $n.' entries found.';
}

function postData($file) {
	$hdr=0;
	$data=fopen($file,'r');
	while($rw=fgets($data)) {
		$line=explode(',',$rw);
		// if($hdr==1)
		getData("'".$line[0]."','".$line[1]."','".$line[3]."','".$line[5]."','".$line[7]."','".$line[8]."',".$line[9].",".$line[12].",".$line[13].",".$line[14].",".$line[15]);
		$hdr=1;
	}
}

function getData($idata) {
	$con=SQLi('distributor');
	$qry=mysqli_query($con,"INSERT INTO bodstp VALUES ($idata);");
	mysqli_close($con);
}
?>
