<?php if(!defined('INCLUDE_CHECK')) die('Invalid Operation');
if(!isset($_SESSION)) session_start();
$msg='';$tbl='tbl'.$content;
$_SESSION['dbprod']=true;
$filter_str=(isset($_GET['filter'])?$_GET['filter']:null);
$filter_add=array('stock','promo','slp','feat','status','no_filter');
$filter_cat=getFilters()+count($filter_add);
$filter=(strlen($filter_str)==$filter_cat)?$filter_str:str_pad('',getFilters(),'1').'000001';
$psearch=isset($_SESSION['prodsearch'])?$_SESSION['prodsearch']:null;
$con=SQLi('products');
if($do==0){
	$d=0;
	$_SESSION['filter_array']=array();
	$rs=mysqli_query($con,"SELECT DISTINCT parent_id FROM tblcategories WHERE status=1") or die(mysqli_error($con));
	$msg.='<div><ul id="filter" class="ct"><li class="s4">FILTER "<strong>Category</strong>"</li>';
	while($rw=mysqli_fetch_assoc($rs)){
		$pid=$rw['parent_id'];
			if(substr($filter,$d++,1)==1){
				$msg.='<li><input type="checkbox" '.C_K.' />'.$pid.'</li>';
				$_SESSION['filter_array'][]=$pid;
			}else{
				$msg.='<li><input type="checkbox" />'.$pid.'</li>';
			}
	}
	$msg.=DIV_CLEAR.'</ul></div>';
	$msg.='<div><ul id="filter" class="ct"><li class="s4">FILTER "<strong>Status</strong>"</li>';
	foreach($filter_add as $v){
		$x=substr($filter,$d++,1);
		$msg.='<li><input type="checkbox" '.($x>0?C_K:'').' value='.$x.' />'.$v.'</li>';
		$_SESSION['filter_array'][]=$x;
	}
	$msg.=DIV_CLEAR.'</ul></div>';
	$msg.='<div id="search">Search Product: <input type="text" class="s5 txt" value="'.$psearch.'" />'.DIV_CLEAR.'</div>';
	echo $msg;
	include '../updateproducts.php';
	$msg.=$dat;
}else{
	if($do==2){
		$rs=mysqli_query($con,"SELECT * FROM $tbl WHERE id='$item'") or die(mysqli_error($con));
		while($rw=mysqli_fetch_assoc($rs)){
			$id=$rw['id'];
			$msg.='<li><label>Product ID:</label><input type="text" name="id" '.READONLY.' value="'.$id.'" /></li>';
			$msg.='<li><label>Name:</label><span>'.getPName($id).'</span></li>';
			$msg.='<li><label>Category:</label><select name="cat"><option value="" '.SELECTED.'>None</option>'.populateCat('tblnavigation','short','navi','*','WHERE parent_id=004',$rw['cat']).'</select></li>';
			$msg.='<li><label>Sub-Category:</label><select name="subcat"><option value="" '.SELECTED.'>None</option>'.populateCat('tblcategories','cat','cat','*','ORDER BY sort_order',$rw['subcat'],'parent_id','products').'</select></li>';
$msg.='<li><input type="hidden" name="pfda" class="txt" value="" /><input type="hidden" name="fdalink" class="txt" value="" /></li>';																											  
			$msg.='<li><label>Package Size:</label><input type="text" name="size" class="txt" value="'.$rw['size'].'" /></li>';
			$msg.='<li><label>Contains:</label><textarea name="contains">'.$rw['contains'].'</textarea></li>';
			$msg.='<li><label>Description:</label><div class="editor"><textarea name="description" id="panel">'.$rw['description'].'</textarea></div></li>';
			$msg.='<li><label>Order:</label><input type="text" name="sort_order" class="txt" value="'.$rw['sort_order'].'" /></li>';
			$msg.='<li><label>Image:</label><img src="/images/products/'.$rw['img'].'" /><input type="file" name="file_img" /><input type="text" name="img" value="'.$rw['img'].'" '.READONLY.' /> <span class="removepic">remove</span></li>';
			$msg.='<li><label>Stock Status:</label><input type="hidden" name="stock" value=0 /><input type="checkbox" name="stock" value=1 '.($rw['stock']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><label>Promo:</label><input type="hidden" name="promo" value=0 /><input type="checkbox" name="promo" value=1 '.($rw['promo']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><label>Sun Life:</label><input type="hidden" name="slp" value=0 /><input type="checkbox" name="slp" value=1 '.($rw['slp']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><label>Featured:</label><input type="hidden" name="feat" value=0 /><input type="checkbox" name="feat" value=1 '.($rw['feat']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><label>Enabled:</label><input type="hidden" name="status" value=0 /><input type="checkbox" name="status" value=1 '.($rw['status']?C_K:'').' class="rdo" /></li>';
			$msg.='<li><input type="hidden" name="do" value="'.$do.','.$id.'" /></li>';
		}
	}
	$msg='<form method="post" enctype="multipart/form-data" action="/admin/update.php?t='.$content.'"><ul>'.$msg;
	$msg.='<input type="submit" name="submit" class="btn" value="Submit" /></ul></form>';
	echo $msg;
}

function getFilters(){
	$con=SQLi('products');
	$rs=mysqli_query($con,"SELECT DISTINCT parent_id FROM tblcategories WHERE status=1") or die(mysqli_error($con));
	return mysqli_num_rows($rs);
}
?>
