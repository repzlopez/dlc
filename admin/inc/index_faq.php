<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$msg='';$tbl='tbl'.$content;
$f_str=(isset($_GET['filter'])?$_GET['filter']:NULL);
$fltr=str_split(strlen($f_str)==2?$f_str:'11');
$con=SQLI('beta');
if($do==0){
	$msg.='<ul id="filter" class="ct"><li>FILTER "<strong>Status</strong>"</li>';
	$msg.='<li><input type="checkbox" '.($fltr[0]?C_K:'').' />OFF</li><li><input type="checkbox" '.($fltr[1]?C_K:'').' />ON</li></ul>';
	$msg.='<ul id="'.$content.'" class="list clear">';
	$msg.='<li class="hdr"><span class="s6">Question</span>
			<span class="s1">PPX</span>
			<span class="s1">MP4</span>
			<span class="s1">Order</span>
			<span class="s1">Status</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl ORDER BY sort_order") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		if($fltr[$rw['status']]){
			$msg.='<li rel="'.$rw['id'].'">
				<span class="s6 lt"><a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.$rw['question'].'</a></span>
				<span class="s1 ct">'.($rw['ppx']!=''?1:0).'</span>
				<span class="s1 ct">'.($rw['mp4']!=''?1:0).'</span>
				<span class="s1 ct">'.$rw['sort_order'].'</span>
				<span class="s1 ct stat '.($rw['status']?'':'bad').'">'.$rw['status'].'</span></li>';
		}
	}mysqli_close($con);
	$msg.='</ul>';
}else{
	if($do==1){
		$msg.='<input type="hidden" name="id" value="" />';
		$msg.='<li><label>Question:</label><input type="text" name="question" class="txt" value="" /></li>';
		$msg.='<li><label>PPX:</label><input type="text" name="ppx" class="txt" value="" /></li>';
		$msg.='<li><label>MP4:</label><input type="text" name="mp4" class="txt" value="" /></li>';
		$msg.='<li><label>Sort Order:</label><input type="text" name="sort_order" class="txt" value="" /></li>';
		$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 /></li>';
		$msg.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2){
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)){
			$c_y=$rw['status']?C_K:'';
			$c_n=!$rw['status']?C_K:'';
			$msg.='<input type="hidden" name="id" value="'.$rw['id'].'" />';
			$msg.='<li><label>Question:</label><input type="text" name="question" class="txt" value="'.$rw['question'].'" /></li>';
			$msg.='<li><label>PPX:</label><input type="text" name="ppx" class="txt" value="'.$rw['ppx'].'" /></li>';
			$msg.='<li><label>MP4:</label><input type="text" name="mp4" class="txt" value="'.$rw['mp4'].'" /></li>';
			$msg.='<li><label>Sort Order:</label><input type="text" name="sort_order" class="txt" value="'.$rw['sort_order'].'" /></li>';
			$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" action="/admin/update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;
?>
