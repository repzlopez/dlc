<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
if(!IS_GLOB) {reloadTo(DLC_ADMIN);exit;}
$msg='';$tbl='tbl'.$content;
$f_str=(isset($_GET['filter'])?$_GET['filter']:NULL);
$fltr=str_split(strlen($f_str)==2?$f_str:'11');
$con=SQLi('beta');
if($do==0) {
	$msg.='<ul id="filter" class="ct"><li>FILTER "<strong>Status</strong>"</li>';
	$msg.='<li><input type="checkbox" '.($fltr[0]?C_K:'').' />OFF</li><li><input type="checkbox" '.($fltr[1]?C_K:'').' />ON</li></ul>';
	$msg.='<ul id="'.$content.'" class="list clear">';
	$msg.='<li class="hdr"><span class="s4 ct">ID</span><span class="s5 lt">Name</span><span class="s1">Level</span><span class="s2 rt">Recognized</span><span class="s1">Enabled</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl ORDER BY status DESC,level DESC,id") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		if($fltr[$rw['status']]) {
			$msg.='<li rel="'.$rw['id'].'"><span class="s4"><a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.$rw['id'].'</a></span><span class="s5">'.getName($rw['id'],'fml').'</span><span class="s1">'.$rw['level'].'</span><span class="s2 rt">'.$rw['date_recog'].'</span><span class="s1 stat '.($rw['status']?'':'bad').'">'.$rw['status'].'</span></li>';
		}
	}mysqli_close($con);
	$msg.='</ul>';
}else{
	if($do==1) {
		$msg.='<li><label>ID:</label><input type="text" name="id" class="txt" value="" /></li>';
		$msg.='<li><label>Level:</label><input type="text" name="level" class="txt" value="" /></li>';
		$msg.='<li><label>Recognized:</label><input type="text" name="date_recog" class="txt" value="" /></li>';
		$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 /></li>';
		$msg.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2) {
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)) {
			$c_y=$rw['status']?C_K:'';
			$c_n=!$rw['status']?C_K:'';
			$msg.='<li><label>ID:</label><input type="text" name="id" class="txt" value="'.$rw['id'].'" /></li>';
			$msg.='<li><label>Level:</label><input type="text" name="level" class="txt" value="'.$rw['level'].'" /></li>';
			$msg.='<li><label>Recognized:</label><input type="text" name="date_recog" class="txt" value="'.$rw['date_recog'].'" /></li>';
			$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" action="update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;
?>
