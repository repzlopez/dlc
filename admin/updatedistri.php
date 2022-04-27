<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
$search_on=(isset($_POST['find']))?$_POST['find']:null;
$page_on=(isset($_POST['page']))?$_POST['page']:null;
$find=isset($search_on)?"WHERE (d.dsdid LIKE '%$search_on%' OR dsfnam LIKE '%$search_on%' OR dsmnam LIKE '%$search_on%' OR dslnam LIKE '%$search_on%')":'';
$dbsrc='distributor';
require_once('info.config');
require_once('infoconfig.php');
$qry="
	SELECT d.dsdid,dslnam,dsfnam,dsmnam,
		(SELECT SUM(CASE WHEN dslid='' THEN 1 ELSE 0 END)
		FROM ".DB."orders.tblslots o
		WHERE o.dsdid=d.dsdid) slots
	FROM ".DB."distributor.distributors d
	$find ORDER BY dsdid";

$y='';
// ini_set('max_execution_time',60);
$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
$num=mysqli_num_rows($rs);
while($rw=mysqli_fetch_assoc($rs)){
	$y.='<li rel="'.$rw['dsdid'].'">';
	$y.='<span class="s4"><a href="?p='.$page_on.'&id='.$rw['dsdid'].'">'.$rw['dsdid'].'</a></span>';
	$y.='<span class="s6">'.$rw['dslnam'].', '.$rw['dsfnam'].' '.$rw['dsmnam'].'</span>';
	$y.='<span class="s2 rt">'.(int)$rw['slots'].'</span></li>';
}

$x ='<ul id="distri" class="list clear">';
$x.='<li>Found <strong class="blue">'.$num.'</strong> Distributor'.($num>1?'s':'').'</li>';
$x.='<li class="hdr"><span class="s4">Distributor ID</span><span class="s6">Distributor Name</span><span class="s2 rt">Slots</span></li>';
$x.=$y;
$x.='</ul>';
echo $x;
?>
