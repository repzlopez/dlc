<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../admin/setup.php');
if((!ISIN_DISTRI&&!ISIN_ADMIN)||UPDATE_ON) { reloadTo(DLC_MYPAGE);exit; }
if(ISIN_ADMIN&&isset($_GET['u'])) { $_SESSION['u_site']=$_GET['u'];}
$title		="| Distributor Recap";
$homepage	='../';

$_SESSION['lastURI']='mypage';
$_SESSION['is_recap']=true;
$_SESSION['multirecap']=false;

require('head.php');
require('updaterecap.php');

ob_start();
echo ISIN_ADMIN?'<div id="recaplookup"><a href="'.DLC_ADMIN.'?p=recap" id="download">BACK TO ADMIN</a></div>':'';
echo '<ul class="print"><li><a href="print_pdf.php"></a></li>';
echo '<li>Recap Schedule: <select id="recapsched">'.getRecapSched($_SESSION['u_site'],$_SESSION['selmonyer']).'</select></li></ul>';
echo $_SESSION['recap_data'];
$out = ob_get_contents();

$arrJs=array('/js/jquery/jquery-1.7.1.min.js','/js/distrilog.js');
echo loadFoot('','',$arrJs);
ob_end_flush();

if(!isset($_GET['w'])) echo '<META HTTP-EQUIV=Refresh CONTENT="0; URL='.basename($_SERVER['PHP_SELF']).'?w='.$_SESSION['recap0'].'">';
$_SESSION['prints']=array('table'=>$out);

function getRecapSched($id,$select) {
	$idx=0;$ret=array();
	$qry="
		SELECT DISTINCT bdyy,bdmm,s.wk,s.*
		FROM diamondl_distributor.bodstp b
		LEFT JOIN diamondl_beta.tblsched s
			ON s.wk=bdmm AND s.yr=bdyy
		WHERE bddid='$id'
		AND bdsid='$id'
		AND bddids='$id'
		AND (bdtype='B' OR bdtype='Y')
		ORDER BY bdyy DESC,CAST(bdmm AS unsigned) DESC
	";
	$con=SQLi('distributor');
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		while($rw=mysqli_fetch_array($rs)) {
			foreach($rw as $k=>$v) $$k=$v;
			$str='Week'.sprintf('%02d',$wk).' '.date('M. d',strtotime($fst)).' - '.date('M. d',strtotime($lst)).', '.$yr;
			$val=sprintf('%02d',$wk).$yr;
			$selected=($val==$select)?SELECTED:'';
			$ret[].="<option value=\"$val\" $selected>$str</option>";
			if($idx==0) $_SESSION['recap0']=$val;
			$idx=1;
		}
		$ret[].="<option value=\"$select\">Select Week</option>";
	}return join($ret);
}
?>
