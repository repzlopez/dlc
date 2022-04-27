<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$err='<span class="bad">Please select a valid .csv file</span> ';
$msg='<div><a href="?p=transfer'.(($do=='transarc')?'':'&do=transarc').'" class="back" id="download">TRANSFER '.(($do=='transarc')?'STOCKS':'HISTORY').'</a>'.($do=='transarc'?'':'<form class="rt" id="transfercsv" enctype="multipart/form-data" method="post" action="transfercsv.php">'.(isset($_SESSION['csverror'])?$err:'').'<span>UPLOAD CSV:</span><input type="file" name="file" /></form>').'</div>';
$_SESSION['isAssembly']=false;
$_SESSION['isSales']=false;
$counterid=getCounterID();$data='';
$apcid=isset($_SESSION['apc_id'])?substr($_SESSION['apc_id'],3):null;
$isapc=testScope("apc");
$apcroot=testScope("global|orders|apc")?'../logistics/':'';
$apcquery=$isapc?"AND whto='".substr($_SESSION['apc_id'],3)."'":(testScope("orders")&&!testScope("logis")?"AND whfrom=$counterid OR whto=$counterid":'');
if($do=='transarc'){
	$notapc=$isapc?'':'<span class="s2">From</span><span class="s2">To</span>';
	$data.='<li><span class="s4">Transfer ID</span><span class="s2">Requester</span>'.$notapc.'<span class="s1">Closed</span>';
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT * FROM tbllogtransfer WHERE whto!='004' $apcquery ORDER BY status,reqstamp") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$notapc=$isapc?'':'<span class="s2">'.$rw['whfrom'].'</span><span class="s2">'.$rw['whto'].'</span>';
		$data.='<li><a href="'.$apcroot.'viewtransfer.php?id='.$rw['id'].'" class="s4">'.$rw['id'].'</a><span class="s2">'.$rw['reqid'].'</span>'.$notapc.'<span class="s1 '.($rw['status']?'':'bad').'">'.$rw['status'].'</span>';
	}
	$msg.='<form><ul id="transarc"><li class="hdr blue">Transfer History</li>'.$data.'</ul></form>';
}else{
	$_SESSION['lastpage']=$_SERVER['REQUEST_URI'];
	$whlist=popCat('tblwarehouse','id','wh','*');
	$popwhfr='<select name="whfr">'.$whlist.(testScope("global|orders")?getTransferSelect(2):'').'</select>';
	$popwhto='<select name="whto">'.$whlist.getTransferSelect(9).'</select>';
	$msg.='<form id="addline"><ul>';
	$msg.='<li class="hdr blue">Transfer Stocks</li>';
	$msg.='<li><span class="s3 ct">ITEM</span><span class="s6 ct">DESCRIPTION</span><span class="s3 ct">QTY</span></li>';
	$msg.='<li><span class="s3 ct"><input type="text" id="stock_item" class="s2 ct" data-cod=1 maxlength=5 /></span><span class="s6" id="stock_desc"></span><span class="s3 ct"><input type="text" id="stock_qty" class="s2" value=0 /></span>';
	$msg.='</li></ul></form>';
	$msg.='<form id="transferstocks" method="post" action="'.$apcroot.'post.php">';
	$msg.=$isapc?'<input type="hidden" name="whfr" value="'.$counterid.'" /><input type="hidden" name="whto" value="'.$apcid.'" />':'<div><div class="rt">From: '.$popwhfr.' To: '.$popwhto.'</div></div>';
	$msg.='<ul>';
	if(isset($_SESSION['transfers'])&&count($_SESSION['transfers'])>0) $msg.=getTransfers($_SESSION['transfers']);
	$msg.='</ul><div id="submitbuttons"><input type="submit" name="submit" class="btn" value="SUBMIT" /><input type="button" id="cleartrans" class="btn" value="CLEAR" /></div></form>';
}
ob_start();
echo $msg;
ob_end_flush();

function getTransferSelect($id){
	$arrTransfer=array(
		'000'=>'Beginning',
		'001'=>'Taiwan',
		'002'=>'Supplier',
		'003'=>'Display',
		'005'=>'Demo',
		'006'=>'BFAD',
		'007'=>'Returns',
		'009'=>'Others'
	);$tran='';

	foreach($arrTransfer as $key=>$val){
		if((int)$key<=$id) $tran.= '<option value="'.$key.'">'.$val.'</option>';
	}return $tran;
}

function popCat($tbl,$id,$val,$distinct='',$qry='',$select='',$rel=''){
	$pCat='';
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT $distinct FROM $tbl $qry") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$rel=($rel!='')?'rel="'.$rw['parent_id'].'"':'';
		$pCat.= '<option value="'.$rw[$id].'" '.$rel.' '.(($select==$rw[$id])?SELECTED:'').'>'.ucwords($rw[$val]).'</option>';
	}return $pCat;
}

function getCounterID(){
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT * FROM tblwarehouse WHERE wh='counter'") or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	return $rw['id'];
}
?>
