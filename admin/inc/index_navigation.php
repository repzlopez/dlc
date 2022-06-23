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
	$msg.='<li class="hdr"><span class="s4">Navigation</span><span class="s4">Tag Line</span><span class="s4">Left Pane</span><span class="s1">ID</span><span class="s1">Parent</span><span class="s1">Order</span><span class="s1">Status</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl ORDER BY sort_order,id") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		if($fltr[$rw['status']]) {
			$msg.='<li rel="'.$rw['id'].'"><span class="s4"><a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.$rw['navi'].'</a></span><span class="s4">'.$rw['tagline'].'</span><span class="s4">'.$rw['leftpane'].'</span><span class="id s1">'.$rw['id'].'</span><span class="s1">'.$rw['parent_id'].'</span><span class="s1">'.$rw['sort_order'].'</span><span class="s1 stat '.($rw['status']?'':'bad').'">'.$rw['status'].'</span></li>';
		}
	}mysqli_close($con);
	$msg.='</ul>';
}else{
	if($do==1) {
		$msg.='<li><label>ID:</label><input type="text" name="id" class="txt" value="" '.READ_ONLY.' /></li>';
		$msg.='<li><label>Navigation:</label><input type="text" name="navi" class="txt" value="" /></li>';
		$msg.='<li><label>Shorthand:</label><input type="text" name="short" class="txt" value="" /></li>';
		$msg.='<li><label>Tag Line:</label><input type="text" name="tagline" class="txt" value="" /></li>';
		$msg.='<li><label>Left Pane:</label><input type="text" name="leftpane" class="txt" value="" /></li>';
		$msg.='<li><label>Parent:</label><select name="parent_id"><option value="0" '.SELECTED.'>None</option>'.populateCat($tbl,'id','navi','*','WHERE parent_id=0','').'</select></li>';
		$msg.='<li><label>Sort Order:</label><input type="text" name="sort_order" class="txt" value=0 /></li>';
		$msg.='<li><label>Require Login:</label><input type="hidden" name="login" value=0 /><input type="checkbox" name="login" value=1 /></li>';
		$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 /></li>';
		$msg.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2) {
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)) {
			$msg.='<li><label>ID:</label><input type="text" name="id" class="txt" value="'.$rw['id'].'" '.READ_ONLY.' /></li>';
			$msg.='<li><label>Navigation:</label><input type="text" name="navi" class="txt" value="'.$rw['navi'].'" /></li>';
			$msg.='<li><label>Shorthand:</label><input type="text" name="short" class="txt" value="'.$rw['short'].'" /></li>';
			$msg.='<li><label>Tag Line:</label><input type="text" name="tagline" class="txt" value="'.$rw['tagline'].'" /></li>';
			$msg.='<li><label>Left Pane:</label><input type="text" name="leftpane" class="txt" value="'.$rw['leftpane'].'" /></li>';
			$msg.='<li><label>Parent:</label><select name="parent_id"><option value="0">None</option>'.populateCat($tbl,'id','navi','*','WHERE parent_id=0',$rw['parent_id']).'</select></li>';
			$msg.='<li><label>Sort Order:</label><input type="text" name="sort_order" class="txt" value="'.$rw['sort_order'].'" /></li>';
			$msg.='<li><label>Require Login:</label><input type="hidden" name="login" value=0 /><input type="checkbox" name="login" value=1 '.($rw['login']?C_K:'').' /></li>';
			$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" action="update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;
?>
