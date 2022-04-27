<?php
if (!isset($_SESSION)) {
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
$msg   = loadHead($title);
$msg  .= '<div id="container">' . $data . '</div>';

ob_start();
echo $msg;
echo '<style type="text/css">.loading{display:none}</style>';

ob_end_flush();

unset($_SESSION['listcount']);

function getList(){
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
	$rs  = mysqli_query($con, $qry) or die(mysqli_error($con));
	$num = mysqli_num_rows($rs);

	$_SESSION['listcount'] = $num;

    if( $num > 0 ) {
		while( $r = mysqli_fetch_array($rs,MYSQLI_BOTH) ) {
            $new = $r['cat'];

            if( $old != $new ) {
                $x .= '<li class="hdr ct"><h4>' . strtoupper(str_replace('others', 'packages',$new)) . '</h4></li>';
                $x .= '<li class="hdr">';
                $x .= '<strong class="s2">Item</strong>';
                // $x .= '<strong class="s2">Category</strong>';
                $x .= '<strong class="s7">Description</strong>';
                $x .= '<strong class="s3 rt">' . ($show_dp ? 'PV' : '') . '</strong>';
                $x .= '<strong class="s3 rt">' . ($show_dp ? 'WSP ' . ($nt ? '($ NT)' : '(Php)') : '') . '</strong>';
                $x .= '<strong class="s3 rt">SRP ' . ($nt ? '($ NT)' : '(Php)') . '</strong>';
                $x .= '</li>';
        }

            if( $nt ) {
                $wsp = number_format($r['wsp'] /$ntp, 2);
                $srp = number_format($r['srp'] /$ntp, 2);
            } else {
                $wsp = number_format($r['wsp']);
                $srp = number_format($r['srp']);
            }

			$x .= '<li>';
            $x .= '<span class="s2">' . $r['id'] . '</span>';
            // $x .= '<span class="s2">' . ucwords($new) . '</span>';
			$x .= '<span class="s7">'. utf8_encode($r['name']) .'</span>';
            $x .= '<span class="s3 rt">'. ( $show_dp ? number_format($r['pv'], 2, '.', '') :'') . '</span>';
			$x .= '<span class="s3 rt">'. ( $show_dp ? $wsp :'' ) .'</span>';
			$x .= '<span class="s3 rt">'. $srp .'</span>';
			// $x .= '<span class="s1">'. $r['status'] .'</span>';
			$x .= '</li>';

            $old = $new;
		}
	}

	$x .= '</ul>';

	return $x;
}

?>