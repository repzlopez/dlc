<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}

if(isset($_SESSION['shoplist'])) {
     echo getOrders();
}

function getOrders() {
	$x ='<li><h4>Cart</h4></li>';
	$shopqty=0;$shopamt=0;$shopppv=0;$shopwt=0;

	if(isset($_SESSION['shoplist'])) {
		foreach($_SESSION['shoplist'] as $v) {
			$v1=$v[1];
			$v2=utf8_encode($v[2]);
			$v3=is_numeric($v[3])?$v[3]:0;
			$v4=is_numeric($v[4])?$v[4]:0;
			$v5=(is_numeric($v[5])?$v[5]:0);
               $v6=$v[6];
			$v7=$v[7];
			$shopqty+=$v3;
			$shopppv+=$v4*$v3;
			$shopamt+=$v5*$v3;
			$shopwt+=$v7*$v3;
			if($v3>0) {
				$x.='<li data-pid="'.$v[1].'">';
				$x.='<span class="s6 link" rel="'.$v6.'">'.($v2).'</span>';
				$x.='<span class="s0 ct">'.number_format($v3).'</span>';
				$x.='<span class="s3 rt">'.number_format($v5*$v3,2).'</span></li>';
			}
		}$_SESSION['payamount']=$shopamt;
	}
	$x.='<li>';
	$x.='<span class="s6">Total</span>';
	$x.='<strong class="s0 ct" title="'.number_format($shopqty,2).'">'.number_format($shopqty).'</strong>';
	$x.='<strong class="s3 rt" title="'.number_format($shopamt,2).'">'.number_format($shopamt,2).'</strong>';
     $x.='<hr>';

     $short=(strpos($_SERVER['SERVER_NAME'],'local')!==false?'http':'https').'://'.$_SERVER['SERVER_NAME'];
     $x.='<input type="button" id="clearcart" class="btn" value="Cancel" /> <input type="button" rel="'.$short.'/distrilog/mycart.php" class="link btn" value="Checkout" /></li>';
	return $x;
}
?>
