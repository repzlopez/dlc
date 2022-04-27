<?php
if ( !isset($_SESSION) ) {
     session_set_cookie_params(0);
     session_start();
}
$x= $z= '';
define( 'INCLUDE_CHECK', 1 );

$qry = "SELECT *,CONCAT(id,'-',LPAD(wsp,5,0),'-',LPAD(pov,5,0)) item_code
		FROM tbllist
          WHERE id>10000
          AND status=1
		ORDER BY id";

require_once( '../admin/setup.php' );

$con = SQLi('products');
$rs  = mysqli_query($con,$qry) or die(mysqli_error($con));

while ( $r = mysqli_fetch_assoc($rs) ) {
     foreach ($r as $k=>$v) $$k = $v;

     $z .= '<li><span class="s5">'.$item_code.'</span><span class="s6">'.$name.'</span><span class="s4 rt">'.number_format($srp,2).'</span></li>';
}

$x  = '<ul id="product_raw_list">';
$x .= '<li><span class="s5">Code</span><span class="s6">Product</span><span class="s4 rt">SRP</span></li>';
$x .= $z . '<ul>';

echo $x;
?>
