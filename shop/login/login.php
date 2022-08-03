<?php
if (!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}
define('INCLUDE_CHECK', 1);

if( !empty($_POST) ) {
    $un = $_POST['un'];
    $pw = $_POST['pw'];

    $endpoint = "/login?dsdid=$un&pass=$pw";

    require_once('../inc/setup.php');
    require_once('../assets/init_api.php');

    ob_start();

    if( !empty($response) ) {
        $data = $response['data'][0];

        $_SESSION['login']['dsdid'] = $data['dsdid'];
        $_SESSION['login']['name'] = $data['name'];
        $_SESSION['login']['dssid'] = $data['dssid'];
        $_SESSION['login']['sponsor'] = $data['sponsor'];
        $_SESSION['login']['dsbrth'] = $data['dsbrth'];
        $_SESSION['login']['dssetd'] = $data['dssetd'];

        header('Location:' . SHOP_URL );

    } else {
        $_SESSION['bad_login'] = true;
        $_SESSION['login']['dsdid'] = $un;

        header('Location:' . SHOP_URL . '/login');
    }

    ob_end_flush();

}

?>