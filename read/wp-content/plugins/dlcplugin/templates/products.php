<?php
if( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}

global $dlcuser;
$pid = $dlcuser['pid'];
$shortlink = wp_cache_get( 'shortlink' );

$qry = "SELECT *,l.id lid
     FROM tbllist l
     LEFT JOIN tblproducts p ON p.id=l.id
     LEFT JOIN tblfda f ON f.id=l.id
     WHERE l.id=$pid
     AND l.status=1
";
$con = SQLi('products');
$rs = mysqli_query($con,$qry) or die(mysqli_error($con));
if( mysqli_num_rows($rs)<1 ) echo '<h4>ITEM NOT FOUND</h4>';

$rw       = mysqli_fetch_array($rs);
$name     = utf8_encode($rw['name']);
$reflink  = ISIN_DISTRI?' <a class="reflink btn smaller" id="download" rel="'.DLC_ROOT.'/read/'.$shortlink.'lifestyle-shop/?rfr='.DIST_ID.'&pid='.$pid.'">Copy Referral Link</a>':'';

$fdalink  = $rw['url'];
$fdacert  = $fdalink?'<br><a href="'.$fdalink.'" target="_blank" class="rt fda" id="download">FDA Certificate</a>':'';

$path     = '/images/products/';
$root     = $_SERVER['DOCUMENT_ROOT'];
$img      = DLC_ROOT.$path.(file_exists($_SERVER['DOCUMENT_ROOT'].$path.$rw['img'])?$rw['img']:"default_product.jpg");

$wp_name  = ($rw['name']!='')?'<h3>'.utf8_encode($rw['name']).'</h3>':'';
$wp_id    = ($rw['lid']!='')?'<p><span>Product Code:</span> '.sprintf("%05d",$rw['lid']).'-'.sprintf("%05d",$rw['wsp']).'-'.sprintf("%05d",$rw['pov']).'</p>':'';
$wp_srp   = ($rw['srp']!=''&&SRP_ON)?'<p><span>SRP:</span> '.number_format((double)$rw['srp']).'</p>':'';
$wp_pv    = ($rw['pv']!='')?'<p><span>PV:</span> '.number_format($rw['pv'],2).'</p>':'';
$wp_fda   = ($rw['fda']!='')?'<p><span>PFDA:</span> '.$rw['fda'].$fdacert.'</p>':'';
$wp_renewal = ($rw['renewal'])?'<p><span>PFDA Status:</span> Renewal under process</p>':'';
$wp_size  = ($rw['size']!='')?'<p><span>Package:</span> '.$rw['size'].'</p>':'';
$wp_contains = ($rw['contains']!='')?'<p><span>Contains:</span> '.$rw['contains'].'</p>':'';
$wp_desc  = ($rw['description']!='<br>'&&$rw['description']!='')?'<p><span>Description:</span> '.$rw['description'].'</p>':'';

$kart=( ( (CART_ON&&ISIN_DISTRI) || GUEST ) && $rw['stock']==1 )?'<input type="button" class="addtocart" title="Add to Cart" alt="Add to Cart" data-id="'.$pid.'" />':'';

$icon = (!$rw['stock']) ? 'nostock': ( ($rw['slp']) ? 'slp' : ( ($rw['promo']) ? 'promo' : '') );
$icon = $icon!='' ? '<img src="/src/'.$icon.'.png" class="nostock" alt="" />' :'';
?>
<div id="product_main">

<!-- wp:media-text {"mediaPosition":"right","mediaId":391,"mediaLink":"http://local.dlc/read/?attachment_id=391","mediaType":"image"} -->
<div class="wp-block-media-text alignwide has-media-on-the-right is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="<?php echo $img; ?>" alt=""/><?php echo $icon.$kart.$reflink; ?></figure><div class="wp-block-media-text__content"><!-- wp:heading {"level":3} -->
     <!-- wp:paragraph -->
     <p><?php echo $wp_name; ?></p>
     <!-- /wp:paragraph -->

     <!-- wp:paragraph -->
     <p><?php echo $wp_id; ?></p>
     <!-- /wp:paragraph -->

     <!-- wp:paragraph -->
     <p><?php echo $wp_srp; ?></p>
     <!-- /wp:paragraph -->

     <!-- wp:paragraph -->
     <p><?php echo $wp_pv; ?></p>
     <!-- /wp:paragraph -->

     <!-- wp:paragraph -->
     <p><?php echo $wp_fda; ?></p>
     <!-- /wp:paragraph -->

     <!-- wp:paragraph -->
     <p><?php echo $wp_renewal; ?></p>
     <!-- /wp:paragraph -->

     <!-- wp:paragraph -->
     <p><?php echo $wp_size; ?></p>
     <!-- /wp:paragraph -->

</div></div>
<!-- /wp:media-text -->

<div>
     <!-- wp:paragraph -->
     <p><?php echo $wp_contains; ?></p>
     <!-- /wp:paragraph -->

     <!-- wp:paragraph -->
     <p><?php echo $wp_desc; ?></p>
     <!-- /wp:paragraph -->
</div>

</div><!-- close -->
