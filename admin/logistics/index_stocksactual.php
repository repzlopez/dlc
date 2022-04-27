<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();

if ( !empty($_POST) ) {
	$idata=$udata=$sto='';
	foreach ( $_POST as $k=>$v ) {
		$con=SQLi('products');
          $$k = $v;
          if ( !empty($pid) && !empty($stocks) ) {
               for ( $i=0; $i<count($pid); $i++ ) {
     			$sto .= "$pid[$i]|$stocks[$i]~";
     		}
     		$udata .= "stocks='$sto'";
     		$idata .= "'".substr($sto,0,-1)."'";
          }
	}
	$dat = "'".date('Ymd_His',time())."',".$idata.",'".$_SESSION['login_id']."'";
	mysqli_query($con,"INSERT INTO tblstocksactual VALUES ($dat)") or die(mysqli_error($con));
	unset($_POST);
}

$_SESSION['isActualStocks']=true;
if(substr($item,0,3)=='Add'){ reloadTo('?p=stocksactual&do=1');exit; }
$allowed=testScope("global|logis|orders|apc",DLC_ADMIN);

$con=SQLi('products');
$qry="SELECT id,name,status FROM tbllist WHERE status=1 ORDER BY id";
$rs=mysqli_query($con,$qry) or die(mysqli_error($con));

$msg ='<div class="totop"><br>Found <strong class="blue">'.mysqli_num_rows($rs).'</strong> ACTIVE items';
if($allowed) $msg.='<select id="search" name="date"><option>Add Record</option>'.popCat('tblstocksactual','id','id','*','',$item).'</select>';
$msg.='</div><span class="rt">Posted by: <strong class="blue">'.getPoster($item).'</strong></span><div id="sig" class="rt">'.date('m.d.Y',time()).'</div><form method="post" enctype="multipart/form-data">';
$msg.='<ul id="'.$content.'" class="list clear"><input type="hidden" name="id" value="'.($do==2?$item:(($do==1)?time():'')).'" />';
$msg.='<li class="hdr"><span class="s1">Item</span><span class="s7">Description</span><span class="s1">Qty</span></li>';

while($rw=mysqli_fetch_array($rs,MYSQLI_BOTH)){
	$buff=0;$id=$rw['id'];
	$pname=utf8_encode($rw['name']);
	$qty=getActual($id);
	$msg.='<li rel="'.$id.'">';
	$msg.='<span class="s1">'.$id.'</span>';
	$msg.='<span class="s7">'.$pname.'</span>';
	if($do==0){
		$msg.='<span class="s1">'.$buff.'</span></li>';
	}else{
		$msg.='<input type="hidden" name="pid[]" value="'.$id.'" />';
		$msg.=((IS_GLOB||testScope("orders"))&&$do==2)?'<span class="s1 rt">'.sprintf('%01d',$qty).'</span>':'<input type="text" name="stocks[]" class="s1 txt rt" value="'.sprintf('%01d',$qty).'" />';
		$msg.='<span class="pbut"></span>';
	}
}
$msg.=($do>0)?'<li><input type="hidden" name="do" value="'.$do.','.($do==2?$item:(($do==1)?time():'')).'" /><input type="submit" name="submit" class="btn" value="Submit" /></li>':'';
$msg.='</ul></form>';
mysqli_close($con);

echo '<ul class="print"><li><a href="javascript:window.print()"></a></li></ul>';
echo $msg;
unset($_SESSION['actualstocks']);

function getPoster($id){
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT * FROM tblstocksactual WHERE id='$id'") or die(mysqli_error($con));
	$rw=mysqli_fetch_array($rs);
	$nam=$rw['oic'];
	$_SESSION['actualstocks']=explode('~',$rw['stocks']);
	return $nam;
}

function getActual($pcod){
	foreach($_SESSION['actualstocks'] as $key){
		if(substr($key,0,5)==$pcod){ return substr($key,6,strlen($key));break; }
	}
}

function popCat($tbl,$id,$val,$distinct='',$qry='',$selected=''){
	$pCat='';
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT $distinct FROM $tbl $qry") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$date=substr($rw[$val],0,8);
		$date=substr($date,0,4).'.'.substr($date,4,2).'.'.substr($date,-2);
		$time=substr($rw[$val],-6);
		$time=substr($time,0,2).':'.substr($time,2,2).':'.substr($time,-2);
		$pCat.= '<option value="'.$rw[$id].'" '.(($selected==$rw[$id])?SELECTED:'').'>'."$date $time".'</option>';
	}return $pCat;
}?>
