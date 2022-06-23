<?php if(!isset($_SESSION)) session_start();
define('INCLUDE_CHECK',1);

require('../func.php');

if(!ISIN_DISTRI&&!GUEST) { reloadTo(DLC_ROOT);exit; }

$_SESSION['lastURI'] = 'welcome';
$page    = 'welcome';
$title   = ' | Welcome';
$reflink = REF_PROD!==null ? REF_PROD : '../products.php';

unset($_SESSION['payamount']);
unset($_SESSION['shoplist']);
unset($_SESSION['olreg_ref']);

$x  = '<div class="content guest"><br><div class="ct">';
$x .= '<iframe width="800" height="450" src="https://www.youtube.com/embed/XfecVverhHw?rel=0&autoplay=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
$x .= '</div><br><br><ul>';
$x .= '<li><a href="'.$reflink.'" class="ct">BUY PRODUCTS</a><p>Choose from our selection of natural products</p></li>';
// $x .= '<li><a href="#" class="ct">GET DISCOUNTS</a><p>Get 10% discount on all products</p></li>';
$x .= '<li><a href="../reg/package.php" class="ct">BE ONE OF US</a><p>Become one of our amazing distributors and turn your expenses into income</p></li>
</ul><br><br><br><br><br><br><br><br><br>
</div>';

$msg  = loadHead($title).loadLogo('');
$msg .= '<div id="container">'.$x;

ob_start();
echo $msg;
echo loadFoot('','','',1);
ob_end_flush();
?>
