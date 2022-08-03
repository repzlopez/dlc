<?php

$logout = '';

if (isset($_SESSION['login'])) {
    $logout = '
    <li>
        <a href="?logout" class="footer-link">
            <ion-icon name="chevron-forward-outline"></ion-icon>

            <span class="footer-link-text">Logout (' . ucwords(strtolower($_SESSION['login']['name'])) . ')</span>
        </a>
    </li>';
}
?>

<footer id="footer" class="footer">

    <div class="footer-top section">
        <div class="container">

            <div class="footer-link-box">

                <ul class="footer-list">

                    <li>
                        <p class="footer-list-title">Account</p>
                    </li>

                    <li>
                        <a href="login" class="footer-link">
                            <ion-icon name="chevron-forward-outline"></ion-icon>

                            <span class="footer-link-text">My Account</span>
                        </a>
                    </li>

                    <li>
                        <a href="#" class="footer-link">
                            <ion-icon name="chevron-forward-outline"></ion-icon>

                            <span class="footer-link-text">View Cart</span>
                        </a>
                    </li>

                    <?php echo $logout; ?>
                </ul>

                <ul class="footer-list">

                    <li>
                        <p class="footer-list-title">Contact Us</p>
                    </li>

                    <li>
                        <a href="tel:<?php echo SHOP_PHONE; ?>" class="footer-link">
                            <ion-icon name="call"></ion-icon>

                            <span class="footer-link-text"><?php echo SHOP_PHONE; ?></span>
                        </a>
                    </li>

                    <li>
                        <a href="tel:<?php echo SHOP_GLOBE; ?>" class="footer-link">
                            <ion-icon name="phone-portrait-sharp"></ion-icon>

                            <span class="footer-link-text"><?php echo SHOP_GLOBE; ?></span>
                        </a>
                    </li>

                    <li>
                        <a href="tel:<?php echo SHOP_SMART; ?>" class="footer-link">
                            <ion-icon name="phone-portrait-sharp"></ion-icon>

                            <span class="footer-link-text"><?php echo SHOP_SMART; ?></span>
                        </a>
                    </li>

                </ul>

                <div class="footer-list">

                    <p class="footer-list-title">Store Hours</p>

                    <table class="footer-table">
                        <tbody>

                            <tr class="table-row">
                                <th class="table-head" scope="row">Mon - Sat:</th>

                                <td class="table-data">10AM - 6PM</td>
                            </tr>

                            <tr class="table-row">
                                <th class="table-head" scope="row">Sun:</th>

                                <td class="table-data">Closed</td>
                            </tr>

                        </tbody>
                    </table>

                </div>

                <ul class="footer-list">

                    <li>
                        <p class="footer-list-title">Map</p>
                    </li>

                    <li>
                        <address class="footer-link">
                            <span class="footer-link-text">
                                <?php echo SHOP_MAP; ?>
                            </span>
                        </address>
                    </li>

                </ul>

                <!-- <div class="footer-list">

                    <p class="footer-list-title">Newsletter</p>

                    <p class="newsletter-text">
                        Authoritatively morph 24/7 potentialities with error-free partnerships.
                    </p>

                    <form action="" class="newsletter-form">
                        <input type="email" name="email" required placeholder="Email Address" class="newsletter-input">

                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>

                </div> -->

            </div>

        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">

            <p class="copyright">
                &copy; 2009-<?php echo date('Y'); ?> <a href="<?php echo SHOP_URL; ?>" class="copyright-link"><?php echo SHOP_FULL; ?></a>. All Rights Reserved
            </p>

        </div>
    </div>

</footer>


<!-- 
  - #GO TO TOP
-->

<a href="#top" class="go-top-btn" data-go-top>
    <ion-icon name="arrow-up-outline"></ion-icon>
</a>