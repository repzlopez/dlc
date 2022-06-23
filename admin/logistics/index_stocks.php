<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$msg='';$tbl='tbllist';
$con=SQLi('products');
if($do==0) {
	list($warehouses,$cols)=getStocksWHID();
	$qry="SELECT id,name,status FROM $tbl WHERE status=1 ORDER BY id";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$msg='<div><br>Found <strong class="blue">'.mysqli_num_rows($rs).'</strong> ACTIVE items</div>';
	$msg.='<div id="box"><ul id="'.$content.'" class="list clear">';
	$msg.='<li class="hdr"><span class="s1">Item</span><span class="s5">Description</span><span class="s1 rt">Safe</span>'.$warehouses.'<span class="s1 rt">QC_Total</span></li>';
	while($rw=mysqli_fetch_array($rs,MYSQLI_BOTH)) {
		$buff=0;$safe=0;$id=$rw['id'];
		$pname=utf8_encode($rw['name']);
		list($stocks,$buff,$safe)=getWHStocks($id,$cols);
		$msg.='<li rel="'.$id.'">';
		$msg.='<span class="s1"><a href="?p='.$content.'&do=2&i='.$id.'">'.$id.'</a></span>';
		$msg.='<span class="s5" title="'.$pname.'">'.$pname.'</span>';
		$msg.='<span class="s1 rt">'.$safe.'</span>';
		$islow=($buff<$safe)?'bad':'';
		$msg.=$stocks.'<span class="s1 rt '.$islow.'">'.$buff.'</span></li>';
	}mysqli_close($con);
	$msg.='</ul></div>';
}else{
	if($do==2) {
		$rs=mysqli_query($con,"SELECT id,name,status FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
			while($rw=mysqli_fetch_assoc($rs)) {
			$id=$rw['id'];
			list($stocks,$buff,$safe)=getWHStocks($id,$cols);
			$msg.='<li><label>Product Code:</label><span>'.$rw['id'].'</span></li>';
			$msg.='<li><label>Description:</label><span>'.$rw['name'].'</span></li>';
			$msg.='<li><label>Safe Qty:</label><input type="text" name="safeqty" class="txt" value="'.$safe.'" /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" action="/admin/update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;

function getWHName($id) {
	$id=substr($id,1);
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT wh FROM tblwarehouse WHERE id=$id") or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	return $rw['wh'];
}

function getStocksWHID() {
	$i=1;$data='';
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT * FROM tblstocks") or die(mysqli_error($con));
	while($i<mysqli_num_fields($rs)) {
		$col=mysqli_fetch_field_direct($rs,$i)->name;
		if($col!='safeqty') {
			$data.='<span class="s1 rt" title="'.getWHName($col).'">'.$col.'</span>';
		}$i++;
	}return array($data,$i);
}

function getWHStocks($id,$cols) {
	$data='';$safe=0;$buff=0;
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT * FROM tblstocks WHERE id='$id'") or die(mysqli_error($con));
	if(mysqli_num_rows($rs)>0) {
		while($rw=mysqli_fetch_array($rs)) {
			$safe=$rw['safeqty'];
			for($i=2;$i<mysqli_num_fields($rs);$i++) {
				$v=$rw[$i];
				$rs1=mysqli_fetch_field($rs)->name;
				$buff+=(substr(($rs1),1)<110)?$v:0;
				$data.='<span class="s1 rt">'.$v.'</span>';
			}
		}
	}
	return array($data,$buff,$safe);
}
?>
