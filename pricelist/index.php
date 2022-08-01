<?php
if(!isset($_SESSION)) {
    session_set_cookie_params(0);
    session_start();
}
defined('INCLUDE_CHECK') or define('INCLUDE_CHECK', 1);
require_once('../admin/setup.php');

$title= 'DLC Pricelist';
$con  = SQLi('products');
$data = getList();
$lic  = isset( $_SESSION['listcount'] ) ? $_SESSION['listcount'] :0;
$num  = ( $lic>0 ? $lic :0 );

ob_start();

$ret = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>DLC Pricelist</title>
<meta http-equiv="Content-Language" content="en" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="shortcut icon" href="/src/favicon.ico"></link>
<link rel="stylesheet" type="text/css" href="styles.css"></link>
</head><body>';

$ret .= '<div id="container">' . $data . '</div></body></html>';
$ret .= '<style type="text/css">.loading{display:none}</style>';

ob_start();
echo $ret;

ob_end_flush();

unset($_SESSION['listcount']);

function getList() {
    $override= isset($_GET['lahat']) ? 1 : 0;
    $show_dp = ISIN_ADMIN || ISIN_DISTRI || ISIN_GOS || ISIN_PCM || $override;
    $nt = 0;//isset($_GET['nt']) ? 1 : 0;

    $x  = '<ul class="print"><li><a href="javascript:window.print()"></a></li></ul>';
    $x .= '<ul id="pricelist" class="list clear">';
    $x .= '<li class="ct"><h2>DLC PRICELIST</h2></li>';

    $qry = "SELECT l.*,p.cat
        FROM tbllist l
        LEFT JOIN tblproducts p ON p.id=l.id
        WHERE l.id >= 10000
        AND l.id NOT BETWEEN 60000 AND 70000
        AND l.status=1
        AND l.wsp>0
        AND p.cat<>'productaids'
        AND (p.cat<>'others' OR (p.cat='others' AND p.subcat='Product Packages'))
        ORDER BY CASE
            WHEN p.cat = 'health' THEN 1
            WHEN p.cat = 'personal' THEN 2
            WHEN p.cat = 'skincare' THEN 3
            WHEN p.cat = 'household' THEN 4
            WHEN p.cat = 'others' THEN 5
            WHEN p.cat = '' THEN 6
            ELSE 9
            END,
        sort_order,id";

    $old = '';
    $ntp = 1.7;
    $con = SQLi('products');
	$rs  = $con->query($qry) or die(mysqli_error($con));
	$num = $rs->num_rows;

	$_SESSION['listcount'] = $num;

    if( $num > 0 ) {
		while( $r = $rs->fetch_array() ) {
            $new = $r['cat'];

            if( $old != $new ) {
                $x .= '<li class="hdr ct"><br><h4>' . ($new!='' ? strtoupper(str_replace('others', 'packages',$new))  : 'OTHER PRODUCTS') . '</h4></li>';
                $x .= '<li class="hdr">';
                $x .= '<strong class="s2">Item</strong>';
                $x .= '<strong class="s7">Description</strong>';
                $x .= '<strong class="s2 rt">' . ($show_dp ? 'PV' : '') . '</strong>';
                $x .= '<strong class="s2 rt">' . ($show_dp ? 'WSP ' . ($nt ? '($ NT)' : '(Php)') : '') . '</strong>';
                $x .= '<strong class="s2 rt">' . ($show_dp ? 'PMP ' . ($nt ? '($ NT)' : '(Php)') : '') . '</strong>';
                $x .= '<strong class="s2 rt">SRP ' . ($nt ? '($ NT)' : '(Php)') . '</strong>';
                $x .= '</li>';
        }

            if( $nt ) {
                $wsp = number_format($r['wsp'] / $ntp, 2);
                $pmp = number_format($r['pmp'] / $ntp, 2);
                $srp = number_format($r['srp'] / $ntp, 2);

            } else {
                $wsp = number_format($r['wsp']);
                $pmp = number_format($r['pmp']);
                $srp = number_format($r['srp']);
            }

			$x .= '<li>';
            $x .= '<span class="s2">' . $r['id'] . '</span>';
			$x .= '<span class="s7">'. utf8_encode($r['name']) .'</span>';
            $x .= '<span class="s2 rt">'. ( $show_dp ? number_format($r['pv'], 2, '.', '') :'') . '</span>';
            $x .= '<span class="s2 rt">' . ($show_dp ? $wsp : '') . '</span>';
            $x .= '<span class="s2 rt">' . ($show_dp ? $pmp : '') . '</span>';
			$x .= '<span class="s2 rt">'. $srp .'</span>';
			$x .= '</li>';

            $old = $new;
		}
	}

	$x .= '</ul>';

	return $x;
}

?>