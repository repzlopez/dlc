<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$msg='';$tbl='tbl'.$content;
$upcal=isset($_SESSION['upcal'])&&$_SESSION['upcal'];
$con=SQLi('beta');
if($do=='list'){
	$msg.='<ul id="'.$content.'" class="list">';
	$msg.='<li class="hdr"><span class="s3">Date</span><span class="s4">Title</span><span class="s6">Description</span><span class="s1">Enabled</span></li>';

	$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE date='$item'") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$styleis=!$rw['status']?'bad':'';
		$msg.='<li rel="'.$rw['id'].'"><span class="s3"><a href="?p='.$content.'&do=2&i='.$rw['id'].'">'.$rw['date'].'</a></span><span class="s4">'.$rw['title'].'</span><span class="s6">'.$rw['description'].'</span><span class="s1 stat '.$styleis.'">'.$rw['status'].'</span></li>';
	}mysqli_close($con);
	$msg.='</ul>';
}elseif($do==0){
	$date=time();
	$seldate=(isset($_GET['sel']))?$_GET['sel']:$date;
	$selmonth=date('m',$seldate);
	$selyear=date('y',$seldate);
	$prev_mon=mktime(0,0,0,$selmonth-1,1,$selyear);
	$next_mon=mktime(0,0,0,$selmonth+1,1,$selyear);
	$first_day=mktime(0,0,0,$selmonth,1,$selyear);
	$day_of_week=date('D',$first_day);
	$days_in_month=cal_days_in_month(0,$selmonth,$selyear);

	switch($day_of_week){
		case "Sun":$blank=0;break;
		case "Mon":$blank=1;break;
		case "Tue":$blank=2;break;
		case "Wed":$blank=3;break;
		case "Thu":$blank=4;break;
		case "Fri":$blank=5;break;
		case "Sat":$blank=6;break;
	}

	$msg.='<ul id="'.$content.'" class="load_calendar">';
	$msg.='<div class="monyear"><a href="?p='.$content.'&do=0&sel='.$prev_mon.'"><<</a><span>'.date('F Y',$seldate).'</span><a href="?p='.$content.'&do=0&sel='.$next_mon.'">>></a></div>';
	$msg.='<li>Su</li><li>Mo</li><li>Tu</li><li>We</li><li>Th</li><li>Fr</li><li>Sa</li>';
	while($blank>0){
		$msg.="<li></li>";
		$blank--;
	}
	$n=1;
	while($n<=$days_in_month){
		$msg.="<li><a href=\"?p=$content&do=list&i=".$selmonth.sprintf('%02d',$n).date('Y',$seldate)."\">$n</a></li>";
		$n++;
	}$msg.='</ul>';
	$dl_file='cal_'.sprintf("%02d",$selmonth).date('Y',$seldate).'.pdf';
	$dl_cal=((file_exists('/downloads/calendar/'.$dl_file))?' <span>'.$dl_file.'</span>':'');
	$msg.=DIV_CLEAR.'<form method="post" action="uploadcalendar.php" enctype="multipart/form-data" class="upload"><ul>';
	$msg.='<li><div class="s5">CALENDAR TEMPLATE</div> <input type="file" name="template" class="iscsv" /><input type="submit" name="submit" value="UPLOAD" /></li>';
	$msg.='<li><div class="s5">DOWNLOADABLE CALENDAR</div> <input type="file" name="download" /><input type="submit" name="submit" value="UPLOAD" /><input type="hidden" name="calmonth" value="'.sprintf('%02d',$selmonth).date('Y',$seldate).'" />'.$dl_cal.'</li>';
	$msg.='<li><p class="rt msg">'.(isset($_SESSION['upcal'])?($upcal?'Upload Successful':'Upload Failed'):'').'</p></li>';
	$msg.='</ul></form>';
	unset($_SESSION['upcal']);
}else{
	if($do==1){
		$def="<div><b>TITLE</b></div><div>TIME</div><div>SPEAKER</div>";
		$msg.='<input type="hidden" name="id" />';
		$msg.='<li><label>Date:</label><input type="text" name="date" class="txt" value="'.$item.'" /></li>';
		$msg.='<li><label>Title:</label>'.dropTitle();
		$msg.='<li><label>Description:</label><div class="editor"><textarea id="panel" name="description">'.$def.'</textarea></div></li>';
		$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.C_K.' /></li>';
		$msg.='<li><input type="hidden" name="do" value="'.$do.'" /></li>';
	}else if($do==2){
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)){
			$msg.='<input type="hidden" name="id" value="'.$rw['id'].'" />';
			$msg.='<li><label>Date:</label><input type="text" name="date" class="txt" value="'.$rw['date'].'" /></li>';
			$msg.='<li><label>Title:</label>'.dropTitle($rw['title']);
			$msg.='<li><label>Description:</label><div class="editor"><textarea id="panel" name="description" class="ckeditor" >'.$rw['description'].'</textarea></div></li>';
			$msg.='<li><label>Status:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$rw['id'].'" /></li>';
		}mysqli_close($con);
	}
	$msg='<form method="post" action="/admin/update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
}echo $msg;

function dropTitle($sel=''){
	$arr=array(
		' - ACTIVITY',
		' - MKTG PLAN',
		' - TRAINING',
		' - HOLIDAY'
	);$ret='';

	$con=SQLi('beta');
	$rs=mysqli_query($con,"SELECT UPPER(id) uid,loc FROM tbllocations WHERE id<>'def' AND status=1 ORDER BY sort_order") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		foreach($arr as $i){
			$u=$rw['uid'].$i;
			$ret.='<option value="'.$u.'"'.(($u===$sel)?' selected="selected"':'').'>'.$rw['loc'].$i.'</option>';
		}
	}
	return '<select name="title" class="txt">'.$ret.'</select>';
}
?>
