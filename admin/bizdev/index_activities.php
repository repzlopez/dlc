<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$msg='';$tbl='tbl'.$content;
$_SESSION['filter']=(isset($_GET['filter'])?$_GET['filter']:(isset($_SESSION['filter'])?$_SESSION['filter']:NULL));
$filter=(strlen($_SESSION['filter'])==3)?$_SESSION['filter']:'111';
$con=SQLi('beta');
if($do==0) {$d=0;
	$fltr_arr=array();
	$rs=mysqli_query($con,"SELECT DISTINCT cat FROM $tbl") or die(mysqli_error($con));
	$msg.='<ul id="filter" class="ct"><li>FILTER "<strong>Activity</strong>"</li>';
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
	$msg.='<li class="hdr"><span class="s5"></span><span class="s4">Title</span><span class="s3">Activity</span><span class="s1">DL</span><span class="s1">Dist.Only</span><span class="s1">Feat</span><span class="s1">Enabled</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl ORDER BY status DESC,feat DESC,date DESC") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)) {
		if(in_array($rw['cat'],$fltr_arr)) {
			$featis=(!$rw['feat'])?' bad':'';
			$linkisglobal='<a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.($rw['title']!=''?$rw['title']:'NO TITLE').'</a>';
			$msg.='<li rel="'.$rw['id'].'"><div class="s5"><img src="/images/activities/'.$rw['img'].'" alt="" /></div><span class="s4">'.$linkisglobal.'</span><span class="s3">'.$rw['cat'].'</span><span class="s1">'.(($rw['dlfile']!='')?'yes':'no').'</span><span class="s1 '.($rw['distonly']?'':'bad').'">'.$rw['distonly'].'</span><span class="s1" '.$featis.'>'.$rw['feat'].'</span><span class="s1 stat '.($rw['status']?'':'bad').'">'.$rw['status'].'</span></li>';
		}
	}mysqli_close($con);
	$msg.='</ul>';
}else{
	if($do==1) {
		$msg.='<input type="hidden" name="id" class="txt" value="'.md5(time()).'" />';
		$msg.='<li><label>Date:</label><input type="text" name="date" class="txt s3" value="" /> <span class="smaller">**yyyymmdd</span></li>';
		$msg.='<li><label>Activity:</label><label class="s2"><input type="radio" name="cat" value="news" '.C_K.' class="rdo" />News</label><label class="s2"><input type="radio" name="cat" value="events" class="rdo" />Event</label><label class="s2"><input type="radio" name="cat" value="promos" class="rdo" />Promo</label></li>';
		$msg.='<li><label>Title:</label><input type="text" name="title" class="txt" value="" /></li>';
		$msg.='<li><label>Content:</label><div class="editor"><textarea id="panel" name="content"></textarea></div></li>';
		$msg.='<li><label>Image:</label><input type="file" name="file_img" /><input type="text" name="img" value="" '.READ_ONLY.' /></li>';
		$msg.='<li><label>Download:</label><input type="file" name="upfile" /><input type="text" name="dlfile" value="" '.READ_ONLY.' /></li>';
		$msg.='<li><label>Distributor Only:</label><input type="hidden" name="distonly" value=0 /><input type="checkbox" name="distonly" value=1 class="rdo" /></li>';
		$msg.='<li><label>Featured:</label><input type="hidden" name="feat" value=0 /><input type="checkbox" name="feat" value=1 class="rdo" /></li>';
		$msg.='<li><label>Enabled:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 class="rdo" /></li>';
		$msg.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2) {
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		if(mysqli_num_rows($rs)>0) {
			$rw=mysqli_fetch_array($rs);
			$chknews=($rw['cat']=='news')?C_K:'';
			$chkevent=($rw['cat']=='events')?C_K:'';
			$chkpromo=($rw['cat']=='promos')?C_K:'';
			$msg.='<input type="hidden" name="id" class="txt" value="'.$rw['id'].'" />';
			$msg.='<li><label>Date:</label><input type="text" name="date" class="txt s3" value="'.$rw['date'].'" /> <span class="smaller">**yyyymmdd</span></li>';
			$msg.='<li><label>Activity:</label><label class="s2"><input type="radio" name="cat" value="news" '.$chknews.' class="rdo" />News</label><label class="s2"><input type="radio" name="cat" value="events" '.$chkevent.' class="rdo" />Event</label><label class="s2"><input type="radio" name="cat" value="promos" '.$chkpromo.' class="rdo" />Promo</label></li>';
			$msg.='<li><label>Title:</label><input type="text" name="title" class="txt" value="'.$rw['title'].'" /></li>';
			$msg.='<li><label>Content:</label><div class="editor"><textarea id="panel" name="content">'.$rw['content'].'</textarea></div></li>';
			$msg.='<li><label>Image:</label><input type="file" name="file_img" /><input type="text" name="img" value="'.$rw['img'].'" '.READ_ONLY.' /></li>';
			$msg.='<li><label>Download:</label><input type="file" name="upfile" /><input type="text" name="dlfile" value="'.$rw['dlfile'].'" '.READ_ONLY.' /></li>';
			$msg.='<li><label>Distributor Only:</label><input type="hidden" name="distonly" value=0 /><input type="checkbox" name="distonly" value=1 '.($rw['distonly']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><label>Featured:</label><input type="hidden" name="feat" value=0 /><input type="checkbox" name="feat" value=1 '.($rw['feat']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><label>Enabled:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" enctype="multipart/form-data" action="/admin/update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;
?>
