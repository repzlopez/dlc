<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
global $z;$z='';
$x='<form method="post" action="index.php?p=lineage"><ul>';
$x.='<li><span class="blue">Distributor Lookup Lineage</span>';
$x.='<li><span class="s3">LOOKUP:</span><input type="text" name="find" class="txt s5" /> <span class="small more">** dist.id</span></li>';
$x.='</ul></form>';
if(isset($_POST['find'])){
	$id=trim_escape($_POST['find']);
	$name=trim(getName($id,'fml'));

	$x.='<form><ul class="list"><li class="hdr"><span class="s4">Dist. ID</span><span class="s6 lt">Name</span></li>';
	$x.='<li class="'.($name!==''?'blue':'bad').'"><span class="s4">'.$id.'</span><span class="s6 ">'.($name!==''?$name:'DISTRIBUTOR NOT FOUND').'</span></li>';
	$x.=$name!==''?getDistList($id):'';
	$x.='</ul></form>';
	unset($_POST);
}
ob_start();
echo $x;
ob_end_flush();

function getDistList($id){
	global $z;
	if($id!=EDDY){
		$con=SQLi('distributor');
		$rs=mysqli_query($con,"SELECT dssid FROM distributors WHERE dsdid='$id'") or die(mysqli_error($con));
		$rw=mysqli_fetch_assoc($rs);
		$z.='<li><span class="s4">'.$rw['dssid'].'</span><span class="s6 ">'.getName($rw['dssid'],'fml').'</span></li>';
		getDistList($rw['dssid']);
	}return $z;
}
?>
