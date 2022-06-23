<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
require_once('admin/setup.php');
include_once 'admin/getwebstat.php';

function getShoplist($shoplist) { 
	$shopppv= $shopamt= 0;

	foreach($shoplist as $v) { 
		$v0 = $v[0];
		$v2 = utf8_encode($v[2]);
		$v3 = $v[3];
		$v4 = $v[4];
		$v5 = GUEST&&!OLREG_REF?$v[8]:$v[5];
		$v6 = $v[6];

		$shopppv += $v4*$v3;
		$shopamt += $v5*$v3;

		$morles = '<input type="button" class="qtydn" title="Less" /><span rel="'.$v0.'">'.number_format($v3).'</span><input type="button" class="qtyup" title="More" />';

		if($v3>0) { 
			echo '<li><span class="link" href="'.$v6.'"><a href="'.$v6.'" title="'.$v2.'" target="_blank">'.$v2.'</a></span>';
			echo '<span class="s2 shopqty" rel="'.$v4.'" title="'.$v3.'">'.$morles.'</span>';
			echo '<span class="s2 shopamt rt" rel="'.$v5.'" title="'.number_format($v5*$v3,2).'">'.number_format($v5*$v3,2).'</span></li>';
		}
	}
}
?>
