<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../fetch.php');
if(!ISIN_ADMIN||!testScope("global|data")){
	reloadTo(DLC_ADMIN);exit;
}else{
	date_default_timezone_set('Asia/Manila');
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: filename=Contacts_asof_'.date('mdY',time()).'.csv');
	if(isset($_GET['do'])){
		print listEmails($_GET['do']);
	}else print listContacts();
}

function listContacts(){$str='';
	$smart=array('63907','63908','63909','63910','63912',
				'63918','63919','63920','63921','63928',
				'63929','63930','63938','63939','63946',
				'63947','63948','63949','63989','63999');
	$globe=array('63905','63906','63915','63916','63917',
				'63925','63926','63927','63935','63936',
				'63937','63996','63997');
	$sun=array('63922','63923','63932','63933','63942','63943');
	$arr=array_merge($smart,$globe,$sun);
	foreach($arr as $v){
		$qry="
			SELECT * FROM distributors
			WHERE dscoid='DLCPH' AND dsmph LIKE '$v%'
			ORDER BY dsdid
		";
		$str.='"ALL '.$v.'-XXXXXXX"'."\n";
		$str.='"ID#","NAME","OFFICE","HOME","MOBILE"'."\n";
		$con=SQLi('distributor');
		$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)) {
			$str.='"'.$rw['dsdid'].'",';
			$str.='"'.$rw['dsfnam'].' '.$rw['dslnam'].'",';
			$str.=(strpos($rw['dsoph'],$v)!==false)?'"'.$rw['dsoph'].'",':'" ",';
			$str.=(strpos($rw['dshph'],$v)!==false)?'"'.$rw['dshph'].'",':'" ",';
			$str.=(strpos($rw['dsmph'],$v)!==false)?'"'.$rw['dsmph'].'"':'" "';
			$str.="\n";
		}$str.="\n\n";
	}mysqli_close($con);
	return $str;
}

function listEmails($do){
	$filter=($do)?'AND email!=dseadd':'';
	$qry="
		SELECT a.dsdid,concat(dslnam,' ',dsfnam) name,email,dseadd
		FROM distributors d,accounts a
		WHERE d.dsdid=a.dsdid
		$filter
		ORDER BY a.dsdid";
	$i=1;
	$str ='"","ID#","NAME","WEB EMAIL","AS400 EMAIL"';
	$str.="\n";
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$str.='"'.$i++.'",';
		$str.='"'.$rw['dsdid'].'",';
		$str.='"'.$rw['name'].'",';
		$str.='"'.$rw['email'].'",';
		$str.='"'.$rw['dseadd'].'"';
		$str.="\n";
	}$str.="\n\n";
	return $str;
}
?>
