<?php
$url = (strpos($_SERVER['SERVER_NAME'], 'local') !== false ) ? '/shop' : 'https://' . $_SERVER['SERVER_NAME'];

define('SHOP_URL', $url);
define('SHOP_FULL', 'DLC Lifestyle Shop');
define('BAK_ROOT', 'https://dlcph.com');

define('SHOP_PHONE', '+63 2 7729 3929');
define('SHOP_GLOBE', '+63 915 170 7388');
define('SHOP_SMART', '+63 947 692 8060');
define('SHOP_EMAIL', 'talktous@dlclifestyle.shop');

define('SHOP_ADDRESS', 'Unit 2B, ABD Building, 64 Kamuning Road, Quezon City, Philippines');
define('SHOP_MAP', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d965.1195911768874!2d121.0394224498065!3d14.628765840732795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b6142583589f%3A0x81f836a84ea81be4!2sDLC+Philippines+Shareconomy!5e0!3m2!1sen!2sph!4v1552206896495" width="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>');

define('PRELOAD', $_SERVER['REQUEST_URI']=='/' ? '<link rel="preload" href="./assets/images/hero-banner.png" as="image">' :'');
define('URI', substr(stristr($_SERVER['REQUEST_URI'], '?'), 1));

if( URI == 'logout' ) {
    if(isset($_COOKIE[session_name()])) setcookie(session_name(),'',time()-3600,'/');
    $_SESSION = array();

    if(ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),'',time() - 42000,
            $params["path"],$params["domain"],
            $params["secure"],$params["httponly"]
        );
    }

    session_destroy();
    header('Location:' . SHOP_URL );
}

?>