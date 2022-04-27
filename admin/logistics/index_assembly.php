<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
echo '<div><a href="?p=assembly'.(($do=='transarc')?'':'&do=transarc').'" class="back" id="download">ASSEMBLE '.(($do=='transarc')?'STOCKS':'HISTORY').'</a></div>';
$_SESSION['isAssembly']=true;$data='';
$apcroot=testScope("global|orders|apc")?'../logistics/':'';
if($do=='transarc'){
	$data.='<li><span class="s4">Assembled ID</span><span class="s5">Assembled Set</span>';
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT * FROM tbllogassembly ORDER BY id") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$data.='<li><a href="'.$apcroot.'viewtransfer.php?id='.$rw['id'].'" class="s4">'.$rw['id'].'</a><span class="s5">'.getPName($rw['id']).'</span>';
	}
	echo '<form><ul id="transarc"><li class="hdr blue">Assemble History</li>'.$data.'</ul></form>';
}else{
	$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
	echo '<form id="addline"><ul>';
	echo '<li class="hdr blue">Assemble Stocks</li>';
	echo '<li><span class="s3 ct">ITEM</span><span class="s6 ct">DESCRIPTION</span><span class="s3 ct">QTY</span></li>';
	echo '<li><span class="s3 ct"><input type="text" id="stock_item" class="s2 ct" data-cod=1 maxlength=5 /></span><span class="s6" id="stock_desc"></span><span class="s3 ct"><input type="text" id="stock_qty" class="s2" value=0 /></span>';
	echo '</li></ul></form>';
	echo '<form id="transferstocks" class="assembly" method="post" action="'.$apcroot.'post.php">';
	echo '<ul>';
	if(isset($_SESSION['transfers'])&&count($_SESSION['transfers'])>0) echo getTransfers($_SESSION['transfers']);
	echo '</ul><div id="submitbuttons"><input type="submit" name="submit" class="btn" value="SUBMIT" /><input type="button" id="cleartrans" class="btn" value="CLEAR" /></div></form>';
}

function popCat($tbl,$id,$val,$distinct='',$qry='',$select='',$rel=''){
	$pCat='';
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT $distinct FROM $tbl $qry") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$rel=($rel!='')?'rel="'.$rw['parent_id'].'"':'';
		$pCat.= '<option value="'.$rw[$id].'" '.$rel.' '.(($select==$rw[$id])?SELECTED:'').'>'.ucwords($rw[$val]).'</option>';
	}
	return $pCat;
}
?>
