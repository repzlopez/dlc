<?php
if(!isset($_SESSION)) {
     session_set_cookie_params(0);
     session_start();
}
define(INCLUDE_CHECK, 1);

$un = null;

require_once('../admin/setup.php');
include_once '../admin/getwebstat.php';

$title = 'DLC API TEST';
$x = loadHead($title);

if( isset($_POST['id']) ) {
     $_SESSION['u_site'] = $_POST['id'];
}

if( $_SESSION['u_site'] != '' ) {
     $endpoint = '/distributors/' . $_SESSION['u_site'] ;
     require_once 'init_api.php';

     foreach($data as $k => $v) {
          $$k = $v;

          $y .= '<h3>'. $dsdid .'</h3>';
     }

     $x .= '<div id="container">';
     $x .= '<h3 class="lt">' . $dsdid . '</h3>';
     $x .= '<h2 class="lt">' . $dsfnam . ' ' . $dslnam . '</h2>';
     $x .= '<h5 class="lt">Sponsor: ' . $sid . ' ' . $sponsor . '</h5>';
     $x .= '<h5 class="lt">Birth Date: ' . $dsbrth . '</h5>';
     $x .= '<h5 class="lt">Setup Date: ' . $dssetd . '</h5>';
     $x .= '</div>';

} else {
     $x .= '<form method="POST"><ul>';
     $x .= '<li><input type="text" name="id" /> <input type="submit" /></li>';
     $x .= '</ul></form>';
}

$x .= '<style type="text/css">
     ul,input { padding:10px; }
     li { padding:5px; }

     #container { padding:10px; }
     .loading{display:none}
     </style>';

echo $x;
?>