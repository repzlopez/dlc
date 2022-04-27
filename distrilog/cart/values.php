<?php
global $PCMPackage,$Membership,$boxfee;
$PCMPackage=array('18159','18164');
$Membership=array('18143','18144');

$met=array(
	// 'Bank - BPI (Current Account) 3041-1310-11'=>0,
	// 'Bank - Metrobank (Current Account) 309-7309513335'=>1,
	// 'Bank - Landbank (Savings Account / Grace Co) 666082940'=>2,
	// 'Bank - PNB (Savings Account / Grace Co) 549149800016'=>3,
	'Bank - BPI (Current Account) 0211-0408-07'=>4,
	'Bank - Landbank (Current Account) 0661-0960-80'=>5,
	// 'E-fund'=>6,
	'Pick-up (Card)'=>7,
	'Pick-up (Cash)'=>8,
	// 'RESERVED'=>9,
	// 'RESERVED'=>10,
	'Remittance Center c/o Alberto Lopez'=>11,
	// 'Remittance Center c/o Vandamme delos Reyes'=>12,
	'Cash on Delivery (COD) by LBC'=>21,
	'Cash on Pick-up (COP) by LBC'=>22,
	'Others (Please state payment info under NOTE)'=>99

);
$dlv1=array(
	'Cancelled'	=>0,
	'Outgoing'	=>1,
	'Processing'	=>2,
	'Incoming'	=>3,
	'Delivered'	=>4
);
$dlv2=array(
	'Cancelled'	=>0,
	'Incoming'	=>1,
	'Processing'	=>2,
	'Outgoing'	=>3,
	'Delivered'	=>4
);
$box=array(
	'Small'	=>0,
	'Medium'	=>1,
	'Large'	=>2,
	'Other'	=>3
);
$boxfee=array(200,300,400,0);

$paid1=array(
	'FOR VERIFICATION'=>0,
	'PAID'=>1
);
$paid2=array(
	'UNPAID'	=>0,
	'PAID'	=>1
);

function popRadio($arr,$chk,$name){$x='';
	global $boxfee;
	foreach($arr as $k=>$v){
		$chk=($chk==$v)?C_K:'';
		$x.='<label for="'.$k.'" class="s3 lt"><input type="radio" id="'.$k.'" name="'.$name.'" value="'.$v.'" data-boxfee="'.$boxfee[$v].'" '.$chk.' /> '.$k.'</label> ';
  	}return $x;
}

function popSelects($arr,$sel,$option){$p='';
	foreach($arr as $k=>$v){
		$p.='<option value="'.$v.'" '.($sel==$v?SELECTED:'').'>'.$k.'</option>';
 	}return $p;
}
?>
