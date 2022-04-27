<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

if ( ! defined( 'GUEST' ) ) {
	define('INCLUDE_CHECK',1);
	require_once( '../admin/setup.php' );
}

function getDetails($id,$name,$addy,$cont){//(GUEST?'hide':'')
	$x ='<div class="orders '.('hide').'" id="details"><h3 class="blue">DISTRIBUTOR DETAILS</h3>';
	$x.='<ul>';
	$x.='<li><label class="s4 lbl">ID:</label><span class="s7">'.$id.'</span></li>';
	$x.='<li><label class="s4 lbl">Name:</label><span class="s7" id="distName">'.$name.'</span></li>';
	$x.='<li><label class="s4 lbl">Address:</label><span class="s7" id="distAddy">'.ucwords(strtolower($addy)).'</span></li>';
	$x.='<li><label class="s4 lbl">Contact #:</label><span class="s7" id="distCont">'.$cont.'</span></li>';
	$x.='</ul></div>';
	return $x;
}

function getDelivery($dlv,$box){
	$dlv=array('Outgoing'=>0);
	$x ='<div class="orders" id="delivery"><h3 class="blue">DELIVERY DETAILS '.(!GUEST?'<span class="s2"></span><label><input type="checkbox" id="useDist" /> Use Distributor Details</label>':'').'</h3><ul>';
	$x.='<li><span class="s4 lbl">Deliver To:</span> <input type="text" id="deliName" name="deliName" placeholder="Name of receiver" required /></li>';
	$x.='<li><span class="s4 lbl">Contact #:</span> <input type="text" id="deliCont" name="deliCont" placeholder="Contact# of receiver" required /></li>';
	$x.='<li><span class="s4 lbl">Delivery Address:</span> <textarea class="s7" id="deliAddy" name="deliAddy" placeholder="Address of receiver" required></textarea></li>';
	$x.='<li><span class="s4 lbl">NOTE:</span> <textarea class="s7" name="deliNote" placeholder="Enter note here"></textarea></li>';
	$x.='<li'.(GUEST?' class="hide"':'').'><span class="s4 lbl">Box Size:</span> '.popRadio($box,$box['Small'],'deliBox').'</li>';
	$x.='<li'.(GUEST?' class="hide"':'').'><span class="s4 lbl">Delivery Reference:</span> <input type="text" name="deliAddto" class="s4" maxlength=14 /> <span class="smaller">(group purchase)</span></li>';
	$x.='<li class="hide"><div class="rt"><a href="#" id="freightinfo">Additional Freight Information here</a><input type="hidden" name="deliStat" value=1 /></div></li>';
	$x.='</ul></div>';
	return $x;
}

function getPayment($met){
	global $boxfee;$olreg_msg=$ol_pv='';$nosf=0;
	switch(OLREG_REF){
		case OLREG_Choice100:$ol_pv=OLREG_min100;$ol_proc=1;break;
		case OLREG_Choice500:$ol_pv=OLREG_min500;$ol_proc=1;break;
		default:$ol_proc=0;
	}
	$olreg_msg=($ol_proc>0)?'purchased '.$ol_pv.'PV of Choice Package':'';
	$delivr=$nosf?0:$boxfee[0];	//default delivery fee
	$payamt=isset($_SESSION['payamount'])?$_SESSION['payamount']:0;
	$payplus=$payamt+$delivr+($ol_proc?PROC_FEE:0);
	$delifee=($delivr>0?' + <span class="smaller delfee">'.number_format($delivr,2).'</span>':'');
	$procfee=($ol_proc?'<span class="smaller procfee"> + '.number_format(PROC_FEE,2).'</span>':'');
	$defPayOp=array_search(4,$met);
	$p=popSelects($met,$defPayOp,true);
	$x ='<div class="orders" id="payment"><h3 class="blue">PAYMENT DETAILS</h3><ul>';
	$x.='<li><span class="s4 lbl">Method:</span><select name="payOp"> ';
	$x.=$p.'</select></li>';
	$x.='<li class="ifremit"><span class="s4 lbl">Pay To</span> <p class="s7">'.DLC_FULL.', Inc.<br>'.$defPayOp.'<br>0915 170 7388 / 0947 692 8060</p></li>';
	$x.='<li><span class="s4 lbl">Amount (Php):</span> <input type="text" rel="'.$payamt.'" value="'.number_format($payplus,2).'" name="payAmt" class="s3" '.(GUEST?READONLY:'').' /> <span class="smaller">( <span class="payamt smaller">'.number_format($payamt,2).'</span> '.$procfee.$delifee.' ** Order'.($ol_proc?' & Processing':'').($delivr?' + Shipping':'').' )</span>';
		$x.='<br><span class="s5"></span><span class="smaller">**Shipping fee may change depending on package and location</span>';
		$x.='<br><span class="s5"></span><span class="smaller">**In case of changes, you will be contacted for confirmation.</span></li>';
	$x.='<li><span class="s4 lbl">NOTE:</span> <textarea class="s7" name="payNote" placeholder="Enter note here">'.$olreg_msg.'</textarea></li>';
	$x.='<li><span class="s4 lbl">Payment Posted:</span> <input type="text" class="s4" id="datePicker" name="payDate" />';
	$x.=' <span class="smaller">** orders will be processed upon verification of payment</span>';
	$x.='<input type="hidden" name="payStat" value=0 /></li>';
	$x.='</ul></div>';
	return $x;
}

