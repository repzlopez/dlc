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

$shoplist   = (isset($_SESSION['shoplist'])) ? $_SESSION['shoplist'] : NULL;
$search_on  = (isset($_POST['find'])) ? $_POST['find'] : '';
$pcat       = (isset($_POST['p'])) ? $_POST['p'] : '';
$cid        = (isset($cid)) ? $cid : '';
$data       = '';

$prodcat    = ($cid == 'categories') ? '' : $cid;
$prodcat    = ($pcat == '') ? $prodcat : $pcat;
$prodcat    = ($pcat == 'packages') ? 'others' : $pcat;
$showaids   = ($cid == 'productaids') ? '' : "AND p.cat!='productaids'";
$searchstring = ($search_on != '') ? " AND l.name LIKE '%$search_on%'" : '';

$packages   = ($prodcat == 'others') ? ($pcat == 'packages' ? "AND subcat='Product Packages'" : "AND subcat!='Product Packages'") : '';

$qry = "SELECT *
  FROM tblproducts p
  LEFT JOIN tbllist l
    ON p.id=l.id
  WHERE p.cat!=''
  AND p.cat LIKE '%$prodcat%'
  $packages
  $showaids
  $searchstring
  AND l.status=1
  AND p.status=1
  ORDER BY cat,sort_order,l.id";

$x = '';

$con = SQLi('products');
$rs  = $con->query($qry) or die($con->mysqli_error);
while ($rw = $rs->fetch_assoc()) {
  foreach ($rw as $k => $v) $$k = $v;

  $x .= '<li class="product-item">
        <div class="product-card" tabindex="0">

          <figure class="card-banner">
            <img src="../images/products/' . $id . '.jpg" loading="lazy" alt="' . $name . '" class="image-contain">

            <div class="card-badge">' . $id . '</div>

            <ul class="card-action-list">

              <li class="card-action-item">
                <button class="card-action-btn" aria-labelledby="card-label-1">
                  <ion-icon name="cart-outline"></ion-icon>
                </button>

                <div class="card-action-tooltip" id="card-label-1">Add to Cart</div>
              </li>

              <li class="card-action-item">
                <button class="card-action-btn" aria-labelledby="card-label-3">
                  <ion-icon name="eye-outline"></ion-icon>
                </button>

                <div class="card-action-tooltip" id="card-label-3">Quick View</div>
              </li>

            </ul>
          </figure>

          <div class="card-content">

            <div class="card-cat">
              <span>' . $id . '-' . sprintf("%05d", $wsp) . '-' . sprintf("%05d", $pov) . '</span>
            </div>

            <h3 class="h3 card-title">
              <a href="#">' . $name . '</a>
            </h3>

            <data class="card-price" value="' . $srp . '">' . number_format($srp, 2) . '</data>

          </div>

        </div>
      </li>
  ';
}
?>


<section class="section product">
  <div class="container">

    <h2 class="h2 section-title">Lifestyle Products</h2>

    <ul class="filter-list">

      <li>
        <button class="filter-btn  active">All</button>
      </li>

      <li>
        <button class="filter-btn">Health</button>
      </li>

      <li>
        <button class="filter-btn">Personal Care</button>
      </li>

      <li>
        <button class="filter-btn">Skin Care</button>
      </li>

      <li>
        <button class="filter-btn">Household</button>
      </li>

      <li>
        <button class="filter-btn">Packages</button>
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