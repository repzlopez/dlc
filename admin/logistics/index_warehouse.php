<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$x='';$tbl='tbl'.$content;
$tblnotfound=false;
$con=SQLi('products');
if($do==0){
	$x.='<ul id="'.$content.'" class="list">';
	$x.='<li class="hdr"><span class="s3">WH ID</span><span class="s6">WAREHOUSE</span><span class="s6">OIC</span><span class="s1">Enabled</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$linkisglobal=testScope("global|logis")?'<a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.$rw['id'].'</a>':$rw['id'];
		$x.='<li rel="'.$rw['id'].'"><span class="s3">'.$linkisglobal.'</span><span class="s6">'.$rw['wh'].'</span><span class="s6">'.$rw['oic'].'</span><span class="s1 stat '.($rw['status']?'':'bad').'">'.$rw['status'].'</span></li>';
	}mysqli_close($con);
	$x.='</ul>';
}else{
	if($do==1){
		$x.='<li><label>ID:</label><input type="text" name="id" class="txt" value="" maxlength=8 required /></li>';
		$x.='<li><label>Warehouse:</label><input type="text" name="wh" class="txt" value="" maxlength=32 required /></li>';
		$x.='<li><label>Officer-In-Charge:</label><input type="text" name="oic" class="txt" value="" maxlength=32 /></li>';
		$x.='<li><label>Distributor ID:</label><input type="text" name="dsdid" class="txt" value="" maxlength=16 /></li>';
		$x.='<li><label>Bond:</label><input type="text" name="bond" class="txt" value="" maxlength=32 /></li>';
		$x.='<li><label>Enabled:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 class="rdo" /></li>';
		$x.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2){
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_array($rs)){
			$x.='<li><label>ID:</label><input type="text" name="id" class="txt" value="'.$rw['id'].'" maxlength=8 /></li>';
			$x.='<li><label>Warehouse:</label><input type="text" name="wh" class="txt" value="'.$rw['wh'].'" maxlength=32 /></li>';
			$x.='<li><label>Officer-In-Charge:</label><input type="text" name="oic" class="txt" value="'.$rw['oic'].'" maxlength=32 /></li>';
			$x.='<li><label>Distributor ID:</label><input type="text" name="dsdid" class="txt" value="'.$rw['dsdid'].'" maxlength=16 /></li>';
			$x.='<li><label>Bond:</label><input type="text" name="bond" class="txt" value="'.$rw['bond'].'" maxlength=32 /></li>';
			$x.='<li><label>Enabled:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' class="rdo" /></li>';
			$x.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$x='<form method="post" action="/admin/update.php?t='.$content.'"><ul>'.$x;
	$x.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}

ob_start();
echo $x;
ob_end_flush();
?>
