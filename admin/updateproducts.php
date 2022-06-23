<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
$find=(isset($_POST['find']))?$_POST['find']:(isset($_SESSION['prodsearch'])?$_SESSION['prodsearch']:'');
$search_in=(!is_numeric($find))?'l.name':'l.id';
$o='';$dat='';$fltr=$_SESSION['filter_array'];$ctr=count($fltr);
$withsearch=($find!==false?"$search_in LIKE '%$find%' AND ":'');
$withfilter=($fltr[$ctr-1]>0)?'':"
		p.stock=".sprintf('%01d',$fltr[$ctr-6])."
	AND p.promo=".sprintf('%01d',$fltr[$ctr-5])."
	AND p.slp=".sprintf('%01d',$fltr[$ctr-4])."
	AND p.feat=".sprintf('%01d',$fltr[$ctr-3])."
	AND p.status=".sprintf('%01d',$fltr[$ctr-2])." AND ";
$qry="SELECT p.*,f.url,l.name,l.wsp,l.pov
	FROM tblproducts p
	LEFT JOIN tbllist l
		ON p.id=l.id
	LEFT JOIN tblfda f
		ON f.id=l.id
	WHERE $withsearch
	$withfilter
	l.status=1
	ORDER BY sort_order,p.id";
$_SESSION['prodsearch']=$find;
$dbsrc='products';
include('infoconfig.php');
$active_products=0;
$r1=mysqli_query($con,"SHOW TABLES LIKE 'tblproducts'") or die(mysqli_error($con));
$r2=mysqli_query($con,"SHOW TABLES LIKE 'tbllist'") or die(mysqli_error($con));
if(mysqli_num_rows($r1)>0&&mysqli_num_rows($r2)>0) {
	$rs=mysqli_query($con,$qry) or die(mysqli_error());
	while($rw=mysqli_fetch_assoc($rs)) {
		if(in_array($rw['cat'],$_SESSION['filter_array'])) {
			$fdalink=$rw['url']!=''?1:0;
			$o.='<li rel="'.$rw['id'].'"><div class="s3"><img src="/images/products/'.$rw['img'].'" alt="'.$rw['name'].'" /></div>';
			$o.='<span class="s3"><a href="?p=products&do=2&i='.$rw['id'].'">'.utf8_encode($rw['name']).'</a></span>';
			$o.='<span class="s2">'.$rw['id'].' '.sprintf('%05d',$rw['wsp']).' '.sprintf('%05d',$rw['pov']).'</span>';
			$o.='<span class="s2">'.$rw['cat'].'</span>';
			$o.='<span class="s2">'.$fdalink.'</span>';
			$o.='<span class="s2">'.$rw['sort_order'].'</span>';
			$o.='<span class="s1 stok '.($rw['stock']?'':'bad').'">'.$rw['stock'].'</span>';
			$o.='<span class="s1">'.$rw['promo'].'</span>';
			$o.='<span class="s1">'.$rw['slp'].'</span>';
			$o.='<span class="s1">'.$rw['feat'].'</span>';
			$o.='<span class="s1 stat '.($rw['status']?'':'bad').'">'.$rw['status'].'</span></li>';
			$active_products++;
		}
	}
}
$dat.='<ul id="products" class="list clear">';
$dat.='<li>Found <strong class="blue">'.$active_products.'</strong> ACTIVE items</li>';
$dat.='<li class="hdr"><span class="s3"></span><span class="s3">Name</span><span class="s2"> Code</span><span class="s2">Category</span><span class="s2">FDA Link</span><span class="s2">Sort Order</span><span class="s1">Stock</span><span class="s1">Promo</span><span class="s1">Sun Life</span><span class="s1">Feat</span><span class="s1">Status</span></li>';
$dat.=$o.'</ul>';
echo $dat;
?>
