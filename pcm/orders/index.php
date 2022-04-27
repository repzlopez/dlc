<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK',1);
require('../../admin/setup.php');
require('../func.php');
if(!ISIN_PCM){ reloadTo(DLC_PCRT);exit; }
$_SESSION['pcm_last']=DLC_PCRT;
$title='PCM | Orders';
$content='orders';
ob_start();
include('../head.php');
echo loadFilters();
include('../foot.php');
ob_end_flush();

function loadFilters(){
	$head='<ul class="list clear"><li class="hdr nobg"><span class="s1">Code</span><span class="s6">Product Name</span><span class="s2 rt">WSP</span><span class="s2 rt">PV</span><span class="s1 rt">Qty</span><span class="s2 rt">Amount</span><span class="s2 rt">PV</span></li></ul>';
	$ad='ending';
	$arrSort=array(
		'Default'=>'def|asc',
		'Code Asc'.$ad=>'id|asc',
		'Code Desc'.$ad=>'id|desc',
		'Product Name Asc'.$ad=>'name|asc',
		'Product Name Desc'.$ad=>'name|desc',
		'WSP Asc'.$ad=>'wsp|asc',
		'WSP Desc'.$ad=>'wsp|desc',
		'PV Asc'.$ad=>'pv|asc',
		'PV Desc'.$ad=>'pv|desc'
	);

	$d=0;
	$con=SQLi('products');
	$gcordata=(isset($_SESSION['for_edit'])&&$_SESSION['for_edit']&&isset($_SESSION['center_orders_data']))?$_SESSION['center_orders_data']:'';
	$srch=isset($_SESSION['prodsearch'])?$_SESSION['prodsearch']:null;
	$sort_str=isset($_SESSION['prodsort'])?$_SESSION['prodsort']:null;
	$data='<div><ul id="filter" class="ct"><li>FILTER BY</li>';
	$fltr=isset($_SESSION['prodfilter'])?$_SESSION['prodfilter']:str_pad('',getFilters()+1,'1');
	$rs=mysqli_query($con,"SELECT DISTINCT parent_id FROM tblcategories WHERE status=1") or die(mysqli_error($con));
	while($rw=mysqli_fetch_assoc($rs)){
		$pid=$rw['parent_id'];
		$pid=($pid=='')?'Uncategorized':ucwords(strtolower($pid));
		$c_y=(substr($fltr,$d++,1)==1)?C_K:'';
		$data.='<li><label><input type="checkbox" '.$c_y.' />'.$pid.'</label></li>';
	}mysqli_close($con);

	$data.='<li><label><input id="checkall" type="checkbox" '.C_K.' />All</label></li>';
	$data.='</ul></div>'.DIV_CLEAR;
	$data.='<div id="search">Sort By: <select class="s5">'.generate_options($arrSort,$sort_str).'</select>';
	$data.='<span class="s3 rt">Search Product:</span> <input type="text" class="s5 txt" value="'.$srch.'" /></div>'.DIV_CLEAR;
	$data.=$head;
	$data.='<div id="center_orders"><strong class="blue">ORDERS</strong> (<span class="ital">Ordered <strong id="num_order" class="blue"></strong> products</span>)<ul class="list clear">';
	$data.='<input type="hidden" id="editorder" value="'.$gcordata.'" />';
	$data.='</ul></div>';
	$data.='<div id="center_products"><strong class="blue">AVAILABLE PRODUCTS</strong> (<span class="ital">Found <strong id="pcm_found" class="blue"></strong> products</span>)<ul class="list clear">';
	$data.='</ul></div>';
	return $data;
}

function getFilters(){
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT DISTINCT parent_id FROM tblcategories WHERE status=1") or die(mysqli_error($con));
	return mysqli_num_rows($rs);
}

function generate_options($arr,$select){
	$return_string=array();
	foreach($arr as $key=>$val){
		$return_string[]='<option value="'.$val.'" '.(($val==$select)?SELECTED:'').'>'.$key.'</option>';
	} return join($return_string);
}
?>
