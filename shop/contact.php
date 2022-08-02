<?php
require('inc/setup.php');
require('inc/head.php');
require('inc/header.php');

echo
'<div class="contact" style="max-width: 920px;margin: 0 auto;">
    <h2>'. SHOP_FULL . '</h2>
    <p>'. SHOP_ADDRESS . '</p>
    <br>

' . SHOP_MAP . '

    <br><br>
    <h4>Store Hours</h4>
    <p>Mon - Sat: 10am - 6pm</p>
    <p>Holidays: please contact the store to confirm schedule</p>

    <br><hr><br>
    <h4>Hotlines</h4>
    <p>Landline: '. SHOP_PHONE . '</p>
    <p>Globe: ' . SHOP_GLOBE . '</p>
    <p>Smart: ' . SHOP_SMART . '</p>

    <br><hr><br>
    <h4>Email</h4>
    <p>' . SHOP_EMAIL . '</p>
    <br><br>

</div>
';

require('inc/footer.php');
require('inc/foot.php');
?>