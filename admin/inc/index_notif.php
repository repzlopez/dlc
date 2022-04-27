<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
if(!IS_GLOB){reloadTo(DLC_ADMIN);exit;}
$msg='';$tbl='tbl'.$content;
$con=SQLi('beta');
if($do==0){
	$msg.='<ul id="'.$content.'" class="list">';
	$msg.='<li class="hdr"><span class="s7">Notification</span><span class="s1">Impt</span><span class="s1">Enabled</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$styleis=!$rw['status']?'bad':'';
		$msg.='<li rel="'.$rw['id'].'"><span class="s7"><a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.$rw['txt'].'</a></span><span class="s1">'.$rw['imp'].'</span><span class="s1 stat '.$styleis.'">'.$rw['status'].'</span></li>';
	}mysqli_close($con);
	$msg.='</ul>';
}else{
	if($do==1){
		$msg.='<input type="hidden" name="id" />';
		$msg.='<li><label>Notification:</label><input type="text" name="txt" class="txt" value="" /></li>';
		$msg.='<li><label>Important:</label><input type="hidden" name="imp" value=0 /><input type="checkbox" name="imp" value=1 /></li>';
		$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 /></li>';
		$msg.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2){
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)){
			$msg.='<input type="hidden" name="id" value="'.$rw['id'].'" />';
			$msg.='<li><label>Notification:</label><input type="text" name="txt" class="txt" value="'.$rw['txt'].'" /></li>';
			$msg.='<li><label>Important:</label><input type="hidden" name="imp" value=0 /><input type="checkbox" name="imp" value=1 '.($rw['imp']?C_K:'').' /></li>';
			$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" action="update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;
?>