function getNotice(){
	$timedaily='10:00am - 6:00pm';
	$daysdaily='Monday - Saturday';
	$exceptday='holidays';
	$cutofftim='3:00pm';

	$con=SQLi('distributor');
	$rs=mysqli_query($con,"SELECT CONCAT(dslnam,', ',dsfnam,' ',dsmnam) name,dsmph FROM distributors WHERE dsdid='".GUEST."'");
	$rw=mysqli_fetch_array($rs);

	$x ='<div class="orders" id="notice"><h3 class="blue">NOTICE</h3><ul>';
	if(GUEST&&!RESELLER){
		$x.='<li><span class="s4 lbl">Referrer Info</span><span class="s3 lt">ID #:</span> <span>'.GUEST.'</span><br>';
		$x.='<span class="s4"></span><span class="s3 lt">Name:</span> <span>'.$rw['name'].'</span><br>';
		$x.='<span class="s4"></span><span class="s3 lt">Contact:</span> <span>'.$rw['dsmph'].'</span></li>';
		$x.='<li><br><h4>Other information</h4></li>';
	}
	$x.='<li><span class="s4 lbl">Cut-Off Time:</span> <span>'.$cutofftim.'</span> <span class="smaller">** orders processed after the cut-off time will be sent the following working day</span></li>';
	$x.='<li><span class="s4 lbl">Processing:</span> <span>'.$daysdaily.', except '.$exceptday.'</span><br>';
	$x.='<span class="s4 lbl"></span> <span>'.$timedaily.'</span></li>';
	$x.='<li><span class="s4 lbl">Delivery:</span> <span class="s4">Metro Manila and Luzon</span> 1 - 3 days<br>';
	$x.='<span class="s4 lbl"></span> <span class="s4">Visayas and Mindanao</span> 2 - 5 days</li>';
	if(GUEST) $x.='<li><br><h4>BEFORE CHECK OUT:</h4> <p>Kindly review your order'.(RESELLER?'':' and your referrer\'s information').'. Thank you.</p></li>';
	$x.='</ul></div>';
	return $x;
}

function getOrders(){
	global $dlcuser;
	$ttl=isset($dlcuser)?'':'<div class="blue">SHOPPING CART</div>';

	$x ='<div class="orders" id="shop_list">'.$ttl.'<ul>';
	$x.='<li class="hdr"><span class="s1"></span>';
	$x.='<span class="s6">Product</span>';
	$x.='<span class="s2 rt">PV</span>';
	$x.='<span class="s3 rt">Price</span>';
	$x.='<span class="s1 rt">Qty</span>';
	$x.='<span class="s3 rt">Total PV</span>';
	$x.='<span class="s3 rt">Total Amt</span></li>';
	$shopqty=0;$shopamt=0;$shopppv=0;$shopwt=0;

	if(isset($_SESSION['shoplist'])){
		foreach($_SESSION['shoplist'] as $v){
			$v1=$v[1];
			$v2=utf8_encode($v[2]);
			$v3=is_numeric($v[3])?$v[3]:0;
			$v4=is_numeric($v[4])?$v[4]:0;
			$v5=GUEST&&OLREG_REF==''?$v[8]:(is_numeric($v[5])?$v[5]:0);
			$v6=$v[6];
			$v7=$v[7];
			$shopqty+=$v3;
			$shopppv+=$v4*$v3;
			$shopamt+=$v5*$v3;
			$shopwt+=$v7*$v3;
			if($v3>0){
				$x.='<li>';
				$x.='<span class="s1 lt">'.$v1.'</span>';
				$x.='<span class="s6 link" rel="'.$v6.'">'.($v2).'</span>';
				$x.='<span class="s2 rt">'.number_format($v4,2).'</span>';
				$x.='<span class="s3 rt">'.number_format($v5,2).'</span>';
				$x.='<span class="s1 rt">'.number_format($v3).'</span>';
				$x.='<span class="s3 rt">'.number_format($v4*$v3,2).'</span>';
				$x.='<span class="s3 rt">'.number_format($v5*$v3,2).'</span></li>';
			}
		}$_SESSION['payamount']=$shopamt;
	}
	$x.='<li class="ct smaller">*** NOTHING FOLLOWS ***</li>';
	$x.='<li>';
	$x.='<span class="s1"></span><span class="s6">Approx Weight (Kg): '.number_format($shopwt,2).'</span>';
	$x.='<strong class="s2 rt"></strong>';
	$x.='<span class="s3 rt">TOTAL:</span>';
	$x.='<strong class="s1 rt" title="'.number_format($shopqty,2).'">'.number_format($shopqty).'</strong>';
	$x.='<strong class="s3 rt" title="'.number_format($shopppv,2).'">'.number_format($shopppv,2).'</strong>';
	$x.='<strong class="s3 rt" title="'.number_format($shopamt,2).'">'.number_format($shopamt,2).'</strong>';
	$x.='</li>';
	$x.='</ul></div>';
	$x.='</li>';
	return $x;
}
?>
