<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
define('INCLUDE_CHECK', 1);

$shoplist	= (isset($_SESSION['shoplist'])) ? $_SESSION['shoplist'] :NULL;
$search_on	= (isset($_POST['find'])) ? $_POST['find'] :'';
$pcat		= (isset($_POST['p'])) ? $_POST['p'] :'';
$wp_call	= (isset($_POST['wp'])) ? $_POST['wp'] :'';
$cid		= (isset($cid)) ? $cid :'';
$data		= '';

$prodcat    = ($cid=='categories') ? '': $cid;
$prodcat    = ($pcat == '') ? $prodcat : $pcat;
$prodcat    = ($pcat == 'packages') ? 'others' : $pcat;
$showaids   = ($cid=='productaids') ? '': "AND p.cat!='productaids'";
$searchstring = ($search_on!='') ? " AND l.name LIKE '%$search_on%'" :'';

$packages   = ($prodcat == 'others') ? ($pcat == 'packages' ? "AND subcat='Product Packages'" : "AND subcat!='Product Packages'") :'';

require('admin/setup.php');

$qry = "SELECT *
		FROM tblproducts p
		LEFT JOIN tbllist l
			ON p.id=l.id
		WHERE p.cat!=''
		AND p.cat LIKE '%$prodcat%'
		$packages
		$showaids
		$searchstring
		AND l.status=1
		AND p.status=1
		ORDER BY sort_order,l.id";

$con = SQLi('products');
$rs  = $con->query($qry) or die(mysqli_error($con));
while( $rw = $rs->fetch_assoc() ) {
	$v3 = 0;
	if( isset($shoplist) && is_array($shoplist) ) {
		foreach($shoplist as $value) {
			if( $rw['id']==$value[0] ) {
				$v3 = number_format($value[3]);
			}
		}
	}

	$path = 'images/products/';
	$icon = (!$rw['stock']) ? 'nostock' : (($rw['slp']) ? 'slp' : (($rw['promo']) ? 'promo' :'') );

	if( file_exists( $path . $rw['img'] ) ) {
		$previmg = $path . $rw['img'];

	} else
		$previmg = $path . 'default_product.jpg';

	if( $wp_call ) {
		$ico   = ( $icon!='' ? '<img src="/src/'. $icon .'.png" class="nostock nostockprev" alt="" />' :'');
		$img   = '<img src="'. "/$previmg" . '" alt="' . (SRP_ON?$rw['srp']:'') .'"/>';
		$title = $rw['id'] .'-'. sprintf("%05d", $rw['wsp']) .'-'. sprintf("%05d", $rw['pov']);
		$nam   = '<p>'. utf8_encode($rw['name']) .'</p>';
		$srp   = SRP_ON ? '<label>P '. number_format($rw['srp'], 0, '', ',') .'</label>' :'';
		$add   = ( ( ( CART_ON && ISIN_DISTRI ) || GUEST ) && $rw['stock']==1 ) ? '<input type="button" class="addtocart" data-id="'. $rw['id'] .'" title="Add to Cart" value="" />' :'';

		$wp_path = '/read/'. $shortlink .'lifestyle-shop/?pid='. $rw['id'];
		$data .= '<li>'. $add .'<a href="'. $wp_path .'" title="'. $title .'" data-cat="'. $rw['cat'] .'" data-id="'. $rw['id'] .'" class="prev">'. $img . $ico . $nam . $srp .'</a></li>';

	} else {
		$morles = '<input type="button" class="qtydn" title="Less" /><input type="text" rel="'. $rw['id'] .'" maxlength=3 value="'. $v3 .'" '. DISABLED .' /><input type="button" class="qtyup" title="More" />';
/*cart*/	$shop = ( ( (CART_ON && ISIN_DISTRI) || GUEST ) && $rw['stock']==1 ) ? '<div id="shop">'. $morles .'</div>' :'';
		$data .= '<li>'. ($icon!='' ? '<img src="src/'. $icon .'.png" class="nostock nostockprev" alt="" />' :'') . $shop .'<a href="products.php?p='. $rw['cat'] .'&pid='. $rw['id'] .'" class="prev"><img src="'. $previmg .'" alt="'. (SRP_ON ? $rw['srp'] :'') .'"/><p>'. utf8_encode($rw['name']) .'</p></a></li>';
	}
}

echo $data;
?>
