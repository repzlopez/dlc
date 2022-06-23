<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$msg='';$tbl='tbl'.$content;
$_SESSION['filter']=(isset($_GET['filter'])?$_GET['filter']:(isset($_SESSION['filter'])?$_SESSION['filter']:NULL));
$filter=(strlen($_SESSION['filter'])==2)?$_SESSION['filter']:'11';
$con=SQLi('beta');
if($do==0) {$d=0;
	$fltr_arr=array();
	$rs=mysqli_query($con,"SELECT DISTINCT cat FROM $tbl") or die(mysqli_error($con));
	$msg.='<ul id="filter" class="ct"><li>FILTER "<strong>Download</strong>"</li>';
	while($rw=mysqli_fetch_assoc($rs)) {
		if(!isset($_SESSION['filter'])) {
			$msg.='<li><input type="checkbox" '.C_K.' />'.$rw['cat'].'</li>';
			$fltr_arr[]=$rw['cat'];
		}else{
			if(substr($filter,$d++,1)==1) {
				$msg.='<li><input type="checkbox" '.C_K.' />'.$rw['cat'].'</li>';
				$fltr_arr[]=$rw['cat'];
			}else{
				$msg.='<li><input type="checkbox" />'.$rw['cat'].'</li>';
			}
		}
	}
	$msg.='</ul>';
	$msg.='<ul id="'.$content.'" class="list clear">';
	$msg.='<li class="hdr"><span class="s4"></span><span class="s5">Title</span><span class="s3">Download</span><span class="s1">Order</span><span class="s2">Dist. Only</span><span class="s1">Enabled</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl ORDER BY sort_order,cat DESC,id DESC") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		if(in_array($rw['cat'],$fltr_arr)) {
			$styleis=!$rw['status']?'bad':'';
			$styledis=!$rw['distonly']?'bad':'';
			$imgprev=($rw['cat']=='newsletters')?'img':'dlfile';
			$ext=substr(pathinfo($rw[$imgprev],PATHINFO_EXTENSION),0,3);
			$imgprevdir=($rw['cat']=='newsletters')?'/images/downloads/'.$rw[$imgprev]:'../src/'.$ext.'.png';
			$linkisglobal='<a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.$rw['title'].'</a>';
			$msg.='<li rel="'.$rw['id'].'"><div class="s4"><img src="'.$imgprevdir.'" alt="" /></div><span class="s5">'.$linkisglobal.'</span><span class="s3">'.$rw['cat'].'</span><span class="s1">'.$rw['sort_order'].'</span><span class="s2 '.$styledis.'">'.$rw['distonly'].'</span><span class="s1 stat '.$styleis.'">'.$rw['status'].'</span></li>';
		}
	}mysqli_close($con);
	$msg.='</ul>';
}else{
	if($do==1) {
		$msg.='<li><label>ID:</label><input type="text" name="id" class="txt" value="" required /></li>';
		$msg.='<li><label>Download:</label><label class="s3"><input type="radio" name="cat" value="newsletters" '.C_K.' class="rdo" />Newsletter</label><label class="s3"><input type="radio" name="cat" value="downloads" class="rdo" />Download</label></li>';
		$msg.='<li><label>Title:</label><input type="text" name="title" class="txt" value="" required /></li>';
		$msg.='<li><label>Image:</label><input type="file" name="file_img" /><input type="text" name="img" value="" '.READ_ONLY.' /></li>';
		$msg.='<li><label>Download:</label><input type="file" name="upfile" /><input type="text" name="dlfile" value="" '.READ_ONLY.' /></li>';
		$msg.='<li><label>Mirror Link:</label><input type="text" name="mirror" class="txt" value="" /></li>';
		$msg.='<li><label>Sort Order:</label><input type="text" name="sort_order" class="txt" value="0" /></li>';
		$msg.='<li><label>Distributor Only:</label><input type="hidden" name="distonly" value=0 /><input type="checkbox" name="distonly" value=1 class="rdo" /></li>';
		$msg.='<li><label>Enabled:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 class="rdo" /></li>';
		$msg.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2) {
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)) {
			$c_news=($rw['cat']=='newsletters')?C_K:'';
			$c_load=($rw['cat']=='downloads')?C_K:'';
			$msg.='<li><label>ID:</label><input type="text" name="id" class="txt" value="'.$rw['id'].'" /></li>';
			$msg.='<li><label>Download:</label><label class="s3"><input type="radio" name="cat" value="newsletters" '.$c_news.' class="rdo" />Newsletter</label><label class="s3"><input type="radio" name="cat" value="downloads" '.$c_load.' class="rdo" />Download</label></li>';
			$msg.='<li><label>Title:</label><input type="text" name="title" class="txt" value="'.$rw['title'].'" /></li>';
			$msg.='<li><label>Image:</label><input type="file" name="file_img" /><input type="text" name="img" value="'.$rw['img'].'" '.READ_ONLY.' /></li>';
			$msg.='<li><label>Download:</label><input type="file" name="upfile" /><input type="text" name="dlfile" value="'.$rw['dlfile'].'" '.READ_ONLY.' /></li>';
			$msg.='<li><label>Mirror Link:</label><input type="text" name="mirror" class="txt" value="'.$rw['mirror'].'" /></li>';
			$msg.='<li><label>Sort Order:</label><input type="text" name="sort_order" class="txt" value="'.$rw['sort_order'].'" /></li>';
			$msg.='<li><label>Distributor Only:</label><input type="hidden" name="distonly" value=0 /><input type="checkbox" name="distonly" value=1 '.($rw['distonly']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><label>Enabled:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" enctype="multipart/form-data" action="/admin/update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;
?>
