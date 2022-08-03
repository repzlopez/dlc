<?php

foreach ($data as $a) {
    foreach ($a as $k => $v) $$k = $v;

    $badge = ''; // '<div class="card-badge">' . $id . '</div>';

    $x .= '<li class="product-item">
        <div class="product-card" tabindex="0">

          <figure class="card-banner">
            <img src="' . BAK_ROOT . '/images/products/' . $img . '" loading="lazy" alt="' . $name . '" class="image-contain">

            ' . $badge . '

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
              <a href="?item=' . $id . '">' . $name . '</a>
            </h3>

            <data class="card-price" value="' . $srp . '">' . number_format($srp, 2) . '</data>

          </div>

        </div>
      </li>
  ';
}

?>