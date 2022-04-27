<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
list($curwk,$yr,$thu,$wed)=getDatWk(date('Y-m-d'));
$x ='<form method="post" action="index.php?p=distripv"><ul>';
$x.='<li><span class="blue">Distributor Lookup PV</span>';
$x.='<li><span class="s3">LOOKUP:</span><input type="text" name="find" class="txt s5" /> <span class="small more">** dist.id, mmyyyy</span></li>';
$x.='<li><span class="s3">CURRENT:</span> Week '.WEEKDESC.'</li>';
$x.='</ul></form>';
$x.=isset($_SESSION['distripv'])?$_SESSION['distripv']:'';
unset($_SESSION['distripv']);
if(isset($_POST['find'])){$qry='';
	$id=trim_escape($_POST['find']);
	unset($_POST);
	if(strpos($id,',')!==false){
		list($id,$do)=explode(',',$id);
	}else $do=WEEK.WKYR;

	$bmh=(WEEK.WKYR==$do)?'m':'h';
	if(isset($do)&&$do!=''){
		$wk=substr($do,0,2);
		$yr=substr($do,2,4);
		$qry=($bmh=='h')?" AND bhpmo=$wk AND bhpyr=$yr":BMPMO;
	}
	$con=SQLi(($bmh=='h')?'distributor':RTDB);
	$qry="SELECT b".$bmh."ppv ppv,b".$bmh."npv tpv FROM bo".$bmh."stp WHERE b".$bmh."did='$id' $qry";
	$rs=mysqli_query($con,$qry) or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$ppv=$rw['ppv'];
	$tpv=$rw['tpv'];
	$gpv=$tpv-$ppv;
	mysqli_close($con);
	$d ='<form><ul><li class="blue">Details</li>';
	$d.='<li><span class="s3">ID:</span>'.$id.'</li>';
	$d.='<li><span class="s3">Wk/Yr:</span><span class="s2 rt">'.$do.'</span></li>';
	$d.='<li><span class="s3">PPV:</span><span class="s2 rt">'.number_format($ppv,2).'</span></li>';
	$d.='<li><span class="s3">GPV:</span><span class="s2 rt">'.number_format($gpv,2).'</span></li>';
	$d.='<li><span class="s3">TPV:</span><span class="s2 rt">'.number_format($tpv,2).'</span></li>';
	$d.='</ul></form>';
	$_SESSION['distripv']=$d;
	reloadTo('index.php?p=distripv');
}
ob_start();
echo $x;
ob_end_flush();
?>
