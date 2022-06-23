<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
date_default_timezone_set('Asia/Manila');
require('../fetch.php');
if(!ISIN_GOS&&!ISIN_PCM&&!LOGIN_BRANCH) testScope("global|logis|orders|apc|gos|pcm",DLC_ADMIN);
$doview=testScope("global|logis|orders|gos|pcm",'');

$_SESSION['releasing']=false;
$id=isset($_GET['id'])?$_GET['id']:null;
$adminpage='logistics';
$title='| '.strtoupper($adminpage);
$page=ucwords($adminpage);

$isasm=(isset($_SESSION['isAssembly'])&&$_SESSION['isAssembly']);
$issales=(substr($id,0,3)=='004')?true:false;
$isapc=testScope("apc",'');
$root=$isapc?'../logistics/':'';
$data='';$msg='';
$tbl=$isasm?'tbllogassembly':'tbllogtransfer';
$ttl=$isasm?'Assembly':($issales?'Sales':'Transfer');

$con=SQLi('products');
$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$id'") or die(mysqli_error($con));
if(mysqli_num_rows($rs)>0) {
	$rec=null;
	$rw=mysqli_fetch_assoc($rs);
	$data.='<li class="hdr blue">'.$ttl.' Details<span class="blk s6 rt">ID: <strong class="blue">'.$rw['id'].'</strong></span></li>';
	if(isset($rw['relstamp'])&&$rw['relstamp']=='') $_SESSION['releasing']=true;

	if($isasm) {
		$data.='<li><span>Assembly: <strong class="blue">'.getPName($id).'</strong></span></li>';
		$data.='<li><span class="s3 ct">Item</span><span class="s5">Description</span><span class="s3 rt">Qty</span></li>';
	}else{
		$data.=$isapc?'':'<li><span class="s5">From: <strong class="blue">'.$rw['whfrom'].'</strong></span>'.($issales?'':'<span class="s5">To: <strong class="blue">'.$rw['whto'].'</strong></span></li>');
		$data.=$issales?'':'<li><span class="s5">Requested: <strong class="blue">'.date('Y-m-d',strtotime($rw['reqstamp'])).'</strong></span><span class="s5">Released: <strong class="blue">'.(($rw['relstamp']!='')?date('Y-m-d',strtotime($rw['relstamp'])):'').'</strong></span><span class="s5">Received: <strong class="blue">'.(($rw['recstamp']!='')?date('Y-m-d',strtotime($rw['recstamp'])):'').'</strong></span></li>';
		$data.=$issales?'<span class="s5">Posted By: <strong class="blue">'.$rw['reqid'].'</strong></span></li>':'<li><span class="s5">Requester ID: <strong class="blue">'.$rw['reqid'].'</strong></span><span class="s5">Releaser ID: <strong class="blue">'.$rw['relid'].'</strong></span><span class="s5">Receiver ID: <strong class="blue">'.$rw['recid'].'</strong></span></li>';
		$data.='<li><span class="s3 ct">Item</span><span class="s5">Description</span><span class="s3 rt">'.($issales?'Qty':'Requested').'</span>'.($issales?'':'<span class="s3 rt">Released</span><span class="s3 rt">Received</span>').'<span class="s3 ct">Remarks</span></li>';
		$rec=($rw['status'])?explode("~",$rw['recdesc']):($issales?explode("~",$rw['recdesc']):null);
	}
	$req=explode("~",$rw['reqdesc']);
	$rel=isset($rw['reldesc'])?explode("~",$rw['reldesc']):null;
	mysqli_close($con);
	foreach($req as $v) {
		$ireq[]=explode("|",$v);
	}
	$sig='<span id="sig"><br /><br />'.str_pad('Signature:',50,"_").'</span>';
	$data.=getTransfers($ireq,$rec,$rel);
	$msg.='<form method="post" action="'.$root.'post.php" id="transarcdetails"><ul>'.$data;
	$msg.=((isset($rw['status'])&&$rw['status'])||($_SESSION['releasing']&&!$doview))?'':'<li><input type="hidden" name="id" value="'.$id.'" />'.($isasm?'':'<input type="submit" name="submit" class="btn" value="SUBMIT" />').DIV_CLEAR.$sig.'</li>';
	$msg.='</ul></form>';
}else{
	$msg='<br /><div class="bad ct">Transfer ID not found</div>';
}
ob_start();
include('../head.php');
echo $isasm?'':'<ul class="print"><li><a href="javascript:window.print()"></a></li></ul>';
echo $msg;
echo '<div><a href="'.DLC_ROOT.$_SESSION['lastpage'].'" class="back" id="download">BACK</a></div>';
require('../foot.php');
ob_end_flush();
?>
