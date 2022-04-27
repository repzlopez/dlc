<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
require_once( '../distrilog/cart/cartdata.php' );

if( isset($_SESSION['shoplist']) ) {
     echo getOrders();
     echo '<input type="button" id="clearcart" class="btn" value="Cancel" /> <input type="button" rel="/distrilog/mycart.php" class="link" value="Checkout" />';
} else echo '<h4>Oops! You forgot your order!</h4>';
?>
