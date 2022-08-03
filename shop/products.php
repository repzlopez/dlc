<?php
if (!isset($_SESSION)) {
  session_set_cookie_params(0);
  session_start();
}

define('INCLUDE_CHECK', 1);
require('../admin/setup.php');

require('inc/setup.php');
require('inc/head.php');
require('inc/header.php');

$x = $pcat = $addcat = '';
$data = array();

if( isset($_GET['p']) ) {
  $pcat = $_GET['p'];
  $addcat = '?cat=' . $pcat;
}

$file = 'inc/productlist.php';
$endpoint = "/products/list/$addcat";

if( isset($_GET['item']) ) {
  $file = 'inc/product.php';
  $item = $_GET['item'];

  $endpoint = "/products/$item";
}

require_once('assets/init_api.php');

if (!empty($response)) {
  if (isset($_GET['item'])) {
    $data = $response['data'][0];

  } else {
    $data = $response['data'];
  }

  require_once($file);
}

?>


<section class="section product">
  <div class="container">

    <h2 class="h2 section-title">Lifestyle Products</h2>

    <ul class="filter-list">

      <li>
        <a href="products.php"><button class="filter-btn <?php echo ($pcat == '' ? 'active' : ''); ?>">All</button></a>
      </li>

      <li>
        <a href="?p=health"><button class="filter-btn <?php echo ($pcat == 'health' ? 'active' : ''); ?>">Health</button></a>
      </li>

      <li>
        <a href="?p=personal"><button class="filter-btn <?php echo ($pcat == 'personal' ? 'active' : ''); ?>">Personal Care</button></a>
      </li>

      <li>
        <a href="?p=skincare"><button class="filter-btn <?php echo ($pcat == 'skincare' ? 'active' : ''); ?>">Skin Care</button></a>
      </li>

      <li>
        <a href="?p=household"><button class="filter-btn <?php echo ($pcat == 'household' ? 'active' : ''); ?>">Household</button></a>
      </li>

      <li>
        <a href="?p=packages"><button class="filter-btn <?php echo ($pcat == 'packages' ? 'active' : ''); ?>">Packages</button></a>
      </li>

    </ul>

    <ul class="product-list">

      <?php echo $x; ?>

    </ul>

  </div>
</section>


<?php
require('inc/footer.php');
require('inc/foot.php');
?>