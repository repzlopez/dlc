<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
echo '<div><a href="?p=sales'.(($do=='transarc')?'':'&do=transarc').'" class="back" id="download">SALES '.(($do=='transarc')?'ENTRY':'HISTORY').'</a></div>';
$_SESSION['isAssembly']=false;
$_SESSION['isSales']=true;
$salesid='004';$data='';
$isapc=testScope("apc");
$apcroot=(testScope("global|orders|apc"))?'../logistics/':'';
$apcquery=$isapc?"WHERE whto='".substr($_SESSION['apc_id'],3)."'":"WHERE whfrom=$salesid OR whto=$salesid";
if($do=='transarc'){
	$notapc=$isapc?'':'<span class="s2">From</span>';
	$data.='<li><span class="s4">Sales ID</span><span class="s2">Posted By</span>'.$notapc;
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT * FROM tbllogtransfer $apcquery ORDER BY status,reqstamp") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$notapc=$isapc?'':'<span class="s2">'.$rw['whfrom'].'</span>';
		$data.='<li><a href="'.$apcroot.'viewtransfer.php?id='.$rw['id'].'" class="s4">'.$rw['id'].'</a><span class="s2">'.$rw['reqid'].'</span>'.$notapc;
	}
	echo '<form><ul id="transarc"><li class="hdr blue">Sales History</li>'.$data.'</ul></form>';
}else{
	$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
	$popwhfr='<select name="whfr">'.popCat('tblwarehouse','id','wh','*',(testScope("orders")?'WHERE id<110':'')).'</select>';
	$popwhto='SALES<input type="hidden" name="whto" value="'.$salesid.'" />';
	echo '<form id="addline"><ul>';
	echo '<li class="hdr blue">Sales</li>';
	echo '<li><span class="s2 ct">ITEM</span> <span class="s6 ct">DESCRIPTION</span> <span class="s2 ct">QTY</span></li>';
	echo '<li><input type="text" id="stock_item" class="s2 ct" data-cod=1 maxlength=5 /> <span class="s6" id="stock_desc"></span> <input type="text" id="stock_qty" class="s1 ct" value=0 />';
	echo '</li></ul></form>';
	echo '<form id="transferstocks" method="post" action="'.$apcroot.'post.php">';
	echo $isapc?'<input type="hidden" name="whfr" value="'.$salesid.'" /><input type="hidden" name="whto" value="'.$salesid.'" />':'<div><div class="rt">From: '.$popwhfr.' To: '.$popwhto.'</div></div>';
	echo '<ul>';
	if(isset($_SESSION['transfers'])&&count($_SESSION['transfers'])>0) echo getTransfers($_SESSION['transfers']);
	echo '</ul><div id="submitbuttons"><input type="submit" name="submit" class="btn" value="SUBMIT" /><input type="button" id="cleartrans" class="btn" value="CLEAR" /></div></form>';
}

function popCat($tbl,$id,$val,$distinct='',$qry='',$selected='',$rel=''){
	$pCat='';
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT $distinct FROM $tbl $qry") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$rel=($rel!='')?'rel="'.$rw['parent_id'].'"':'';
		$pCat.= '<option value="'.$rw[$id].'" '.$rel.' '.(($selected==$rw[$id])?SELECTED:'').'>'.ucwords($rw[$val]).'</option>';
	}
	return $pCat;
}
?>
