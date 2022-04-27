<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$msg='';$tbl='tbl'.$content;
$con=SQLi('products');
if($do==0){
	$msg.='<ul id="'.$content.'" class="list clear">';
	$msg.='<li class="hdr"><span class="s2 lt">ID</span><span class="s5 lt">GROUP</span><span class="s5">REFERRAL BONUS</span><span class="s1">No Slot</span><span class="s1">Status</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl ORDER BY id") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$msg.='<li rel="'.$rw['id'].'"><span class="s2 lt"><a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.$rw['id'].'</a></span>';
		$msg.='<span class="s5 lt">'.$rw['forgroup'].'</span>';
		$msg.='<span class="s5">'.$rw['referral'].'</span>';
		$msg.='<span class="s1 '.($rw['noslot']?'':'bad').'">'.$rw['noslot'].'</span>';
		$msg.='<span class="s1 stat '.($rw['status']?'':'bad').'">'.$rw['status'].'</span></li>';
	}mysqli_close($con);
	$msg.='</ul>';
}else{
	if($do==1){
		$msg.='<li><label>ID:</label><input type="text" name="id" class="txt" value="" required /></li>';
		$msg.='<li><label>Group:</label><input type="text" name="forgroup" class="txt" value="" /></li>';
		$msg.='<li><label>Referral Bonus:</label><input type="text" name="referral" class="txt" value="" /></li>';
		$msg.='<li><label>No Slot:</label><input type="hidden" name="noslot" value=0 /><input type="checkbox" name="noslot" value=1 /></li>';
		$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 /></li>';
		$msg.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2){
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)){
			$c_y=$rw['status']?C_K:'';
			$c_n=!$rw['status']?C_K:'';
			$msg.='<li><label>ID:</label><input type="text" name="id" class="txt" value="'.$rw['id'].'" /></li>';
			$msg.='<li><label>Group:</label><input type="text" name="forgroup" class="txt" value="'.$rw['forgroup'].'" /></li>';
			$msg.='<li><label>Referral Bonus:</label><input type="text" name="referral" class="txt" value="'.$rw['referral'].'" /></li>';
			$msg.='<li><label>No Slot:</label><input type="hidden" name="noslot" value=0 /><input type="checkbox" name="noslot" value=1 '.($rw['noslot']?C_K:'').' /></li>';
			$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" action="/admin/update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;
?>
